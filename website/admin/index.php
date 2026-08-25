<?php
/**
 * Executive Admin Dashboard & Institutional Intelligence Center
 * Shri V.J. Modha College Portal
 */

$pageTitle = 'Executive Dashboard';
require_once __DIR__ . '/includes/header.php';

$db = getDB();

// 1. Core Entity Counts
$facultyCount = (int)$db->query('SELECT COUNT(*) FROM faculties')->fetchColumn();
$courseCount  = (int)$db->query('SELECT COUNT(*) FROM courses')->fetchColumn();
$labCount     = (int)$db->query('SELECT COUNT(*) FROM labs')->fetchColumn();
$eventCount   = (int)$db->query("SELECT COUNT(*) FROM events WHERE event_type = 'event'")->fetchColumn();
$newsCount    = (int)$db->query("SELECT COUNT(*) FROM events WHERE event_type = 'news'")->fetchColumn();
$rankerCount  = (int)$db->query('SELECT COUNT(*) FROM rankers')->fetchColumn();
$albumCount   = (int)$db->query('SELECT COUNT(*) FROM gallery_albums')->fetchColumn();
$photoCount   = (int)$db->query('SELECT COUNT(*) FROM gallery_photos')->fetchColumn();
$testimCount  = (int)$db->query('SELECT COUNT(*) FROM testimonials')->fetchColumn();
$magCount     = (int)$db->query('SELECT COUNT(*) FROM magazines')->fetchColumn();

// 2. Financial Aid & Scholarships
$totalDisbursed = (int)$db->query('SELECT SUM(amount_numeric) FROM scholarships')->fetchColumn() ?: 0;
$latestScholarship = $db->query('SELECT * FROM scholarships ORDER BY sort_order ASC, id ASC LIMIT 1')->fetch();

// 3. Faculty Department Distribution
$faculties = $db->query('SELECT depts FROM faculties')->fetchAll();
$deptStats = [
    'bca'        => ['label' => 'B.C.A. & M.Sc.(IT)', 'count' => 0, 'color' => '#3b82f6'],
    'bsc'        => ['label' => 'B.Sc. & M.Sc.(Chem)', 'count' => 0, 'color' => '#10b981'],
    'bcom'       => ['label' => 'B.Com. & M.Com.',     'count' => 0, 'color' => '#f59e0b'],
    'bba'        => ['label' => 'B.B.A.',              'count' => 0, 'color' => '#8b5cf6'],
    'bsw'        => ['label' => 'B.S.W.',              'count' => 0, 'color' => '#06b6d4'],
    'admin'      => ['label' => 'Administration',      'count' => 0, 'color' => '#64748b'],
];

foreach ($faculties as $f) {
    $depts = explode(',', $f['depts'] ?? '');
    foreach ($depts as $d) {
        $d = trim($d);
        if (isset($deptStats[$d])) {
            $deptStats[$d]['count']++;
        }
    }
}

// 4. Latest Academic Pass Rates
$latestPassRates = $db->query('SELECT * FROM pass_rates WHERE is_latest = 1 LIMIT 1')->fetch();
if (!$latestPassRates) {
    $latestPassRates = $db->query('SELECT * FROM pass_rates ORDER BY year DESC LIMIT 1')->fetch();
}

// 5. Recent Events & Academic Notices (Activity Radar)
$recentEvents = $db->query('SELECT * FROM events ORDER BY sort_order ASC, id DESC LIMIT 5')->fetchAll();

// 6. Latest E-Magazine Edition
$latestMag = $db->query('SELECT * FROM magazines ORDER BY sort_order ASC, year DESC LIMIT 1')->fetch();

// 7. Storage & Asset Metrics
$uploadBase = dirname(__DIR__) . '/assets/uploads';

function getDirInfo(string $dir): array {
    if (!is_dir($dir)) return ['count' => 0, 'size' => '0 KB'];
    $files = array_diff(scandir($dir), ['.', '..']);
    $totalSize = 0;
    $count = 0;
    foreach ($files as $f) {
        $path = $dir . '/' . $f;
        if (is_file($path)) {
            $totalSize += filesize($path);
            $count++;
        } elseif (is_dir($path)) {
            $sub = getDirInfo($path);
            $count += $sub['count'];
        }
    }
    $formattedSize = ($totalSize > 1048576) 
        ? round($totalSize / 1048576, 1) . ' MB' 
        : round($totalSize / 1024, 1) . ' KB';
    return ['count' => $count, 'size' => $formattedSize];
}

$storageFaculty = getDirInfo($uploadBase . '/faculties');
$storageGallery = getDirInfo($uploadBase . '/gallery');
$storageEmag    = getDirInfo($uploadBase . '/emag');
$storageRankers = getDirInfo($uploadBase . '/rankers');

$dbFileSize = file_exists(DB_FILE_PATH) ? round(filesize(DB_FILE_PATH) / 1024, 1) . ' KB' : '0 KB';
$activeTicker = getSetting('announcement_banner', 'Admissions Open for Academic Year 2025-26');
$counterPassRate = getSetting('counter_pass_rate', '97.6');
?>

<!-- Executive Banner -->
<div class="dash-banner">
    <div>
        <div class="dash-banner-title">
            <span class="status-dot online"></span>
            Shri V.J. Modha College Portal Center
        </div>
        <div class="dash-banner-sub">
            Affiliated to BKNMU Junagadh &bull; System Status: <strong>Operational &amp; Dynamic</strong> &bull; <?= date('l, d F Y') ?>
        </div>
    </div>
    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <div class="dash-ticker-box">
            <span style="font-weight: 700; color: var(--primary); text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;">Live Ticker:</span>
            <span style="color: var(--text-muted); text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 320px;">
                <?= htmlspecialchars($activeTicker ?: 'No active ticker text broadcasted.') ?>
            </span>
        </div>
        <a href="../index.php" target="_blank" class="btn btn-secondary btn-sm" title="Open Public Website">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
            View Live Site
        </a>
    </div>
</div>

<!-- High-Level Key Performance Indicators -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Faculty Strength</div>
            <div class="stat-value"><?= $facultyCount ?> <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">Professors</span></div>
        </div>
        <div class="stat-icon primary">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Total Student Aid Disbursed</div>
            <div class="stat-value">₹ <?= number_format($totalDisbursed) ?></div>
        </div>
        <div class="stat-icon success">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Benchmark Pass Rate</div>
            <div class="stat-value"><?= htmlspecialchars($counterPassRate) ?>%</div>
        </div>
        <div class="stat-icon warning">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Media &amp; Publication Assets</div>
            <div class="stat-value"><?= $photoCount ?> <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">(<?= $albumCount ?> Albums, <?= $magCount ?> Mags)</span></div>
        </div>
        <div class="stat-icon info">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
        </div>
    </div>
</div>

<!-- Two-Column Primary Analytics Grid -->
<div class="dash-grid-2">
    
    <!-- Left Column: Academic Intelligence & Distribution -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        
        <!-- Department Faculty Distribution -->
        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Faculty Strength by Academic Department</div>
                    <span class="form-hint">Staff distribution across specialized wings</span>
                </div>
                <a href="faculties.php" class="btn btn-secondary btn-sm">Manage Faculty</a>
            </div>
            <div class="panel-body">
                <div class="dept-bar-group">
                    <?php 
                    $maxCount = max(array_column($deptStats, 'count')) ?: 1;
                    foreach ($deptStats as $key => $d): 
                        $pct = round(($d['count'] / $maxCount) * 100);
                    ?>
                    <div class="dept-bar-item">
                        <div class="dept-bar-label">
                            <span><?= htmlspecialchars($d['label']) ?></span>
                            <span style="font-weight: 700;"><?= $d['count'] ?> Members</span>
                        </div>
                        <div class="dept-bar-track">
                            <div class="dept-bar-fill" style="width: <?= $pct ?>%; background-color: <?= $d['color'] ?>;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Academic Performance Pulse -->
        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Latest Academic Pass Rate Matrix (<?= htmlspecialchars($latestPassRates['year'] ?? 'Latest') ?>)</div>
                    <span class="form-hint">Stream-wise graduation success benchmarks</span>
                </div>
                <a href="pass_rates.php" class="btn btn-secondary btn-sm">All Batches</a>
            </div>
            <div class="panel-body">
                <?php if ($latestPassRates): ?>
                <div class="pass-rates-mini-grid">
                    <div class="pass-rate-pill">
                        <span class="pass-rate-pill-stream">B.Sc.</span>
                        <span class="pass-rate-pill-value <?= (strpos($latestPassRates['bsc'], '100') !== false) ? 'perfect' : '' ?>"><?= htmlspecialchars($latestPassRates['bsc']) ?></span>
                    </div>
                    <div class="pass-rate-pill">
                        <span class="pass-rate-pill-stream">B.Com.</span>
                        <span class="pass-rate-pill-value <?= (strpos($latestPassRates['bcom'], '100') !== false) ? 'perfect' : '' ?>"><?= htmlspecialchars($latestPassRates['bcom']) ?></span>
                    </div>
                    <div class="pass-rate-pill">
                        <span class="pass-rate-pill-stream">B.S.W.</span>
                        <span class="pass-rate-pill-value <?= (strpos($latestPassRates['bsw'], '100') !== false) ? 'perfect' : '' ?>"><?= htmlspecialchars($latestPassRates['bsw']) ?></span>
                    </div>
                    <div class="pass-rate-pill">
                        <span class="pass-rate-pill-stream">B.B.A.</span>
                        <span class="pass-rate-pill-value <?= (strpos($latestPassRates['bba'], '100') !== false) ? 'perfect' : '' ?>"><?= htmlspecialchars($latestPassRates['bba']) ?></span>
                    </div>
                    <div class="pass-rate-pill">
                        <span class="pass-rate-pill-stream">B.C.A.</span>
                        <span class="pass-rate-pill-value <?= (strpos($latestPassRates['bca'], '100') !== false) ? 'perfect' : '' ?>"><?= htmlspecialchars($latestPassRates['bca']) ?></span>
                    </div>
                    <div class="pass-rate-pill">
                        <span class="pass-rate-pill-stream">M.Sc. IT</span>
                        <span class="pass-rate-pill-value <?= (strpos($latestPassRates['msc_it'], '100') !== false) ? 'perfect' : '' ?>"><?= htmlspecialchars($latestPassRates['msc_it']) ?></span>
                    </div>
                </div>
                <?php else: ?>
                    <p style="color: var(--text-muted); font-size: 13px;">No pass rates recorded yet.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Right Column: Live Activity Radar & Publications -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        
        <!-- Events & Academic Notices Radar -->
        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Campus Events &amp; Circulars Radar</div>
                    <span class="form-hint"><?= $eventCount ?> Upcoming Events &bull; <?= $newsCount ?> Academic Notices</span>
                </div>
                <a href="events.php" class="btn btn-secondary btn-sm">Manage Feed</a>
            </div>
            <div class="panel-body">
                <div class="radar-timeline">
                    <?php if (empty($recentEvents)): ?>
                        <p style="color: var(--text-muted); font-size: 13px;">No active events or notices scheduled.</p>
                    <?php else: ?>
                        <?php foreach ($recentEvents as $ev): 
                            $isEvent = ($ev['event_type'] === 'event');
                        ?>
                        <div class="radar-item">
                            <div class="radar-icon" style="background: <?= $isEvent ? 'rgba(59,130,246,0.15)' : 'rgba(16,185,129,0.15)' ?>; color: <?= $isEvent ? 'var(--primary)' : 'var(--success)' ?>;">
                                <?php if ($isEvent): ?>
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                                <?php else: ?>
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                <?php endif; ?>
                            </div>
                            <div class="radar-content">
                                <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 8px;">
                                    <div class="radar-title"><?= htmlspecialchars($ev['title']) ?></div>
                                    <span class="badge badge-<?= $ev['badge_type'] === 'confirmed' ? 'success' : ($ev['badge_type'] === 'latest' ? 'info' : 'secondary') ?>" style="font-size: 10.5px;">
                                        <?= htmlspecialchars($ev['badge']) ?>
                                    </span>
                                </div>
                                <div class="radar-meta">
                                    <?= htmlspecialchars(mb_strimwidth($ev['description'] ?? '', 0, 75, '...')) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Latest Publication & Scholarship Highlight -->
        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Institutional Highlights</div>
                    <span class="form-hint">E-Magazine edition &amp; student aid record</span>
                </div>
            </div>
            <div class="panel-body" style="display: flex; flex-direction: column; gap: 16px;">
                <!-- E-Mag Highlight -->
                <?php if ($latestMag): ?>
                <div style="background: var(--bg-input); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 14px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="radar-icon" style="background: rgba(139,92,246,0.15); color: #a78bfa;">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 13.5px; color: var(--text-main);"><?= htmlspecialchars($latestMag['title']) ?></div>
                            <div style="font-size: 11.5px; color: var(--text-muted);"><?= htmlspecialchars($latestMag['edition']) ?> &bull; <?= htmlspecialchars($latestMag['file_size']) ?></div>
                        </div>
                    </div>
                    <a href="../<?= htmlspecialchars($latestMag['file_path']) ?>" target="_blank" class="btn btn-secondary btn-sm">View PDF</a>
                </div>
                <?php endif; ?>

                <!-- Latest Scholarship Highlight -->
                <?php if ($latestScholarship): ?>
                <div style="background: var(--bg-input); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 14px; display: flex; justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="radar-icon" style="background: rgba(16,185,129,0.15); color: var(--success);">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 13.5px; color: var(--text-main);">Year <?= htmlspecialchars($latestScholarship['year']) ?> Aid</div>
                            <div style="font-size: 11.5px; color: var(--text-muted);">Disbursed: <strong style="color: var(--success);"><?= htmlspecialchars($latestScholarship['amount_str']) ?></strong></div>
                        </div>
                    </div>
                    <span class="badge badge-success"><?= htmlspecialchars($latestScholarship['status']) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<!-- Storage Utilization & System Health Diagnostics -->
<div class="dash-grid-2">
    
    <!-- Media & Storage Health -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Media Storage &amp; Asset Footprint</div>
            <span class="badge badge-primary">Disk Utilization</span>
        </div>
        <div class="panel-body">
            <div class="storage-bar-group">
                <div class="storage-row">
                    <div class="storage-info">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span>Faculty Profile Images</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-main);"><?= $storageFaculty['count'] ?> files</strong>
                        <span style="color: var(--text-muted); font-size: 11.5px; margin-left: 6px;">(<?= $storageFaculty['size'] ?>)</span>
                    </div>
                </div>

                <div class="storage-row">
                    <div class="storage-info">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        <span>Gallery Photos &amp; Albums</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-main);"><?= $storageGallery['count'] ?> files</strong>
                        <span style="color: var(--text-muted); font-size: 11.5px; margin-left: 6px;">(<?= $storageGallery['size'] ?>)</span>
                    </div>
                </div>

                <div class="storage-row">
                    <div class="storage-info">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                        <span>E-Magazines &amp; Publications</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-main);"><?= $storageEmag['count'] ?> files</strong>
                        <span style="color: var(--text-muted); font-size: 11.5px; margin-left: 6px;">(<?= $storageEmag['size'] ?>)</span>
                    </div>
                </div>

                <div class="storage-row">
                    <div class="storage-info">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                        <span>Student Laurels &amp; Rankers</span>
                    </div>
                    <div>
                        <strong style="color: var(--text-main);"><?= $storageRankers['count'] ?> files</strong>
                        <span style="color: var(--text-muted); font-size: 11.5px; margin-left: 6px;">(<?= $storageRankers['size'] ?>)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security & System Architecture Info -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Server &amp; Security Diagnostics</div>
            <span class="badge badge-success">Healthy</span>
        </div>
        <div class="panel-body" style="padding: 0;">
            <table class="admin-table">
                <tbody>
                    <tr>
                        <td><strong>Database Engine</strong></td>
                        <td>SQLite 3 (PDO WAL Mode &bull; <?= $dbFileSize ?>)</td>
                    </tr>
                    <tr>
                        <td><strong>Runtime Stack</strong></td>
                        <td>PHP <?= phpversion() ?> &bull; Apache 2.4 Server</td>
                    </tr>
                    <tr>
                        <td><strong>Session Security</strong></td>
                        <td><span class="badge badge-success">Strict Mode &bull; HttpOnly &bull; SameSite=Lax</span></td>
                    </tr>
                    <tr>
                        <td><strong>CSRF Protection</strong></td>
                        <td><span class="badge badge-success">Active on all POST operations</span></td>
                    </tr>
                    <tr>
                        <td><strong>Directory Protection</strong></td>
                        <td><span class="badge badge-info"><code>website/data/.htaccess</code> Active</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Jump-to-Module Navigation Cards -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Administration Modules Hub</div>
        <span class="form-hint">Direct access to specialized content sections</span>
    </div>
    <div class="panel-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px;">
            <a href="faculties.php" class="dash-ticker-box" style="justify-content: space-between; text-decoration: none; transition: transform 0.2s;">
                <span style="font-weight: 600; color: var(--text-main);">Faculties</span>
                <span class="badge badge-primary"><?= $facultyCount ?></span>
            </a>
            <a href="courses.php" class="dash-ticker-box" style="justify-content: space-between; text-decoration: none; transition: transform 0.2s;">
                <span style="font-weight: 600; color: var(--text-main);">Courses</span>
                <span class="badge badge-success"><?= $courseCount ?></span>
            </a>
            <a href="labs.php" class="dash-ticker-box" style="justify-content: space-between; text-decoration: none; transition: transform 0.2s;">
                <span style="font-weight: 600; color: var(--text-main);">Labs</span>
                <span class="badge badge-info"><?= $labCount ?></span>
            </a>
            <a href="events.php" class="dash-ticker-box" style="justify-content: space-between; text-decoration: none; transition: transform 0.2s;">
                <span style="font-weight: 600; color: var(--text-main);">Events</span>
                <span class="badge badge-warning"><?= $eventCount + $newsCount ?></span>
            </a>
            <a href="rankers.php" class="dash-ticker-box" style="justify-content: space-between; text-decoration: none; transition: transform 0.2s;">
                <span style="font-weight: 600; color: var(--text-main);">Rankers</span>
                <span class="badge badge-primary"><?= $rankerCount ?></span>
            </a>
            <a href="gallery.php" class="dash-ticker-box" style="justify-content: space-between; text-decoration: none; transition: transform 0.2s;">
                <span style="font-weight: 600; color: var(--text-main);">Gallery</span>
                <span class="badge badge-info"><?= $albumCount ?></span>
            </a>
            <a href="pass_rates.php" class="dash-ticker-box" style="justify-content: space-between; text-decoration: none; transition: transform 0.2s;">
                <span style="font-weight: 600; color: var(--text-main);">Pass Rates</span>
                <span class="badge badge-secondary">Matrix</span>
            </a>
            <a href="scholarships.php" class="dash-ticker-box" style="justify-content: space-between; text-decoration: none; transition: transform 0.2s;">
                <span style="font-weight: 600; color: var(--text-main);">Scholarships</span>
                <span class="badge badge-success">Aid</span>
            </a>
            <a href="settings.php" class="dash-ticker-box" style="justify-content: space-between; text-decoration: none; transition: transform 0.2s;">
                <span style="font-weight: 600; color: var(--text-main);">Settings</span>
                <span class="badge badge-secondary">Stats</span>
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
