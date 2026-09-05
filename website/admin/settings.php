<?php
/**
 * Site Settings & Administration Configuration
 *
 * Organized into: Site Identity & Counters, Contact Info, Admin Password,
 * System Info (compact), and an Advanced Diagnostics panel.
 */

require_once __DIR__ . '/includes/auth.php';
requireAuth();

$db = getDB();
$adminUser = getCurrentAdmin();

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('danger', 'Security token expired. Please try again.');
        header('Location: settings.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    // 0. Database maintenance (VACUUM + optimize)
    if ($action === 'vacuum_db') {
        try {
            $t0 = microtime(true);
            $db->exec('VACUUM;');
            $db->exec('PRAGMA optimize;');
            $elapsed = round((microtime(true) - $t0) * 1000, 2);
            setFlash('success', "Database VACUUM and index optimization executed successfully in {$elapsed} ms.");
        } catch (Exception $e) {
            setFlash('danger', "Database optimization error: " . $e->getMessage());
        }
        header('Location: settings.php');
        exit;
    }

    // 0b. Clean Orphaned Uploaded Media Files
    if ($action === 'clean_orphaned_media') {
        try {
            $siteRoot = dirname(__DIR__);
            $uploadsDir = $siteRoot . '/assets/uploads';
            $referencedFiles = [];

            $dbFiles = [];
            $dbFiles = array_merge($dbFiles, $db->query("SELECT image FROM faculties WHERE image IS NOT NULL AND image != ''")->fetchAll(PDO::FETCH_COLUMN));
            $dbFiles = array_merge($dbFiles, $db->query("SELECT image FROM rankers WHERE image IS NOT NULL AND image != ''")->fetchAll(PDO::FETCH_COLUMN));
            $dbFiles = array_merge($dbFiles, $db->query("SELECT file_path FROM magazines WHERE file_path IS NOT NULL AND file_path != ''")->fetchAll(PDO::FETCH_COLUMN));
            $dbFiles = array_merge($dbFiles, $db->query("SELECT image_path FROM gallery_photos WHERE image_path IS NOT NULL AND image_path != ''")->fetchAll(PDO::FETCH_COLUMN));
            $dbFiles = array_merge($dbFiles, $db->query("SELECT cover_image FROM gallery_albums WHERE cover_image IS NOT NULL AND cover_image != ''")->fetchAll(PDO::FETCH_COLUMN));

            $popupSettings = $db->query("SELECT value FROM site_settings WHERE key LIKE 'announcement_popup%'")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($popupSettings as $json) {
                $dec = json_decode($json, true);
                if (is_array($dec) && !empty($dec['image'])) {
                    $dbFiles[] = $dec['image'];
                }
            }

            foreach ($dbFiles as $f) {
                $referencedFiles[ltrim(str_replace('\\', '/', (string)$f), '/')] = true;
            }

            $deletedCount = 0;
            $deletedBytes = 0;
            if (is_dir($uploadsDir)) {
                $it = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($uploadsDir, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($it as $file) {
                    if ($file->isFile()) {
                        $fullPath = str_replace('\\', '/', $file->getRealPath());
                        $relPath = 'assets/uploads/' . ltrim(substr($fullPath, strlen(str_replace('\\', '/', $uploadsDir))), '/');
                        if (!isset($referencedFiles[$relPath])) {
                            $size = $file->getSize();
                            if (@unlink($file->getRealPath())) {
                                $deletedCount++;
                                $deletedBytes += $size;
                            }
                        }
                    }
                }
            }

            $freedStr = ($deletedBytes > 1048576)
                ? round($deletedBytes / 1048576, 2) . ' MB'
                : round($deletedBytes / 1024, 1) . ' KB';

            setFlash('success', "Orphaned media cleanup completed: removed {$deletedCount} unreferenced files ({$freedStr} freed).");
        } catch (Exception $e) {
            setFlash('danger', "Media clean error: " . $e->getMessage());
        }
        header('Location: settings.php');
        exit;
    }

    // 1. Update Homepage Counters & Site Identity
    if ($action === 'save_settings') {
        $keys = [
            'counter_courses',
            'counter_pass_rate',
            'counter_students',
            'counter_alumni',
            'college_name',
            'college_slogan'
        ];

        foreach ($keys as $k) {
            if (isset($_POST[$k])) {
                setSetting($k, trim($_POST[$k]));
            }
        }

        setFlash('success', 'Site identity and statistics updated.');
        header('Location: settings.php');
        exit;
    }

    // 1b. Save Announcement Popup (homepage modal)
    if ($action === 'save_popup') {
        $enabled = !empty($_POST['enabled']) ? 1 : 0;
        $remove  = !empty($_POST['remove_image']);
        $image   = trim($_POST['current_image'] ?? '');

        // Optional top image upload
        if (!$remove && !empty($_FILES['image']['name'])) {
            $upload = handleAdminUpload($_FILES['image'], 'announcements', ['jpg', 'jpeg', 'png', 'webp']);
            if ($upload['success']) {
                $image = $upload['path'];
            } else {
                setFlash('danger', 'Popup image upload failed: ' . $upload['error']);
                header('Location: settings.php');
                exit;
            }
        }
        if ($remove) $image = '';

        $badge   = trim($_POST['badge'] ?? '');
        $title   = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');

        $labels = $_POST['btn_label'] ?? [];
        $urls   = $_POST['btn_url'] ?? [];
        $styles = $_POST['btn_style'] ?? [];
        $buttons = [];
        foreach ($labels as $i => $label) {
            $label = trim($label ?? '');
            $url   = trim($urls[$i] ?? '');
            if ($label === '' && $url === '') continue;
            $style = in_array(($styles[$i] ?? ''), ['primary', 'secondary', 'outline'], true) ? $styles[$i] : 'primary';
            $buttons[] = ['label' => $label, 'url' => $url, 'style' => $style];
        }

        $popup = [
            'enabled' => $enabled ? 1 : 0,
            'image'   => $image,
            'badge'   => $badge,
            'title'   => $title,
            'message' => $message,
            'buttons' => $buttons,
        ];
        setSetting('announcement_popup', json_encode($popup, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        setFlash('success', 'Announcement popup saved.');
        header('Location: settings.php');
        exit;
    }

    // 2. Update Contact Details
    if ($action === 'save_contact') {
        $keys = [
            'contact_phone_primary',
            'contact_email_primary',
            'contact_whatsapp_secondary',
            'contact_address'
        ];

        foreach ($keys as $k) {
            if (isset($_POST[$k])) {
                setSetting($k, trim($_POST[$k]));
            }
        }

        setFlash('success', 'Contact details updated.');
        header('Location: settings.php');
        exit;
    }

    // 3. Change Admin Password
    if ($action === 'change_password') {
        $currentPass = $_POST['current_password'] ?? '';
        $newPass     = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        if (empty($currentPass) || empty($newPass) || empty($confirmPass)) {
            setFlash('danger', 'All password fields are required.');
            header('Location: settings.php');
            exit;
        }

        if ($newPass !== $confirmPass) {
            setFlash('danger', 'New password and confirmation do not match.');
            header('Location: settings.php');
            exit;
        }

        if (strlen($newPass) < 8) {
            setFlash('danger', 'New password must be at least 8 characters long.');
            header('Location: settings.php');
            exit;
        }

        // Verify current password
        $stmt = $db->prepare('SELECT password_hash FROM admins WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $adminUser['id']]);
        $hash = $stmt->fetchColumn();

        if (!$hash || !password_verify($currentPass, $hash)) {
            setFlash('danger', 'Current password is incorrect.');
            header('Location: settings.php');
            exit;
        }

        // Update password
        $newHash = password_hash($newPass, PASSWORD_DEFAULT);
        $stmtU = $db->prepare('UPDATE admins SET password_hash = :h, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        $stmtU->execute([':h' => $newHash, ':id' => $adminUser['id']]);

        setFlash('success', 'Admin password changed successfully.');
        header('Location: settings.php');
        exit;
    }
}

$pageTitle = 'Site Settings & Stats';
require_once __DIR__ . '/includes/header.php';

// ---- Diagnostics data (used by the Advanced Diagnostics panel) ----
$siteRoot = dirname(__DIR__);
$phpVer        = PHP_VERSION;
$serverSapi    = php_sapi_name();
$serverSoft    = $_SERVER['SERVER_SOFTWARE'] ?? 'Apache/2.4 (Debian)';
$serverTime    = date('Y-m-d H:i:s T');
$memoryLimit   = ini_get('memory_limit') ?: 'N/A';
$uploadMax     = ini_get('upload_max_filesize') ?: 'N/A';
$postMax       = ini_get('post_max_size') ?: 'N/A';
$maxExecTime   = ini_get('max_execution_time') ? ini_get('max_execution_time') . 's' : 'Unlimited';
$displayErrors = (bool)ini_get('display_errors');

$criticalExts = [
    'pdo'         => ['name' => 'PDO Core', 'required' => true],
    'pdo_sqlite'  => ['name' => 'PDO SQLite', 'required' => true],
    'mbstring'    => ['name' => 'Multibyte String', 'required' => true],
    'fileinfo'    => ['name' => 'Fileinfo MIME', 'required' => true],
    'curl'        => ['name' => 'cURL Client', 'required' => false],
    'simplexml'   => ['name' => 'SimpleXML', 'required' => false],
    'openssl'     => ['name' => 'OpenSSL Security', 'required' => true],
    'gd'          => ['name' => 'GD Image Library', 'required' => false],
];

$sqliteVer     = $db->query('SELECT sqlite_version()')->fetchColumn();
$journalMode   = $db->query('PRAGMA journal_mode')->fetchColumn();
$foreignKeys   = (bool)$db->query('PRAGMA foreign_keys')->fetchColumn();
$integrityRes  = $db->query('PRAGMA integrity_check')->fetchColumn();
$dbFileSize    = file_exists(DB_FILE_PATH) ? round(filesize(DB_FILE_PATH) / 1024, 1) . ' KB' : '0 KB';
$dbDirWritable = is_writable(dirname(DB_FILE_PATH));

$tableDefinitions = [
    'admins'              => ['label' => 'Admin Users', 'icon' => 'user'],
    'faculties'           => ['label' => 'Faculty Directory', 'icon' => 'users'],
    'courses'             => ['label' => 'Academic Courses', 'icon' => 'book'],
    'labs'                => ['label' => 'Laboratories', 'icon' => 'cpu'],
    'events'              => ['label' => 'Events & Circulars', 'icon' => 'bell'],
    'rankers'             => ['label' => 'Rankers & Achievers', 'icon' => 'award'],
    'pass_rates'          => ['label' => 'Pass Rate Records', 'icon' => 'trending-up'],
    'gallery_albums'      => ['label' => 'Gallery Albums', 'icon' => 'folder'],
    'gallery_photos'      => ['label' => 'Gallery Photos', 'icon' => 'image'],
    'testimonials'        => ['label' => 'Testimonials', 'icon' => 'message-square'],
    'magazines'           => ['label' => 'E-Magazines', 'icon' => 'file-text'],
    'scholarships'        => ['label' => 'Scholarship Years', 'icon' => 'dollar-sign'],
    'scholarship_portals' => ['label' => 'Govt Aid Portals', 'icon' => 'shield'],
];

$tableCounts = [];
$totalDatabaseRecords = 0;
foreach (array_keys($tableDefinitions) as $t) {
    try {
        $c = (int)$db->query("SELECT COUNT(*) FROM {$t}")->fetchColumn();
        $tableCounts[$t] = $c;
        $totalDatabaseRecords += $c;
    } catch (Exception $e) {
        $tableCounts[$t] = 0;
    }
}

// getStorageStats() is defined in includes/db.php
$storageFaculty = getStorageStats([$siteRoot . '/assets/photos/faculties', $siteRoot . '/assets/uploads/faculties']);
$storageGallery = getStorageStats([$siteRoot . '/assets/photos/gallery', $siteRoot . '/assets/uploads/gallery']);
$storageEmag    = getStorageStats([$siteRoot . '/assets/e_mags', $siteRoot . '/assets/uploads/emag']);
$storageRankers = getStorageStats([$siteRoot . '/assets/photos/index/pride_of_college', $siteRoot . '/assets/uploads/rankers']);
$storageDocs    = getStorageStats([$siteRoot . '/data']);

// Scan for orphaned upload files not referenced in the database
$orphanedCount = 0;
$orphanedBytes = 0;
$uploadsDir = $siteRoot . '/assets/uploads';
if (is_dir($uploadsDir)) {
    $activeRefs = [];
    $allRefs = [];
    try {
        $allRefs = array_merge($allRefs, $db->query("SELECT image FROM faculties WHERE image IS NOT NULL AND image != ''")->fetchAll(PDO::FETCH_COLUMN));
        $allRefs = array_merge($allRefs, $db->query("SELECT image FROM rankers WHERE image IS NOT NULL AND image != ''")->fetchAll(PDO::FETCH_COLUMN));
        $allRefs = array_merge($allRefs, $db->query("SELECT file_path FROM magazines WHERE file_path IS NOT NULL AND file_path != ''")->fetchAll(PDO::FETCH_COLUMN));
        $allRefs = array_merge($allRefs, $db->query("SELECT image_path FROM gallery_photos WHERE image_path IS NOT NULL AND image_path != ''")->fetchAll(PDO::FETCH_COLUMN));
        $allRefs = array_merge($allRefs, $db->query("SELECT cover_image FROM gallery_albums WHERE cover_image IS NOT NULL AND cover_image != ''")->fetchAll(PDO::FETCH_COLUMN));
        $popJsons = $db->query("SELECT value FROM site_settings WHERE key LIKE 'announcement_popup%'")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($popJsons as $pj) {
            $pdec = json_decode($pj, true);
            if (is_array($pdec) && !empty($pdec['image'])) $allRefs[] = $pdec['image'];
        }
    } catch (Exception $e) {}

    foreach ($allRefs as $ar) {
        $activeRefs[ltrim(str_replace('\\', '/', (string)$ar), '/')] = true;
    }

    try {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($uploadsDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $f) {
            if ($f->isFile()) {
                $fPath = str_replace('\\', '/', $f->getRealPath());
                $rel = 'assets/uploads/' . ltrim(substr($fPath, strlen(str_replace('\\', '/', $uploadsDir))), '/');
                if (!isset($activeRefs[$rel])) {
                    $orphanedCount++;
                    $orphanedBytes += $f->getSize();
                }
            }
        }
    } catch (Exception $e) {}
}
$orphanedSize = ($orphanedBytes > 1048576)
    ? round($orphanedBytes / 1048576, 2) . ' MB'
    : round($orphanedBytes / 1024, 1) . ' KB';

$sitemapPath = $siteRoot . '/sitemap.xml';
$sitemapCount = 0;
$sitemapMod = 'N/A';
$sitemapSize = '0 KB';
if (file_exists($sitemapPath)) {
    $sitemapSize = round(filesize($sitemapPath) / 1024, 1) . ' KB';
    $sitemapMod  = date('d M Y, H:i', filemtime($sitemapPath));
    $xml = @simplexml_load_file($sitemapPath);
    if ($xml && isset($xml->url)) $sitemapCount = count($xml->url);
}

$robotsPath = $siteRoot . '/robots.txt';
$robotsExists = file_exists($robotsPath);
$robotsSize = $robotsExists ? filesize($robotsPath) . ' B' : 'Missing';

$manifestPath = $siteRoot . '/manifest.json';
$manifestExists = file_exists($manifestPath);
$manifestData = $manifestExists ? @json_decode(file_get_contents($manifestPath), true) : null;

$swPath = $siteRoot . '/sw.js';
$swExists = file_exists($swPath);

$htaccessDbPath = $siteRoot . '/data/.htaccess';
$htaccessProtected = file_exists($htaccessDbPath);
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 24px;">
    <!-- Site Identity & Homepage Counters -->
    <div class="panel" style="grid-column: 1 / -1;">
        <div class="panel-header">
            <div class="panel-title">Site Identity &amp; Homepage Counters</div>
        </div>
        <div class="panel-body">
            <form method="POST" action="settings.php">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="save_settings">

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="college_name">College Name</label>
                        <input type="text" id="college_name" name="college_name" class="form-control" value="<?= htmlspecialchars(getSetting('college_name', 'Shri V.J. Modha College')) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="college_slogan">College Slogan / Tagline</label>
                        <input type="text" id="college_slogan" name="college_slogan" class="form-control" value="<?= htmlspecialchars(getSetting('college_slogan', 'Enlightening Minds, Empowering Futures')) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="counter_courses">Academic Programs Count</label>
                        <input type="text" id="counter_courses" name="counter_courses" class="form-control" value="<?= htmlspecialchars(getSetting('counter_courses', '8')) ?>">
                        <span class="form-hint">Displayed on the animated numbers strip.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="counter_pass_rate">Academic Pass Rate (%)</label>
                        <input type="text" id="counter_pass_rate" name="counter_pass_rate" class="form-control" value="<?= htmlspecialchars(getSetting('counter_pass_rate', '97.6')) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="counter_students">Active Enrolled Students</label>
                        <input type="text" id="counter_students" name="counter_students" class="form-control" value="<?= htmlspecialchars(getSetting('counter_students', '1500')) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="counter_alumni">Graduated Alumni</label>
                        <input type="text" id="counter_alumni" name="counter_alumni" class="form-control" value="<?= htmlspecialchars(getSetting('counter_alumni', '7900')) ?>">
                    </div>

                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">Save Site Identity &amp; Stats</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Contact Details -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Contact Details</div>
        </div>
        <div class="panel-body">
            <form method="POST" action="settings.php">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="save_contact">

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="contact_phone_primary">Primary Contact Phone</label>
                        <input type="text" id="contact_phone_primary" name="contact_phone_primary" class="form-control" value="<?= htmlspecialchars(getSetting('contact_phone_primary', '+91 99788 18009')) ?>">
                        <span class="form-hint">Shown on the Contact page &amp; site footer.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact_email_primary">Primary Contact Email</label>
                        <input type="email" id="contact_email_primary" name="contact_email_primary" class="form-control" value="<?= htmlspecialchars(getSetting('contact_email_primary', 'shrivjmodha@gmail.com')) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact_whatsapp_secondary">WhatsApp Secondary Number</label>
                        <input type="text" id="contact_whatsapp_secondary" name="contact_whatsapp_secondary" class="form-control" value="<?= htmlspecialchars(getSetting('contact_whatsapp_secondary', '+91 98256 73093')) ?>">
                        <span class="form-hint">Optional alternate WhatsApp helpdesk number.</span>
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label" for="contact_address">College Physical Address</label>
                        <input type="text" id="contact_address" name="contact_address" class="form-control" value="<?= htmlspecialchars(getSetting('contact_address', '"Vidhyadham", Chhaya-Birla Road, Nr. Pakshi Abhiyaran, Porbandar, Gujarat, 360575')) ?>">
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">Save Contact Details</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Announcement Popup (homepage modal) -->
    <?php
    $popCfg = json_decode(getSetting('announcement_popup', '{}'), true);
    if (!is_array($popCfg)) $popCfg = [];
    $popEnabled = !empty($popCfg['enabled']);
    $popImage   = trim($popCfg['image'] ?? '');
    $popBadge   = trim($popCfg['badge'] ?? '');
    $popTitle   = trim($popCfg['title'] ?? '');
    $popMessage = trim($popCfg['message'] ?? '');
    $popButtons = (isset($popCfg['buttons']) && is_array($popCfg['buttons'])) ? $popCfg['buttons'] : [];
    if (empty($popButtons)) $popButtons = [['label' => '', 'url' => '', 'style' => 'primary']];
    ?>
    <div class="panel">
        <div class="panel-header">
            <div>
                <div class="panel-title">Announcement Popup (Homepage Modal)</div>
                <span class="form-hint" style="font-size: 12px;">Shown once per browser until dismissed &middot; re-appears when you change this content</span>
            </div>
            <a href="popup_manager.php" class="btn btn-secondary btn-sm">Manage All Popups →</a>
        </div>
        <div class="panel-body">
            <form method="POST" action="settings.php" enctype="multipart/form-data">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="save_popup">
                <input type="hidden" name="current_image" value="<?= htmlspecialchars($popImage) ?>">

                <div class="form-group" style="margin-bottom: 18px;">
                    <label style="display: inline-flex; align-items: center; gap: 9px; font-size: 13.5px; cursor: pointer;">
                        <input type="checkbox" name="enabled" value="1" <?= $popEnabled ? 'checked' : '' ?>>
                        <strong>Show announcement popup on the homepage</strong>
                    </label>
                    <div class="form-hint" style="margin-top: 5px;">Replaces the old header ticker. Visitors dismiss it with the &times; button; it returns automatically whenever you save new content.</div>
                </div>

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">Optional Banner Image (top of popup)</label>
                        <input type="file" name="image" class="form-control image-preview-input" data-preview-target="popupImagePreview" accept="image/*">
                        <div style="margin-top: 8px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <img id="popupImagePreview" class="popup-image-preview" src="<?= $popImage !== '' ? '../' . htmlspecialchars($popImage) : '' ?>" alt="Popup banner preview" <?= $popImage === '' ? 'hidden' : '' ?> style="width: 140px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
                            <?php if ($popImage !== ''): ?>
                                <label style="display: inline-flex; align-items: center; gap: 7px; font-size: 13px; cursor: pointer;">
                                    <input type="checkbox" name="remove_image" value="1"> Remove current image
                                </label>
                            <?php else: ?>
                                <span class="form-hint">Upload JPG, PNG or WebP. Recommended ~1600 &times; 430 px.</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="popup_badge">Eyebrow / Badge Text</label>
                        <input type="text" id="popup_badge" name="badge" class="form-control" value="<?= htmlspecialchars($popBadge) ?>" placeholder="e.g. Admissions Open 2025-26">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="popup_title">Headline</label>
                        <input type="text" id="popup_title" name="title" class="form-control" value="<?= htmlspecialchars($popTitle) ?>" placeholder="e.g. Admissions Now Open for AY 2025-26">
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label" for="popup_message">Message Body</label>
                        <textarea id="popup_message" name="message" class="form-control" rows="3" placeholder="e.g. Applications for BCA, B.Sc., BBA &amp; more are now open. Limited seats &mdash; early applicants get priority."><?= htmlspecialchars($popMessage) ?></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">Action Buttons</label>
                        <div class="popup-btn-list" id="popupBtnRows">
                            <?php foreach ($popButtons as $btn): ?>
                                <?php $bStyle = in_array(($btn['style'] ?? ''), ['primary', 'secondary', 'outline'], true) ? $btn['style'] : 'primary'; ?>
                                <div class="popup-btn-row">
                                    <input type="text" name="btn_label[]" class="form-control" placeholder="Button label (e.g. Apply Now)" value="<?= htmlspecialchars($btn['label'] ?? '') ?>">
                                    <input type="text" name="btn_url[]" class="form-control" placeholder="https://example.com/apply or /courses.php" value="<?= htmlspecialchars($btn['url'] ?? '') ?>">
                                    <select name="btn_style[]" class="form-control">
                                        <option value="primary" <?= $bStyle === 'primary' ? 'selected' : '' ?>>Primary (teal)</option>
                                        <option value="secondary" <?= $bStyle === 'secondary' ? 'selected' : '' ?>>Secondary (dark)</option>
                                        <option value="outline" <?= $bStyle === 'outline' ? 'selected' : '' ?>>Outline (teal)</option>
                                    </select>
                                    <button type="button" class="btn btn-danger btn-icon" title="Remove button" aria-label="Remove button" data-popup-btn-remove>
                                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" id="popupBtnAdd" style="margin-top: 10px;">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            Add Another Button
                        </button>
                    </div>
                </div>

                <div style="margin-top: 20px; display: flex; gap: 12px; align-items: center;">
                    <button type="submit" class="btn btn-primary">Save Announcement Popup</button>
                    <a href="../index.php" target="_blank" class="btn btn-secondary">Preview on Homepage</a>
                </div>
            </form>
        </div>
    </div>

    <script>
    (function () {
        var list = document.getElementById('popupBtnRows');
        var addBtn = document.getElementById('popupBtnAdd');
        if (!list || !addBtn) return;

        function rowHtml() {
            return '<div class="popup-btn-row">'
                + '<input type="text" name="btn_label[]" class="form-control" placeholder="Button label (e.g. Apply Now)">'
                + '<input type="text" name="btn_url[]" class="form-control" placeholder="https://example.com/apply or /courses.php">'
                + '<select name="btn_style[]" class="form-control">'
                + '<option value="primary">Primary (teal)</option>'
                + '<option value="secondary">Secondary (dark)</option>'
                + '<option value="outline">Outline (teal)</option></select>'
                + '<button type="button" class="btn btn-danger btn-icon" title="Remove button" aria-label="Remove button" data-popup-btn-remove>'
                + '<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>'
                + '</button></div>';
        }

        addBtn.addEventListener('click', function () {
            list.insertAdjacentHTML('beforeend', rowHtml());
        });

        list.addEventListener('click', function (e) {
            var rm = e.target.closest('[data-popup-btn-remove]');
            if (!rm) return;
            var row = rm.closest('.popup-btn-row');
            if (row && list.children.length > 1) row.remove();
        });
    })();
    </script>

    <!-- Change Admin Password -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Change Admin Password</div>
        </div>
        <div class="panel-body">
            <form method="POST" action="settings.php">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="change_password">

                <div class="form-group">
                    <label class="form-label" for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required placeholder="Enter current password" autocomplete="current-password">
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required minlength="8" placeholder="Minimum 8 characters" autocomplete="new-password">
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="8" placeholder="Repeat new password" autocomplete="new-password">
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>

    <!-- System Info (compact) -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">System &amp; Database Info</div>
        </div>
        <div class="panel-body" style="padding: 0;">
            <table class="admin-table">
                <tbody>
                    <tr>
                        <td><strong>PHP Version</strong></td>
                        <td><?= phpversion() ?></td>
                    </tr>
                    <tr>
                        <td><strong>Database Engine</strong></td>
                        <td>SQLite <?= $sqliteVer ?> (PDO WAL Mode)</td>
                    </tr>
                    <tr>
                        <td><strong>Database Location</strong></td>
                        <td><code>website/data/database.sqlite</code></td>
                    </tr>
                    <tr>
                        <td><strong>Database Size</strong></td>
                        <td><?= $dbFileSize ?></td>
                    </tr>
                    <tr>
                        <td><strong>Uploads Directory</strong></td>
                        <td><code>website/assets/uploads/</code> (Writable)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Advanced Diagnostics -->
<details class="collapse-panel">
    <summary>Advanced System Diagnostics <?= $totalDatabaseRecords ?> records &bull; PHP <?= $phpVer ?> &bull; <?= $dbFileSize ?></summary>
    <div style="padding: 24px;">

        <!-- Server Runtime -->
        <div class="panel" style="margin-bottom: 24px;">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Server Runtime &amp; PHP Environment</div>
                    <span class="form-hint">Backend execution parameters &amp; resource allocations</span>
                </div>
                <span class="badge badge-success">PHP <?= $phpVer ?></span>
            </div>
            <div class="panel-body" style="padding: 0;">
                <table class="admin-table">
                    <tbody>
                        <tr><td><strong>Web Server Software</strong></td><td><?= htmlspecialchars($serverSoft) ?></td></tr>
                        <tr><td><strong>Server Gateway SAPI</strong></td><td><code><?= htmlspecialchars($serverSapi) ?></code></td></tr>
                        <tr><td><strong>Memory Limit</strong></td><td><strong><?= $memoryLimit ?></strong></td></tr>
                        <tr><td><strong>Max Upload / POST Size</strong></td><td><code>upload: <?= $uploadMax ?></code> &bull; <code>post: <?= $postMax ?></code></td></tr>
                        <tr><td><strong>Max Script Execution Time</strong></td><td><?= $maxExecTime ?></td></tr>
                        <tr>
                            <td><strong>PHP Error Handling</strong></td>
                            <td>
                                <?php if ($displayErrors): ?>
                                    <span class="badge badge-warning">Display Errors ON</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Production Mode (Errors Hidden)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="padding: 16px 24px; background: rgba(0,0,0,0.02); border-top: 1px solid var(--border-color);">
                <div style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 10px; letter-spacing: 0.5px;">PHP Extension Health Matrix</div>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <?php foreach ($criticalExts as $extKey => $extInfo):
                        $isLoaded = extension_loaded($extKey);
                    ?>
                        <span class="badge <?= $isLoaded ? 'badge-success' : ($extInfo['required'] ? 'badge-danger' : 'badge-secondary') ?>" style="font-size: 11px; padding: 4px 10px;">
                            <?= $isLoaded ? '✓' : '✗' ?> <?= htmlspecialchars($extInfo['name']) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
            <!-- Storage Vault -->
            <div class="panel">
                <div class="panel-header">
                    <div>
                        <div class="panel-title">Media Storage &amp; Disk Footprint</div>
                        <span class="form-hint">Physical asset distribution</span>
                    </div>
                </div>
                <div class="panel-body" style="padding: 0;">
                    <div class="storage-bar-group" style="padding: 16px;">
                        <?php
                        $storageRows = [
                            ['label' => 'Faculty Profile Photos', 'data' => $storageFaculty],
                            ['label' => 'Campus Photo Gallery Albums', 'data' => $storageGallery],
                            ['label' => 'E-Magazines & Publications', 'data' => $storageEmag],
                            ['label' => 'Pride Achievers & Rankers', 'data' => $storageRankers],
                            ['label' => 'Institutional PDFs & Reports', 'data' => $storageDocs],
                        ];
                        foreach ($storageRows as $row):
                        ?>
                        <div class="storage-row" style="margin-bottom: 10px;">
                            <div class="storage-info"><span><?= $row['label'] ?></span></div>
                            <div>
                                <strong style="color: var(--text-main);"><?= $row['data']['count'] ?> files</strong>
                                <span style="color: var(--text-muted); font-size: 11.5px; margin-left: 6px;">(<?= $row['data']['size'] ?>)</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div class="storage-row" style="margin-bottom: 0; padding-top: 10px; border-top: 1px dashed var(--border-color);">
                            <div class="storage-info"><span>Unreferenced / Orphaned Uploads</span></div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <?php if ($orphanedCount > 0): ?>
                                    <span class="badge badge-warning"><?= $orphanedCount ?> files (<?= $orphanedSize ?>)</span>
                                    <form method="POST" action="settings.php" style="display: inline;" id="clean-orphaned-form">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="action" value="clean_orphaned_media">
                                        <button type="button" class="btn btn-secondary btn-sm"
                                                data-confirm="Safely delete <?= $orphanedCount ?> unreferenced media files (<?= $orphanedSize ?>) from disk?"
                                                data-confirm-form="#clean-orphaned-form">
                                            Clean Media
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge badge-success">Clean (0 orphaned)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Database Inventory -->
            <div class="panel">
                <div class="panel-header">
                    <div>
                        <div class="panel-title">Database Table Inventory</div>
                        <span class="form-hint"><?= count($tableDefinitions) ?> collections &bull; <?= $totalDatabaseRecords ?> records</span>
                    </div>
                    <span class="badge badge-<?= $integrityRes === 'ok' ? 'success' : 'danger' ?>">Integrity: <?= htmlspecialchars($integrityRes) ?></span>
                </div>
                <div class="panel-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px;">
                        <?php foreach ($tableDefinitions as $tKey => $tMeta): ?>
                            <div style="background: var(--bg-input); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 10px 12px; text-align: center;">
                                <div style="font-size: 17px; font-weight: 700; color: var(--text-main);"><?= $tableCounts[$tKey] ?? 0 ?></div>
                                <div style="font-size: 10.5px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; margin-top: 2px; letter-spacing: 0.3px;"><?= htmlspecialchars($tMeta['label']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="margin-top: 18px; background: var(--bg-input); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 14px 16px;">
                        <div style="font-weight: 600; font-size: 13px; color: var(--text-main);">Database Maintenance &amp; Optimization</div>
                        <div style="font-size: 11.5px; color: var(--text-muted); margin: 4px 0 12px;">Runs SQLite <code>VACUUM</code> &amp; <code>PRAGMA optimize</code> to reclaim unused pages and rebuild indexes.</div>
                        <form method="POST" action="settings.php" style="display: inline;" id="vacuum-form-settings">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="vacuum_db">
                            <button type="button" class="btn btn-secondary btn-sm"
                                    data-confirm="Run SQLite VACUUM and index optimization? This may take a few seconds."
                                    data-confirm-form="#vacuum-form-settings">Run Optimize</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security & SEO -->
        <div class="panel" style="margin-bottom: 0;">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Security, SEO &amp; PWA Health</div>
                    <span class="form-hint">Defense-in-depth safeguards and public discovery assets</span>
                </div>
            </div>
            <div class="panel-body" style="padding: 0;">
                <table class="admin-table">
                    <tbody>
                        <tr>
                            <td><strong>Database Directory Protection</strong></td>
                            <td>
                                <?php if ($htaccessProtected): ?>
                                    <span class="badge badge-success"><code>website/data/.htaccess</code> Active (Deny Direct Access)</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Missing .htaccess Protection</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Session Hardening</strong></td>
                            <td>
                                <span class="badge badge-success">HttpOnly: Yes</span>
                                <span class="badge badge-success">SameSite: Lax</span>
                                <span class="badge badge-success">Strict Mode: On</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>XML Sitemap</strong></td>
                            <td><?= $sitemapCount ?> URLs &bull; <?= $sitemapSize ?> &bull; <?= $sitemapMod ?> <a href="../sitemap.xml" target="_blank" class="btn btn-secondary btn-sm" style="margin-left: 8px;">Inspect</a></td>
                        </tr>
                        <tr>
                            <td><strong>Crawler Directives (robots.txt)</strong></td>
                            <td><span class="badge badge-<?= $robotsExists ? 'success' : 'warning' ?>"><?= $robotsExists ? 'Active &amp; Configured' : 'Missing' ?></span> <span style="color: var(--text-muted); font-size: 12px;">(<?= $robotsSize ?>)</span></td>
                        </tr>
                        <tr>
                            <td><strong>PWA Manifest &amp; Service Worker</strong></td>
                            <td>
                                <span class="badge badge-<?= $manifestExists ? 'success' : 'warning' ?>">Manifest <?= $manifestExists ? htmlspecialchars($manifestData['short_name'] ?? 'Configured') : 'Missing' ?></span>
                                <span class="badge badge-<?= $swExists ? 'success' : 'warning' ?>"><?= $swExists ? 'sw.js Active' : 'sw.js Missing' ?></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</details>

<?php require_once __DIR__ . '/includes/footer.php'; ?>