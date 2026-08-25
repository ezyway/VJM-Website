<?php
/**
 * E-Magazines & Publications Management Module
 */

require_once __DIR__ . '/includes/auth.php';
requireAuth();

$db = getDB();

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('danger', 'Security token expired. Please try again.');
        header('Location: magazines.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM magazines WHERE id = :id');
        $stmt->execute([':id' => $id]);
        setFlash('success', 'Magazine edition deleted.');
        header('Location: magazines.php');
        exit;
    }

    if ($action === 'save') {
        $id          = (int)($_POST['id'] ?? 0);
        $year        = trim($_POST['year'] ?? '');
        $title       = trim($_POST['title'] ?? '');
        $edition     = trim($_POST['edition'] ?? '');
        $theme       = trim($_POST['theme'] ?? '');
        $file_size   = trim($_POST['file_size'] ?? '');
        $pages       = trim($_POST['pages'] ?? 'Full Edition');
        $badge       = trim($_POST['badge'] ?? '');
        $sort_order  = (int)($_POST['sort_order'] ?? 0);
        $currentFile = trim($_POST['current_file'] ?? '');

        $filePath = $currentFile;
        if (!empty($_FILES['pdf_file']['name'])) {
            $upload = handleAdminUpload($_FILES['pdf_file'], 'emag', ['pdf']);
            if ($upload['success']) {
                $filePath = $upload['path'];
                $bytes = $_FILES['pdf_file']['size'];
                $file_size = '~' . round($bytes / (1024 * 1024), 1) . ' MB';
            } else {
                setFlash('danger', 'PDF upload failed: ' . $upload['error']);
                header('Location: magazines.php');
                exit;
            }
        }

        if (empty($title) || empty($year) || empty($filePath)) {
            setFlash('danger', 'Title, Year, and PDF file are required.');
            header('Location: magazines.php');
            exit;
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE magazines SET year = :yr, title = :t, edition = :ed, theme = :th, file_path = :fp, file_size = :fs, pages = :pg, badge = :b, sort_order = :so WHERE id = :id');
            $stmt->execute([
                ':yr' => $year,
                ':t'  => $title,
                ':ed' => $edition,
                ':th' => $theme,
                ':fp' => $filePath,
                ':fs' => $file_size,
                ':pg' => $pages,
                ':b'  => $badge,
                ':so' => $sort_order,
                ':id' => $id
            ]);
            setFlash('success', 'Magazine details updated.');
        } else {
            $stmt = $db->prepare('INSERT INTO magazines (year, title, edition, theme, file_path, file_size, pages, badge, sort_order) VALUES (:yr, :t, :ed, :th, :fp, :fs, :pg, :b, :so)');
            $stmt->execute([
                ':yr' => $year,
                ':t'  => $title,
                ':ed' => $edition,
                ':th' => $theme,
                ':fp' => $filePath,
                ':fs' => $file_size,
                ':pg' => $pages,
                ':b'  => $badge,
                ':so' => $sort_order
            ]);
            setFlash('success', 'New magazine publication added.');
        }

        header('Location: magazines.php');
        exit;
    }
}

$pageTitle = 'E-Magazines & PDFs';
require_once __DIR__ . '/includes/header.php';

// Fetch edit target
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $db->prepare('SELECT * FROM magazines WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $editId]);
    $editItem = $stmt->fetch();
}

$isCreate = isset($_GET['action']) && $_GET['action'] === 'create';
$magazines = $db->query('SELECT * FROM magazines ORDER BY sort_order ASC, year DESC')->fetchAll();
?>

<?php if ($editItem || $isCreate): ?>
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><?= $editItem ? 'Edit Magazine: ' . htmlspecialchars($editItem['title']) : 'Upload New E-Magazine Edition' ?></div>
        <a href="magazines.php" class="btn btn-secondary btn-sm">← Back to List</a>
    </div>
    <div class="panel-body">
        <form method="POST" action="magazines.php" enctype="multipart/form-data">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editItem ? (int)$editItem['id'] : 0 ?>">
            <input type="hidden" name="current_file" value="<?= htmlspecialchars($editItem['file_path'] ?? '') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="year">Publication Year *</label>
                    <input type="text" id="year" name="year" class="form-control" required value="<?= htmlspecialchars($editItem['year'] ?? date('Y')) ?>" placeholder="e.g. 2025">
                </div>

                <div class="form-group">
                    <label class="form-label" for="edition">Edition Label</label>
                    <input type="text" id="edition" name="edition" class="form-control" value="<?= htmlspecialchars($editItem['edition'] ?? 'Edition 2025') ?>" placeholder="e.g. Edition 2025">
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="title">Magazine Title *</label>
                    <input type="text" id="title" name="title" class="form-control" required value="<?= htmlspecialchars($editItem['title'] ?? '') ?>" placeholder="e.g. Shri V.J. Modha College Annual E-Magazine 2025">
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="theme">Edition Theme / Subtitle</label>
                    <input type="text" id="theme" name="theme" class="form-control" value="<?= htmlspecialchars($editItem['theme'] ?? '') ?>" placeholder="e.g. Resilience, Innovation &amp; Digital Transformation">
                </div>

                <div class="form-group">
                    <label class="form-label" for="badge">Badge</label>
                    <input type="text" id="badge" name="badge" class="form-control" value="<?= htmlspecialchars($editItem['badge'] ?? 'Latest Edition') ?>" placeholder="e.g. Latest Edition or Archive">
                </div>

                <div class="form-group">
                    <label class="form-label" for="file_size">File Size Label</label>
                    <input type="text" id="file_size" name="file_size" class="form-control" value="<?= htmlspecialchars($editItem['file_size'] ?? '') ?>" placeholder="e.g. ~31.6 MB (auto-calculated on upload)">
                </div>

                <div class="form-group">
                    <label class="form-label" for="sort_order">Display Priority</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= htmlspecialchars($editItem['sort_order'] ?? '1') ?>">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Upload Magazine PDF Document *</label>
                    <input type="file" name="pdf_file" class="form-control" accept="application/pdf">
                    <?php if (!empty($editItem['file_path'])): ?>
                        <div style="margin-top: 6px;" class="form-hint">
                            Current file: <a href="../<?= htmlspecialchars($editItem['file_path']) ?>" target="_blank" style="color: var(--primary);"><?= htmlspecialchars($editItem['file_path']) ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">Save Publication</button>
                <a href="magazines.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Magazines Table List -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">E-Magazines &amp; Publications (<?= count($magazines) ?>)</div>
        <a href="magazines.php?action=create" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Upload New Magazine
        </a>
    </div>

    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Year</th>
                        <th>Magazine Title</th>
                        <th>Edition &amp; Theme</th>
                        <th>Size</th>
                        <th>Badge</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($magazines)): ?>
                        <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 32px;">No magazines uploaded.</td></tr>
                    <?php else: ?>
                        <?php foreach ($magazines as $m): ?>
                        <tr>
                            <td><strong style="color: var(--primary);"><?= htmlspecialchars($m['year']) ?></strong></td>
                            <td>
                                <strong style="color: var(--text-main);"><?= htmlspecialchars($m['title']) ?></strong>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($m['edition']) ?></div>
                                <div style="font-size: 11.5px; color: var(--text-muted);"><?= htmlspecialchars($m['theme']) ?></div>
                            </td>
                            <td><span class="badge badge-secondary"><?= htmlspecialchars($m['file_size']) ?></span></td>
                            <td>
                                <?php if (!empty($m['badge'])): ?>
                                    <span class="badge badge-<?= $m['badge'] === 'Latest Edition' ? 'success' : 'secondary' ?>">
                                        <?= htmlspecialchars($m['badge']) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="../<?= htmlspecialchars($m['file_path']) ?>" target="_blank" class="btn btn-secondary btn-sm" title="View PDF">View</a>
                                    <a href="magazines.php?edit=<?= (int)$m['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="magazines.php" style="display: inline;" onsubmit="return confirm('Delete this publication?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
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
