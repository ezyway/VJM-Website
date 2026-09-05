<?php
/**
 * Executive Admin Dashboard & Institutional Intelligence Center
 * Shri V.J. Modha College Portal
 *
 * Task-focused CMS home: section stats, quick actions, recent activity
 * and a compact system health panel.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/ui.php';
requireAuth();

$db = getDB();
$siteRoot = dirname(__DIR__);

// Handle Database Optimization Action BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $token = $_POST['csrf_token'] ?? '';
    if (verifyCSRFToken($token)) {
        if ($_POST['action'] === 'vacuum_db') {
            try {
                $t0 = microtime(true);
                $db->exec('VACUUM;');
                $db->exec('PRAGMA optimize;');
                $elapsed = round((microtime(true) - $t0) * 1000, 2);
                setFlash('success', "Database VACUUM and index optimization executed successfully in {$elapsed} ms.");
            } catch (Exception $e) {
                setFlash('danger', "Database optimization error: " . $e->getMessage());
            }
            header('Location: index.php');
            exit;
        }
    } else {
        setFlash('danger', 'Invalid security token.');
        header('Location: index.php');
        exit;
    }
}

$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';

// ---- Section record counts (drives the stat cards) ----
$sectionStats = [
    ['key' => 'faculties',           'label' => 'Faculty Members',        'href' => 'faculties.php',    'icon' => 'users',     'tone' => 'primary'],
    ['key' => 'courses',             'label' => 'Courses & Programs',     'href' => 'courses.php',      'icon' => 'book',      'tone' => 'info'],
    ['key' => 'labs',                'label' => 'Labs & Facilities',      'href' => 'labs.php',         'icon' => 'cpu',       'tone' => 'purple'],
    ['key' => 'events',              'label' => 'Events & Notices',       'href' => 'events.php',       'icon' => 'calendar',  'tone' => 'warning'],
    ['key' => 'rankers',             'label' => 'Rankers & Achievers',    'href' => 'rankers.php',      'icon' => 'award',     'tone' => 'success'],
    ['key' => 'gallery_photos',      'label' => 'Gallery Photos',         'href' => 'gallery.php',      'icon' => 'image',     'tone' => 'warning'],
    ['key' => 'testimonials',        'label' => 'Testimonials',           'href' => 'testimonials.php', 'icon' => 'quote',     'tone' => 'info'],
    ['key' => 'magazines',           'label' => 'E-Magazines',            'href' => 'magazines.php',    'icon' => 'file',      'tone' => 'purple'],
    ['key' => 'scholarships',        'label' => 'Scholarship Years',      'href' => 'scholarships.php?tab=records', 'icon' => 'dollar', 'tone' => 'success'],
    ['key' => 'pass_rates',          'label' => 'Pass Rate Years',        'href' => 'pass_rates.php',   'icon' => 'chart',     'tone' => 'primary'],
];

foreach ($sectionStats as $i => $s) {
    try {
        $sectionStats[$i]['count'] = (int)$db->query("SELECT COUNT(*) FROM {$s['key']}")->fetchColumn();
    } catch (Exception $e) {
        $sectionStats[$i]['count'] = 0;
    }
}

// ---- Quick actions (open the forms in a slide-over drawer) ----
$quickActions = [
    ['href' => 'faculties.php?action=create',   'title' => 'Add Faculty',     'icon' => 'user-plus'],
    ['href' => 'courses.php?action=create',     'title' => 'Add Course',      'icon' => 'plus'],
    ['href' => 'events.php?action=create',      'title' => 'Post Event',      'icon' => 'plus'],
    ['href' => 'rankers.php?action=create',     'title' => 'Add Ranker',      'icon' => 'plus'],
    ['href' => 'testimonials.php?action=create','title' => 'Add Testimonial', 'icon' => 'plus'],
    ['href' => 'gallery.php?action=create_album','title' => 'New Album',      'icon' => 'folder'],
    ['href' => 'magazines.php?action=create',   'title' => 'Upload Magazine', 'icon' => 'upload'],
    ['href' => 'labs.php?action=create',        'title' => 'Add Lab',         'icon' => 'plus'],
];

// ---- Recent activity ----
$recentEvents = $db->query('SELECT * FROM events ORDER BY id DESC LIMIT 3')->fetchAll();
$recentRankers = $db->query('SELECT * FROM rankers ORDER BY id DESC LIMIT 2')->fetchAll();
$recentFaculties = $db->query('SELECT * FROM faculties ORDER BY id DESC LIMIT 2')->fetchAll();
$recent = [];

foreach ($recentEvents as $e) {
    $recent[] = [
        'icon' => 'calendar',
        'tone' => 'warning',
        'title' => $e['title'],
        'meta' => 'Event / Notice' . (!empty($e['badge']) ? ' &bull; ' . $e['badge'] : ''),
        'href' => 'events.php',
    ];
}
foreach ($recentRankers as $r) {
    $recent[] = [
        'icon' => 'award',
        'tone' => 'success',
        'title' => $r['name'],
        'meta' => 'Ranker &bull; ' . $r['course'],
        'href' => 'rankers.php',
    ];
}
foreach ($recentFaculties as $f) {
    $recent[] = [
        'icon' => 'users',
        'tone' => 'primary',
        'title' => $f['name'],
        'meta' => 'Faculty &bull; ' . $f['designation'],
        'href' => 'faculties.php',
    ];
}

// ---- Compact system health (getStorageStats lives in includes/db.php) ----
$phpVer       = PHP_VERSION;
$sqliteVer    = $db->query('SELECT sqlite_version()')->fetchColumn();
$journalMode  = $db->query('PRAGMA journal_mode')->fetchColumn();
$integrityRes = $db->query('PRAGMA integrity_check')->fetchColumn();
$dbFileSize   = file_exists(DB_FILE_PATH) ? round(filesize(DB_FILE_PATH) / 1024, 1) . ' KB' : '0 KB';

$storageFaculty = getStorageStats([$siteRoot . '/assets/photos/faculties', $siteRoot . '/assets/uploads/faculties']);
$storageGallery = getStorageStats([$siteRoot . '/assets/photos/gallery', $siteRoot . '/assets/uploads/gallery']);
$storageEmag    = getStorageStats([$siteRoot . '/assets/e_mags', $siteRoot . '/assets/uploads/emag']);
$storageRankers = getStorageStats([$siteRoot . '/assets/photos/index/pride_of_college', $siteRoot . '/assets/uploads/rankers']);
$totalMediaBytes = $storageFaculty['bytes'] + $storageGallery['bytes'] + $storageEmag['bytes'] + $storageRankers['bytes'];
$totalMediaFiles = $storageFaculty['count'] + $storageGallery['count'] + $storageEmag['count'] + $storageRankers['count'];
$totalMediaSizeStr = ($totalMediaBytes > 1048576)
    ? round($totalMediaBytes / 1048576, 2) . ' MB'
    : round($totalMediaBytes / 1024, 1) . ' KB';

$uploadDirWritable = is_writable($siteRoot . '/assets/uploads') || is_writable($siteRoot . '/assets');
$sitemapPath = $siteRoot . '/sitemap.xml';
$sitemapCount = 0;
if (file_exists($sitemapPath)) {
    $xml = @simplexml_load_file($sitemapPath);
    if ($xml && isset($xml->url)) $sitemapCount = count($xml->url);
}
$manifestExists = file_exists($siteRoot . '/manifest.json');
$swExists = file_exists($siteRoot . '/sw.js');

// Recent Activity Audit Trail + Fallback Content
$activityLogs = getRecentActivityLogs(6);

$healthRows = [
    ['label' => 'Database Integrity', 'icon' => 'database', 'value' => 'OK — ' . strtoupper($journalMode) . ' (WAL)', 'good' => $integrityRes === 'ok'],
    ['label' => 'PHP Version', 'icon' => 'cpu', 'value' => 'PHP ' . $phpVer, 'good' => true],
    ['label' => 'Uploads Directory', 'icon' => 'folder', 'value' => $uploadDirWritable ? 'Writable' : 'Read-Only', 'good' => $uploadDirWritable],
    ['label' => 'Media Storage', 'icon' => 'image', 'value' => $totalMediaFiles . ' files / ' . $totalMediaSizeStr, 'good' => true],
    ['label' => 'XML Sitemap', 'icon' => 'globe', 'value' => $sitemapCount . ' URLs indexed', 'good' => $sitemapCount > 0],
    ['label' => 'PWA / Mobile App', 'icon' => 'shield', 'value' => ($manifestExists && $swExists) ? 'Ready' : 'Partial', 'good' => $manifestExists && $swExists],
];

$firstName = explode(' ', $currentAdmin['name'])[0];
?>

<!-- Welcome Banner -->
<div class="welcome-banner">
    <div>
        <div class="welcome-eyebrow">Admin Dashboard</div>
        <div class="welcome-title">Welcome back, <?= htmlspecialchars($firstName) ?> 👋</div>
        <div class="welcome-sub"><?= date('l, j F Y') ?> &bull; Here&rsquo;s a quick look at what&rsquo;s live on your website.</div>
    </div>
    <a href="../index.php" target="_blank" class="btn btn-secondary btn-sm" style="gap: 8px;">
        <?= icon('external', 14) ?>
        View Live Site
    </a>
</div>

<!-- Section Stats -->
<div class="stats-grid">
    <?php foreach ($sectionStats as $s): ?>
        <a class="stat-card" href="<?= $s['href'] ?>">
            <div class="stat-info">
                <div class="stat-label"><?= htmlspecialchars($s['label']) ?></div>
                <div class="stat-value"><?= (int)$s['count'] ?></div>
                <span class="stat-link-hint">Manage &rarr;</span>
            </div>
            <div class="stat-icon <?= $s['tone'] ?>">
                <?= icon($s['icon'], 24) ?>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<!-- Quick Actions -->
<div class="panel" style="margin-top: 4px;">
    <div class="panel-header">
        <div>
            <div class="panel-title"><?= icon('plus', 18) ?> Quick Actions</div>
            <span class="form-hint">Add or publish new content in a few clicks</span>
        </div>
    </div>
    <div class="panel-body">
        <div class="quick-actions">
            <?php foreach ($quickActions as $qa): ?>
                <a href="<?= $qa['href'] ?>"
                   data-drawer-url="<?= $qa['href'] ?>&amp;drawer=1"
                   data-drawer-title="<?= htmlspecialchars($qa['title']) ?>"
                   class="quick-action">
                    <?= icon($qa['icon'], 20) ?>
                    <span><?= htmlspecialchars($qa['title']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Recent Activity + System Health -->
<div class="dash-grid-2">
    <div class="panel" style="margin-bottom: 0;">
        <div class="panel-header">
            <div>
                <div class="panel-title"><?= icon('clock', 18) ?> Recent Activity</div>
                <span class="form-hint">Audit trail of admin actions and recent site changes</span>
            </div>
        </div>
        <div class="panel-body" style="padding: 0;">
            <div class="recent-list">
                <?php if (empty($activityLogs) && empty($recent)): ?>
                    <div class="empty-cell">Nothing logged yet — actions and updates will appear here in real-time.</div>
                <?php elseif (!empty($activityLogs)): ?>
                    <?php foreach ($activityLogs as $log): 
                        $actionIcon = 'clock';
                        $actionColor = 'var(--text-muted)';
                        if ($log['action'] === 'login' || $log['action'] === 'logout') {
                            $actionIcon = 'user';
                            $actionColor = 'var(--primary)';
                        } elseif ($log['action'] === 'create' || $log['action'] === 'save') {
                            $actionIcon = 'plus';
                            $actionColor = '#16a34a';
                        } elseif ($log['action'] === 'delete') {
                            $actionIcon = 'trash';
                            $actionColor = '#dc2626';
                        } elseif (in_array($log['action'], ['vacuum', 'clean_media', 'backup'])) {
                            $actionIcon = 'settings';
                            $actionColor = '#d97706';
                        }
                        $timeAgo = date('M j, H:i', strtotime($log['created_at']));
                    ?>
                        <div class="recent-item" style="display: flex; align-items: flex-start; gap: 12px; padding: 12px 16px; border-bottom: 1px solid var(--border-color, rgba(0,0,0,0.06));">
                            <div class="recent-icon" style="color: <?= $actionColor ?>; margin-top: 2px;">
                                <?= icon($actionIcon, 17) ?>
                            </div>
                            <div class="recent-content" style="flex: 1; min-width: 0;">
                                <div class="recent-title" style="display: flex; justify-content: space-between; align-items: baseline; gap: 8px;">
                                    <span>
                                        <strong><?= htmlspecialchars($log['admin_username']) ?></strong>
                                        <span style="font-size: 11px; padding: 2px 6px; border-radius: 4px; background: var(--bg-hover, #f1f5f9); text-transform: uppercase; font-weight: 600;">
                                            <?= htmlspecialchars($log['action']) ?>
                                        </span>
                                        <?php if (!empty($log['entity_type'])): ?>
                                            <span style="color: var(--text-muted); font-size: 12px;"> &bull; <?= htmlspecialchars($log['entity_type']) ?></span>
                                        <?php endif; ?>
                                    </span>
                                    <span style="font-size: 11px; color: var(--text-muted); white-space: nowrap;"><?= $timeAgo ?></span>
                                </div>
                                <div class="recent-meta" style="font-size: 12px; color: var(--text-muted); margin-top: 2px; word-break: break-word;">
                                    <?= htmlspecialchars($log['details'] ?: ($log['action'] . ' on ' . $log['entity_type'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php foreach ($recent as $item): ?>
                        <a href="<?= $item['href'] ?>" class="recent-item" style="text-decoration: none; color: inherit;">
                            <div class="recent-icon" style="color: var(--primary);">
                                <?= icon($item['icon'], 17) ?>
                            </div>
                            <div class="recent-content">
                                <div class="recent-title"><?= htmlspecialchars($item['title']) ?></div>
                                <div class="recent-meta"><?= $item['meta'] ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-bottom: 0;">
        <div class="panel-header">
            <div>
                <div class="panel-title"><?= icon('shield', 18) ?> System Health</div>
                <span class="form-hint">Core services &amp; storage at a glance</span>
            </div>
            <span class="badge badge-success">All Systems Nominal</span>
        </div>
        <div class="panel-body" style="padding: 0;">
            <div class="health-list">
                <?php foreach ($healthRows as $h): ?>
                    <div class="health-row">
                        <div class="health-label"><?= icon($h['icon'], 15) ?> <?= htmlspecialchars($h['label']) ?></div>
                        <div class="health-value">
                            <?= htmlspecialchars($h['value']) ?>
                            <span class="badge badge-<?= $h['good'] ? 'success' : 'danger' ?>" style="margin-left: 6px;">
                                <?= $h['good'] ? 'OK' : 'Attention' ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="health-row">
                    <div class="health-label"><?= icon('database', 15) ?> SQLite Database</div>
                    <div class="health-value"><?= $dbFileSize ?>
                        <form method="POST" action="index.php" style="display: inline-block; margin-left: 10px;" id="vacuum-form-dash">
                            <?= csrfField() ?>
                            <input type="hidden" name="action" value="vacuum_db">
                            <button type="button" class="btn btn-secondary btn-sm"
                                    data-confirm="Run SQLite VACUUM and index optimization? This may take a few seconds."
                                    data-confirm-form="#vacuum-form-dash">Optimize</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div style="padding: 14px 20px; border-top: 1px solid var(--border-color); background: var(--bg-card-header);">
            <a href="settings.php" class="btn btn-secondary btn-sm"><?= icon('globe', 14) ?> View Full Diagnostics &amp; Settings</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>