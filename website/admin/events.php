<?php
/**
 * Events & Announcements Management Module
 */

require_once __DIR__ . '/includes/auth.php';
requireAuth();

$db = getDB();

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('danger', 'Security token expired. Please try again.');
        header('Location: events.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM events WHERE id = :id');
        $stmt->execute([':id' => $id]);
        setFlash('success', 'Item removed successfully.');
        header('Location: events.php');
        exit;
    }

    if ($action === 'save') {
        $id          = (int)($_POST['id'] ?? 0);
        $title       = trim($_POST['title'] ?? '');
        $badge       = trim($_POST['badge'] ?? '');
        $badge_type  = trim($_POST['badge_type'] ?? 'confirmed');
        $description = trim($_POST['description'] ?? '');
        $event_type  = trim($_POST['event_type'] ?? 'event');
        $sort_order  = (int)($_POST['sort_order'] ?? 0);

        if (empty($title)) {
            setFlash('danger', 'Title is required.');
            header('Location: events.php');
            exit;
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE events SET title = :t, badge = :b, badge_type = :bt, description = :d, event_type = :et, sort_order = :s WHERE id = :id');
            $stmt->execute([
                ':t'  => $title,
                ':b'  => $badge,
                ':bt' => $badge_type,
                ':d'  => $description,
                ':et' => $event_type,
                ':s'  => $sort_order,
                ':id' => $id
            ]);
            setFlash('success', 'Event / Notice updated successfully.');
        } else {
            $stmt = $db->prepare('INSERT INTO events (title, badge, badge_type, description, event_type, sort_order) VALUES (:t, :b, :bt, :d, :et, :s)');
            $stmt->execute([
                ':t'  => $title,
                ':b'  => $badge,
                ':bt' => $badge_type,
                ':d'  => $description,
                ':et' => $event_type,
                ':s'  => $sort_order
            ]);
            setFlash('success', 'New item created successfully.');
        }

        header('Location: events.php');
        exit;
    }
}

$pageTitle = 'Events & News Hub';
require_once __DIR__ . '/includes/header.php';

// Fetch edit target
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $db->prepare('SELECT * FROM events WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $editId]);
    $editItem = $stmt->fetch();
}

$isCreate = isset($_GET['action']) && $_GET['action'] === 'create';
$items = $db->query('SELECT * FROM events ORDER BY event_type ASC, sort_order ASC, id DESC')->fetchAll();
?>

<?php if ($editItem || $isCreate): ?>
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><?= $editItem ? 'Edit Event / Announcement' : 'Post New Event or Notice' ?></div>
        <a href="events.php" class="btn btn-secondary btn-sm">← Back to List</a>
    </div>
    <div class="panel-body">
        <form method="POST" action="events.php">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editItem ? (int)$editItem['id'] : 0 ?>">

            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label" for="title">Title / Headline *</label>
                    <input type="text" id="title" name="title" class="form-control" required value="<?= htmlspecialchars($editItem['title'] ?? '') ?>" placeholder="e.g. Independence Day Celebration">
                </div>

                <div class="form-group">
                    <label class="form-label" for="event_type">Category Section *</label>
                    <select id="event_type" name="event_type" class="form-control">
                        <option value="event" <?= ($editItem['event_type'] ?? '') === 'event' ? 'selected' : '' ?>>Upcoming Event (Campus Life)</option>
                        <option value="news" <?= ($editItem['event_type'] ?? '') === 'news' ? 'selected' : '' ?>>Academic News &amp; Circular</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="badge">Badge / Date Text</label>
                    <input type="text" id="badge" name="badge" class="form-control" value="<?= htmlspecialchars($editItem['badge'] ?? 'TBA') ?>" placeholder="e.g. 15 Aug 2025 or TBA or Latest">
                </div>

                <div class="form-group">
                    <label class="form-label" for="badge_type">Badge Visual Style</label>
                    <select id="badge_type" name="badge_type" class="form-control">
                        <option value="confirmed" <?= ($editItem['badge_type'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed / Highlighted (Green)</option>
                        <option value="tba" <?= ($editItem['badge_type'] ?? '') === 'tba' ? 'selected' : '' ?>>TBA / Neutral (Gray/Muted)</option>
                        <option value="latest" <?= ($editItem['badge_type'] ?? '') === 'latest' ? 'selected' : '' ?>>Latest / Notice (Blue/Purple)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="sort_order">Display Order Priority</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= htmlspecialchars($editItem['sort_order'] ?? '1') ?>">
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="description">Summary / Content</label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Provide a brief summary of the event or notice..."><?= htmlspecialchars($editItem['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="events.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Events Table List -->
<div class="panel">
    <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
            <div class="panel-title">All Events &amp; Circulars (<?= count($items) ?>)</div>
            <input type="text" class="form-control" data-table-search="eventsTable" placeholder="Search events..." style="max-width: 320px; font-size: 13px;">
        </div>
        <a href="events.php?action=create" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add New Item
        </a>
    </div>

    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table" id="eventsTable">
                <thead>
                    <tr>
                        <th style="width: 50px;">Order</th>
                        <th>Category</th>
                        <th>Title</th>
                        <th>Date / Badge</th>
                        <th>Summary</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr><td colspan="6" style="text-align: center; color: var(--text-muted); padding: 32px;">No items found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($items as $ev): ?>
                        <tr>
                            <td><span class="badge badge-secondary">#<?= (int)$ev['sort_order'] ?></span></td>
                            <td>
                                <span class="badge badge-<?= $ev['event_type'] === 'event' ? 'primary' : 'info' ?>">
                                    <?= $ev['event_type'] === 'event' ? 'Upcoming Event' : 'Academic News' ?>
                                </span>
                            </td>
                            <td><strong style="color: var(--text-main);"><?= htmlspecialchars($ev['title']) ?></strong></td>
                            <td>
                                <span class="badge badge-<?= $ev['badge_type'] === 'confirmed' ? 'success' : ($ev['badge_type'] === 'latest' ? 'info' : 'secondary') ?>">
                                    <?= htmlspecialchars($ev['badge']) ?>
                                </span>
                            </td>
                            <td style="color: var(--text-muted); max-width: 300px; font-size: 12.5px;">
                                <?= htmlspecialchars(mb_strimwidth($ev['description'], 0, 80, '...')) ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="events.php?edit=<?= (int)$ev['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="events.php" style="display: inline;" onsubmit="return confirm('Delete this event?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int)$ev['id'] ?>">
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
