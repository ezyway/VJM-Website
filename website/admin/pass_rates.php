<?php
/**
 * Academic Pass Rates Management Module
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/crud.php';
requireAuth();

$db = getDB();
$isDrawerMode = isset($_GET['drawer']) && $_GET['drawer'] === '1';

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    crudCsrfGuard('pass_rates.php');
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        crudDelete($db, 'pass_rates', (int)($_POST['id'] ?? 0), 'Pass rate record deleted.', $isDrawerMode, 'pass_rates.php');
    }

    if ($action === 'save') {
        $id         = (int)($_POST['id'] ?? 0);
        $year       = trim($_POST['year'] ?? '');
        $bca        = trim($_POST['bca'] ?? '—');
        $bsc        = trim($_POST['bsc'] ?? '—');
        $bba        = trim($_POST['bba'] ?? '—');
        $bcom       = trim($_POST['bcom'] ?? '—');
        $bsw        = trim($_POST['bsw'] ?? '—');
        $pgdca      = trim($_POST['pgdca'] ?? '—');
        $msc_it     = trim($_POST['msc_it'] ?? '—');
        $mcom       = trim($_POST['mcom'] ?? '—');
        $msc_chem   = trim($_POST['msc_chem'] ?? '—');
        $is_latest  = !empty($_POST['is_latest']) ? 1 : 0;

        if (empty($year)) {
            crudFormFail('Academic Year is required.');
        }

        if ($is_latest) {
            $db->exec('UPDATE pass_rates SET is_latest = 0');
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE pass_rates SET year = :yr, bca = :bca, bsc = :bsc, bba = :bba, bcom = :bcom, bsw = :bsw, pgdca = :pg, msc_it = :it, mcom = :mc, msc_chem = :ch, is_latest = :lat WHERE id = :id');
            $stmt->execute([
                ':yr'   => $year,
                ':bca'  => $bca,
                ':bsc'  => $bsc,
                ':bba'  => $bba,
                ':bcom' => $bcom,
                ':bsw'  => $bsw,
                ':pg'   => $pgdca,
                ':it'   => $msc_it,
                ':mc'   => $mcom,
                ':ch'   => $msc_chem,
                ':lat'  => $is_latest,
                ':id'   => $id
            ]);
            setFlash('success', 'Pass rates updated.');
        } else {
            $stmt = $db->prepare('INSERT INTO pass_rates (year, bca, bsc, bba, bcom, bsw, pgdca, msc_it, mcom, msc_chem, is_latest, sort_order) VALUES (:yr, :bca, :bsc, :bba, :bcom, :bsw, :pg, :it, :mc, :ch, :lat, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM pass_rates))');
            $stmt->execute([
                ':yr'   => $year,
                ':bca'  => $bca,
                ':bsc'  => $bsc,
                ':bba'  => $bba,
                ':bcom' => $bcom,
                ':bsw'  => $bsw,
                ':pg'   => $pgdca,
                ':it'   => $msc_it,
                ':mc'   => $mcom,
                ':ch'   => $msc_chem,
                ':lat'  => $is_latest,
            ]);
            setFlash('success', 'New academic pass rate year added.');
        }

        crudRedirect('pass_rates.php', $isDrawerMode);
    }
}

$pageTitle = 'Academic Pass Rates';
require_once __DIR__ . '/includes/header.php';

$editItem = crudLoadItem($db, 'pass_rates');
$isCreate = crudIsCreate();
$records = $db->query('SELECT * FROM pass_rates ORDER BY sort_order ASC, year DESC')->fetchAll();

if ($editItem || $isCreate) {
    crudFormPanel([
        'page'        => 'pass_rates.php',
        'pageParam'   => $drawerParam,
        'item'        => $editItem,
        'creating'    => $isCreate,
        'title'       => fn($item) => $item ? 'Edit Pass Rates (' . htmlspecialchars($item['year']) . ')' : 'Add New Academic Year Record',
        'submitLabel' => 'Save Pass Rates',
        'fields'      => [
            ['name' => 'year', 'label' => 'Academic Year *', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. 2026 or 2025-26'],
            ['name' => 'bca', 'label' => 'BCA Rate', 'type' => 'text', 'default' => '—', 'placeholder' => 'e.g. 98.40% or —'],
            ['name' => 'bsc', 'label' => 'B.Sc. Rate', 'type' => 'text', 'default' => '—', 'placeholder' => 'e.g. 100.00%'],
            ['name' => 'bba', 'label' => 'BBA Rate', 'type' => 'text', 'default' => '—', 'placeholder' => 'e.g. 96.87%'],
            ['name' => 'bcom', 'label' => 'B.Com. Rate', 'type' => 'text', 'default' => '—', 'placeholder' => 'e.g. 95.59%'],
            ['name' => 'bsw', 'label' => 'BSW Rate', 'type' => 'text', 'default' => '—', 'placeholder' => 'e.g. 100.00%'],
            ['name' => 'pgdca', 'label' => 'PGDCA Rate', 'type' => 'text', 'default' => '—', 'placeholder' => 'e.g. 100.00% or —'],
            ['name' => 'msc_it', 'label' => 'M.Sc.(IT) Rate', 'type' => 'text', 'default' => '—', 'placeholder' => 'e.g. 100.00%'],
            ['name' => 'mcom', 'label' => 'M.Com. Rate', 'type' => 'text', 'default' => '—', 'placeholder' => 'e.g. 100.00%'],
            ['name' => 'msc_chem', 'label' => 'M.Sc.(Chem) Rate', 'type' => 'text', 'default' => '—', 'placeholder' => 'e.g. 97.53%'],
            ['name' => 'is_latest', 'label' => '', 'type' => 'check', 'full' => true, 'checkText' => 'Highlight as "Latest Batch" on the Homepage table'],
        ],
    ]);
}

crudListPanel([
    'page'              => 'pass_rates.php',
    'pageParam'         => $drawerParam,
    'suffix'            => $drawerUrlSuffix,
    'listTitleHtml'     => 'Academic Pass Rate History (' . count($records) . ' years)',
    'rows'              => $records,
    'tableId'           => 'passRatesTable',
    'reorder'           => 'pass_rates',
    'searchPlaceholder' => 'Search years...',
    'emptyText'         => 'No pass rate records yet. Click <strong>Add Academic Year</strong> to start.',
    'add'               => ['url' => 'pass_rates.php?action=create', 'drawerTitle' => 'Add New Academic Year', 'label' => 'Add Academic Year'],
    'columns'           => [
        [
            'th' => 'Year',
            'td' => fn($r) => '<strong>' . htmlspecialchars($r['year']) . '</strong>' . ($r['is_latest'] ? '<span class="badge badge-success" style="margin-left: 6px;">Latest</span>' : ''),
        ],
        ['th' => 'BCA', 'td' => fn($r) => htmlspecialchars($r['bca'])],
        ['th' => 'B.Sc.', 'td' => fn($r) => htmlspecialchars($r['bsc'])],
        ['th' => 'BBA', 'td' => fn($r) => htmlspecialchars($r['bba'])],
        ['th' => 'B.Com.', 'td' => fn($r) => htmlspecialchars($r['bcom'])],
        ['th' => 'BSW', 'td' => fn($r) => htmlspecialchars($r['bsw'])],
        ['th' => 'PGDCA', 'td' => fn($r) => htmlspecialchars($r['pgdca'])],
        ['th' => 'M.Sc.(IT)', 'td' => fn($r) => htmlspecialchars($r['msc_it'])],
        ['th' => 'M.Com.', 'td' => fn($r) => htmlspecialchars($r['mcom'])],
        ['th' => 'M.Sc.(Chem)', 'td' => fn($r) => htmlspecialchars($r['msc_chem'])],
    ],
    'editUrl'           => fn($r) => 'pass_rates.php?edit=' . $r['id'],
    'actions'           => ['edit' => ['drawerTitle' => 'Edit Pass Rates'], 'delete' => true],
    'deleteConfirm'     => fn() => 'Delete this pass rate record? This cannot be undone.',
    'formPrefix'        => 'passrate',
]);

require_once __DIR__ . '/includes/footer.php';
