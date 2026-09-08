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
        $eligibilityRaw  = trim($_POST['eligibility'] ?? '');
        $medium          = trim($_POST['medium'] ?? '');
        $duration        = trim($_POST['duration'] ?? '');
        $seats_or_intake = trim($_POST['seats_or_intake'] ?? '');
        $next_step       = trim($_POST['next_step'] ?? '');
        $timings         = trim($_POST['timings'] ?? '');
        $syllabus_url    = trim($_POST['syllabus_url'] ?? '');
        $sort_order      = (int)($_POST['sort_order'] ?? 0);

        // Auto-render ordinal indicator in eligibility criteria (e.g. 12th or 12 Pass -> 12<sup>th</sup> Pass)
        $eligibility = formatEligibilityOrdinal($eligibilityRaw);

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

/**
 * Format eligibility string with automatic ordinal indicators (12th -> 12<sup>th</sup>)
 */
function formatEligibilityOrdinal(string $str): string {
    $str = trim($str);
    if ($str === '') return '';

    // If already contains <sup> tags, normalize but preserve
    if (stripos($str, '<sup>') !== false) {
        return $str;
    }

    // Match numbers with explicit ordinal suffixes (12th, 10th, 1st, 2nd, 3rd, 4th)
    $str = preg_replace('/\b(\d+)(st|nd|rd|th)\b/i', '$1<sup>$2</sup>', $str);

    // Match numbers before common eligibility qualifiers (e.g. "12 Pass", "10 Pass", "12 Std")
    $str = preg_replace_callback('/\b(\d+)\s+(Pass|Std|Standard|Class|Stream)\b/i', function ($matches) {
        $num = (int)$matches[1];
        $suffix = 'th';
        $mod100 = $num % 100;
        if ($mod100 < 11 || $mod100 > 13) {
            $mod10 = $num % 10;
            if ($mod10 === 1) $suffix = 'st';
            elseif ($mod10 === 2) $suffix = 'nd';
            elseif ($mod10 === 3) $suffix = 'rd';
        }
        return $num . '<sup>' . $suffix . '</sup> ' . $matches[2];
    }, $str);

    return $str;
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
            [
                'name' => 'duration_structure',
                'raw'  => function ($item) {
                    $durVal = $item ? ($item['duration'] ?? '4 Years') : '4 Years';
                    $semVal = $item ? ($item['seats_or_intake'] ?? '8 Semesters') : '8 Semesters';

                    // Extract initial year number from duration or default to 4
                    preg_match('/(\d+)/', $durVal, $m);
                    $selectedYear = !empty($m[1]) ? (int)$m[1] : 4;
                    if ($selectedYear < 1 || $selectedYear > 5) $selectedYear = 4;

                    // Determine if pattern is yearly or semester
                    $isYearly = (stripos($semVal, 'year') !== false || stripos($semVal, 'annual') !== false || (stripos($durVal, 'year') !== false && stripos($semVal, 'sem') === false && (int)$semVal == 0 && !empty($semVal)));
                    $pattern = $isYearly ? 'yearly' : 'semester';

                    $summary = htmlspecialchars($durVal . ' • ' . $semVal);

                    $html = '<div class="form-group full-width">'
                        . '<div class="duration-semester-builder" id="durationSemesterBuilder">'
                        . '  <div class="dsb-header">'
                        . '    <div class="dsb-title">'
                        . '      <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>'
                        . '      <span>Program Duration & Academic Structure *</span>'
                        . '    </div>'
                        . '    <span class="dsb-badge" id="dsbSummaryBadge">' . $summary . '</span>'
                        . '  </div>'
                        . '  <div class="dsb-controls-row">'
                        . '    <div class="dsb-control-group">'
                        . '      <span class="dsb-sublabel">Duration (Years)</span>'
                        . '      <div class="dsb-years-pills" role="radiogroup" aria-label="Course Duration in Years">';
                    for ($y = 1; $y <= 5; $y++) {
                        $activeCls = ($y === $selectedYear) ? ' active' : '';
                        $html .= '<button type="button" class="dsb-year-btn' . $activeCls . '" data-years="' . $y . '">' . $y . '</button>';
                    }
                    $html .= '        <span class="dsb-years-unit">Years</span>'
                        . '      </div>'
                        . '    </div>'
                        . '    <div class="dsb-control-group">'
                        . '      <span class="dsb-sublabel">Academic System</span>'
                        . '      <div class="dsb-pattern-toggle">'
                        . '        <button type="button" class="dsb-pattern-btn' . (!$isYearly ? ' active' : '') . '" data-pattern="semester">'
                        . '          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"></rect><line x1="12" y1="4" x2="12" y2="20"></line></svg>'
                        . '          Semester'
                        . '        </button>'
                        . '        <button type="button" class="dsb-pattern-btn' . ($isYearly ? ' active' : '') . '" data-pattern="yearly">'
                        . '          <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 10"></polyline></svg>'
                        . '          Yearly'
                        . '        </button>'
                        . '      </div>'
                        . '    </div>'
                        . '  </div>'
                        . '  <div class="dsb-inputs-row">'
                        . '    <div class="dsb-input-field">'
                        . '      <label for="duration">Duration Text</label>'
                        . '      <input type="text" name="duration" id="duration" class="form-control" value="' . htmlspecialchars($durVal) . '" placeholder="e.g. 4 Years" required>'
                        . '    </div>'
                        . '    <div class="dsb-input-field">'
                        . '      <label for="seats_or_intake" id="dsbSemestersLabel">Semesters / Intake</label>'
                        . '      <input type="text" name="seats_or_intake" id="seats_or_intake" class="form-control" value="' . htmlspecialchars($semVal) . '" placeholder="e.g. 8 Semesters or 6 to 7">'
                        . '    </div>'
                        . '  </div>'
                        . '</div>'
                        . '</div>';
                    return $html;
                }
            ],
            ['name' => 'medium', 'label' => 'Medium of Instruction', 'type' => 'text', 'default' => 'English', 'placeholder' => 'e.g. English or English / Gujarati'],
            [
                'name'        => 'eligibility',
                'label'       => 'Eligibility Criteria',
                'type'        => 'text',
                'default'     => '12th Pass',
                'placeholder' => 'e.g. 12th Pass (Any Stream) or 12th Pass (Science)',
                'load'        => fn($item) => $item ? preg_replace('/<\/?sup>/i', '', $item['eligibility'] ?? '') : '12th Pass',
                'hintHtml'    => fn($item) => '<div class="ordinal-preview-helper" id="eligibilityOrdinalPreview">Rendered: <strong>' . ($item ? ($item['eligibility'] ?? '12<sup>th</sup> Pass') : '12<sup>th</sup> Pass') . '</strong></div>',
            ],
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
        ['th' => 'Eligibility', 'td' => fn($r) => $r['eligibility']],
        ['th' => 'Timing', 'td' => fn($r) => htmlspecialchars(strip_tags($r['timings']))],
    ],
    'editUrl'           => fn($r) => 'courses.php?edit=' . $r['id'],
    'actions'           => ['edit' => ['drawerTitle' => 'Edit Course'], 'delete' => true],
    'deleteConfirm'     => fn() => 'Delete this course? This cannot be undone.',
    'formPrefix'        => 'course',
]);

require_once __DIR__ . '/includes/footer.php';
