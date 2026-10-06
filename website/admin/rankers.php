<?php
/**
 * Pride of the College (Rankers & Laurels) Management Module
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/crud.php';
requireAuth();

$db = getDB();
$isDrawerMode = isset($_GET['drawer']) && $_GET['drawer'] === '1';

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    crudCsrfGuard('rankers.php');
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        // Fetch the image path before deleting the DB record
        $stmtImg = $db->prepare('SELECT image FROM rankers WHERE id = :id');
        $stmtImg->execute([':id' => $id]);
        $imagePath = $stmtImg->fetchColumn();
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
        crudDelete($db, 'rankers', $id, 'Student ranker record removed.', $isDrawerMode, 'rankers.php');
    }

    if ($action === 'save') {
        $id         = (int)($_POST['id'] ?? 0);
        $name       = trim($_POST['name'] ?? '');
        $course     = trim($_POST['course'] ?? '');
        $semester   = trim($_POST['semester'] ?? '');
        $language   = trim($_POST['language'] ?? '');
        $rank_text  = trim($_POST['rank_text'] ?? '');
        $currentImg = trim($_POST['current_image'] ?? '');

        $imgPath = $currentImg;
        if (!empty($_FILES['image']['name'])) {
            $upload = handleAdminUpload($_FILES['image'], 'rankers');
            if ($upload['success']) {
                $imgPath = $upload['path'];
                if ($id > 0 && $currentImg && $currentImg !== $imgPath) {
                    $oldFull = dirname(__DIR__) . '/' . $currentImg;
                    if (file_exists($oldFull)) @unlink($oldFull);
                    if (file_exists($oldFull . '.webp')) @unlink($oldFull . '.webp');
                }
            } else {
                crudFormFail('Photo upload failed: ' . $upload['error']);
            }
        }

        if (empty($name) || empty($course) || empty($rank_text)) {
            crudFormFail('Student name, course, and rank text are required.');
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE rankers SET name = :n, course = :c, semester = :s, language = :l, rank_text = :r, image = :img WHERE id = :id');
            $stmt->execute([
                ':n'   => $name,
                ':c'   => $course,
                ':s'   => $semester,
                ':l'   => $language,
                ':r'   => $rank_text,
                ':img' => $imgPath,
                ':id'  => $id
            ]);
            setFlash('success', 'Ranker details updated successfully.');
        } else {
            $stmt = $db->prepare('INSERT INTO rankers (name, course, semester, language, rank_text, image, sort_order) VALUES (:n, :c, :s, :l, :r, :img, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM rankers))');
            $stmt->execute([
                ':n'   => $name,
                ':c'   => $course,
                ':s'   => $semester,
                ':l'   => $language,
                ':r'   => $rank_text,
                ':img' => $imgPath,
            ]);
            setFlash('success', 'New student ranker added.');
        }

        crudRedirect('rankers.php', $isDrawerMode);
    }
}

$pageTitle = 'Pride Laurels (Rankers)';
require_once __DIR__ . '/includes/header.php';

$editItem = crudLoadItem($db, 'rankers');
$isCreate = crudIsCreate();
$rankers = $db->query('SELECT * FROM rankers ORDER BY sort_order ASC, id ASC')->fetchAll();

if ($editItem || $isCreate) {
    crudFormPanel([
        'page'        => 'rankers.php',
        'pageParam'   => $drawerParam,
        'item'        => $editItem,
        'creating'    => $isCreate,
        'multipart'   => true,
        'title'       => fn($item) => $item ? 'Edit Ranker Record' : 'Add New Student Ranker',
        'submitLabel' => 'Save Ranker',
        'hiddenHtml'  => fn($item) => '<input type="hidden" name="current_image" value="' . htmlspecialchars($item['image'] ?? '') . '">',
        'fields'      => [
            ['name' => 'name', 'label' => 'Student Full Name *', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Odedra Ranjitji'],
            ['name' => 'course', 'label' => 'Course / Degree *', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. BCA or B.Sc. Chemistry or BBA'],
            ['name' => 'semester', 'label' => 'Semester (Optional)', 'type' => 'text', 'placeholder' => 'e.g. 6'],
            ['name' => 'language', 'label' => 'Medium / Stream (Optional)', 'type' => 'text', 'placeholder' => 'e.g. English or Gujarati'],
            ['name' => 'rank_text', 'label' => 'Rank / Laurel Award *', 'type' => 'text', 'required' => true, 'createDefault' => 'BKNMU Rank 1^st', 'placeholder' => 'e.g. BKNMU Rank 1^st or University Rank 2^nd', 'hintHtml' => 'Tip: Use <code>^st</code>, <code>^nd</code>, <code>^rd</code>, or <code>^th</code> for superscript suffixes.'],
            ['name' => 'image', 'label' => 'Student Photo *', 'type' => 'file', 'full' => true, 'accept' => 'image/*', 'preview' => ['id' => 'rankerPhotoPreview', 'img' => fn($item) => ($item['image'] ?? '') ?: 'assets/logo.ico', 'hint' => 'Passport style photo or portrait image.']],
        ],
    ]);
}

crudListPanel([
    'page'              => 'rankers.php',
    'pageParam'         => $drawerParam,
    'suffix'            => $drawerUrlSuffix,
    'listTitleHtml'     => 'Pride of the College Rankers (' . count($rankers) . ')',
    'rows'              => $rankers,
    'tableId'           => 'rankersTable',
    'reorder'           => 'rankers',
    'searchPlaceholder' => 'Search rankers...',
    'emptyText'         => 'No rankers recorded yet. Click <strong>Add New Ranker</strong> to celebrate your first achiever.',
    'add'               => ['url' => 'rankers.php?action=create', 'drawerTitle' => 'Add New Student Ranker', 'label' => 'Add New Ranker'],
    'columns'           => [
        [
            'th' => 'Student',
            'td' => fn($r) => '<div style="display: flex; align-items: center; gap: 12px;">'
                . '<img src="../' . htmlspecialchars($r['image'] ?: 'assets/logo.ico') . '" alt="" class="preview-avatar" loading="lazy" onerror="this.src=\'../assets/logo.ico\'">'
                . '<strong style="color: var(--text-main); font-size: 13.5px;">' . htmlspecialchars($r['name']) . '</strong>'
                . '</div>',
        ],
        [
            'th' => 'Course / Program',
            'td' => fn($r) => htmlspecialchars($r['course'])
                . ($r['semester'] ? ' (Sem ' . htmlspecialchars($r['semester']) . ')' : '')
                . ($r['language'] ? ' • ' . htmlspecialchars($r['language']) : ''),
        ],
        ['th' => 'Rank Achievement', 'td' => fn($r) => badge(htmlspecialchars(str_replace('^', '', $r['rank_text'])), 'warning')],
    ],
    'editUrl'           => fn($r) => 'rankers.php?edit=' . $r['id'],
    'actions'           => ['edit' => ['drawerTitle' => 'Edit Ranker Record'], 'delete' => true],
    'deleteConfirm'     => fn() => 'Delete this ranker record? This cannot be undone.',
    'formPrefix'        => 'ranker',
]);

require_once __DIR__ . '/includes/footer.php';
