<?php
/**
 * Pride of the College (Rankers & Laurels) Management Module
 */

require_once __DIR__ . '/includes/auth.php';
requireAuth();

$db = getDB();

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('danger', 'Security token expired. Please try again.');
        header('Location: rankers.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM rankers WHERE id = :id');
        $stmt->execute([':id' => $id]);
        setFlash('success', 'Student ranker record removed.');
        header('Location: rankers.php');
        exit;
    }

    if ($action === 'save') {
        $id         = (int)($_POST['id'] ?? 0);
        $name       = trim($_POST['name'] ?? '');
        $course     = trim($_POST['course'] ?? '');
        $semester   = trim($_POST['semester'] ?? '');
        $language   = trim($_POST['language'] ?? '');
        $rank_text  = trim($_POST['rank_text'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);
        $currentImg = trim($_POST['current_image'] ?? '');

        $imgPath = $currentImg;
        if (!empty($_FILES['image']['name'])) {
            $upload = handleAdminUpload($_FILES['image'], 'rankers');
            if ($upload['success']) {
                $imgPath = $upload['path'];
            } else {
                setFlash('danger', 'Photo upload failed: ' . $upload['error']);
                header('Location: rankers.php');
                exit;
            }
        }

        if (empty($name) || empty($course) || empty($rank_text)) {
            setFlash('danger', 'Student name, course, and rank text are required.');
            header('Location: rankers.php');
            exit;
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE rankers SET name = :n, course = :c, semester = :s, language = :l, rank_text = :r, image = :img, sort_order = :so WHERE id = :id');
            $stmt->execute([
                ':n'   => $name,
                ':c'   => $course,
                ':s'   => $semester,
                ':l'   => $language,
                ':r'   => $rank_text,
                ':img' => $imgPath,
                ':so'  => $sort_order,
                ':id'  => $id
            ]);
            setFlash('success', 'Ranker details updated successfully.');
        } else {
            $stmt = $db->prepare('INSERT INTO rankers (name, course, semester, language, rank_text, image, sort_order) VALUES (:n, :c, :s, :l, :r, :img, :so)');
            $stmt->execute([
                ':n'   => $name,
                ':c'   => $course,
                ':s'   => $semester,
                ':l'   => $language,
                ':r'   => $rank_text,
                ':img' => $imgPath,
                ':so'  => $sort_order
            ]);
            setFlash('success', 'New student ranker added.');
        }

        header('Location: rankers.php');
        exit;
    }
}

$pageTitle = 'Pride Laurels (Rankers)';
require_once __DIR__ . '/includes/header.php';

// Fetch edit target
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $db->prepare('SELECT * FROM rankers WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $editId]);
    $editItem = $stmt->fetch();
}

$isCreate = isset($_GET['action']) && $_GET['action'] === 'create';
$rankers = $db->query('SELECT * FROM rankers ORDER BY sort_order ASC, id ASC')->fetchAll();
?>

<?php if ($editItem || $isCreate): ?>
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><?= $editItem ? 'Edit Ranker Record' : 'Add New Student Ranker' ?></div>
        <a href="rankers.php" class="btn btn-secondary btn-sm">← Back to List</a>
    </div>
    <div class="panel-body">
        <form method="POST" action="rankers.php" enctype="multipart/form-data">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editItem ? (int)$editItem['id'] : 0 ?>">
            <input type="hidden" name="current_image" value="<?= htmlspecialchars($editItem['image'] ?? '') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="name">Student Full Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required value="<?= htmlspecialchars($editItem['name'] ?? '') ?>" placeholder="e.g. Odedra Ranjitji">
                </div>

                <div class="form-group">
                    <label class="form-label" for="course">Course / Degree *</label>
                    <input type="text" id="course" name="course" class="form-control" required value="<?= htmlspecialchars($editItem['course'] ?? '') ?>" placeholder="e.g. BCA or B.Sc. Chemistry or BBA">
                </div>

                <div class="form-group">
                    <label class="form-label" for="semester">Semester (Optional)</label>
                    <input type="text" id="semester" name="semester" class="form-control" value="<?= htmlspecialchars($editItem['semester'] ?? '') ?>" placeholder="e.g. 6">
                </div>

                <div class="form-group">
                    <label class="form-label" for="language">Medium / Stream (Optional)</label>
                    <input type="text" id="language" name="language" class="form-control" value="<?= htmlspecialchars($editItem['language'] ?? '') ?>" placeholder="e.g. English or Gujarati">
                </div>

                <div class="form-group">
                    <label class="form-label" for="rank_text">Rank / Laurel Award *</label>
                    <input type="text" id="rank_text" name="rank_text" class="form-control" required value="<?= htmlspecialchars($editItem['rank_text'] ?? 'BKNMU Rank 1^st') ?>" placeholder="e.g. BKNMU Rank 1^st or University Rank 2^nd">
                    <span class="form-hint">Tip: Use <code>^st</code>, <code>^nd</code>, <code>^rd</code>, or <code>^th</code> for superscript suffixes.</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="sort_order">Display Priority</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= htmlspecialchars($editItem['sort_order'] ?? '1') ?>">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Student Photo *</label>
                    <input type="file" name="image" class="form-control image-preview-input" data-preview-target="rankerPhotoPreview" accept="image/*">
                    <div style="margin-top: 8px; display: flex; align-items: center; gap: 12px;">
                        <img id="rankerPhotoPreview" class="preview-avatar" src="../<?= htmlspecialchars($editItem['image'] ?? 'assets/logo.ico') ?>" onerror="this.src='../assets/logo.ico'">
                        <span class="form-hint">Passport style photo or portrait image.</span>
                    </div>
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">Save Ranker</button>
                <a href="rankers.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Rankers Table List -->
<div class="panel">
    <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
            <div class="panel-title">Pride of the College Rankers (<?= count($rankers) ?>)</div>
            <input type="text" class="form-control" data-table-search="rankersTable" placeholder="Search rankers..." style="max-width: 320px; font-size: 13px;">
        </div>
        <a href="rankers.php?action=create" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add New Ranker
        </a>
    </div>

    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table" id="rankersTable">
                <thead>
                    <tr>
                        <th style="width: 50px;">Order</th>
                        <th>Student</th>
                        <th>Course / Program</th>
                        <th>Rank Achievement</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rankers)): ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 32px;">No rankers found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($rankers as $r): ?>
                        <tr>
                            <td><span class="badge badge-secondary">#<?= (int)$r['sort_order'] ?></span></td>
                            <td style="display: flex; align-items: center; gap: 12px;">
                                <img src="../<?= htmlspecialchars($r['image'] ?: 'assets/logo.ico') ?>" alt="" class="preview-avatar" onerror="this.src='../assets/logo.ico'">
                                <strong style="color: var(--text-main); font-size: 13.5px;"><?= htmlspecialchars($r['name']) ?></strong>
                            </td>
                            <td>
                                <?= htmlspecialchars($r['course']) ?>
                                <?= $r['semester'] ? '(Sem ' . htmlspecialchars($r['semester']) . ')' : '' ?>
                                <?= $r['language'] ? '• ' . htmlspecialchars($r['language']) : '' ?>
                            </td>
                            <td>
                                <span class="badge badge-warning"><?= htmlspecialchars(str_replace('^', '', $r['rank_text'])) ?></span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="rankers.php?edit=<?= (int)$r['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="rankers.php" style="display: inline;" onsubmit="return confirm('Delete this ranker record?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
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
