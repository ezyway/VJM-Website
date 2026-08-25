<?php
/**
 * Academic Pass Rates Management Module
 */

require_once __DIR__ . '/includes/auth.php';
requireAuth();

$db = getDB();

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('danger', 'Security token expired. Please try again.');
        header('Location: pass_rates.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM pass_rates WHERE id = :id');
        $stmt->execute([':id' => $id]);
        setFlash('success', 'Pass rate record deleted.');
        header('Location: pass_rates.php');
        exit;
    }

    if ($action === 'save') {
        $id         = (int)($_POST['id'] ?? 0);
        $year       = trim($_POST['year'] ?? '');
        $bca        = trim($_POST['bca'] ?? '—');
        $bsc        = trim($_POST['bsc'] ?? '—');
        $bba        = trim($_POST['bba'] ?? '—');
        $bcom       = trim($_POST['bcom'] ?? '—');
        $bsw        = trim($_POST['bsw'] ?? '—');
        $pgdca      = trim($_POST['pgdca'] ?? '—');
        $msc_it     = trim($_POST['msc_it'] ?? '—');
        $mcom       = trim($_POST['mcom'] ?? '—');
        $msc_chem   = trim($_POST['msc_chem'] ?? '—');
        $is_latest  = !empty($_POST['is_latest']) ? 1 : 0;
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if (empty($year)) {
            setFlash('danger', 'Academic Year is required.');
            header('Location: pass_rates.php');
            exit;
        }

        if ($is_latest) {
            $db->exec('UPDATE pass_rates SET is_latest = 0');
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE pass_rates SET year = :yr, bca = :bca, bsc = :bsc, bba = :bba, bcom = :bcom, bsw = :bsw, pgdca = :pg, msc_it = :it, mcom = :mc, msc_chem = :ch, is_latest = :lat, sort_order = :so WHERE id = :id');
            $stmt->execute([
                ':yr'   => $year,
                ':bca'  => $bca,
                ':bsc'  => $bsc,
                ':bba'  => $bba,
                ':bcom' => $bcom,
                ':bsw'  => $bsw,
                ':pg'   => $pgdca,
                ':it'   => $msc_it,
                ':mc'   => $mcom,
                ':ch'   => $msc_chem,
                ':lat'  => $is_latest,
                ':so'   => $sort_order,
                ':id'   => $id
            ]);
            setFlash('success', 'Pass rates updated.');
        } else {
            $stmt = $db->prepare('INSERT INTO pass_rates (year, bca, bsc, bba, bcom, bsw, pgdca, msc_it, mcom, msc_chem, is_latest, sort_order) VALUES (:yr, :bca, :bsc, :bba, :bcom, :bsw, :pg, :it, :mc, :ch, :lat, :so)');
            $stmt->execute([
                ':yr'   => $year,
                ':bca'  => $bca,
                ':bsc'  => $bsc,
                ':bba'  => $bba,
                ':bcom' => $bcom,
                ':bsw'  => $bsw,
                ':pg'   => $pgdca,
                ':it'   => $msc_it,
                ':mc'   => $mcom,
                ':ch'   => $msc_chem,
                ':lat'  => $is_latest,
                ':so'   => $sort_order
            ]);
            setFlash('success', 'New academic pass rate year added.');
        }

        header('Location: pass_rates.php');
        exit;
    }
}

$pageTitle = 'Academic Pass Rates';
require_once __DIR__ . '/includes/header.php';

// Fetch edit target
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $db->prepare('SELECT * FROM pass_rates WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $editId]);
    $editItem = $stmt->fetch();
}

$isCreate = isset($_GET['action']) && $_GET['action'] === 'create';
$records = $db->query('SELECT * FROM pass_rates ORDER BY sort_order ASC, year DESC')->fetchAll();
?>

<?php if ($editItem || $isCreate): ?>
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><?= $editItem ? 'Edit Pass Rates (' . htmlspecialchars($editItem['year']) . ')' : 'Add New Academic Year Record' ?></div>
        <a href="pass_rates.php" class="btn btn-secondary btn-sm">← Back to List</a>
    </div>
    <div class="panel-body">
        <form method="POST" action="pass_rates.php">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editItem ? (int)$editItem['id'] : 0 ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="year">Academic Year *</label>
                    <input type="text" id="year" name="year" class="form-control" required value="<?= htmlspecialchars($editItem['year'] ?? '') ?>" placeholder="e.g. 2026 or 2025-26">
                </div>

                <div class="form-group">
                    <label class="form-label" for="sort_order">Display Order</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= htmlspecialchars($editItem['sort_order'] ?? '1') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="bca">BCA Rate</label>
                    <input type="text" id="bca" name="bca" class="form-control" value="<?= htmlspecialchars($editItem['bca'] ?? '—') ?>" placeholder="e.g. 98.40% or —">
                </div>

                <div class="form-group">
                    <label class="form-label" for="bsc">B.Sc. Rate</label>
                    <input type="text" id="bsc" name="bsc" class="form-control" value="<?= htmlspecialchars($editItem['bsc'] ?? '—') ?>" placeholder="e.g. 100.00%">
                </div>

                <div class="form-group">
                    <label class="form-label" for="bba">BBA Rate</label>
                    <input type="text" id="bba" name="bba" class="form-control" value="<?= htmlspecialchars($editItem['bba'] ?? '—') ?>" placeholder="e.g. 96.87%">
                </div>

                <div class="form-group">
                    <label class="form-label" for="bcom">B.Com. Rate</label>
                    <input type="text" id="bcom" name="bcom" class="form-control" value="<?= htmlspecialchars($editItem['bcom'] ?? '—') ?>" placeholder="e.g. 95.59%">
                </div>

                <div class="form-group">
                    <label class="form-label" for="bsw">BSW Rate</label>
                    <input type="text" id="bsw" name="bsw" class="form-control" value="<?= htmlspecialchars($editItem['bsw'] ?? '—') ?>" placeholder="e.g. 100.00%">
                </div>

                <div class="form-group">
                    <label class="form-label" for="pgdca">PGDCA Rate</label>
                    <input type="text" id="pgdca" name="pgdca" class="form-control" value="<?= htmlspecialchars($editItem['pgdca'] ?? '—') ?>" placeholder="e.g. 100.00% or —">
                </div>

                <div class="form-group">
                    <label class="form-label" for="msc_it">M.Sc.(IT) Rate</label>
                    <input type="text" id="msc_it" name="msc_it" class="form-control" value="<?= htmlspecialchars($editItem['msc_it'] ?? '—') ?>" placeholder="e.g. 100.00%">
                </div>

                <div class="form-group">
                    <label class="form-label" for="mcom">M.Com. Rate</label>
                    <input type="text" id="mcom" name="mcom" class="form-control" value="<?= htmlspecialchars($editItem['mcom'] ?? '—') ?>" placeholder="e.g. 100.00%">
                </div>

                <div class="form-group">
                    <label class="form-label" for="msc_chem">M.Sc.(Chem) Rate</label>
                    <input type="text" id="msc_chem" name="msc_chem" class="form-control" value="<?= htmlspecialchars($editItem['msc_chem'] ?? '—') ?>" placeholder="e.g. 97.53%">
                </div>

                <div class="form-group full-width">
                    <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; cursor: pointer;">
                        <input type="checkbox" name="is_latest" value="1" <?= !empty($editItem['is_latest']) ? 'checked' : '' ?>>
                        <strong>Highlight as "Latest Batch" on the Homepage table</strong>
                    </label>
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">Save Pass Rates</button>
                <a href="pass_rates.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Records Table -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Academic Pass Rate History (<?= count($records) ?> years)</div>
        <a href="pass_rates.php?action=create" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Academic Year
        </a>
    </div>

    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>BCA</th>
                        <th>B.Sc.</th>
                        <th>BBA</th>
                        <th>B.Com.</th>
                        <th>BSW</th>
                        <th>PGDCA</th>
                        <th>M.Sc.(IT)</th>
                        <th>M.Com.</th>
                        <th>M.Sc.(Chem)</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr><td colspan="11" style="text-align: center; color: var(--text-muted); padding: 32px;">No pass rates records.</td></tr>
                    <?php else: ?>
                        <?php foreach ($records as $r): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($r['year']) ?></strong>
                                <?php if ($r['is_latest']): ?>
                                    <span class="badge badge-success" style="margin-left: 6px;">Latest</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($r['bca']) ?></td>
                            <td><?= htmlspecialchars($r['bsc']) ?></td>
                            <td><?= htmlspecialchars($r['bba']) ?></td>
                            <td><?= htmlspecialchars($r['bcom']) ?></td>
                            <td><?= htmlspecialchars($r['bsw']) ?></td>
                            <td><?= htmlspecialchars($r['pgdca']) ?></td>
                            <td><?= htmlspecialchars($r['msc_it']) ?></td>
                            <td><?= htmlspecialchars($r['mcom']) ?></td>
                            <td><?= htmlspecialchars($r['msc_chem']) ?></td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="pass_rates.php?edit=<?= (int)$r['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="pass_rates.php" style="display: inline;" onsubmit="return confirm('Delete this record?');">
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
