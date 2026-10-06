<?php
/**
 * Faculty Directory Management Module
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/crud.php';
requireAuth();

$db = getDB();
$isDrawerMode = isset($_GET['drawer']) && $_GET['drawer'] === '1';
$departments = [
    'admin'      => 'Administrators (Leadership)',
    'data-admin' => 'Data Administrators (Operations)',
    'bca'        => 'B.C.A. / M.Sc.(IT) & C.A.',
    'bsc'        => 'B.Sc. / M.Sc.(Chem.)',
    'bcom'       => 'B.Com. / M.Com.',
    'bba'        => 'B.B.A.',
    'bsw'        => 'B.S.W.'
];

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    crudCsrfGuard('faculties.php');
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        // Fetch the image path before deleting the DB record
        $stmtImg = $db->prepare('SELECT image FROM faculties WHERE id = :id');
        $stmtImg->execute([':id' => $id]);
        $imagePath = $stmtImg->fetchColumn();
        // Delete the physical file from disk BEFORE crudDelete()'s exit
        if ($imagePath) {
            $fullPath = dirname(__DIR__) . '/' . $imagePath;
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
            $webpSidecar = $fullPath . '.webp';
            if (file_exists($webpSidecar)) {
                @unlink($webpSidecar);
            }
        }
        crudDelete($db, 'faculties', $id, 'Faculty member removed successfully.', $isDrawerMode, 'faculties.php');
    }

    if ($action === 'save') {
        $id          = (int)($_POST['id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $designation = trim($_POST['designation'] ?? '');
        $depts       = isset($_POST['depts']) && is_array($_POST['depts']) ? implode(',', $_POST['depts']) : '';
        $badge       = trim($_POST['badge'] ?? '');
        $dept_label  = trim($_POST['dept_label'] ?? '');
        $featured    = !empty($_POST['featured']) ? 1 : 0;
        $currentImg  = trim($_POST['current_image'] ?? '');

        // Handle Image Upload
        $imgPath = $currentImg;
        if (!empty($_FILES['image']['name'])) {
            $upload = handleAdminUpload($_FILES['image'], 'faculties');
            if ($upload['success']) {
                $imgPath = $upload['path'];
                // Remove the previous image from disk when replacing on edit
                if ($id > 0 && $currentImg && $currentImg !== $imgPath) {
                    $oldFull = dirname(__DIR__) . '/' . $currentImg;
                    if (file_exists($oldFull)) @unlink($oldFull);
                    if (file_exists($oldFull . '.webp')) @unlink($oldFull . '.webp');
                }
            } else {
                crudFormFail('Image upload failed: ' . $upload['error']);
            }
        }

        if (empty($name) || empty($designation)) {
            crudFormFail('Name and Designation are required.');
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE faculties SET name = :n, designation = :d, depts = :dept, badge = :b, dept_label = :dl, image = :img, featured = :f WHERE id = :id');
            $stmt->execute([
                ':n'    => $name,
                ':d'    => $designation,
                ':dept' => $depts,
                ':b'    => $badge,
                ':dl'   => $dept_label,
                ':img'  => $imgPath,
                ':f'    => $featured,
                ':id'   => $id
            ]);
            setFlash('success', 'Faculty details updated successfully.');
        } else {
            $stmt = $db->prepare('INSERT INTO faculties (name, designation, depts, badge, dept_label, image, featured, sort_order) VALUES (:n, :d, :dept, :b, :dl, :img, :f, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM faculties))');
            $stmt->execute([
                ':n'    => $name,
                ':d'    => $designation,
                ':dept' => $depts,
                ':b'    => $badge,
                ':dl'   => $dept_label,
                ':img'  => $imgPath,
                ':f'    => $featured,
            ]);
            setFlash('success', 'New faculty member added successfully.');
        }

        crudRedirect('faculties.php', $isDrawerMode);
    }
}

$pageTitle = 'Faculty Directory';
require_once __DIR__ . '/includes/header.php';

$editItem = crudLoadItem($db, 'faculties');
$isCreate = crudIsCreate();
$faculties = $db->query('SELECT * FROM faculties ORDER BY sort_order ASC, id ASC')->fetchAll();

if ($editItem || $isCreate) {
    crudFormPanel([
        'page'        => 'faculties.php',
        'pageParam'   => $drawerParam,
        'item'        => $editItem,
        'creating'    => $isCreate,
        'multipart'   => true,
        'title'       => fn($item) => $item ? 'Edit Faculty Member' : 'Add New Faculty Member',
        'submitLabel' => 'Save Faculty Member',
        'hiddenHtml'  => fn($item) => '<input type="hidden" name="current_image" value="' . htmlspecialchars($item['image'] ?? '') . '">',
        'fields'      => [
            ['name' => 'name', 'label' => 'Full Name *', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Prof. Paresh Savjani'],
            ['name' => 'designation', 'label' => 'Designation *', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Incharge Principal &amp; Associate Professor'],
            ['name' => 'badge', 'label' => 'Badge Text', 'type' => 'text', 'placeholder' => 'e.g. Leadership • IT'],
            ['name' => 'dept_label', 'label' => 'Department Display Label', 'type' => 'text', 'placeholder' => 'e.g. Administration &amp; IT'],
            ['name' => 'image', 'label' => 'Profile Photo', 'type' => 'file', 'accept' => 'image/*', 'preview' => ['id' => 'facultyPhotoPreview', 'img' => fn($item) => ($item['image'] ?? '') ?: 'assets/photos/faculties/placeholder.jpg', 'hint' => 'Upload JPG, PNG or WebP image.']],
            [
                'name'  => 'depts',
                'label' => 'Associated Departments *',
                'raw'   => function ($item) use ($departments) {
                    $active = $item ? explode(',', $item['depts']) : [];
                    $html = '<div class="form-group full-width">'
                        . '<label class="form-label">Associated Departments *</label>'
                        . '<div class="pill-checkbox-group">';
                    foreach ($departments as $key => $label) {
                        $checked = in_array($key, $active) ? ' checked' : '';
                        $html .= '<label class="pill-checkbox">'
                            . '<input type="checkbox" name="depts[]" value="' . $key . '"' . $checked . '>'
                            . '<span>' . htmlspecialchars($label) . '</span>'
                            . '</label>';
                    }
                    $html .= '</div><span class="form-hint" style="margin-top: 6px;">Select all academic departments this faculty belongs to.</span></div>';
                    return $html;
                },
            ],
            ['name' => 'featured', 'label' => '', 'type' => 'switch', 'full' => true, 'checkText' => 'Feature on Leadership / Highlights Header'],
        ],
    ]);
}

crudListPanel([
    'page'              => 'faculties.php',
    'pageParam'         => $drawerParam,
    'suffix'            => $drawerUrlSuffix,
    'listTitleHtml'     => 'All Faculty Members (' . count($faculties) . ')',
    'rows'              => $faculties,
    'tableId'           => 'facultyTable',
    'reorder'           => 'faculties',
    'searchPlaceholder' => 'Search by name, designation, department...',
    'emptyText'         => 'No faculty members yet. Click <strong>Add New Faculty</strong> to build your directory.',
    'add'               => ['url' => 'faculties.php?action=create', 'drawerTitle' => 'Add New Faculty Member', 'label' => 'Add New Faculty'],
    'columns'           => [
        [
            'th' => 'Member',
            'td' => fn($r) => '<div style="display: flex; align-items: center; gap: 12px;">'
                . '<img src="../' . htmlspecialchars($r['image'] ?: 'assets/logo.ico') . '" alt="" class="preview-avatar" loading="lazy" onerror="this.src=\'../assets/logo.ico\'">'
                . '<div><strong style="color: var(--text-main); font-size: 13.5px;">' . htmlspecialchars($r['name']) . '</strong>'
                . '<div style="font-size: 11.5px; color: var(--text-muted);">' . htmlspecialchars($r['dept_label']) . '</div></div>'
                . '</div>',
        ],
        ['th' => 'Designation', 'td' => fn($r) => htmlspecialchars($r['designation'])],
        [
            'th' => 'Departments',
            'td' => function ($r) {
                $html = '';
                foreach (explode(',', $r['depts']) as $dKey) {
                    if (empty($dKey)) continue;
                    $html .= '<span class="badge badge-info" style="margin-right: 4px;">' . strtoupper(htmlspecialchars($dKey)) . '</span>';
                }
                return $html;
            },
        ],
        ['th' => 'Badge', 'td' => fn($r) => !empty($r['badge']) ? badge(htmlspecialchars($r['badge']), 'primary') : '<span style="color: var(--text-muted); font-size: 12px;">—</span>'],
        ['th' => 'Featured', 'td' => fn($r) => $r['featured'] ? badge('Featured', 'success') : badge('Standard', 'secondary')],
    ],
    'editUrl'           => fn($r) => 'faculties.php?edit=' . $r['id'],
    'actions'           => ['edit' => ['drawerTitle' => 'Edit Faculty Member'], 'delete' => true],
    'deleteConfirm'     => fn($r) => 'Remove ' . htmlspecialchars($r['name']) . ' from the faculty directory? This cannot be undone.',
    'formPrefix'        => 'faculty',
]);

require_once __DIR__ . '/includes/footer.php';
