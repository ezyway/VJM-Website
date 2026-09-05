<?php
/**
 * Scholarships & Financial Aid Management Module
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/ui.php';
requireAuth();

$db = getDB();
$isDrawerMode = isset($_GET['drawer']) && $_GET['drawer'] === '1';

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('danger', 'Security token expired. Please try again.');
        header('Location: ' . ($isDrawerMode ? $_SERVER['REQUEST_URI'] : 'scholarships.php'));
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete_record') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM scholarships WHERE id = :id');
        $stmt->execute([':id' => $id]);
        setFlash('success', 'Scholarship year record removed.');
        header('Location: ' . ($isDrawerMode ? $_SERVER['REQUEST_URI'] : 'scholarships.php'));
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
            header('Location: ' . ($isDrawerMode ? $_SERVER['REQUEST_URI'] : 'scholarships.php'));
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

        header('Location: ' . ($isDrawerMode ? $_SERVER['REQUEST_URI'] : 'scholarships.php'));
        exit;
    }

    // Portals CRUD
    if ($action === 'delete_portal') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM scholarship_portals WHERE id = :id');
        $stmt->execute([':id' => $id]);
        setFlash('success', 'Portal removed.');
        header('Location: ' . ($isDrawerMode ? $_SERVER['REQUEST_URI'] : 'scholarships.php?tab=portals'));
        exit;
    }

    if ($action === 'save_portal') {
        $id          = (int)($_POST['id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $provider    = trim($_POST['provider'] ?? '');
        $badge       = trim($_POST['badge'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $link        = trim($_POST['link'] ?? '');
        $icon        = trim($_POST['icon'] ?? 'shield');
        $sort_order  = (int)($_POST['sort_order'] ?? 0);

        if (empty($name) || empty($link)) {
            setFlash('danger', 'Portal name and link are required.');
            header('Location: ' . ($isDrawerMode ? $_SERVER['REQUEST_URI'] : 'scholarships.php?tab=portals'));
            exit;
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE scholarship_portals SET name = :n, provider = :p, badge = :b, description = :d, link = :l, icon = :i, sort_order = :so WHERE id = :id');
            $stmt->execute([
                ':n'  => $name,
                ':p'  => $provider,
                ':b'  => $badge,
                ':d'  => $description,
                ':l'  => $link,
                ':i'  => $icon,
                ':so' => $sort_order,
                ':id' => $id
            ]);
            setFlash('success', 'Portal updated.');
        } else {
            $stmt = $db->prepare('INSERT INTO scholarship_portals (name, provider, badge, description, link, icon, sort_order) VALUES (:n, :p, :b, :d, :l, :i, :so)');
            $stmt->execute([
                ':n'  => $name,
                ':p'  => $provider,
                ':b'  => $badge,
                ':d'  => $description,
                ':l'  => $link,
                ':i'  => $icon,
                ':so' => $sort_order
            ]);
            setFlash('success', 'New portal added.');
        }

        header('Location: ' . ($isDrawerMode ? $_SERVER['REQUEST_URI'] : 'scholarships.php?tab=portals'));
        exit;
    }
}

$pageTitle = 'Scholarships & Aid';
require_once __DIR__ . '/includes/header.php';

$tab = $_GET['tab'] ?? 'records';

// Fetch edit targets
$editRecord = null;
if (isset($_GET['edit_record'])) {
    $rId = (int)$_GET['edit_record'];
    $stmt = $db->prepare('SELECT * FROM scholarships WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $rId]);
    $editRecord = $stmt->fetch();
}

$editPortal = null;
if (isset($_GET['edit_portal'])) {
    $pId = (int)$_GET['edit_portal'];
    $stmt = $db->prepare('SELECT * FROM scholarship_portals WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $pId]);
    $editPortal = $stmt->fetch();
}

$isCreateRecord = isset($_GET['action']) && $_GET['action'] === 'create_record';
$isCreatePortal = isset($_GET['action']) && $_GET['action'] === 'create_portal';

$records = $db->query('SELECT * FROM scholarships ORDER BY sort_order ASC, id ASC')->fetchAll();
$portals = $db->query('SELECT * FROM scholarship_portals ORDER BY sort_order ASC, id ASC')->fetchAll();
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

<!-- Tab Navigation -->
<div class="panel" style="margin-bottom: 24px; padding: 0; background: var(--bg-panel); border-radius: var(--radius-lg); overflow: hidden;">
    <div class="panel-header" style="padding: 0; border: 0; background: var(--bg-input);">
        <nav class="tab-nav">
            <a href="scholarships.php?tab=records<?= $drawerSuffix ?>" class="tab-link <?= $tab === 'records' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                <span>Yearly Records</span>
                <span class="badge badge-primary" style="margin-left: 8px;"><?= count($records) ?></span>
            </a>
            <a href="scholarships.php?tab=portals<?= $drawerSuffix ?>" class="tab-link <?= $tab === 'portals' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                <span>Portals & Schemes</span>
                <span class="badge badge-info" style="margin-left: 8px;"><?= count($portals) ?></span>
            </a>
        </nav>
    </div>
</div>

<?php if (($editRecord || $isCreateRecord) && $tab === 'records'): ?>
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><?= $editRecord ? 'Edit Scholarship Year' : 'Add Scholarship Year Record' ?></div>
        <a href="scholarships.php?tab=records<?= $drawerSuffix ?>" class="btn btn-secondary btn-sm">← Back to List</a>
    </div>
    <div class="panel-body">
        <form method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'scholarships.php') ?>">
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
                <a href="scholarships.php?tab=records<?= $drawerSuffix ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if (($editPortal || $isCreatePortal) && $tab === 'portals'): ?>
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><?= $editPortal ? 'Edit Portal' : 'Add Scholarship Portal' ?></div>
        <a href="scholarships.php?tab=portals<?= $drawerSuffix ?>" class="btn btn-secondary btn-sm">← Back to List</a>
    </div>
    <div class="panel-body">
        <form method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'scholarships.php') ?>">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="save_portal">
            <input type="hidden" name="id" value="<?= $editPortal ? (int)$editPortal['id'] : 0 ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="name">Portal Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required value="<?= htmlspecialchars($editPortal['name'] ?? '') ?>" placeholder="e.g. Digital Gujarat Scholarship Portal">
                </div>

                <div class="form-group">
                    <label class="form-label" for="provider">Provider Authority</label>
                    <input type="text" id="provider" name="provider" class="form-control" value="<?= htmlspecialchars($editPortal['provider'] ?? '') ?>" placeholder="e.g. Government of Gujarat">
                </div>

                <div class="form-group">
                    <label class="form-label" for="badge">Badge Label</label>
                    <input type="text" id="badge" name="badge" class="form-control" value="<?= htmlspecialchars($editPortal['badge'] ?? '') ?>" placeholder="e.g. State Government">
                </div>

                <div class="form-group">
                    <label class="form-label" for="icon">Icon</label>
                    <select id="icon" name="icon" class="form-control">
                        <option value="shield" <?= ($editPortal['icon'] ?? 'shield') === 'shield' ? 'selected' : '' ?>>Shield (Shield)</option>
                        <option value="award" <?= ($editPortal['icon'] ?? '') === 'award' ? 'selected' : '' ?>>Award (Award)</option>
                        <option value="cap" <?= ($editPortal['icon'] ?? '') === 'cap' ? 'selected' : '' ?>>Cap (Graduation)</option>
                        <option value="briefcase" <?= ($editPortal['icon'] ?? '') === 'briefcase' ? 'selected' : '' ?>>Briefcase (Career)</option>
                        <option value="globe" <?= ($editPortal['icon'] ?? '') === 'globe' ? 'selected' : '' ?>>Globe (National)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="sort_order">Display Priority</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= htmlspecialchars($editPortal['sort_order'] ?? '1') ?>">
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Brief description of the portal and eligibility..."><?= htmlspecialchars($editPortal['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="link">Portal URL *</label>
                    <input type="url" id="link" name="link" class="form-control" required value="<?= htmlspecialchars($editPortal['link'] ?? '') ?>" placeholder="https://...">
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">Save Portal</button>
                <a href="scholarships.php?tab=portals<?= $drawerSuffix ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if ($tab === 'records'): ?>
<!-- Records Table -->
<div class="panel">
    <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
            <div class="panel-title">Yearly Disbursement Records (<?= count($records) ?> years)</div>
            <input type="text" class="form-control" data-table-search="scholarshipsRecordsTable" placeholder="Search years or status..." style="max-width: 280px; font-size: 13px;">
        </div>
        <a href="scholarships.php?tab=records&action=create_record<?= $drawerUrlSuffix ?>" 
           data-drawer-url="scholarships.php?tab=records&action=create_record<?= $drawerUrlSuffix ?>" 
           data-drawer-title="Add Academic Year" 
           class="btn btn-primary btn-sm">
            <?= icon('plus', 14) ?>
            Add Academic Year
        </a>
    </div>

    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table" id="scholarshipsRecordsTable" data-reorder="scholarships">
                <thead>
                    <tr>
                        <th class="reorder-col" style="width: 48px;"></th>
                        <th style="width: 60px;">Order</th>
                        <th>Academic Year</th>
                        <th>Amount Disbursed</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr><td colspan="6" class="empty-cell">No scholarship records yet. Click <strong>Add Academic Year</strong> to start tracking aid.</td></tr>
                    <?php else: ?>
                        <?php foreach ($records as $r): ?>
                        <tr data-reorder-id="<?= (int)$r['id'] ?>">
                            <td class="reorder-col">
                                <button type="button" class="sortable-handle" aria-label="Drag to reorder" title="Drag to reorder">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="6" r="1.5"></circle><circle cx="15" cy="6" r="1.5"></circle><circle cx="9" cy="12" r="1.5"></circle><circle cx="15" cy="12" r="1.5"></circle><circle cx="9" cy="18" r="1.5"></circle><circle cx="15" cy="18" r="1.5"></circle></svg>
                                </button>
                            </td>
                            <td><span class="badge badge-secondary order-badge">#<?= (int)$r['sort_order'] ?></span></td>
                            <td><strong style="color: var(--text-main);"><?= htmlspecialchars($r['year']) ?></strong></td>
                            <td style="color: var(--success); font-weight: 600; font-size: 14px;"><?= htmlspecialchars($r['amount_str']) ?></td>
                            <td>
                                <span class="badge badge-<?= $r['status'] === 'Latest' ? 'success' : ($r['status'] === 'Peak' ? 'warning' : 'secondary') ?>">
                                    <?= htmlspecialchars($r['status']) ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="scholarships.php?tab=records&edit_record=<?= (int)$r['id'] ?><?= $drawerUrlSuffix ?>" 
                                       data-drawer-url="scholarships.php?tab=records&edit_record=<?= (int)$r['id'] ?><?= $drawerUrlSuffix ?>" 
                                       data-drawer-title="Edit Scholarship Year" 
                                       class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="scholarships.php<?= $drawerParam ?>" style="display: inline;" id="delete-record-<?= (int)$r['id'] ?>">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="delete_record">
                                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                data-confirm="Delete this scholarship record?" 
                                                data-confirm-form="#delete-record-<?= (int)$r['id'] ?>">
                                            Delete
                                        </button>
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
<?php endif; ?>

<?php if ($tab === 'portals'): ?>
<!-- Portals Table -->
<div class="panel">
    <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
            <div class="panel-title">Government Scholarship Portals (<?= count($portals) ?>)</div>
            <input type="text" class="form-control" data-table-search="scholarshipPortalsTable" placeholder="Search portals..." style="max-width: 280px; font-size: 13px;">
        </div>
        <a href="scholarships.php?tab=portals&action=create_portal<?= $drawerUrlSuffix ?>" 
           data-drawer-url="scholarships.php?tab=portals&action=create_portal<?= $drawerUrlSuffix ?>" 
           data-drawer-title="Add Portal" 
           class="btn btn-primary btn-sm">
            <?= icon('plus', 14) ?>
            Add Portal
        </a>
    </div>

    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table" id="scholarshipPortalsTable" data-reorder="scholarship_portals">
                <thead>
                    <tr>
                        <th class="reorder-col" style="width: 48px;"></th>
                        <th style="width: 60px;">Order</th>
                        <th>Portal Name</th>
                        <th>Provider</th>
                        <th>Badge</th>
                        <th>Link</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($portals)): ?>
                        <tr><td colspan="7" class="empty-cell">No scholarship portals configured yet. Click <strong>Add Portal</strong> to link one.</td></tr>
                    <?php else: ?>
                        <?php foreach ($portals as $p): ?>
                        <tr data-reorder-id="<?= (int)$p['id'] ?>">
                            <td class="reorder-col">
                                <button type="button" class="sortable-handle" aria-label="Drag to reorder" title="Drag to reorder">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="6" r="1.5"></circle><circle cx="15" cy="6" r="1.5"></circle><circle cx="9" cy="12" r="1.5"></circle><circle cx="15" cy="12" r="1.5"></circle><circle cx="9" cy="18" r="1.5"></circle><circle cx="15" cy="18" r="1.5"></circle></svg>
                                </button>
                            </td>
                            <td><span class="badge badge-secondary order-badge">#<?= (int)$p['sort_order'] ?></span></td>
                            <td><strong style="color: var(--text-main);"><?= htmlspecialchars($p['name']) ?></strong></td>
                            <td><?= htmlspecialchars($p['provider'] ?: '—') ?></td>
                            <td>
                                <?php if (!empty($p['badge'])): ?>
                                    <span class="badge badge-info"><?= htmlspecialchars($p['badge']) ?></span>
                                <?php else: ?>
                                    <span style="color: var(--text-muted); font-size: 12px;">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="max-width: 250px; font-size: 12.5px; color: var(--text-muted);">
                                <a href="<?= htmlspecialchars($p['link']) ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($p['link']) ?></a>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="scholarships.php?tab=portals&edit_portal=<?= (int)$p['id'] ?><?= $drawerUrlSuffix ?>" 
                                       data-drawer-url="scholarships.php?tab=portals&edit_portal=<?= (int)$p['id'] ?><?= $drawerUrlSuffix ?>" 
                                       data-drawer-title="Edit Portal" 
                                       class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="scholarships.php<?= $drawerParam ?>" style="display: inline;" id="delete-portal-<?= (int)$p['id'] ?>">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="delete_portal">
                                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                data-confirm="Delete this portal?" 
                                                data-confirm-form="#delete-portal-<?= (int)$p['id'] ?>">
                                            Delete
                                        </button>
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
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
