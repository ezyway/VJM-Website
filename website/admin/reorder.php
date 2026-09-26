<?php
/**
 * Generic Row Reorder Endpoint
 * Accepts POST { csrf_token, table, order[] } and updates sort_order for the given IDs.
 * Returns JSON.
 */

require_once __DIR__ . '/includes/auth.php';
header('Content-Type: application/json');

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid or expired security token.']);
    exit;
}

// Rate limiting: prevent rapid-fire reorder requests
$lastOrder = $_SESSION['last_reorder'] ?? 0;
if (time() - $lastOrder < 2) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Please wait before reordering again']);
    exit;
}
$_SESSION['last_reorder'] = time();

$table = preg_replace('/[^a-z_]/', '', (string)($_POST['table'] ?? ''));

$allowed = [
    'scholarships',
    'scholarship_portals',
    'gallery_albums',
    'gallery_photos',
    'faculties',
    'events',
    'rankers',
    'testimonials',
    'labs',
    'courses',
    'pass_rates',
    'magazines',
];

if (!in_array($table, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Table not allowed.']);
    exit;
}

$order = $_POST['order'] ?? null;

// Handle JSON-encoded order (from fetch with URLSearchParams)
if (is_string($order) && str_starts_with(trim($order), '[')) {
    $decoded = json_decode($order, true);
    if (is_array($decoded)) {
        $order = $decoded;
    }
}

// Also handle order[] repeated fields
if (isset($_POST['order']) && is_array($_POST['order']) && $order === null) {
    $order = $_POST['order'];
}

if (!is_array($order) || empty($order)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing or invalid order payload.']);
    exit;
}

// Coerce to integers
$ids = [];
foreach ($order as $id) {
    $id = (int)$id;
    if ($id > 0) $ids[] = $id;
}
$ids = array_values(array_unique($ids));

if (empty($ids)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No valid IDs supplied.']);
    exit;
}

try {
    $db = getDB();
    $db->beginTransaction();

    // Fetch all valid IDs in one query before the loop
    $validIds = $db->query("SELECT id FROM {$table}")->fetchAll(PDO::FETCH_COLUMN);
    $validIdSet = array_flip($validIds);

    $update = $db->prepare("UPDATE {$table} SET sort_order = :so WHERE id = :id");

    $position = 1;
    foreach ($ids as $id) {
        if (!isset($validIdSet[$id])) {
            continue;
        }
        $update->execute([':so' => $position, ':id' => $id]);
        $position++;
    }

    $db->commit();

    echo json_encode(['success' => true, 'count' => count($ids), 'table' => $table]);
} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
