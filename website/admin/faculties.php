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
        crudDelete($db, 'faculties', (int)($_POST['id'] ?? 0), 'Faculty member removed successfully.', $isDrawerMode, 'faculties.php');
    }

    if ($action === 'save') {
        $id          = (int)($_POST['id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $designation = trim($_POST['designation'] ?? '');
        $depts       = isset($_POST['depts']) && is_array($_POST['depts']) ? implode(',', $_POST['depts']) : '';
        $badge       = trim($_POST['badge'] ?? '');
        $dept_label  = trim($_POST['dept_label'] ?? '');
        $featured    = !empty($_POST['featured']) ? 1 : 0;
        $sort_order  = (int)($_POST['sort_order'] ?? 0);
        $currentImg  = trim($_POST['current_image'] ?? '');

        // Handle Image Upload
        $imgPath = $currentImg;
        if (!empty($_FILES['image']['name'])) {
            $upload = handleAdminUpload($_FILES['image'], 'faculties');
            if ($upload['success']) {
                $imgPath = $upload['path'];
            } else {
                setFlash('danger', 'Image upload failed: ' . $upload['error']);
                crudRedirect('faculties.php', $isDrawerMode);
            }
        }

        if (empty($name) || empty($designation)) {
            setFlash('danger', 'Name and Designation are required.');
            crudRedirect('faculties.php', $isDrawerMode);
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE faculties SET name = :n, designation = :d, depts = :dept, badge = :b, dept_label = :dl, image = :img, featured = :f, sort_order = :s WHERE id = :id');
            $stmt->execute([
                ':n'    => $name,
                ':d'    => $designation,
                ':dept' => $depts,
                ':b'    => $badge,
                ':dl'   => $dept_label,
                ':img'  => $imgPath,
                ':f'    => $featured,
                ':s'    => $sort_order,
                ':id'   => $id
            ]);
            setFlash('success', 'Faculty details updated successfully.');
        } else {
            $stmt = $db->prepare('INSERT INTO faculties (name, designation, depts, badge, dept_label, image, featured, sort_order) VALUES (:n, :d, :dept, :b, :dl, :img, :f, :s)');
            $stmt->execute([
                ':n'    => $name,
                ':d'    => $designation,
                ':dept' => $depts,
                ':b'    => $badge,
                ':dl'   => $dept_label,
                ':img'  => $imgPath,
                ':f'    => $featured,
                ':s'    => $sort_order
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
            ['name' => 'sort_order', 'label' => 'Sort Order Priority', 'type' => 'number', 'default' => '1'],
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
    'searchPlaceholder' => 'Search by name, designation, department...',
    'emptyText'         => 'No faculty members yet. Click <strong>Add New Faculty</strong> to build your directory.',
    'add'               => ['url' => 'faculties.php?action=create', 'drawerTitle' => 'Add New Faculty Member', 'label' => 'Add New Faculty'],
    'columns'           => [
        ['th' => 'Order', 'td' => fn($r) => '<span class="badge badge-secondary">#' . (int)$r['sort_order'] . '</span>'],
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
