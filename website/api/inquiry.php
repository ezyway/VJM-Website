<?php
/**
 * Public admission/course inquiry submission endpoint.
 * POST JSON: { name, phone, program, stream?, message?, source_page? }
 */

header('Content-Type: application/json');
header('Cache-Control: no-store');

require_once __DIR__ . '/../admin/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Light session-based rate limiting
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$now = time();
$last = $_SESSION['last_inquiry_post'] ?? 0;
if ($now - $last < 20) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Please wait a moment before submitting again.']);
    exit;
}
$_SESSION['last_inquiry_post'] = $now;

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    // Fall back to form-encoded posts
    $data = $_POST;
}

$name    = trim((string)($data['name'] ?? ''));
$phone   = preg_replace('/[^0-9+]/', '', (string)($data['phone'] ?? ''));
$program = trim((string)($data['program'] ?? ''));
$stream  = trim((string)($data['stream'] ?? ''));
$message = trim((string)($data['message'] ?? ''));
$source  = trim((string)($data['source_page'] ?? ($_SERVER['HTTP_REFERER'] ?? '')));

if (mb_strlen($name) < 2) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'Please enter your name.']);
    exit;
}
if (!preg_match('/^[0-9+]{10,15}$/', $phone)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'Enter a valid phone number.']);
    exit;
}
if ($program === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => 'Please select a program.']);
    exit;
}

$db = getDB();
$stmt = $db->prepare('INSERT INTO inquiries (name, phone, program, stream, message, source_page, ip_address) VALUES (:n, :p, :prog, :st, :msg, :src, :ip)');
$stmt->execute([
    ':n'   => mb_substr($name, 0, 120),
    ':p'   => $phone,
    ':prog'=> mb_substr($program, 0, 120),
    ':st'  => mb_substr($stream, 0, 120),
    ':msg' => mb_substr($message, 0, 2000),
    ':src' => mb_substr($source, 0, 255),
    ':ip'  => $_SERVER['REMOTE_ADDR'] ?? '',
]);

echo json_encode(['success' => true]);
