<?php
/**
 * Admin Dashboard
 */

$pageTitle = 'Dashboard Overview';
require_once __DIR__ . '/includes/header.php';

$db = getDB();

// Fetch summary metrics
$facultyCount = $db->query('SELECT COUNT(*) FROM faculties')->fetchColumn();
$courseCount  = $db->query('SELECT COUNT(*) FROM courses')->fetchColumn();
$labCount     = $db->query('SELECT COUNT(*) FROM labs')->fetchColumn();
$eventCount   = $db->query('SELECT COUNT(*) FROM events')->fetchColumn();
$rankerCount  = $db->query('SELECT COUNT(*) FROM rankers')->fetchColumn();
$albumCount   = $db->query('SELECT COUNT(*) FROM gallery_albums')->fetchColumn();
$photoCount   = $db->query('SELECT COUNT(*) FROM gallery_photos')->fetchColumn();
$testimCount  = $db->query('SELECT COUNT(*) FROM testimonials')->fetchColumn();
$magCount     = $db->query('SELECT COUNT(*) FROM magazines')->fetchColumn();

// Fetch recent events
$recentEvents = $db->query('SELECT * FROM events ORDER BY id DESC LIMIT 5')->fetchAll();
// Fetch recent rankers
$recentRankers = $db->query('SELECT * FROM rankers ORDER BY id DESC LIMIT 4')->fetchAll();
?>

<!-- Statistics Overview -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Faculty Members</div>
            <div class="stat-value"><?= (int)$facultyCount ?></div>
        </div>
        <div class="stat-icon primary">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Academic Programs</div>
            <div class="stat-value"><?= (int)$courseCount ?></div>
        </div>
        <div class="stat-icon success">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Events &amp; Notices</div>
            <div class="stat-value"><?= (int)$eventCount ?></div>
        </div>
        <div class="stat-icon warning">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Student Laurels</div>
            <div class="stat-value"><?= (int)$rankerCount ?></div>
        </div>
        <div class="stat-icon purple">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">Gallery Photos</div>
            <div class="stat-value"><?= (int)$photoCount ?> <span style="font-size: 13px; color: var(--text-muted); font-weight: 500;">(<?= (int)$albumCount ?> albums)</span></div>
        </div>
        <div class="stat-icon info">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-label">E-Magazines</div>
            <div class="stat-value"><?= (int)$magCount ?></div>
        </div>
        <div class="stat-icon primary">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
        </div>
    </div>
</div>

<!-- Quick Action Shortcuts -->
<div class="panel">
    <div class="panel-header">
        <div class="panel-title">Quick Management Shortcuts</div>
    </div>
    <div class="panel-body" style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="faculties.php?action=create" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Faculty Member
        </a>
        <a href="events.php?action=create" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Post Event / Notice
        </a>
        <a href="rankers.php?action=create" class="btn btn-primary btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Add Student Ranker
        </a>
        <a href="gallery.php" class="btn btn-secondary btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            Upload Gallery Photos
        </a>
        <a href="pass_rates.php" class="btn btn-secondary btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line></svg>
            Update Pass Rates
        </a>
        <a href="settings.php" class="btn btn-secondary btn-sm">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle></svg>
            Site Settings &amp; Counters
        </a>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 24px;">
    <!-- Recent Events -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Recent Events &amp; Notices</div>
            <a href="events.php" class="btn btn-secondary btn-sm">View All</a>
        </div>
        <div class="panel-body" style="padding: 0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Badge</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentEvents as $ev): ?>
                    <tr>
                        <td>
                            <strong style="color: var(--text-main);"><?= htmlspecialchars($ev['title']) ?></strong>
                        </td>
                        <td>
                            <span class="badge badge-<?= $ev['event_type'] === 'event' ? 'primary' : 'info' ?>">
                                <?= strtoupper($ev['event_type']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-secondary"><?= htmlspecialchars($ev['badge']) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pride of College Rankers -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Top Academic Rankers</div>
            <a href="rankers.php" class="btn btn-secondary btn-sm">View All</a>
        </div>
        <div class="panel-body" style="padding: 0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Rank</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentRankers as $rk): ?>
                    <tr>
                        <td style="display: flex; align-items: center; gap: 10px;">
                            <?php if (!empty($rk['image'])): ?>
                                <img src="../<?= htmlspecialchars($rk['image']) ?>" alt="Ranker" class="preview-avatar">
                            <?php endif; ?>
                            <strong><?= htmlspecialchars($rk['name']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($rk['course']) ?> <?= $rk['semester'] ? '(Sem ' . htmlspecialchars($rk['semester']) . ')' : '' ?></td>
                        <td>
                            <span class="badge badge-warning"><?= htmlspecialchars(str_replace('^', '', $rk['rank_text'])) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
