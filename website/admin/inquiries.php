<?php
/**
 * Student Inquiries (Admission/Course Enquiries) Admin Module
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/crud.php';
requireAuth();

$db = getDB();
$isDrawerMode = isset($_GET['drawer']) && $_GET['drawer'] === '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    crudCsrfGuard('inquiries.php');
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        crudDelete($db, 'inquiries', (int)($_POST['id'] ?? 0), 'Inquiry record deleted.', $isDrawerMode, 'inquiries.php');
    }

    if ($action === 'mark_all_read') {
        $db->exec('UPDATE inquiries SET is_read = 1 WHERE is_read = 0');
        setFlash('success', 'All inquiries marked as read.');
        crudRedirect('inquiries.php', $isDrawerMode);
    }
}

$pageTitle = 'Student Inquiries';
require_once __DIR__ . '/includes/header.php';

// Viewing this page clears the "new" flag for the dashboard banner.
// Capture the count first so we can confirm what was just reviewed.
$newSinceLast = (int)$db->query('SELECT COUNT(*) FROM inquiries WHERE is_read = 0')->fetchColumn();
if (!$isDrawerMode && $newSinceLast > 0) {
    $db->exec("UPDATE inquiries SET is_read = 1 WHERE is_read = 0");
}

$inquiries = $db->query('SELECT * FROM inquiries ORDER BY created_at DESC, id DESC LIMIT 500')->fetchAll();

crudListPanel([
    'page'              => 'inquiries.php',
    'pageParam'         => $drawerParam,
    'suffix'            => $drawerUrlSuffix,
    'listTitleHtml'     => 'Admission & Course Inquiries (' . count($inquiries) . ')',
    'rows'              => $inquiries,
    'tableId'           => 'inquiriesTable',
    'searchPlaceholder' => 'Search name, phone, program...',
    'emptyText'         => 'No inquiries received yet. Submissions from the website inquiry form will appear here.',
    'columns'           => [
        ['th' => 'Date', 'td' => fn($r) => '<span style="white-space: nowrap; font-size: 12px;">' . htmlspecialchars($r['created_at']) . '</span>'],
        ['th' => 'Name', 'td' => fn($r) => '<strong style="color: var(--text-main);">' . htmlspecialchars($r['name']) . '</strong>'],
        ['th' => 'Phone', 'td' => fn($r) => '<a href="tel:' . htmlspecialchars($r['phone']) . '">' . htmlspecialchars($r['phone']) . '</a>'],
        ['th' => 'Program', 'td' => fn($r) => '<span class="badge badge-info">' . htmlspecialchars($r['program']) . '</span>'],
        ['th' => 'Qualification', 'td' => fn($r) => htmlspecialchars($r['stream'] ?: '—')],
        ['th' => 'Message', 'td' => fn($r) => '<span title="' . htmlspecialchars($r['message']) . '">' . htmlspecialchars(mb_strimwidth($r['message'], 0, 60, '…')) . '</span>'],
    ],
    'actions'           => ['delete' => true],
    'deleteConfirm'     => fn() => 'Delete this inquiry? This cannot be undone.',
    'formPrefix'        => 'inquiry',
]);
?>

<?php if ($newSinceLast > 0 && !$isDrawerMode): ?>
<div style="margin-bottom: 16px; padding: 12px 16px; background: rgba(16,185,129,0.08); border: 1px solid var(--primary); border-radius: var(--radius-md); font-size: 13.5px;">
    <strong style="color: var(--primary);"><?= $newSinceLast ?> new <?= $newSinceLast === 1 ? 'inquiry' : 'inquiries' ?></strong>
    <span style="color: var(--text-muted);"> marked as reviewed. The dashboard notification is now cleared.</span>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
