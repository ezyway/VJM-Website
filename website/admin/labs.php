<?php
/**
 * Labs & Infrastructure Management Module
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/crud.php';
requireAuth();

$db = getDB();
$isDrawerMode = isset($_GET['drawer']) && $_GET['drawer'] === '1';

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    crudCsrfGuard('labs.php');
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        crudDelete($db, 'labs', (int)($_POST['id'] ?? 0), 'Laboratory facility deleted.', $isDrawerMode, 'labs.php');
    }

    if ($action === 'save') {
        $id          = (int)($_POST['id'] ?? 0);
        $slug        = strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', $_POST['slug'] ?? '')));
        $name        = trim($_POST['name'] ?? '');
        $code        = trim($_POST['code'] ?? '');
        $tagline     = trim($_POST['tagline'] ?? '');
        $badge       = trim($_POST['badge'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // Features list
        $rawFeat = explode("\n", str_replace("\r", "", $_POST['features'] ?? ''));
        $features = array_values(array_filter(array_map('trim', $rawFeat)));
        $featuresJson = json_encode($features);

        // Specs key-value pairs (Key: Value per line)
        $rawSpecs = explode("\n", str_replace("\r", "", $_POST['specs'] ?? ''));
        $specs = [];
        foreach ($rawSpecs as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            if (strpos($line, ':') !== false) {
                [$k, $v] = explode(':', $line, 2);
                $specs[trim($k)] = trim($v);
            }
        }
        $specsJson = json_encode($specs);

        if (empty($slug) || empty($name)) {
            crudFormFail('Lab slug and name are required.');
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE labs SET slug = :s, name = :n, code = :c, tagline = :t, badge = :b, description = :d, features = :f, specs = :sp WHERE id = :id');
            $stmt->execute([
                ':s'  => $slug,
                ':n'  => $name,
                ':c'  => $code,
                ':t'  => $tagline,
                ':b'  => $badge,
                ':d'  => $description,
                ':f'  => $featuresJson,
                ':sp' => $specsJson,
                ':id' => $id
            ]);
            setFlash('success', 'Lab details updated successfully.');
        } else {
            $stmt = $db->prepare('INSERT INTO labs (slug, name, code, tagline, badge, description, features, specs, sort_order) VALUES (:s, :n, :c, :t, :b, :d, :f, :sp, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM labs))');
            $stmt->execute([
                ':s'  => $slug,
                ':n'  => $name,
                ':c'  => $code,
                ':t'  => $tagline,
                ':b'  => $badge,
                ':d'  => $description,
                ':f'  => $featuresJson,
                ':sp' => $specsJson,
            ]);
            setFlash('success', 'New lab facility created.');
        }

        crudRedirect('labs.php', $isDrawerMode);
    }
}

$pageTitle = 'Labs & Facilities';
require_once __DIR__ . '/includes/header.php';

$editItem = crudLoadItem($db, 'labs');
$isCreate = crudIsCreate();
$labs = $db->query('SELECT * FROM labs ORDER BY sort_order ASC, id ASC')->fetchAll();

if ($editItem || $isCreate) {
    crudFormPanel([
        'page'        => 'labs.php',
        'pageParam'   => $drawerParam,
        'item'        => $editItem,
        'creating'    => $isCreate,
        'title'       => fn($item) => $item ? 'Edit Facility: ' . htmlspecialchars($item['name']) : 'Add New Lab Facility',
        'submitLabel' => 'Save Laboratory',
        'fields'      => [
            ['name' => 'slug', 'label' => 'Identifier Slug * (Unique)', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. computer, chemistry, physics'],
            ['name' => 'name', 'label' => 'Laboratory Full Name *', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Computer &amp; Advanced IT Lab'],
            ['name' => 'code', 'label' => 'Short Code / Tab Label *', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Computer Lab'],
            ['name' => 'badge', 'label' => 'Department Badge', 'type' => 'text', 'placeholder' => 'e.g. IT &amp; Computer Applications'],
            ['name' => 'tagline', 'label' => 'Tagline', 'type' => 'text', 'full' => true, 'placeholder' => 'e.g. High-Speed Computing, Modern IDEs &amp; Software Innovation'],
            ['name' => 'description', 'label' => 'Detailed Description', 'type' => 'textarea', 'full' => true, 'rows' => 3],
            ['name' => 'features', 'label' => 'Key Features (One feature per line)', 'type' => 'textarea', 'full' => true, 'rows' => 4, 'placeholder' => 'High-Speed Gigabit LAN &amp; Enterprise Wi-Fi&#10;Latest Development IDEs, Python, Java', 'load' => fn($item) => is_array($arr = json_decode($item['features'] ?? '[]', true)) ? implode("\n", $arr) : ''],
            ['name' => 'specs', 'label' => 'Specifications (Key: Value per line)', 'type' => 'textarea', 'full' => true, 'rows' => 4, 'placeholder' => 'Capacity: 120+ Workstations&#10;Networking: High-Speed Fiber Optic&#10;Operating Systems: Windows 11 &amp; Linux Ubuntu', 'load' => function ($item) {
                $specs = json_decode($item['specs'] ?? '{}', true);
                $text = '';
                if (is_array($specs)) {
                    foreach ($specs as $k => $v) $text .= "{$k}: {$v}\n";
                }
                return $text;
            }],
        ],
    ]);
}

crudListPanel([
    'page'              => 'labs.php',
    'pageParam'         => $drawerParam,
    'suffix'            => $drawerUrlSuffix,
    'listTitleHtml'     => 'Laboratories &amp; Campus Facilities (' . count($labs) . ')',
    'rows'              => $labs,
    'tableId'           => 'labsTable',
    'reorder'           => 'labs',
    'searchPlaceholder' => 'Search labs...',
    'emptyText'         => 'No labs configured yet. Click <strong>Add New Lab</strong> to get started.',
    'add'               => ['url' => 'labs.php?action=create', 'drawerTitle' => 'Add New Lab Facility', 'label' => 'Add New Lab'],
    'columns'           => [
        ['th' => 'Code', 'td' => fn($r) => badge(htmlspecialchars($r['code']), 'primary')],
        ['th' => 'Facility Name', 'td' => fn($r) => '<strong style="color: var(--text-main);">' . htmlspecialchars($r['name']) . '</strong>'],
        ['th' => 'Badge', 'td' => fn($r) => badge(htmlspecialchars($r['badge']), 'info')],
        ['th' => 'Tagline', 'td' => fn($r) => '<div style="color: var(--text-muted); font-size: 12.5px; max-width: 250px;">' . htmlspecialchars(mb_strimwidth($r['tagline'], 0, 60, '...')) . '</div>'],
    ],
    'editUrl'           => fn($r) => 'labs.php?edit=' . $r['id'],
    'actions'           => ['edit' => ['drawerTitle' => 'Edit Lab Facility'], 'delete' => true],
    'deleteConfirm'     => fn() => 'Delete this lab facility? This cannot be undone.',
    'formPrefix'        => 'lab',
]);

require_once __DIR__ . '/includes/footer.php';
