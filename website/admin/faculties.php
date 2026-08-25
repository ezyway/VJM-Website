<?php
/**
 * Faculty Directory Management Module
 */

require_once __DIR__ . '/includes/auth.php';
requireAuth();

$db = getDB();
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
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('danger', 'Security token expired. Please try again.');
        header('Location: faculties.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM faculties WHERE id = :id');
        $stmt->execute([':id' => $id]);
        setFlash('success', 'Faculty member removed successfully.');
        header('Location: faculties.php');
        exit;
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
                header('Location: faculties.php');
                exit;
            }
        }

        if (empty($name) || empty($designation)) {
            setFlash('danger', 'Name and Designation are required.');
            header('Location: faculties.php');
            exit;
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

        header('Location: faculties.php');
        exit;
    }
}

$pageTitle = 'Faculty Directory';
require_once __DIR__ . '/includes/header.php';

// Fetch edit target if editing
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $db->prepare('SELECT * FROM faculties WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $editId]);
    $editItem = $stmt->fetch();
}

$isCreate = isset($_GET['action']) && $_GET['action'] === 'create';
$faculties = $db->query('SELECT * FROM faculties ORDER BY sort_order ASC, id ASC')->fetchAll();
?>

<?php if ($editItem || $isCreate): ?>
<!-- Add / Edit Form Panel -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><?= $editItem ? 'Edit Faculty Member' : 'Add New Faculty Member' ?></div>
        <a href="faculties.php" class="btn btn-secondary btn-sm">← Back to List</a>
    </div>
    <div class="panel-body">
        <form method="POST" action="faculties.php" enctype="multipart/form-data">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editItem ? (int)$editItem['id'] : 0 ?>">
            <input type="hidden" name="current_image" value="<?= htmlspecialchars($editItem['image'] ?? '') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required value="<?= htmlspecialchars($editItem['name'] ?? '') ?>" placeholder="e.g. Prof. Paresh Savjani">
                </div>

                <div class="form-group">
                    <label class="form-label" for="designation">Designation *</label>
                    <input type="text" id="designation" name="designation" class="form-control" required value="<?= htmlspecialchars($editItem['designation'] ?? '') ?>" placeholder="e.g. Incharge Principal &amp; Associate Professor">
                </div>

                <div class="form-group">
                    <label class="form-label" for="badge">Badge Text</label>
                    <input type="text" id="badge" name="badge" class="form-control" value="<?= htmlspecialchars($editItem['badge'] ?? '') ?>" placeholder="e.g. Leadership • IT">
                </div>

                <div class="form-group">
                    <label class="form-label" for="dept_label">Department Display Label</label>
                    <input type="text" id="dept_label" name="dept_label" class="form-control" value="<?= htmlspecialchars($editItem['dept_label'] ?? '') ?>" placeholder="e.g. Administration &amp; IT">
                </div>

                <div class="form-group">
                    <label class="form-label" for="sort_order">Sort Order Priority</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= htmlspecialchars($editItem['sort_order'] ?? '1') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Profile Photo</label>
                    <input type="file" name="image" class="form-control image-preview-input" data-preview-target="facultyPhotoPreview" accept="image/*">
                    <div style="margin-top: 8px; display: flex; align-items: center; gap: 12px;">
                        <img id="facultyPhotoPreview" class="preview-avatar" src="../<?= htmlspecialchars($editItem['image'] ?? 'assets/photos/faculties/placeholder.jpg') ?>" onerror="this.src='../assets/logo.ico'">
                        <span class="form-hint">Upload JPG, PNG or WebP image.</span>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Associated Departments *</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 16px; margin-top: 6px;">
                        <?php 
                        $activeDepts = $editItem ? explode(',', $editItem['depts']) : [];
                        foreach ($departments as $key => $label): 
                        ?>
                            <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; cursor: pointer;">
                                <input type="checkbox" name="depts[]" value="<?= $key ?>" <?= in_array($key, $activeDepts) ? 'checked' : '' ?>>
                                <span><?= htmlspecialchars($label) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; cursor: pointer;">
                        <input type="checkbox" name="featured" value="1" <?= !empty($editItem['featured']) ? 'checked' : '' ?>>
                        <strong>Feature on Leadership / Highlights Header</strong>
                    </label>
                </div>
            </div>

            <div style="margin-top: 24px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">Save Faculty Member</button>
                <a href="faculties.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Faculty Table List -->
<div class="panel">
    <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
            <div class="panel-title">All Faculty Members (<?= count($faculties) ?>)</div>
            <input type="text" class="form-control" data-table-search="facultyTable" placeholder="Search by name, designation, department..." style="max-width: 320px; font-size: 13px;">
        </div>
        <a href="faculties.php?action=create" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add New Faculty
        </a>
    </div>

    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table" id="facultyTable">
                <thead>
                    <tr>
                        <th style="width: 50px;">Order</th>
                        <th>Member</th>
                        <th>Designation</th>
                        <th>Departments</th>
                        <th>Badge</th>
                        <th>Featured</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($faculties)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 32px;">No faculty members found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($faculties as $f): ?>
                        <tr>
                            <td><span class="badge badge-secondary">#<?= (int)$f['sort_order'] ?></span></td>
                            <td style="display: flex; align-items: center; gap: 12px;">
                                <img src="../<?= htmlspecialchars($f['image'] ?: 'assets/logo.ico') ?>" alt="" class="preview-avatar" onerror="this.src='../assets/logo.ico'">
                                <div>
                                    <strong style="color: var(--text-main); font-size: 13.5px;"><?= htmlspecialchars($f['name']) ?></strong>
                                    <div style="font-size: 11.5px; color: var(--text-muted);"><?= htmlspecialchars($f['dept_label']) ?></div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($f['designation']) ?></td>
                            <td>
                                <?php 
                                $fDepts = explode(',', $f['depts']);
                                foreach ($fDepts as $dKey):
                                    if (empty($dKey)) continue;
                                ?>
                                    <span class="badge badge-info" style="margin-right: 4px;"><?= strtoupper(htmlspecialchars($dKey)) ?></span>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <?php if (!empty($f['badge'])): ?>
                                    <span class="badge badge-primary"><?= htmlspecialchars($f['badge']) ?></span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 12px;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($f['featured']): ?>
                                    <span class="badge badge-success">Featured</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Standard</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="faculties.php?edit=<?= (int)$f['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="faculties.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete <?= htmlspecialchars(addslashes($f['name'])) ?>?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
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
