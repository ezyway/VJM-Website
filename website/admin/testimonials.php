<?php
/**
 * Student Testimonials Management Module
 */

require_once __DIR__ . '/includes/auth.php';
requireAuth();

$db = getDB();

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('danger', 'Security token expired. Please try again.');
        header('Location: testimonials.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM testimonials WHERE id = :id');
        $stmt->execute([':id' => $id]);
        setFlash('success', 'Testimonial deleted.');
        header('Location: testimonials.php');
        exit;
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
            header('Location: testimonials.php');
            exit;
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

        header('Location: testimonials.php');
        exit;
    }
}

$pageTitle = 'Testimonials';
require_once __DIR__ . '/includes/header.php';

// Fetch edit target
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $db->prepare('SELECT * FROM testimonials WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $editId]);
    $editItem = $stmt->fetch();
}

$isCreate = isset($_GET['action']) && $_GET['action'] === 'create';
$testimonials = $db->query('SELECT * FROM testimonials ORDER BY sort_order ASC, id ASC')->fetchAll();
?>

<?php if ($editItem || $isCreate): ?>
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><?= $editItem ? 'Edit Testimonial' : 'Add New Student Testimonial' ?></div>
        <a href="testimonials.php" class="btn btn-secondary btn-sm">← Back to List</a>
    </div>
    <div class="panel-body">
        <form method="POST" action="testimonials.php">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editItem ? (int)$editItem['id'] : 0 ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="name">Student Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required value="<?= htmlspecialchars($editItem['name'] ?? '') ?>" placeholder="e.g. Sida Jay">
                </div>

                <div class="form-group">
                    <label class="form-label" for="course">Program / Batch *</label>
                    <input type="text" id="course" name="course" class="form-control" required value="<?= htmlspecialchars($editItem['course'] ?? '') ?>" placeholder="e.g. B.B.A. Graduate">
                </div>

                <div class="form-group">
                    <label class="form-label" for="avatar_text">Initials Avatar (2 letters)</label>
                    <input type="text" id="avatar_text" name="avatar_text" class="form-control" maxlength="3" value="<?= htmlspecialchars($editItem['avatar_text'] ?? '') ?>" placeholder="e.g. SJ">
                </div>

                <div class="form-group">
                    <label class="form-label" for="stars">Star Rating</label>
                    <select id="stars" name="stars" class="form-control">
                        <option value="5" <?= ($editItem['stars'] ?? 5) == 5 ? 'selected' : '' ?>>★★★★★ (5 Stars)</option>
                        <option value="4" <?= ($editItem['stars'] ?? 5) == 4 ? 'selected' : '' ?>>★★★★☆ (4 Stars)</option>
                        <option value="3" <?= ($editItem['stars'] ?? 5) == 3 ? 'selected' : '' ?>>★★★☆☆ (3 Stars)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="sort_order">Display Priority</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= htmlspecialchars($editItem['sort_order'] ?? '1') ?>">
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="text">Review Quote *</label>
                    <textarea id="text" name="text" class="form-control" rows="3" required placeholder="Write the student's quote..."><?= htmlspecialchars($editItem['text'] ?? '') ?></textarea>
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">Save Testimonial</button>
                <a href="testimonials.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Testimonials Table List -->
<div class="panel">
    <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
            <div class="panel-title">All Student Voices (<?= count($testimonials) ?>)</div>
            <input type="text" class="form-control" data-table-search="testimonialsTable" placeholder="Search testimonials..." style="max-width: 320px; font-size: 13px;">
        </div>
        <a href="testimonials.php?action=create" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add New Testimonial
        </a>
    </div>

    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table" id="testimonialsTable">
                <thead>
                    <tr>
                        <th style="width: 50px;">Order</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Rating</th>
                        <th>Quote</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($testimonials)): ?>
                        <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 32px;">No testimonials found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($testimonials as $t): ?>
                        <tr>
                            <td><span class="badge badge-secondary">#<?= (int)$t['sort_order'] ?></span></td>
                            <td style="display: flex; align-items: center; gap: 10px;">
                                <div class="user-avatar" style="width: 34px; height: 34px; font-size: 11px;">
                                    <?= htmlspecialchars($t['avatar_text']) ?>
                                </div>
                                <strong style="color: var(--text-main); font-size: 13.5px;"><?= htmlspecialchars($t['name']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($t['course']) ?></td>
                            <td style="color: #fbbf24; letter-spacing: 2px;">
                                <?= str_repeat('★', (int)$t['stars']) ?>
                            </td>
                            <td style="color: var(--text-muted); font-size: 12.5px; max-width: 350px;">
                                "<?= htmlspecialchars(mb_strimwidth($t['text'], 0, 90, '...')) ?>"
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="testimonials.php?edit=<?= (int)$t['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="testimonials.php" style="display: inline;" onsubmit="return confirm('Delete this testimonial?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
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
