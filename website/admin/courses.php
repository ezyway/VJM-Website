<?php
/**
 * Courses & Academic Programs Management Module
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/crud.php';
requireAuth();

$db = getDB();
$isDrawerMode = isset($_GET['drawer']) && $_GET['drawer'] === '1';

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    crudCsrfGuard('courses.php');
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        crudDelete($db, 'courses', (int)($_POST['id'] ?? 0), 'Course program deleted.', $isDrawerMode, 'courses.php');
    }

    if ($action === 'save') {
        $id              = (int)($_POST['id'] ?? 0);
        $slug            = strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', $_POST['slug'] ?? '')));
        $name            = trim($_POST['name'] ?? '');
        $quick_info      = trim($_POST['quick_info'] ?? '');
        $eligibility     = trim($_POST['eligibility'] ?? '');
        $medium          = trim($_POST['medium'] ?? '');
        $duration        = trim($_POST['duration'] ?? '');
        $seats_or_intake = trim($_POST['seats_or_intake'] ?? '');
        $next_step       = trim($_POST['next_step'] ?? '');
        $timings         = trim($_POST['timings'] ?? '');
        $syllabus_url    = trim($_POST['syllabus_url'] ?? '');
        $sort_order      = (int)($_POST['sort_order'] ?? 0);

        // Job roles from textarea (newline separated)
        $rawRoles = explode("\n", str_replace("\r", "", $_POST['job_roles'] ?? ''));
        $jobRoles = array_values(array_filter(array_map('trim', $rawRoles)));
        $rolesJson = json_encode($jobRoles);

        if (empty($slug) || empty($name)) {
            setFlash('danger', 'Course slug and name are required.');
            crudRedirect('courses.php', $isDrawerMode);
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE courses SET slug = :slug, name = :name, quick_info = :info, job_roles = :roles, eligibility = :elig, medium = :med, duration = :dur, seats_or_intake = :seats, next_step = :next, timings = :time, syllabus_url = :syl, sort_order = :so WHERE id = :id');
            $stmt->execute([
                ':slug'  => $slug,
                ':name'  => $name,
                ':info'  => $quick_info,
                ':roles' => $rolesJson,
                ':elig'  => $eligibility,
                ':med'   => $medium,
                ':dur'   => $duration,
                ':seats' => $seats_or_intake,
                ':next'  => $next_step,
                ':time'  => $timings,
                ':syl'   => $syllabus_url,
                ':so'    => $sort_order,
                ':id'    => $id
            ]);
            setFlash('success', 'Course updated successfully.');
        } else {
            $stmt = $db->prepare('INSERT INTO courses (slug, name, quick_info, job_roles, eligibility, medium, duration, seats_or_intake, next_step, timings, syllabus_url, sort_order) VALUES (:slug, :name, :info, :roles, :elig, :med, :dur, :seats, :next, :time, :syl, :so)');
            $stmt->execute([
                ':slug'  => $slug,
                ':name'  => $name,
                ':info'  => $quick_info,
                ':roles' => $rolesJson,
                ':elig'  => $eligibility,
                ':med'   => $medium,
                ':dur'   => $duration,
                ':seats' => $seats_or_intake,
                ':next'  => $next_step,
                ':time'  => $timings,
                ':syl'   => $syllabus_url,
                ':so'    => $sort_order
            ]);
            setFlash('success', 'New course program added.');
        }

        crudRedirect('courses.php', $isDrawerMode);
    }
}

$pageTitle = 'Courses & Programs';
require_once __DIR__ . '/includes/header.php';

$editItem = crudLoadItem($db, 'courses');
$isCreate = crudIsCreate();
$courses = $db->query('SELECT * FROM courses ORDER BY sort_order ASC, id ASC')->fetchAll();

if ($editItem || $isCreate) {
    crudFormPanel([
        'page'        => 'courses.php',
        'pageParam'   => $drawerParam,
        'item'        => $editItem,
        'creating'    => $isCreate,
        'title'       => fn($item) => $item ? 'Edit Course: ' . htmlspecialchars($item['name']) : 'Add New Course Program',
        'submitLabel' => 'Save Course',
        'fields'      => [
            ['name' => 'slug', 'label' => 'Identifier Slug * (Unique, lowercase)', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. bca, bsc, bba, bcom'],
            ['name' => 'name', 'label' => 'Program Full Name *', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Bachelor in Computer Applications (BCA)'],
            ['name' => 'duration', 'label' => 'Duration', 'type' => 'text', 'default' => '4 Years', 'placeholder' => 'e.g. 4 Years or 2 Years'],
            ['name' => 'medium', 'label' => 'Medium of Instruction', 'type' => 'text', 'default' => 'English', 'placeholder' => 'e.g. English or English / Gujarati'],
            ['name' => 'eligibility', 'label' => 'Eligibility Criteria', 'type' => 'text', 'default' => '12th Pass', 'placeholder' => 'e.g. 12th Pass (Any Stream) or 12th Pass (Science)'],
            ['name' => 'seats_or_intake', 'label' => 'Number of Semesters / Div', 'type' => 'text', 'placeholder' => 'e.g. 6 to 7'],
            ['name' => 'timings', 'label' => 'Session Timings', 'type' => 'text', 'default' => 'Morning', 'placeholder' => 'e.g. Morning or Afternoon'],
            ['name' => 'next_step', 'label' => 'Higher Study / Next Step', 'type' => 'text', 'placeholder' => 'e.g. M.Sc. IT / MCA'],
            ['name' => 'syllabus_url', 'label' => 'University Syllabus URL', 'type' => 'url', 'default' => 'https://www.bknmu.edu.in/Academic/page/Syllabus', 'placeholder' => 'https://...'],
            ['name' => 'sort_order', 'label' => 'Display Order', 'type' => 'number', 'default' => '1'],
            ['name' => 'quick_info', 'label' => 'Program Overview &amp; Curriculum Description', 'type' => 'textarea', 'full' => true, 'rows' => 4],
            [
                'name'  => 'job_roles',
                'label' => 'Career Opportunities / Job Roles (One role per line)',
                'type'  => 'textarea', 'full' => true, 'rows' => 5,
                'placeholder' => 'Software Developer&#10;Web Developer&#10;System Analyst',
                'load'  => fn($item) => is_array($arr = json_decode($item['job_roles'] ?? '[]', true)) ? implode("\n", $arr) : '',
            ],
        ],
    ]);
}

crudListPanel([
    'page'              => 'courses.php',
    'pageParam'         => $drawerParam,
    'suffix'            => $drawerUrlSuffix,
    'listTitleHtml'     => 'All Course Offerings (' . count($courses) . ')',
    'rows'              => $courses,
    'tableId'           => 'courseTable',
    'reorder'           => 'courses',
    'searchPlaceholder' => 'Search courses...',
    'emptyText'         => 'No courses found yet. Click <strong>Add New Course</strong> to create your first program.',
    'add'               => ['url' => 'courses.php?action=create', 'drawerTitle' => 'Add New Course', 'label' => 'Add New Course'],
    'columns'           => [
        ['th' => 'Slug', 'td' => fn($r) => badge(htmlspecialchars($r['slug']), 'info')],
        ['th' => 'Program Name', 'td' => fn($r) => '<strong style="color: var(--text-main);">' . htmlspecialchars($r['name']) . '</strong>'],
        ['th' => 'Duration', 'td' => fn($r) => htmlspecialchars($r['duration'])],
        ['th' => 'Eligibility', 'td' => fn($r) => htmlspecialchars(strip_tags($r['eligibility']))],
        ['th' => 'Timing', 'td' => fn($r) => htmlspecialchars(strip_tags($r['timings']))],
    ],
    'editUrl'           => fn($r) => 'courses.php?edit=' . $r['id'],
    'actions'           => ['edit' => ['drawerTitle' => 'Edit Course'], 'delete' => true],
    'deleteConfirm'     => fn() => 'Delete this course? This cannot be undone.',
    'formPrefix'        => 'course',
]);

require_once __DIR__ . '/includes/footer.php';
