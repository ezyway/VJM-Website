<?php
/**
 * Admin Panel Common Header & Navigation
 */

require_once __DIR__ . '/auth.php';
requireAuth();

$currentAdmin = getCurrentAdmin();
$flash = getFlash();
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');

$navItems = [
    ['slug' => 'index.php', 'title' => 'Dashboard', 'icon' => '<rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect>'],
    ['section' => 'Academic & Staff'],
    ['slug' => 'faculties.php', 'title' => 'Faculty Directory', 'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>'],
    ['slug' => 'courses.php', 'title' => 'Courses & Programs', 'icon' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>'],
    ['slug' => 'labs.php', 'title' => 'Labs & Facilities', 'icon' => '<rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line>'],
    ['slug' => 'pass_rates.php', 'title' => 'Pass Rates', 'icon' => '<line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line>'],
    ['section' => 'Campus & Media'],
    ['slug' => 'events.php', 'title' => 'Events & Notices', 'icon' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line>'],
    ['slug' => 'rankers.php', 'title' => 'Pride Laurels (Rankers)', 'icon' => '<circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>'],
    ['slug' => 'gallery.php', 'title' => 'Photo Gallery & Albums', 'icon' => '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline>'],
    ['slug' => 'testimonials.php', 'title' => 'Testimonials', 'icon' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>'],
    ['slug' => 'magazines.php', 'title' => 'E-Magazines & PDFs', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline>'],
    ['section' => 'Configuration'],
    ['slug' => 'scholarships.php', 'title' => 'Scholarships & Aid', 'icon' => '<circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path>'],
    ['slug' => 'settings.php', 'title' => 'Site Settings & Stats', 'icon' => '<circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>']
];
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars(getCSRFToken()) ?>">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' : '' ?>Admin CMS | Shri V.J. Modha College</title>
    <link rel="icon" type="image/x-icon" href="../assets/logo.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css?v=<?= time() ?>">
</head>
<body>

<?php if ($flash): ?>
<div class="toast-container">
    <div class="toast <?= htmlspecialchars($flash['type']) ?>">
        <span><?= htmlspecialchars($flash['message']) ?></span>
    </div>
</div>
<?php endif; ?>

<div class="admin-layout">
    <!-- Sidebar Navigation -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <img src="../assets/logo.ico" alt="Logo" class="sidebar-logo">
            <div>
                <div class="sidebar-title">VJM College</div>
                <div class="sidebar-subtitle">CMS Administration</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <?php foreach ($navItems as $item): ?>
                <?php if (isset($item['section'])): ?>
                    <div class="nav-section-title"><?= htmlspecialchars($item['section']) ?></div>
                <?php else: ?>
                    <a href="<?= $item['slug'] ?>" class="nav-link <?= ($currentPage === $item['slug']) ? 'active' : '' ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <?= $item['icon'] ?>
                        </svg>
                        <span><?= htmlspecialchars($item['title']) ?></span>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>

        <div class="sidebar-footer">
            <div class="user-badge">
                <div class="user-avatar"><?= strtoupper(substr($currentAdmin['name'], 0, 1)) ?></div>
                <div>
                    <div class="user-name"><?= htmlspecialchars($currentAdmin['name']) ?></div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
            <a href="logout.php" title="Logout" class="btn btn-secondary btn-icon" style="color: #ef4444;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Topbar -->
        <header class="admin-topbar">
            <div class="topbar-left">
                <button id="mobileSidebarToggle" class="btn btn-secondary btn-icon" style="display: none;" aria-label="Toggle Menu">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
                <h1 class="topbar-title"><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Admin Dashboard' ?></h1>
            </div>

            <div class="topbar-actions">
                <a href="../index.php" target="_blank" class="btn btn-secondary btn-sm" title="View Public Website">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    <span>View Website</span>
                </a>
                <button id="themeToggleBtn" class="btn btn-secondary btn-icon" title="Toggle Dark/Light Mode">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                </button>
            </div>
        </header>

        <main class="admin-content">
