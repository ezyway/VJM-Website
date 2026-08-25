<?php
/**
 * Labs & Infrastructure Management Module
 */

require_once __DIR__ . '/includes/auth.php';
requireAuth();

$db = getDB();

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('danger', 'Security token expired. Please try again.');
        header('Location: labs.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM labs WHERE id = :id');
        $stmt->execute([':id' => $id]);
        setFlash('success', 'Laboratory facility deleted.');
        header('Location: labs.php');
        exit;
    }

    if ($action === 'save') {
        $id          = (int)($_POST['id'] ?? 0);
        $slug        = strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', $_POST['slug'] ?? '')));
        $name        = trim($_POST['name'] ?? '');
        $code        = trim($_POST['code'] ?? '');
        $tagline     = trim($_POST['tagline'] ?? '');
        $badge       = trim($_POST['badge'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $sort_order  = (int)($_POST['sort_order'] ?? 0);

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
            setFlash('danger', 'Lab slug and name are required.');
            header('Location: labs.php');
            exit;
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE labs SET slug = :s, name = :n, code = :c, tagline = :t, badge = :b, description = :d, features = :f, specs = :sp, sort_order = :so WHERE id = :id');
            $stmt->execute([
                ':s'  => $slug,
                ':n'  => $name,
                ':c'  => $code,
                ':t'  => $tagline,
                ':b'  => $badge,
                ':d'  => $description,
                ':f'  => $featuresJson,
                ':sp' => $specsJson,
                ':so' => $sort_order,
                ':id' => $id
            ]);
            setFlash('success', 'Lab details updated successfully.');
        } else {
            $stmt = $db->prepare('INSERT INTO labs (slug, name, code, tagline, badge, description, features, specs, sort_order) VALUES (:s, :n, :c, :t, :b, :d, :f, :sp, :so)');
            $stmt->execute([
                ':s'  => $slug,
                ':n'  => $name,
                ':c'  => $code,
                ':t'  => $tagline,
                ':b'  => $badge,
                ':d'  => $description,
                ':f'  => $featuresJson,
                ':sp' => $specsJson,
                ':so' => $sort_order
            ]);
            setFlash('success', 'New lab facility created.');
        }

        header('Location: labs.php');
        exit;
    }
}

$pageTitle = 'Labs & Facilities';
require_once __DIR__ . '/includes/header.php';

// Fetch edit target
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $db->prepare('SELECT * FROM labs WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $editId]);
    $editItem = $stmt->fetch();
}

$isCreate = isset($_GET['action']) && $_GET['action'] === 'create';
$labs = $db->query('SELECT * FROM labs ORDER BY sort_order ASC, id ASC')->fetchAll();
?>

<?php if ($editItem || $isCreate): ?>
<?php
$featArray = $editItem ? json_decode($editItem['features'] ?? '[]', true) : [];
$featText = is_array($featArray) ? implode("\n", $featArray) : '';

$specsArray = $editItem ? json_decode($editItem['specs'] ?? '{}', true) : [];
$specsText = '';
if (is_array($specsArray)) {
    foreach ($specsArray as $k => $v) {
        $specsText .= "{$k}: {$v}\n";
    }
}
?>
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><?= $editItem ? 'Edit Facility: ' . htmlspecialchars($editItem['name']) : 'Add New Lab Facility' ?></div>
        <a href="labs.php" class="btn btn-secondary btn-sm">← Back to List</a>
    </div>
    <div class="panel-body">
        <form method="POST" action="labs.php">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editItem ? (int)$editItem['id'] : 0 ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="slug">Identifier Slug * (Unique)</label>
                    <input type="text" id="slug" name="slug" class="form-control" required value="<?= htmlspecialchars($editItem['slug'] ?? '') ?>" placeholder="e.g. computer, chemistry, physics">
                </div>

                <div class="form-group">
                    <label class="form-label" for="name">Laboratory Full Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required value="<?= htmlspecialchars($editItem['name'] ?? '') ?>" placeholder="e.g. Computer &amp; Advanced IT Lab">
                </div>

                <div class="form-group">
                    <label class="form-label" for="code">Short Code / Tab Label *</label>
                    <input type="text" id="code" name="code" class="form-control" required value="<?= htmlspecialchars($editItem['code'] ?? '') ?>" placeholder="e.g. Computer Lab">
                </div>

                <div class="form-group">
                    <label class="form-label" for="badge">Department Badge</label>
                    <input type="text" id="badge" name="badge" class="form-control" value="<?= htmlspecialchars($editItem['badge'] ?? '') ?>" placeholder="e.g. IT &amp; Computer Applications">
                </div>

                <div class="form-group">
                    <label class="form-label" for="sort_order">Display Order</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= htmlspecialchars($editItem['sort_order'] ?? '1') ?>">
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="tagline">Tagline</label>
                    <input type="text" id="tagline" name="tagline" class="form-control" value="<?= htmlspecialchars($editItem['tagline'] ?? '') ?>" placeholder="e.g. High-Speed Computing, Modern IDEs &amp; Software Innovation">
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="description">Detailed Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3"><?= htmlspecialchars($editItem['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="features">Key Features (One feature per line)</label>
                    <textarea id="features" name="features" class="form-control" rows="4" placeholder="High-Speed Gigabit LAN &amp; Enterprise Wi-Fi&#10;Latest Development IDEs, Python, Java"><?= htmlspecialchars($featText) ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="specs">Specifications (Key: Value per line)</label>
                    <textarea id="specs" name="specs" class="form-control" rows="4" placeholder="Capacity: 120+ Workstations&#10;Networking: High-Speed Fiber Optic&#10;Operating Systems: Windows 11 &amp; Linux Ubuntu"><?= htmlspecialchars($specsText) ?></textarea>
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">Save Laboratory</button>
                <a href="labs.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Labs Table List -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Laboratories &amp; Campus Facilities (<?= count($labs) ?>)</div>
        <a href="labs.php?action=create" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add New Lab
        </a>
    </div>

    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Order</th>
                        <th>Code</th>
                        <th>Facility Name</th>
                        <th>Badge</th>
                        <th>Tagline</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($labs)): ?>
                        <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 32px;">No labs configured.</td></tr>
                    <?php else: ?>
                        <?php foreach ($labs as $l): ?>
                        <tr>
                            <td><span class="badge badge-secondary">#<?= (int)$l['sort_order'] ?></span></td>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($l['code']) ?></span></td>
                            <td><strong style="color: var(--text-main);"><?= htmlspecialchars($l['name']) ?></strong></td>
                            <td><span class="badge badge-info"><?= htmlspecialchars($l['badge']) ?></span></td>
                            <td style="color: var(--text-muted); font-size: 12.5px; max-width: 250px;">
                                <?= htmlspecialchars(mb_strimwidth($l['tagline'], 0, 60, '...')) ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="labs.php?edit=<?= (int)$l['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="labs.php" style="display: inline;" onsubmit="return confirm('Delete this facility?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
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
