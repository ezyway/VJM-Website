<?php
/**
 * Courses & Academic Programs Management Module
 */

require_once __DIR__ . '/includes/auth.php';
requireAuth();

$db = getDB();

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('danger', 'Security token expired. Please try again.');
        header('Location: courses.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM courses WHERE id = :id');
        $stmt->execute([':id' => $id]);
        setFlash('success', 'Course program deleted.');
        header('Location: courses.php');
        exit;
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
            header('Location: courses.php');
            exit;
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

        header('Location: courses.php');
        exit;
    }
}

$pageTitle = 'Courses & Programs';
require_once __DIR__ . '/includes/header.php';

// Fetch edit target
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $db->prepare('SELECT * FROM courses WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $editId]);
    $editItem = $stmt->fetch();
}

$isCreate = isset($_GET['action']) && $_GET['action'] === 'create';
$courses = $db->query('SELECT * FROM courses ORDER BY sort_order ASC, id ASC')->fetchAll();
?>

<?php if ($editItem || $isCreate): ?>
<?php
$jobRolesArray = $editItem ? json_decode($editItem['job_roles'] ?? '[]', true) : [];
$rolesText = is_array($jobRolesArray) ? implode("\n", $jobRolesArray) : '';
?>
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><?= $editItem ? 'Edit Course: ' . htmlspecialchars($editItem['name']) : 'Add New Course Program' ?></div>
        <a href="courses.php" class="btn btn-secondary btn-sm">← Back to List</a>
    </div>
    <div class="panel-body">
        <form method="POST" action="courses.php">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editItem ? (int)$editItem['id'] : 0 ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="slug">Identifier Slug * (Unique, lowercase)</label>
                    <input type="text" id="slug" name="slug" class="form-control" required value="<?= htmlspecialchars($editItem['slug'] ?? '') ?>" placeholder="e.g. bca, bsc, bba, bcom">
                </div>

                <div class="form-group">
                    <label class="form-label" for="name">Program Full Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required value="<?= htmlspecialchars($editItem['name'] ?? '') ?>" placeholder="e.g. Bachelor in Computer Applications (BCA)">
                </div>

                <div class="form-group">
                    <label class="form-label" for="duration">Duration</label>
                    <input type="text" id="duration" name="duration" class="form-control" value="<?= htmlspecialchars($editItem['duration'] ?? '4 Years') ?>" placeholder="e.g. 4 Years or 2 Years">
                </div>

                <div class="form-group">
                    <label class="form-label" for="medium">Medium of Instruction</label>
                    <input type="text" id="medium" name="medium" class="form-control" value="<?= htmlspecialchars($editItem['medium'] ?? 'English') ?>" placeholder="e.g. English or English / Gujarati">
                </div>

                <div class="form-group">
                    <label class="form-label" for="eligibility">Eligibility Criteria</label>
                    <input type="text" id="eligibility" name="eligibility" class="form-control" value="<?= htmlspecialchars($editItem['eligibility'] ?? '12th Pass') ?>" placeholder="e.g. 12th Pass (Any Stream) or 12th Pass (Science)">
                </div>

                <div class="form-group">
                    <label class="form-label" for="seats_or_intake">Number of Semesters / Div</label>
                    <input type="text" id="seats_or_intake" name="seats_or_intake" class="form-control" value="<?= htmlspecialchars($editItem['seats_or_intake'] ?? '') ?>" placeholder="e.g. 6 to 7">
                </div>

                <div class="form-group">
                    <label class="form-label" for="timings">Session Timings</label>
                    <input type="text" id="timings" name="timings" class="form-control" value="<?= htmlspecialchars($editItem['timings'] ?? 'Morning') ?>" placeholder="e.g. Morning or Afternoon">
                </div>

                <div class="form-group">
                    <label class="form-label" for="next_step">Higher Study / Next Step</label>
                    <input type="text" id="next_step" name="next_step" class="form-control" value="<?= htmlspecialchars($editItem['next_step'] ?? '') ?>" placeholder="e.g. M.Sc. IT / MCA">
                </div>

                <div class="form-group">
                    <label class="form-label" for="syllabus_url">University Syllabus URL</label>
                    <input type="url" id="syllabus_url" name="syllabus_url" class="form-control" value="<?= htmlspecialchars($editItem['syllabus_url'] ?? 'https://www.bknmu.edu.in/Academic/page/Syllabus') ?>" placeholder="https://...">
                </div>

                <div class="form-group">
                    <label class="form-label" for="sort_order">Display Order</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= htmlspecialchars($editItem['sort_order'] ?? '1') ?>">
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="quick_info">Program Overview &amp; Curriculum Description</label>
                    <textarea id="quick_info" name="quick_info" class="form-control" rows="4"><?= htmlspecialchars($editItem['quick_info'] ?? '') ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="job_roles">Career Opportunities / Job Roles (One role per line)</label>
                    <textarea id="job_roles" name="job_roles" class="form-control" rows="5" placeholder="Software Developer&#10;Web Developer&#10;System Analyst"><?= htmlspecialchars($rolesText) ?></textarea>
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">Save Course</button>
                <a href="courses.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Course Table List -->
<div class="panel">
    <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
            <div class="panel-title">All Course Offerings (<?= count($courses) ?>)</div>
            <input type="text" class="form-control" data-table-search="courseTable" placeholder="Search courses..." style="max-width: 320px; font-size: 13px;">
        </div>
        <a href="courses.php?action=create" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add New Course
        </a>
    </div>

    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table" id="courseTable">
                <thead>
                    <tr>
                        <th style="width: 50px;">Order</th>
                        <th>Slug</th>
                        <th>Program Name</th>
                        <th>Duration</th>
                        <th>Eligibility</th>
                        <th>Timing</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses)): ?>
                        <tr><td colspan="7" style="text-align: center; color: var(--text-muted); padding: 32px;">No courses found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($courses as $c): ?>
                        <tr>
                            <td><span class="badge badge-secondary">#<?= (int)$c['sort_order'] ?></span></td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($c['slug']) ?></span></td>
                            <td><strong style="color: var(--text-main);"><?= htmlspecialchars($c['name']) ?></strong></td>
                            <td><?= htmlspecialchars($c['duration']) ?></td>
                            <td><?= htmlspecialchars(strip_tags($c['eligibility'])) ?></td>
                            <td><?= htmlspecialchars(strip_tags($c['timings'])) ?></td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="courses.php?edit=<?= (int)$c['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="courses.php" style="display: inline;" onsubmit="return confirm('Delete this course?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
