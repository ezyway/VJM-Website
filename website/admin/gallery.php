<?php
/**
 * Gallery & Media Albums Management Module
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
        header('Location: gallery.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'delete_album') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM gallery_albums WHERE id = :id');
        $stmt->execute([':id' => $id]);
        setFlash('success', 'Album and its photos deleted successfully.');
        header('Location: ' . ($isDrawerMode ? $_SERVER['REQUEST_URI'] : 'gallery.php'));
        exit;
    }

    if ($action === 'delete_photo') {
        $photoId = (int)($_POST['photo_id'] ?? 0);
        $albumId = (int)($_POST['album_id'] ?? 0);
        $stmt = $db->prepare('DELETE FROM gallery_photos WHERE id = :id');
        $stmt->execute([':id' => $photoId]);
        setFlash('success', 'Photo removed from album.');
        header("Location: gallery.php?manage_photos={$albumId}" . ($isDrawerMode ? '&drawer=1' : ''));
        exit;
    }

    if ($action === 'set_cover') {
        $albumId   = (int)($_POST['album_id'] ?? 0);
        $imagePath = trim($_POST['image_path'] ?? '');
        if ($albumId > 0 && !empty($imagePath)) {
            $stmt = $db->prepare('UPDATE gallery_albums SET cover_image = :cov WHERE id = :id');
            $stmt->execute([':cov' => $imagePath, ':id' => $albumId]);
            setFlash('success', 'Album cover photo updated.');
        }
        header("Location: gallery.php?manage_photos={$albumId}" . ($isDrawerMode ? '&drawer=1' : ''));
        exit;
    }

    if ($action === 'update_photo') {
        $photoId = (int)($_POST['photo_id'] ?? 0);
        $albumId = (int)($_POST['album_id'] ?? 0);
        $caption = trim($_POST['caption'] ?? '');
        if ($photoId > 0) {
            $stmt = $db->prepare('UPDATE gallery_photos SET caption = :cap WHERE id = :id');
            $stmt->execute([':cap' => $caption, ':id' => $photoId]);
            setFlash('success', 'Caption updated.');
        }
        header("Location: gallery.php?manage_photos={$albumId}" . ($isDrawerMode ? '&drawer=1' : ''));
        exit;
    }

    if ($action === 'upload_photos') {
        $albumId = (int)($_POST['album_id'] ?? 0);
        $stmtA = $db->prepare('SELECT slug FROM gallery_albums WHERE id = :id LIMIT 1');
        $stmtA->execute([':id' => $albumId]);
        $albumSlug = $stmtA->fetchColumn();

        if (!$albumSlug) {
            setFlash('danger', 'Album not found.');
            header('Location: ' . ($isDrawerMode ? $_SERVER['REQUEST_URI'] : 'gallery.php'));
            exit;
        }

        $uploadedCount = 0;
        if (!empty($_FILES['photos']['name'][0])) {
            $stmtP = $db->prepare('INSERT INTO gallery_photos (album_id, image_path, caption, sort_order) VALUES (:aid, :path, :cap, :sort)');
            $currentMax = (int)$db->query("SELECT MAX(sort_order) FROM gallery_photos WHERE album_id = {$albumId}")->fetchColumn();

            $filesCount = count($_FILES['photos']['name']);
            for ($i = 0; $i < $filesCount; $i++) {
                if ($_FILES['photos']['error'][$i] === UPLOAD_ERR_OK) {
                    $fileItem = [
                        'name'     => $_FILES['photos']['name'][$i],
                        'type'     => $_FILES['photos']['type'][$i],
                        'tmp_name' => $_FILES['photos']['tmp_name'][$i],
                        'error'    => $_FILES['photos']['error'][$i],
                        'size'     => $_FILES['photos']['size'][$i]
                    ];
                    $upload = handleAdminUpload($fileItem, 'gallery/' . $albumSlug);
                    if ($upload['success']) {
                        $currentMax++;
                        $stmtP->execute([
                            ':aid'  => $albumId,
                            ':path' => $upload['path'],
                            ':cap'  => '',
                            ':sort' => $currentMax
                        ]);
                        $uploadedCount++;

                        $db->exec("UPDATE gallery_albums SET cover_image = '{$upload['path']}' WHERE id = {$albumId} AND (cover_image IS NULL OR cover_image = '')");
                    }
                }
            }
        }

        setFlash('success', "Uploaded {$uploadedCount} photo(s) into album.");
        header("Location: gallery.php?manage_photos={$albumId}" . ($isDrawerMode ? '&drawer=1' : ''));
        exit;
    }

    if ($action === 'save_album') {
        $id             = (int)($_POST['id'] ?? 0);
        $slug           = strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', $_POST['slug'] ?? '')));
        $title          = trim($_POST['title'] ?? '');
        $category       = trim($_POST['category'] ?? 'events');
        $category_label = trim($_POST['category_label'] ?? '');
        $description    = trim($_POST['description'] ?? '');
        $sort_order     = (int)($_POST['sort_order'] ?? 0);

        if (empty($slug) || empty($title)) {
            setFlash('danger', 'Album title and slug are required.');
            header('Location: ' . ($isDrawerMode ? $_SERVER['REQUEST_URI'] : 'gallery.php'));
            exit;
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE gallery_albums SET slug = :s, title = :t, category = :cat, category_label = :cl, description = :d, sort_order = :so WHERE id = :id');
            $stmt->execute([
                ':s'   => $slug,
                ':t'   => $title,
                ':cat' => $category,
                ':cl'  => $category_label,
                ':d'   => $description,
                ':so'  => $sort_order,
                ':id'  => $id
            ]);
            setFlash('success', 'Album details updated.');
        } else {
            $stmt = $db->prepare('INSERT INTO gallery_albums (slug, title, category, category_label, description, cover_image, sort_order) VALUES (:s, :t, :cat, :cl, :d, "", :so)');
            $stmt->execute([
                ':s'   => $slug,
                ':t'   => $title,
                ':cat' => $category,
                ':cl'  => $category_label,
                ':d'   => $description,
                ':so'  => $sort_order
            ]);
            setFlash('success', 'New gallery album created. You can now upload photos to it.');
        }

        header('Location: ' . ($isDrawerMode ? $_SERVER['REQUEST_URI'] : 'gallery.php'));
        exit;
    }
}

$pageTitle = 'Photo Gallery & Albums';
require_once __DIR__ . '/includes/header.php';

// Manage Photos view
$manageAlbum = null;
$albumPhotos = [];
if (isset($_GET['manage_photos'])) {
    $mId = (int)$_GET['manage_photos'];
    $stmt = $db->prepare('SELECT * FROM gallery_albums WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $mId]);
    $manageAlbum = $stmt->fetch();

    if ($manageAlbum) {
        $stmtP = $db->prepare('SELECT * FROM gallery_photos WHERE album_id = :aid ORDER BY sort_order ASC, id ASC');
        $stmtP->execute([':aid' => $mId]);
        $albumPhotos = $stmtP->fetchAll();
    }
}

// Edit Album view
$editAlbum = null;
if (isset($_GET['edit_album'])) {
    $eId = (int)$_GET['edit_album'];
    $stmt = $db->prepare('SELECT * FROM gallery_albums WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $eId]);
    $editAlbum = $stmt->fetch();
}

$isCreate = isset($_GET['action']) && $_GET['action'] === 'create_album';

$albums = $db->query('
    SELECT a.*, COUNT(p.id) AS total_photos 
    FROM gallery_albums a 
    LEFT JOIN gallery_photos p ON a.id = p.album_id 
    GROUP BY a.id 
    ORDER BY a.sort_order ASC, a.id ASC
')->fetchAll();
?>

<?php if ($manageAlbum): ?>
<!-- Manage Photos for Album -->
<div class="panel">
    <div class="panel-header">
        <div>
            <div class="panel-title">Photos in: <?= htmlspecialchars($manageAlbum['title']) ?> (<?= count($albumPhotos) ?> photos)</div>
            <span class="form-hint">Category: <?= htmlspecialchars($manageAlbum['category_label'] ?: $manageAlbum['category']) ?></span>
        </div>
        <a href="gallery.php<?= $drawerParam ?>" class="btn btn-secondary btn-sm">← Back to Albums</a>
    </div>

    <div class="panel-body">
        <!-- Batch Upload Form -->
        <form method="POST" action="gallery.php<?= $drawerParam ?>" enctype="multipart/form-data" style="margin-bottom: 28px; background: rgba(255,255,255,0.02); padding: 18px; border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="upload_photos">
            <input type="hidden" name="album_id" value="<?= (int)$manageAlbum['id'] ?>">

            <div style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
                <div class="form-group" style="flex: 1; min-width: 260px; margin-bottom: 0;">
                    <label class="form-label">Upload New Photos (Select Multiple)</label>
                    <input type="file" name="photos[]" multiple class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Upload Selected Photos
                </button>
            </div>
        </form>

        <!-- Photo Grid -->
        <?php if (empty($albumPhotos)): ?>
            <div style="text-align: center; color: var(--text-muted); padding: 40px 20px;">
                No photos in this album yet. Use the upload field above to add photos.
            </div>
        <?php else: ?>
            <div class="gallery-admin-grid">
                <?php foreach ($albumPhotos as $ph): 
                    $isCover = ($ph['image_path'] === $manageAlbum['cover_image']);
                    $caption = htmlspecialchars($ph['caption'] ?? '');
                ?>
                <div class="gallery-admin-item" style="<?= $isCover ? 'border: 2px solid #3b82f6;' : '' ?>">
                    <img src="../<?= htmlspecialchars($ph['image_path']) ?>" alt="Photo" loading="lazy">
                    
                    <?php if ($isCover): ?>
                        <div style="position: absolute; top: 6px; left: 6px; background: #3b82f6; color: white; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px;">
                            COVER
                        </div>
                    <?php endif; ?>

                    <?php if ($caption): ?>
                        <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 8px; background: linear-gradient(transparent, rgba(0,0,0,0.85)); font-size: 11.5px; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?= $caption ?>
                        </div>
                    <?php endif; ?>

                    <div class="gallery-item-overlay">
                        <?php if (!$isCover): ?>
                        <form method="POST" action="gallery.php<?= $drawerParam ?>" style="display: inline;">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="set_cover">
                            <input type="hidden" name="album_id" value="<?= (int)$manageAlbum['id'] ?>">
                            <input type="hidden" name="image_path" value="<?= htmlspecialchars($ph['image_path']) ?>">
                            <button type="submit" class="btn btn-secondary btn-sm" title="Set as Album Cover">Cover</button>
                        </form>
                        <?php endif; ?>

                        <form method="POST" action="gallery.php<?= $drawerParam ?>" style="display: inline-flex; align-items: center; gap: 6px;">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="update_photo">
                            <input type="hidden" name="photo_id" value="<?= (int)$ph['id'] ?>">
                            <input type="hidden" name="album_id" value="<?= (int)$manageAlbum['id'] ?>">
                            <input type="text" name="caption" class="form-control" style="width: 180px; font-size: 12px; padding: 4px 8px;" value="<?= $caption ?>" placeholder="Add caption..." title="Caption">
                            <button type="submit" class="btn btn-secondary btn-sm" title="Save Caption">✎</button>
                        </form>

                        <form method="POST" action="gallery.php<?= $drawerParam ?>" style="display: inline;" id="delete-photo-<?= (int)$ph['id'] ?>">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="delete_photo">
                            <input type="hidden" name="photo_id" value="<?= (int)$ph['id'] ?>">
                            <input type="hidden" name="album_id" value="<?= (int)$manageAlbum['id'] ?>">
                            <button type="button" class="btn btn-danger btn-sm" title="Delete Photo"
                                    data-confirm="Remove this photo from the album?"
                                    data-confirm-form="#delete-photo-<?= (int)$ph['id'] ?>">✕</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php elseif ($editAlbum || $isCreate): ?>
<!-- Add / Edit Album Form -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title"><?= $editAlbum ? 'Edit Album Details' : 'Create New Gallery Album' ?></div>
        <a href="gallery.php<?= $drawerParam ?>" class="btn btn-secondary btn-sm">← Back to Albums</a>
    </div>
    <div class="panel-body">
        <form method="POST" action="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'gallery.php') ?>">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="save_album">
            <input type="hidden" name="id" value="<?= $editAlbum ? (int)$editAlbum['id'] : 0 ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="slug">Folder / Identifier Slug *</label>
                    <input type="text" id="slug" name="slug" class="form-control" required value="<?= htmlspecialchars($editAlbum['slug'] ?? '') ?>" placeholder="e.g. freshers_party, aavishkar_event">
                </div>

                <div class="form-group">
                    <label class="form-label" for="title">Album Title *</label>
                    <input type="text" id="title" name="title" class="form-control" required value="<?= htmlspecialchars($editAlbum['title'] ?? '') ?>" placeholder="e.g. Freshers Welcome Celebration">
                </div>

                <div class="form-group">
                    <label class="form-label" for="category">Category Filter Key *</label>
                    <select id="category" name="category" class="form-control">
                        <option value="events" <?= ($editAlbum['category'] ?? '') === 'events' ? 'selected' : '' ?>>Events</option>
                        <option value="campus" <?= ($editAlbum['category'] ?? '') === 'campus' ? 'selected' : '' ?>>Campus &amp; Facilities</option>
                        <option value="cultural" <?= ($editAlbum['category'] ?? '') === 'cultural' ? 'selected' : '' ?>>Cultural &amp; Celebrations</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="category_label">Category Display Label</label>
                    <input type="text" id="category_label" name="category_label" class="form-control" value="<?= htmlspecialchars($editAlbum['category_label'] ?? '') ?>" placeholder="e.g. Cultural &amp; Social">
                </div>

                <div class="form-group">
                    <label class="form-label" for="sort_order">Display Order</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= htmlspecialchars($editAlbum['sort_order'] ?? '1') ?>">
                </div>

                <div class="form-group full-width">
                    <label class="form-label" for="description">Album Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3"><?= htmlspecialchars($editAlbum['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div style="margin-top: 20px; display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">Save Album</button>
                <a href="gallery.php<?= $drawerParam ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<!-- All Albums Grid / Table -->
<div class="panel">
    <div class="panel-header">
        <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
            <div class="panel-title">Gallery Albums (<?= count($albums) ?>)</div>
            <input type="text" class="form-control" data-table-search="albumsTable" placeholder="Search albums..." style="max-width: 320px; font-size: 13px;">
        </div>
        <a href="gallery.php?action=create_album<?= $drawerUrlSuffix ?>" 
           data-drawer-url="gallery.php?action=create_album<?= $drawerUrlSuffix ?>" 
           data-drawer-title="Create New Album" 
           class="btn btn-primary btn-sm">
            <?= icon('plus', 14) ?>
            Create New Album
        </a>
    </div>

    <div class="panel-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table" id="albumsTable" data-reorder="gallery_albums">
                <thead>
                    <tr>
                        <th class="reorder-col" style="width: 48px;"></th>
                        <th style="width: 50px;">Order</th>
                        <th>Cover</th>
                        <th>Album Title</th>
                        <th>Category</th>
                        <th>Total Photos</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($albums)): ?>
                        <tr><td colspan="7" class="empty-cell">No albums yet. Click <strong>Create New Album</strong> to build your first gallery.</td></tr>
                    <?php else: ?>
                        <?php foreach ($albums as $a): ?>
                        <tr data-reorder-id="<?= (int)$a['id'] ?>">
                            <td class="reorder-col">
                                <button type="button" class="sortable-handle" aria-label="Drag to reorder" title="Drag to reorder">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="6" r="1.5"></circle><circle cx="15" cy="6" r="1.5"></circle><circle cx="9" cy="12" r="1.5"></circle><circle cx="15" cy="12" r="1.5"></circle><circle cx="9" cy="18" r="1.5"></circle><circle cx="15" cy="18" r="1.5"></circle></svg>
                                </button>
                            </td>
                            <td><span class="badge badge-secondary order-badge">#<?= (int)$a['sort_order'] ?></span></td>
                            <td>
                                <img src="../<?= htmlspecialchars($a['cover_image'] ?: 'assets/logo.ico') ?>" alt="" class="preview-thumbnail" onerror="this.src='../assets/logo.ico'">
                            </td>
                            <td>
                                <strong style="color: var(--text-main); font-size: 13.5px;"><?= htmlspecialchars($a['title']) ?></strong>
                                <div style="font-size: 11.5px; color: var(--text-muted);"><?= htmlspecialchars(mb_strimwidth($a['description'], 0, 70, '...')) ?></div>
                            </td>
                            <td>
                                <span class="badge badge-info"><?= htmlspecialchars($a['category_label'] ?: $a['category']) ?></span>
                            </td>
                            <td>
                                <span class="badge badge-primary"><?= (int)$a['total_photos'] ?> Photos</span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="gallery.php?manage_photos=<?= (int)$a['id'] ?>" target="_top" class="btn btn-primary btn-sm">
                                        <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                                        Photos
                                    </a>
                                    <a href="gallery.php?edit_album=<?= (int)$a['id'] ?><?= $drawerUrlSuffix ?>" 
                                       data-drawer-url="gallery.php?edit_album=<?= (int)$a['id'] ?><?= $drawerUrlSuffix ?>" 
                                       data-drawer-title="Edit Album Details" 
                                       class="btn btn-secondary btn-sm">Edit</a>
                                    <form method="POST" action="gallery.php<?= $drawerParam ?>" style="display: inline;" id="delete-album-<?= (int)$a['id'] ?>">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="delete_album">
                                        <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                data-confirm="Delete this entire album and its photos?" 
                                                data-confirm-form="#delete-album-<?= (int)$a['id'] ?>">
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
