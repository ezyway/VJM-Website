<?php
/**
 * Scholarships & Financial Aid Management Module
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/crud.php';
requireAuth();

$db = getDB();
$isDrawerMode = isset($_GET['drawer']) && $_GET['drawer'] === '1';

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    crudCsrfGuard('scholarships.php');

    $action = $_POST['action'] ?? '';

    // The shared list panel posts a generic 'delete'; dispatch by tab.
    if ($action === 'delete') {
        $action = (strpos($_SERVER['REQUEST_URI'] ?? '', 'tab=portals') !== false) ? 'delete_portal' : 'delete_record';
    }

    if ($action === 'delete_record') {
        crudDelete($db, 'scholarships', (int)($_POST['id'] ?? 0), 'Scholarship year record removed.', $isDrawerMode, 'scholarships.php?tab=records');
    }

    if ($action === 'delete_portal') {
        crudDelete($db, 'scholarship_portals', (int)($_POST['id'] ?? 0), 'Portal removed.', $isDrawerMode, 'scholarships.php?tab=portals');
    }

    if ($action === 'save_record') {
        $id         = (int)($_POST['id'] ?? 0);
        $year       = trim($_POST['year'] ?? '');
        $amount_str = trim($_POST['amount_str'] ?? '');
        $numeric    = (int)preg_replace('/[^0-9]/', '', $amount_str);
        $status     = trim($_POST['status'] ?? 'Completed');

        if (empty($year) || empty($amount_str)) {
            crudFormFail('Year and Amount are required.');
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE scholarships SET year = :yr, amount_str = :amt, amount_numeric = :num, status = :st WHERE id = :id');
            $stmt->execute([':yr' => $year, ':amt' => $amount_str, ':num' => $numeric, ':st' => $status, ':id' => $id]);
            setFlash('success', 'Scholarship record updated.');
        } else {
            $stmt = $db->prepare('INSERT INTO scholarships (year, amount_str, amount_numeric, status, sort_order) VALUES (:yr, :amt, :num, :st, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM scholarships))');
            $stmt->execute([':yr' => $year, ':amt' => $amount_str, ':num' => $numeric, ':st' => $status]);
            setFlash('success', 'New scholarship year added.');
        }

        header('Location: ' . ($isDrawerMode ? $_SERVER['REQUEST_URI'] : 'scholarships.php?tab=records'));
        exit;
    }

    if ($action === 'save_portal') {
        $id          = (int)($_POST['id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $provider    = trim($_POST['provider'] ?? '');
        $badge       = trim($_POST['badge'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $link        = trim($_POST['link'] ?? '');
        $icon        = trim($_POST['icon'] ?? 'shield');

        if (empty($name) || empty($link)) {
            crudFormFail('Portal name and link are required.');
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE scholarship_portals SET name = :n, provider = :p, badge = :b, description = :d, link = :l, icon = :i WHERE id = :id');
            $stmt->execute([':n' => $name, ':p' => $provider, ':b' => $badge, ':d' => $description, ':l' => $link, ':i' => $icon, ':id' => $id]);
            setFlash('success', 'Portal updated.');
        } else {
            $stmt = $db->prepare('INSERT INTO scholarship_portals (name, provider, badge, description, link, icon, sort_order) VALUES (:n, :p, :b, :d, :l, :i, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM scholarship_portals))');
            $stmt->execute([':n' => $name, ':p' => $provider, ':b' => $badge, ':d' => $description, ':l' => $link, ':i' => $icon]);
            setFlash('success', 'New portal added.');
        }

        header('Location: ' . ($isDrawerMode ? $_SERVER['REQUEST_URI'] : 'scholarships.php?tab=portals'));
        exit;
    }
}

$pageTitle = 'Scholarships & Aid';
require_once __DIR__ . '/includes/header.php';

$tab = $_GET['tab'] ?? 'records';

// Fetch edit targets
$editRecord = null;
if (isset($_GET['edit_record'])) {
    $rId = (int)$_GET['edit_record'];
    $stmt = $db->prepare('SELECT * FROM scholarships WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $rId]);
    $editRecord = $stmt->fetch();
}

$editPortal = null;
if (isset($_GET['edit_portal'])) {
    $pId = (int)$_GET['edit_portal'];
    $stmt = $db->prepare('SELECT * FROM scholarship_portals WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $pId]);
    $editPortal = $stmt->fetch();
}

$isCreateRecord = isset($_GET['action']) && $_GET['action'] === 'create_record';
$isCreatePortal = isset($_GET['action']) && $_GET['action'] === 'create_portal';

$records = $db->query('SELECT * FROM scholarships ORDER BY sort_order ASC, id ASC')->fetchAll();
$portals = $db->query('SELECT * FROM scholarship_portals ORDER BY sort_order ASC, id ASC')->fetchAll();
$totalDisbursed = $db->query('SELECT SUM(amount_numeric) FROM scholarships')->fetchColumn() ?: 0;
?>

<!-- Total Disbursed Metric -->
<div class="stats-grid" style="margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Total Historical Aid Disbursed</div>
            <div class="stat-value">₹ <?= number_format($totalDisbursed) ?></div>
        </div>
        <div class="stat-icon success">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        </div>
    </div>
</div>

<!-- Tab Navigation -->
<div class="panel" style="margin-bottom: 24px; padding: 0; background: var(--bg-panel); border-radius: var(--radius-lg); overflow: hidden;">
    <div class="panel-header" style="padding: 0; border: 0; background: var(--bg-input);">
        <nav class="tab-nav">
            <a href="scholarships.php?tab=records<?= $drawerSuffix ?>" class="tab-link <?= $tab === 'records' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                <span>Yearly Records</span>
                <span class="badge badge-primary" style="margin-left: 8px;"><?= count($records) ?></span>
            </a>
            <a href="scholarships.php?tab=portals<?= $drawerSuffix ?>" class="tab-link <?= $tab === 'portals' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                <span>Portals & Schemes</span>
                <span class="badge badge-info" style="margin-left: 8px;"><?= count($portals) ?></span>
            </a>
        </nav>
    </div>
</div>

<?php if (($editRecord || $isCreateRecord) && $tab === 'records'): ?>
<?php
crudFormPanel([
    'page'        => 'scholarships.php',
    'pageParam'   => $isDrawerMode ? '?tab=records&drawer=1' : '?tab=records',
    'item'        => $editRecord,
    'creating'    => $isCreateRecord,
    'title'       => fn($item) => $item && isset($item['year']) ? 'Edit Scholarship Year' : 'Add Scholarship Year Record',
    'submitLabel' => 'Save Record',
    'previewType' => 'none',
    'hiddenHtml'  => '<input type="hidden" name="action" value="save_record">',
    'fields'      => [
        ['name' => 'year', 'label' => 'Academic Year *', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. 2024 - 2025'],
        ['name' => 'amount_str', 'label' => 'Disbursed Amount String *', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. ₹ 21,22,000'],
        ['name' => 'status', 'label' => 'Status Badge', 'type' => 'text', 'default' => 'Completed', 'placeholder' => 'e.g. Latest, Completed, Peak'],
    ],
]);
?>
<?php endif; ?>

<?php if (($editPortal || $isCreatePortal) && $tab === 'portals'): ?>
<?php
crudFormPanel([
    'page'        => 'scholarships.php',
    'pageParam'   => $isDrawerMode ? '?tab=portals&drawer=1' : '?tab=portals',
    'item'        => $editPortal,
    'creating'    => $isCreatePortal,
    'title'       => fn($item) => $item && isset($item['name']) ? 'Edit Portal' : 'Add Scholarship Portal',
    'submitLabel' => 'Save Portal',
    'previewType' => 'none',
    'hiddenHtml'  => '<input type="hidden" name="action" value="save_portal">',
    'fields'      => [
        ['name' => 'name', 'label' => 'Portal Name *', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Digital Gujarat Scholarship Portal'],
        ['name' => 'provider', 'label' => 'Provider Authority', 'type' => 'text', 'placeholder' => 'e.g. Government of Gujarat'],
        ['name' => 'badge', 'label' => 'Badge Label', 'type' => 'text', 'placeholder' => 'e.g. State Government'],
        ['name' => 'icon', 'label' => 'Icon', 'type' => 'select', 'default' => 'shield', 'options' => [
            'shield' => 'Shield', 'award' => 'Award', 'cap' => 'Cap (Graduation)', 'briefcase' => 'Briefcase (Career)', 'globe' => 'Globe (National)',
        ]],
        ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'full' => true, 'rows' => 3, 'placeholder' => 'Brief description of the portal and eligibility...'],
        ['name' => 'link', 'label' => 'Portal URL *', 'type' => 'url', 'full' => true, 'required' => true, 'placeholder' => 'https://...'],
    ],
]);
?>
<?php endif; ?>

<?php if ($tab === 'records' && !$editRecord && !$isCreateRecord): ?>
<?php
crudListPanel([
    'page'              => 'scholarships.php',
    'pageParam'         => $isDrawerMode ? '?tab=records&drawer=1' : '?tab=records',
    'suffix'            => $drawerUrlSuffix,
    'listTitleHtml'     => 'Yearly Disbursement Records (' . count($records) . ' years)',
    'rows'              => $records,
    'tableId'           => 'scholarshipsRecordsTable',
    'reorder'           => 'scholarships',
    'searchPlaceholder' => 'Search years or status...',
    'emptyText'         => 'No scholarship records yet. Click <strong>Add Academic Year</strong> to start tracking aid.',
    'add'               => ['url' => 'scholarships.php?tab=records&action=create_record', 'drawerTitle' => 'Add Academic Year', 'label' => 'Add Academic Year'],
    'columns'           => [
        ['th' => 'Academic Year', 'td' => fn($r) => '<strong style="color: var(--text-main);">' . htmlspecialchars($r['year']) . '</strong>'],
        ['th' => 'Amount Disbursed', 'td' => fn($r) => '<span style="color: var(--success); font-weight: 600; font-size: 14px;">' . htmlspecialchars($r['amount_str']) . '</span>'],
        ['th' => 'Status', 'td' => fn($r) => '<span class="badge badge-' . ($r['status'] === 'Latest' ? 'success' : ($r['status'] === 'Peak' ? 'warning' : 'secondary')) . '">' . htmlspecialchars($r['status']) . '</span>'],
    ],
    'editUrl'           => fn($r) => 'scholarships.php?tab=records&edit_record=' . $r['id'],
    'actions'           => ['edit' => ['drawerTitle' => 'Edit Scholarship Year'], 'delete' => true],
    'deleteConfirm'     => fn() => 'Delete this scholarship record?',
    'formPrefix'        => 'record',
]);
?>
<?php endif; ?>

<?php if ($tab === 'portals' && !$editPortal && !$isCreatePortal): ?>
<?php
crudListPanel([
    'page'              => 'scholarships.php',
    'pageParam'         => $isDrawerMode ? '?tab=portals&drawer=1' : '?tab=portals',
    'suffix'            => $drawerUrlSuffix,
    'listTitleHtml'     => 'Government Scholarship Portals (' . count($portals) . ')',
    'rows'              => $portals,
    'tableId'           => 'scholarshipPortalsTable',
    'reorder'           => 'scholarship_portals',
    'searchPlaceholder' => 'Search portals...',
    'emptyText'         => 'No scholarship portals configured yet. Click <strong>Add Portal</strong> to link one.',
    'add'               => ['url' => 'scholarships.php?tab=portals&action=create_portal', 'drawerTitle' => 'Add Portal', 'label' => 'Add Portal'],
    'columns'           => [
        ['th' => 'Portal Name', 'td' => fn($r) => '<strong style="color: var(--text-main);">' . htmlspecialchars($r['name']) . '</strong>'],
        ['th' => 'Provider', 'td' => fn($r) => htmlspecialchars($r['provider'] ?: '—')],
        ['th' => 'Badge', 'td' => fn($r) => !empty($r['badge']) ? '<span class="badge badge-info">' . htmlspecialchars($r['badge']) . '</span>' : '<span style="color: var(--text-muted); font-size: 12px;">—</span>'],
        ['th' => 'Link', 'td' => fn($r) => '<span style="max-width: 250px; font-size: 12.5px; color: var(--text-muted);"><a href="' . htmlspecialchars($r['link']) . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($r['link']) . '</a></span>'],
    ],
    'editUrl'           => fn($r) => 'scholarships.php?tab=portals&edit_portal=' . $r['id'],
    'actions'           => ['edit' => ['drawerTitle' => 'Edit Portal'], 'delete' => true],
    'deleteConfirm'     => fn() => 'Delete this portal?',
    'formPrefix'        => 'portal',
]);
?>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
