<?php
/**
 * E-Magazines & Publications Management Module
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/crud.php';
requireAuth();

$db = getDB();
$isDrawerMode = isset($_GET['drawer']) && $_GET['drawer'] === '1';

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    crudCsrfGuard('magazines.php');
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        crudDelete($db, 'magazines', (int)($_POST['id'] ?? 0), 'Magazine edition deleted.', $isDrawerMode, 'magazines.php');
    }

    if ($action === 'save') {
        $id          = (int)($_POST['id'] ?? 0);
        $year        = trim($_POST['year'] ?? '');
        $title       = trim($_POST['title'] ?? '');
        $edition     = trim($_POST['edition'] ?? '');
        $theme       = trim($_POST['theme'] ?? '');
        $file_size   = trim($_POST['file_size'] ?? '');
        $pages       = trim($_POST['pages'] ?? 'Full Edition');
        $badge       = trim($_POST['badge'] ?? '');
        $sort_order  = (int)($_POST['sort_order'] ?? 0);
        $currentFile = trim($_POST['current_file'] ?? '');

        $filePath = $currentFile;
        if (!empty($_FILES['pdf_file']['name'])) {
            $upload = handleAdminUpload($_FILES['pdf_file'], 'emag', ['pdf']);
            if ($upload['success']) {
                $filePath = $upload['path'];
                $bytes = $_FILES['pdf_file']['size'];
                $file_size = '~' . round($bytes / (1024 * 1024), 1) . ' MB';
            } else {
                setFlash('danger', 'PDF upload failed: ' . $upload['error']);
                crudRedirect('magazines.php', $isDrawerMode);
            }
        }

        if (empty($title) || empty($year) || empty($filePath)) {
            setFlash('danger', 'Title, Year, and PDF file are required.');
            crudRedirect('magazines.php', $isDrawerMode);
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE magazines SET year = :yr, title = :t, edition = :ed, theme = :th, file_path = :fp, file_size = :fs, pages = :pg, badge = :b, sort_order = :so WHERE id = :id');
            $stmt->execute([
                ':yr' => $year,
                ':t'  => $title,
                ':ed' => $edition,
                ':th' => $theme,
                ':fp' => $filePath,
                ':fs' => $file_size,
                ':pg' => $pages,
                ':b'  => $badge,
                ':so' => $sort_order,
                ':id' => $id
            ]);
            setFlash('success', 'Magazine details updated.');
        } else {
            $stmt = $db->prepare('INSERT INTO magazines (year, title, edition, theme, file_path, file_size, pages, badge, sort_order) VALUES (:yr, :t, :ed, :th, :fp, :fs, :pg, :b, :so)');
            $stmt->execute([
                ':yr' => $year,
                ':t'  => $title,
                ':ed' => $edition,
                ':th' => $theme,
                ':fp' => $filePath,
                ':fs' => $file_size,
                ':pg' => $pages,
                ':b'  => $badge,
                ':so' => $sort_order
            ]);
            setFlash('success', 'New magazine publication added.');
        }

        crudRedirect('magazines.php', $isDrawerMode);
    }
}

$pageTitle = 'E-Magazines & PDFs';
require_once __DIR__ . '/includes/header.php';

$editItem = crudLoadItem($db, 'magazines');
$isCreate = crudIsCreate();
$magazines = $db->query('SELECT * FROM magazines ORDER BY sort_order ASC, year DESC')->fetchAll();

if ($editItem || $isCreate) {
    crudFormPanel([
        'page'        => 'magazines.php',
        'pageParam'   => $drawerParam,
        'item'        => $editItem,
        'creating'    => $isCreate,
        'multipart'   => true,
        'title'       => fn($item) => $item ? 'Edit Magazine: ' . htmlspecialchars($item['title']) : 'Upload New E-Magazine Edition',
        'submitLabel' => 'Save Publication',
        'hiddenHtml'  => '<input type="hidden" name="current_file" value="' . htmlspecialchars($editItem['file_path'] ?? '') . '">',
        'fields'      => [
            ['name' => 'year', 'label' => 'Publication Year *', 'type' => 'text', 'required' => true, 'createDefault' => date('Y'), 'placeholder' => 'e.g. 2025'],
            ['name' => 'edition', 'label' => 'Edition Label', 'type' => 'text', 'createDefault' => 'Edition 2025', 'placeholder' => 'e.g. Edition 2025'],
            ['name' => 'title', 'label' => 'Magazine Title *', 'type' => 'text', 'full' => true, 'required' => true, 'placeholder' => 'e.g. Shri V.J. Modha College Annual E-Magazine 2025'],
            ['name' => 'theme', 'label' => 'Edition Theme / Subtitle', 'type' => 'text', 'full' => true, 'placeholder' => 'e.g. Resilience, Innovation &amp; Digital Transformation'],
            ['name' => 'badge', 'label' => 'Badge', 'type' => 'text', 'createDefault' => 'Latest Edition', 'placeholder' => 'e.g. Latest Edition or Archive'],
            ['name' => 'file_size', 'label' => 'File Size Label', 'type' => 'text', 'placeholder' => 'e.g. ~31.6 MB (auto-calculated on upload)'],
            ['name' => 'sort_order', 'label' => 'Display Priority', 'type' => 'number', 'default' => '1'],
            [
                'name'     => 'pdf_file',
                'label'    => 'Upload Magazine PDF Document *',
                'type'     => 'file',
                'full'     => true,
                'accept'   => 'application/pdf',
                'hintHtml' => function ($item) {
                    if (empty($item['file_path'])) return '';
                    return 'Current file: <a href="../' . htmlspecialchars($item['file_path']) . '" target="_blank" style="color: var(--primary);">' . htmlspecialchars($item['file_path']) . '</a>';
                },
            ],
        ],
    ]);
}

crudListPanel([
    'page'              => 'magazines.php',
    'pageParam'         => $drawerParam,
    'suffix'            => $drawerUrlSuffix,
    'listTitleHtml'     => 'E-Magazines &amp; Publications (' . count($magazines) . ')',
    'rows'              => $magazines,
    'tableId'           => 'magazinesTable',
    'searchPlaceholder' => 'Search magazines...',
    'emptyText'         => 'No magazines uploaded yet. Click <strong>Upload New Magazine</strong> to publish an edition.',
    'add'               => ['url' => 'magazines.php?action=create', 'drawerTitle' => 'Upload New E-Magazine', 'label' => 'Upload New Magazine'],
    'columns'           => [
        ['th' => 'Year', 'td' => fn($r) => '<strong style="color: var(--primary);">' . htmlspecialchars($r['year']) . '</strong>'],
        ['th' => 'Magazine Title', 'td' => fn($r) => '<strong style="color: var(--text-main);">' . htmlspecialchars($r['title']) . '</strong>'],
        ['th' => 'Edition &amp; Theme', 'td' => fn($r) => '<div>' . htmlspecialchars($r['edition']) . '</div><div style="font-size: 11.5px; color: var(--text-muted);">' . htmlspecialchars($r['theme']) . '</div>'],
        ['th' => 'Size', 'td' => fn($r) => badge(htmlspecialchars($r['file_size']), 'secondary')],
        ['th' => 'Badge', 'td' => fn($r) => !empty($r['badge']) ? badge(htmlspecialchars($r['badge']), $r['badge'] === 'Latest Edition' ? 'success' : 'secondary') : ''],
    ],
    'editUrl'           => fn($r) => 'magazines.php?edit=' . $r['id'],
    'actions'           => [
        'edit'   => ['drawerTitle' => 'Edit Magazine'],
        'view'   => ['label' => 'View', 'url' => fn($r) => '../' . $r['file_path'], 'blank' => true, 'title' => 'View PDF', 'class' => 'btn-secondary'],
        'delete' => true,
    ],
    'deleteConfirm'     => fn() => 'Delete this magazine publication? This cannot be undone.',
    'formPrefix'        => 'magazine',
]);

require_once __DIR__ . '/includes/footer.php';
