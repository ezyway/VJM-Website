<?php
/**
 * Gallery & Media Albums Management Module
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/crud.php';
requireAuth();

$db = getDB();
$isDrawerMode = isset($_GET['drawer']) && $_GET['drawer'] === '1';

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    crudCsrfGuard('gallery.php');

    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $albumId = (int)($_POST['id'] ?? 0);
        // Collect all photo + cover files on disk before deleting rows
        $srcDir = dirname(__DIR__) . '/';
        $photos = $db->prepare('SELECT image_path FROM gallery_photos WHERE album_id = :id');
        $photos->execute([':id' => $albumId]);
        $paths = $photos->fetchAll(PDO::FETCH_COLUMN);
        $cov = $db->prepare('SELECT cover_image FROM gallery_albums WHERE id = :id');
        $cov->execute([':id' => $albumId]);
        $cover = $cov->fetchColumn() ?: '';
        // Delete photo rows + album row first
        $db->prepare('DELETE FROM gallery_photos WHERE album_id = :id')->execute([':id' => $albumId]);
        logAdminActivity('delete', 'gallery_albums', $albumId, "Deleted album #{$albumId}");
        $db->prepare('DELETE FROM gallery_albums WHERE id = :id')->execute([':id' => $albumId]);
        setFlash('success', 'Album and its photos deleted successfully.');
        // Delete files from disk
        $all = array_merge($paths, $cover ? [$cover] : []);
        foreach (array_unique($all) as $p) {
            $full = $srcDir . $p;
            if (file_exists($full)) @unlink($full);
            if (file_exists($full . '.webp')) @unlink($full . '.webp');
        }
        crudRedirect('gallery.php', $isDrawerMode);
    }

    if ($action === 'delete_photo') {
        $photoId = (int)($_POST['photo_id'] ?? 0);
        $albumId = (int)($_POST['album_id'] ?? 0);
        // Fetch the image path before deleting the DB record
        $stmtPath = $db->prepare('SELECT image_path FROM gallery_photos WHERE id = :id');
        $stmtPath->execute([':id' => $photoId]);
        $photoPath = $stmtPath->fetchColumn();
        $stmt = $db->prepare('DELETE FROM gallery_photos WHERE id = :id');
        $stmt->execute([':id' => $photoId]);
        // Delete the physical file from disk
        if ($photoPath) {
            $fullPath = dirname(__DIR__) . '/' . $photoPath;
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }
        setFlash($isDrawerMode ? 'info' : 'success', 'Photo removed from album.');
        header("Location: gallery.php?edit={$albumId}" . ($isDrawerMode ? '&drawer=1' : ''));
        exit;
    }

    if ($action === 'set_cover') {
        $albumId   = (int)($_POST['album_id'] ?? 0);
        $imagePath = trim($_POST['image_path'] ?? '');
        if ($albumId > 0 && !empty($imagePath)) {
            $stmt = $db->prepare('UPDATE gallery_albums SET cover_image = :cov WHERE id = :id');
            $stmt->execute([':cov' => $imagePath, ':id' => $albumId]);
            setFlash($isDrawerMode ? 'info' : 'success', 'Album cover photo updated.');
        }
        header("Location: gallery.php?edit={$albumId}" . ($isDrawerMode ? '&drawer=1' : ''));
        exit;
    }

    if ($action === 'update_photo') {
        $photoId = (int)($_POST['photo_id'] ?? 0);
        $albumId = (int)($_POST['album_id'] ?? 0);
        $caption = trim($_POST['caption'] ?? '');
        if ($photoId > 0) {
            $stmt = $db->prepare('UPDATE gallery_photos SET caption = :cap WHERE id = :id');
            $stmt->execute([':cap' => $caption, ':id' => $photoId]);
            setFlash($isDrawerMode ? 'info' : 'success', 'Caption updated.');
        }
        header("Location: gallery.php?edit={$albumId}" . ($isDrawerMode ? '&drawer=1' : ''));
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

        setFlash($isDrawerMode ? 'info' : 'success', "Uploaded {$uploadedCount} photo(s) into album.");
        header("Location: gallery.php?edit={$albumId}" . ($isDrawerMode ? '&drawer=1' : ''));
        exit;
    }

    if ($action === 'save') {
        $id             = (int)($_POST['id'] ?? 0);
        $slug           = strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', $_POST['slug'] ?? '')));
        $title          = trim($_POST['title'] ?? '');
        $category       = trim($_POST['category'] ?? 'events');
        $category_label = trim($_POST['category_label'] ?? '');
        $description    = trim($_POST['description'] ?? '');

        if (empty($slug) || empty($title)) {
            crudFormFail('Album title and slug are required.');
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE gallery_albums SET slug = :s, title = :t, category = :cat, category_label = :cl, description = :d WHERE id = :id');
            $stmt->execute([':s' => $slug, ':t' => $title, ':cat' => $category, ':cl' => $category_label, ':d' => $description, ':id' => $id]);
            setFlash('success', 'Album details updated.');
            header('Location: ' . ($isDrawerMode ? $_SERVER['REQUEST_URI'] : 'gallery.php'));
        } else {
            $stmt = $db->prepare('INSERT INTO gallery_albums (slug, title, category, category_label, description, cover_image, sort_order) VALUES (:s, :t, :cat, :cl, :d, "", (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM gallery_albums))');
            $stmt->execute([':s' => $slug, ':t' => $title, ':cat' => $category, ':cl' => $category_label, ':d' => $description]);
            $newId = (int)$db->lastInsertId();
            // 'info' keeps the drawer open; 'success' is the normal list-page toast.
            setFlash($isDrawerMode ? 'info' : 'success', 'New gallery album created. You can now upload photos to it.');
            // Land on the edit view so the user can upload photos immediately.
            header("Location: gallery.php?edit={$newId}" . ($isDrawerMode ? '&drawer=1' : ''));
        }
        exit;
    }
}

$pageTitle = 'Photo Gallery & Albums';
require_once __DIR__ . '/includes/header.php';

// Manage Photos view is folded into the Edit Album view; redirect old links.
if (isset($_GET['manage_photos'])) {
    $mId = (int)$_GET['manage_photos'];
    header('Location: gallery.php?edit=' . $mId . ($isDrawerMode ? '&drawer=1' : ''));
    exit;
}

$editAlbum = crudLoadItem($db, 'gallery_albums');
$isCreate = crudIsCreate();

// Load photos for the album being edited so they can be managed inline.
$editAlbumPhotos = [];
if ($editAlbum) {
    $stmtP = $db->prepare('SELECT * FROM gallery_photos WHERE album_id = :aid ORDER BY sort_order ASC, id ASC');
    $stmtP->execute([':aid' => $editAlbum['id']]);
    $editAlbumPhotos = $stmtP->fetchAll();
}

$albums = $db->query('
    SELECT a.*, COUNT(p.id) AS total_photos
    FROM gallery_albums a
    LEFT JOIN gallery_photos p ON a.id = p.album_id
    GROUP BY a.id
    ORDER BY a.sort_order ASC, a.id ASC
')->fetchAll();
?>

<?php if ($editAlbum || $isCreate): ?>
<?php
crudFormPanel([
    'page'        => 'gallery.php',
    'pageParam'   => $drawerParam,
    'item'        => $editAlbum,
    'creating'    => $isCreate,
    'title'       => fn($item) => $item && isset($item['title']) ? 'Edit Album Details' : 'Create New Gallery Album',
    'submitLabel' => 'Save Album',
    'previewType' => 'none',
    'hideActions' => true,
    'fields'      => [
        ['name' => 'slug', 'label' => 'Folder / Identifier Slug *', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. freshers_party, aavishkar_event'],
        ['name' => 'title', 'label' => 'Album Title *', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. Freshers Welcome Celebration', 'hintHtml' => '<span class="form-hint">The slug above is auto-detected from this title.</span>'],
        ['name' => 'category', 'label' => 'Category Filter Key *', 'type' => 'select', 'default' => 'events', 'options' => ['events' => 'Events', 'campus' => 'Campus & Facilities', 'cultural' => 'Cultural & Celebrations']],
        ['name' => 'category_label', 'label' => 'Category Display Label', 'type' => 'text', 'placeholder' => 'e.g. Cultural & Social'],
        ['name' => 'description', 'label' => 'Album Description', 'type' => 'textarea', 'full' => true, 'rows' => 3],
    ],
]);
?>

<?php if ($editAlbum): ?>
<div class="panel" style="margin-top: 20px;">
    <div class="panel-header">
        <div class="panel-title">Photos (<?= count($editAlbumPhotos) ?>)</div>
    </div>
    <div class="panel-body">
        <form method="POST" action="gallery.php<?= $drawerParam ?>" enctype="multipart/form-data" style="margin-bottom: 24px; background: rgba(255,255,255,0.02); padding: 18px; border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="upload_photos">
            <input type="hidden" name="album_id" value="<?= (int)$editAlbum['id'] ?>">
            <div style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
                <div class="form-group" style="flex: 1; min-width: 240px; margin-bottom: 0;">
                    <label class="form-label">Upload New Photos (Select Multiple)</label>
                    <input type="file" name="photos[]" multiple class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary"><?= icon('upload', 16) ?> Upload</button>
            </div>
        </form>

        <?php if (empty($editAlbumPhotos)): ?>
            <div style="text-align: center; color: var(--text-muted); padding: 30px 20px;">
                No photos yet. Use the upload field above to add images to this album.
            </div>
        <?php else: ?>
            <div class="gallery-admin-grid">
                <?php foreach ($editAlbumPhotos as $ph):
                    $isCover = ($ph['image_path'] === $editAlbum['cover_image']);
                    $caption = htmlspecialchars($ph['caption'] ?? '');
                ?>
                <div class="gallery-admin-item" style="<?= $isCover ? 'border: 2px solid #3b82f6;' : '' ?>">
                    <div class="gci-img">
                        <img src="../<?= htmlspecialchars($ph['image_path']) ?>" alt="<?= $caption ?>" loading="lazy">
                        <?php if ($isCover): ?>
                            <div style="position: absolute; top: 6px; left: 6px; background: #3b82f6; color: white; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px;">COVER</div>
                        <?php endif; ?>
                        <div class="gallery-item-overlay">
                            <?php if (!$isCover): ?>
                            <form method="POST" action="gallery.php<?= $drawerParam ?>" style="display: inline;">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="set_cover">
                                <input type="hidden" name="album_id" value="<?= (int)$editAlbum['id'] ?>">
                                <input type="hidden" name="image_path" value="<?= htmlspecialchars($ph['image_path']) ?>">
                                <button type="submit" class="btn btn-secondary btn-sm" title="Set as Album Cover">Cover</button>
                            </form>
                            <?php endif; ?>
                            <form method="POST" action="gallery.php<?= $drawerParam ?>" style="display: inline;" id="delete-photo-<?= (int)$ph['id'] ?>">
                                <?= csrfField() ?>
                                <input type="hidden" name="action" value="delete_photo">
                                <input type="hidden" name="photo_id" value="<?= (int)$ph['id'] ?>">
                                <input type="hidden" name="album_id" value="<?= (int)$editAlbum['id'] ?>">
                                <button type="button" class="btn btn-danger btn-sm" title="Delete Photo" data-confirm="Remove this photo from the album?" data-confirm-form="#delete-photo-<?= (int)$ph['id'] ?>">✕</button>
                            </form>
                        </div>
                    </div>
                    <form class="gci-caption" method="POST" action="gallery.php<?= $drawerParam ?>">
                        <?= csrfField() ?>
                        <input type="hidden" name="action" value="update_photo">
                        <input type="hidden" name="photo_id" value="<?= (int)$ph['id'] ?>">
                        <input type="hidden" name="album_id" value="<?= (int)$editAlbum['id'] ?>">
                        <input type="text" name="caption" class="form-control" value="<?= $caption ?>" placeholder="Add caption (Enter to save)..." title="Caption">
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<script>
(function () {
    // Album slug auto-detect + save button placement note handled above.
    var titleEl = document.getElementById('title');
    var slugEl = document.getElementById('slug');
    if (titleEl && slugEl) {
        var touched = slugEl.value !== '';
        slugEl.addEventListener('input', function () { touched = true; });
        titleEl.addEventListener('input', function () {
            if (touched) return;
            slugEl.value = titleEl.value.toLowerCase().trim()
                .replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
        });
    }
    // Captions auto-save on Enter or when the field loses focus after edits.
    document.querySelectorAll('.gci-caption input[name="caption"]').forEach(function (inp) {
        inp.dataset.orig = inp.value;
        inp.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); if (inp.value !== inp.dataset.orig) inp.form.submit(); }
        });
        inp.addEventListener('blur', function () {
            if (inp.value !== inp.dataset.orig) inp.form.submit();
        });
    });
})();
</script>
<?php if ($editAlbum || $isCreate): ?>
<div style="margin-top: 22px; display: flex; gap: 12px; align-items: center;" class="form-actions-sticky">
    <button type="submit" form="crudAdminForm" class="btn btn-primary">Save Album</button>
    <a href="gallery.php<?= $drawerParam ?>" class="btn btn-secondary">Cancel</a>
</div>
<?php endif; ?>
<?php else: ?>
<?php
crudListPanel([
    'page'              => 'gallery.php',
    'pageParam'         => $drawerParam,
    'suffix'            => $drawerUrlSuffix,
    'listTitleHtml'     => 'Gallery Albums (' . count($albums) . ')',
    'rows'              => $albums,
    'tableId'           => 'albumsTable',
    'reorder'           => 'gallery_albums',
    'searchPlaceholder' => 'Search albums...',
    'emptyText'         => 'No albums yet. Click <strong>Create New Album</strong> to build your first gallery.',
    'add'               => ['url' => 'gallery.php?action=create', 'drawerTitle' => 'Create New Album', 'label' => 'Create New Album'],
    'columns'           => [
        ['th' => 'Cover', 'td' => fn($a) => '<img src="../' . htmlspecialchars($a['cover_image'] ?: 'assets/logo.ico') . '" alt="" class="preview-thumbnail" loading="lazy" onerror="this.src=\'../assets/logo.ico\'">'],
        ['th' => 'Album Title', 'td' => fn($a) => '<strong style="color: var(--text-main); font-size: 13.5px;">' . htmlspecialchars($a['title']) . '</strong><div style="font-size: 11.5px; color: var(--text-muted);">' . htmlspecialchars(mb_strimwidth($a['description'], 0, 70, '...')) . '</div>'],
        ['th' => 'Category', 'td' => fn($a) => badge(htmlspecialchars($a['category_label'] ?: $a['category']), 'info')],
        ['th' => 'Total Photos', 'td' => fn($a) => '<span class="badge badge-primary">' . (int)$a['total_photos'] . ' Photos</span>'],
    ],
    'editUrl'           => fn($a) => 'gallery.php?edit=' . $a['id'],
    'actions'           => ['edit' => ['drawerTitle' => 'Edit Album & Photos'], 'delete' => true],
    'deleteConfirm'     => fn() => 'Delete this entire album and its photos?',
    'formPrefix'        => 'album',
]);
?>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
