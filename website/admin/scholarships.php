<?php
/**
 * Scholarships & Financial Aid Management Module
 */

require_once __DIR__ . '/includes/auth.php';
requireAuth();

$db = getDB();

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('danger', 'Security token expired. Please try again.');
        header('Location: scholarships.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete_record') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM scholarships WHERE id = :id');
        $stmt->execute([':id' => $id]);
        setFlash('success', 'Scholarship year record removed.');
        header('Location: scholarships.php');
        exit;
    }

    if ($action === 'save_record') {
        $id         = (int)($_POST['id'] ?? 0);
        $year       = trim($_POST['year'] ?? '');
        $amount_str = trim($_POST['amount_str'] ?? '');
        $numeric    = (int)preg_replace('/[^0-9]/', '', $amount_str);
        $status     = trim($_POST['status'] ?? 'Completed');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if (empty($year) || empty($amount_str)) {
            setFlash('danger', 'Year and Amount are required.');
            header('Location: scholarships.php');
            exit;
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE scholarships SET year = :yr, amount_str = :amt, amount_numeric = :num, status = :st, sort_order = :so WHERE id = :id');
            $stmt->execute([
                ':yr'  => $year,
                ':amt' => $amount_str,
                ':num' => $numeric,
                ':st'  => $status,
                ':so'  => $sort_order,
                ':id'  => $id
            ]);
            setFlash('success', 'Scholarship record updated.');
        } else {
            $stmt = $db->prepare('INSERT INTO scholarships (year, amount_str, amount_numeric, status, sort_order) VALUES (:yr, :amt, :num, :st, :so)');
            $stmt->execute([
                ':yr'  => $year,
                ':amt' => $amount_str,
                ':num' => $numeric,
                ':st'  => $status,
                ':so'  => $sort_order
            ]);
            setFlash('success', 'New scholarship year added.');
        }

        header('Location: scholarships.php');
        exit;
    }
}

$pageTitle = 'Scholarships & Aid';
require_once __DIR__ . '/includes/header.php';

// Fetch edit target
$editRecord = null;
if (isset($_GET['edit_record'])) {
    $rId = (int)$_GET['edit_record'];
    $stmt = $db->prepare('SELECT * FROM scholarships WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $rId]);
    $editRecord = $stmt->fetch();
}

$isCreateRecord = isset($_GET['action']) && $_GET['action'] === 'create_record';
$records = $db->query('SELECT * FROM scholarships ORDER BY sort_order ASC, id ASC')->fetchAll();
$totalDisbursed = $db->query('SELECT SUM(amount_numeric) FROM scholarships')->fetchColumn() ?: 0;
?>

<!-- Total Disbursed Metric -->
<div class="stats-grid" style="margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Total Historical Aid Disbursed</div>
            <div class="stat-value">₹ <?= number_format($totalDisbursed) ?></div>
        </div>
        <div class="stat-icon success">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        </div>
    </div>
</div>

<?php if ($editRecord || $isCreateRecord): ?>
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><?= $editRecord ? 'Edit Scholarship Year' : 'Add Scholarship Year Record' ?></div>
        <a href="scholarships.php" class="btn btn-secondary btn-sm">← Back to List</a>
    </div>
    <div class="panel-body">
        <form method="POST" action="scholarships.php">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="save_record">
            <input type="hidden" name="id" value="<?= $editRecord ? (int)$editRecord['id'] : 0 ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="year">Academic Year *</label>
                    <input type="text" id="year" name="year" class="form-control" required value="<?= htmlspecialchars($editRecord['year'] ?? '') ?>" placeholder="e.g. 2024 - 2025">
                </div>

                <div class="form-group">
                    <label class="form-label" for="amount_str">Disbursed Amount String *</label>
                    <input type="text" id="amount_str" name="amount_str" class="form-control" required value="<?= htmlspecialchars($editRecord['amount_str'] ?? '') ?>" placeholder="e.g. ₹ 21,22,000">
                </div>

                <div class="form-group">
                    <label class="form-label" for="status">Status Badge</label>
                    <input type="text" id="status" name="status" class="form-control" value="<?= htmlspecialchars($editRecord['status'] ?? 'Completed') ?>" placeholder="e.g. Latest, Completed, Peak">
                </div>

                <div class="form-group">
                    <label class="form-label" for="sort_order">Display Priority</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= htmlspecialchars($editRecord['sort_order'] ?? '1') ?>">
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">Save Record</button>
                <a href="scholarships.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Records Table -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Yearly Disbursement Records (<?= count($records) ?> years)</div>
        <a href="scholarships.php?action=create_record" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Academic Year
        </a>
    </div>

    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Order</th>
                        <th>Academic Year</th>
                        <th>Amount Disbursed</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--text-muted); padding: 32px;">No scholarship records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($records as $r): ?>
                        <tr>
                            <td><span class="badge badge-secondary">#<?= (int)$r['sort_order'] ?></span></td>
                            <td><strong style="color: var(--text-main);"><?= htmlspecialchars($r['year']) ?></strong></td>
                            <td style="color: var(--success); font-weight: 600; font-size: 14px;"><?= htmlspecialchars($r['amount_str']) ?></td>
                            <td>
                                <span class="badge badge-<?= $r['status'] === 'Latest' ? 'success' : ($r['status'] === 'Peak' ? 'warning' : 'secondary') ?>">
                                    <?= htmlspecialchars($r['status']) ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="scholarships.php?edit_record=<?= (int)$r['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="scholarships.php" style="display: inline;" onsubmit="return confirm('Delete this record?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="delete_record">
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
