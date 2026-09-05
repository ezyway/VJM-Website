<?php
/**
 * Student Testimonials Management Module
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/crud.php';
requireAuth();

$db = getDB();
$isDrawerMode = isset($_GET['drawer']) && $_GET['drawer'] === '1';

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    crudCsrfGuard('testimonials.php');
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        crudDelete($db, 'testimonials', (int)($_POST['id'] ?? 0), 'Testimonial deleted.', $isDrawerMode, 'testimonials.php');
    }

    if ($action === 'save') {
        $id          = (int)($_POST['id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $course      = trim($_POST['course'] ?? '');
        $avatar_text = trim($_POST['avatar_text'] ?? '');
        $stars       = min(5, max(1, (int)($_POST['stars'] ?? 5)));
        $text        = trim($_POST['text'] ?? '');
        $sort_order  = (int)($_POST['sort_order'] ?? 0);

        if (empty($avatar_text) && !empty($name)) {
            $parts = explode(' ', $name);
            $avatar_text = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
        }

        if (empty($name) || empty($text)) {
            setFlash('danger', 'Student name and testimonial text are required.');
            crudRedirect('testimonials.php', $isDrawerMode);
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE testimonials SET name = :n, course = :c, avatar_text = :av, stars = :st, text = :txt, sort_order = :so WHERE id = :id');
            $stmt->execute([
                ':n'   => $name,
                ':c'   => $course,
                ':av'  => $avatar_text,
                ':st'  => $stars,
                ':txt' => $text,
                ':so'  => $sort_order,
                ':id'  => $id
            ]);
            setFlash('success', 'Testimonial updated.');
        } else {
            $stmt = $db->prepare('INSERT INTO testimonials (name, course, avatar_text, stars, text, sort_order) VALUES (:n, :c, :av, :st, :txt, :so)');
            $stmt->execute([
                ':n'   => $name,
                ':c'   => $course,
                ':av'  => $avatar_text,
                ':st'  => $stars,
                ':txt' => $text,
                ':so'  => $sort_order
            ]);
            setFlash('success', 'New testimonial added.');
        }

        crudRedirect('testimonials.php', $isDrawerMode);
    }
}

$pageTitle = 'Testimonials';
require_once __DIR__ . '/includes/header.php';

$editItem = crudLoadItem($db, 'testimonials');
$isCreate = crudIsCreate();
$testimonials = $db->query('SELECT * FROM testimonials ORDER BY sort_order ASC, id ASC')->fetchAll();

if ($editItem || $isCreate) {
    crudFormPanel([
        'page'        => 'testimonials.php',
        'pageParam'   => $drawerParam,
        'item'        => $editItem,
        'creating'    => $isCreate,
        'title'       => fn($item) => $item ? 'Edit Testimonial' : 'Add New Student Testimonial',
        'submitLabel' => 'Save Testimonial',
        'fields'      => [
            ['name' => 'name', 'label' => 'Student Name *', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Sida Jay'],
            ['name' => 'course', 'label' => 'Program / Batch *', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. B.B.A. Graduate'],
            ['name' => 'avatar_text', 'label' => 'Initials Avatar (2 letters)', 'type' => 'text', 'maxlength' => 3, 'placeholder' => 'e.g. SJ'],
            ['name' => 'stars', 'label' => 'Star Rating', 'type' => 'select', 'createDefault' => 5, 'options' => [5 => '★★★★★ (5 Stars)', 4 => '★★★★☆ (4 Stars)', 3 => '★★★☆☆ (3 Stars)']],
            ['name' => 'sort_order', 'label' => 'Display Priority', 'type' => 'number', 'default' => '1'],
            ['name' => 'text', 'label' => 'Review Quote *', 'type' => 'textarea', 'full' => true, 'rows' => 3, 'required' => true, 'placeholder' => "Write the student's quote..."],
        ],
    ]);
}

crudListPanel([
    'page'              => 'testimonials.php',
    'pageParam'         => $drawerParam,
    'suffix'            => $drawerUrlSuffix,
    'listTitleHtml'     => 'All Student Voices (' . count($testimonials) . ')',
    'rows'              => $testimonials,
    'tableId'           => 'testimonialsTable',
    'reorder'           => 'testimonials',
    'searchPlaceholder' => 'Search testimonials...',
    'emptyText'         => 'No testimonials yet. Click <strong>Add New Testimonial</strong> to feature the first student voice.',
    'add'               => ['url' => 'testimonials.php?action=create', 'drawerTitle' => 'Add New Student Testimonial', 'label' => 'Add New Testimonial'],
    'columns'           => [
        [
            'th' => 'Student',
            'td' => fn($r) => '<div style="display: flex; align-items: center; gap: 10px;">'
                . '<div class="user-avatar" style="width: 34px; height: 34px; font-size: 11px;">' . htmlspecialchars($r['avatar_text']) . '</div>'
                . '<strong style="color: var(--text-main); font-size: 13.5px;">' . htmlspecialchars($r['name']) . '</strong>'
                . '</div>',
        ],
        ['th' => 'Course', 'td' => fn($r) => htmlspecialchars($r['course'])],
        ['th' => 'Rating', 'td' => fn($r) => '<div style="color: #fbbf24; letter-spacing: 2px;">' . str_repeat('★', (int)$r['stars']) . '</div>'],
        ['th' => 'Quote', 'td' => fn($r) => '<div style="color: var(--text-muted); font-size: 12.5px; max-width: 350px;">&quot;' . htmlspecialchars(mb_strimwidth($r['text'], 0, 90, '...')) . '&quot;</div>'],
    ],
    'editUrl'           => fn($r) => 'testimonials.php?edit=' . $r['id'],
    'actions'           => ['edit' => ['drawerTitle' => 'Edit Testimonial'], 'delete' => true],
    'deleteConfirm'     => fn() => 'Delete this testimonial? This cannot be undone.',
    'formPrefix'        => 'testimonial',
]);

require_once __DIR__ . '/includes/footer.php';
