<?php
    $currentFileName = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
    $title_data = [
        "index" => "Shri V.J. Modha College",
        "about" => "About Us",
        "anti_ragging" => "Anti-Ragging Committee",
        "contact" => "Contact Us",
        "courses" => "Academic Courses & Programs",
        "disclaimer" => "Disclaimer & Policies",
        "e_mag" => "Annual College E-Magazines",
        "faculties" => "Faculty Directory",
        "gallery" => "Photo Gallery & Campus Life",
        "labs" => isset($lab["name"]) ? $lab["name"] . " - Facilities" : "Laboratories & Facilities",
        "online_courses" => "Free Online MOOCs & Courses",
        "placement" => "Placement & Career Desk",
        "scholarship" => "Scholarships & Government Schemes"
    ];

    // Compute dynamic title
    $rawTitle = $title_data[$currentFileName] ?? "Shri V.J. Modha College";
    if ($currentFileName === "index") {
        $page_full_title = "Shri V.J. Modha College — Empowering Higher Education in Porbandar";
    } else {
        $page_full_title = $rawTitle . " | Shri V.J. Modha College, Porbandar";
    }

    // Default Meta Description
    $page_description = isset($meta_description) 
        ? $meta_description 
        : "Shri V.J. Modha College of Information Technology, Porbandar — Offering BCA, B.Sc, BBA, B.Com, BSW, M.Com, and M.Sc IT with world-class academic infrastructure.";

    // Base URL & Canonical Calculation
    $site_base = "https://shrivjmodhacollege.com";
    $script_path = basename($_SERVER['PHP_SELF']);
    
    if ($currentFileName === "index") {
        $canonical_url = $site_base . "/";
    } else {
        $queryString = "";
        if (!empty($_SERVER['QUERY_STRING'])) {
            $safeQuery = htmlspecialchars($_SERVER['QUERY_STRING']);
            $queryString = "?" . $safeQuery;
        }
        $canonical_url = $site_base . "/" . $script_path . $queryString;
    }

    $og_image = $site_base . "/assets/background.png";
?>

<!-- Document Title -->
<title><?= htmlspecialchars($page_full_title); ?></title>

<!-- ===================================================
     Metadata & SEO Setup
     =================================================== -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="description" content="<?= htmlspecialchars($page_description); ?>">
<meta name="keywords" content="Shri V.J. Modha College, VJM College Porbandar, BCA Porbandar, B.Sc College, BBA, B.Com, BSW, M.Sc IT, Bhakta Kavi Narsinh Mehta University, BKNMU Affiliated College, Higher Education Porbandar">
<meta name="author" content="Shri V.J. Modha Educational &amp; Charitable Trust">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

<!-- Canonical Link -->
<link rel="canonical" href="<?= htmlspecialchars($canonical_url); ?>">

<!-- ===================================================
     Open Graph (Facebook, WhatsApp, LinkedIn)
     =================================================== -->
<meta property="og:site_name" content="Shri V.J. Modha College, Porbandar">
<meta property="og:title" content="<?= htmlspecialchars($page_full_title); ?>">
<meta property="og:description" content="<?= htmlspecialchars($page_description); ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= htmlspecialchars($canonical_url); ?>">
<meta property="og:image" content="<?= htmlspecialchars($og_image); ?>">
<meta property="og:image:secure_url" content="<?= htmlspecialchars($og_image); ?>">
<meta property="og:image:type" content="image/png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="Shri V.J. Modha College Campus &amp; Infrastructure">
<meta property="og:locale" content="en_IN">

<!-- ===================================================
     Twitter Cards
     =================================================== -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($page_full_title); ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($page_description); ?>">
<meta name="twitter:image" content="<?= htmlspecialchars($og_image); ?>">
<meta name="twitter:image:alt" content="Shri V.J. Modha College Campus &amp; Infrastructure">

<!-- ===================================================
     Mobile & App Shell Metadata
     =================================================== -->
<meta name="theme-color" content="#155C4F">
<meta name="msapplication-TileColor" content="#155C4F">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="VJM College">

<!-- Favicon & Touch Icons -->
<link rel="shortcut icon" type="image/x-icon" href="assets/logo.ico">
<link rel="apple-touch-icon" href="assets/logo.ico">

<!-- Web App Manifest (PWA) -->
<link rel="manifest" href="manifest.json">

<!-- ===================================================
     Fonts & Stylesheets
     =================================================== -->

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

<!-- Custom Styles -->
<link href="styles/global.css?v=<?= filemtime('styles/global.css') ?>" rel="stylesheet">
<link href="styles/nav.css?v=<?= filemtime('styles/nav.css') ?>" rel="stylesheet">
<link href="styles/footer.css?v=<?= filemtime('styles/footer.css') ?>" rel="stylesheet">
<?php
    $path = "styles/".$currentFileName.".css";
    if(file_exists($path)){
        echo "<link href='{$path}?v=" . filemtime($path) . "' rel='stylesheet'>";
    }
?>

<!-- ===================================================
     Scripts & PWA Service Worker Registration
     =================================================== -->
<script src='scripts/bg_particles.js?v=<?= filemtime('scripts/bg_particles.js') ?>' defer></script>
<script src='scripts/nav.js?v=<?= filemtime('scripts/nav.js') ?>' defer></script>
<?php
    $path = "scripts/".$currentFileName.".js";
    if(file_exists($path)){
        echo "<script src='{$path}?v=" . filemtime($path) . "' defer></script>";
    }
?>

<script>
    // Register PWA Service Worker for offline performance
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('sw.js')
                .catch((err) => console.debug('ServiceWorker registration note:', err));
        });
    }
</script>

<!-- ===================================================
     Google Analytics
     =================================================== -->
<?php
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if ($host !== 'localhost' && !preg_match('/^192\.168\./', $host)) {
        // Production only: output GA script
        ?>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-TPLVFB56YC"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-TPLVFB56YC');
        </script>
        <?php
    }
?>

<!-- ===================================================
     Announcement Ticker (managed in Admin CMS)
     =================================================== -->
<?php
    $vjmTicker = '';
    try {
        require_once __DIR__ . '/admin/includes/db.php';
        $vjmTicker = trim(getSetting('announcement_banner', ''));
    } catch (Exception $e) { $vjmTicker = ''; }

    if ($vjmTicker !== '') { ?>
    <style>
        .vjm-ticker{position:sticky;top:0;z-index:10000;display:flex;align-items:center;justify-content:center;gap:12px;
            padding:9px 44px 9px 16px;background:linear-gradient(90deg,#155C4F,#0f4a3e);color:#fff;font-family:'Montserrat',sans-serif;
            font-size:13.5px;font-weight:600;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,.25)}
        .vjm-ticker__dot{width:8px;height:8px;border-radius:50%;background:#4ade80;flex:0 0 auto;animation:vjmTickerPulse 1.6s infinite}
        @keyframes vjmTickerPulse{0%,100%{opacity:1}50%{opacity:.35}}
        .vjm-ticker__close{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:0;color:#ffffffb0;
            font-size:17px;line-height:1;cursor:pointer;padding:4px}
        .vjm-ticker__close:hover{color:#fff}
    </style>
    <script>
    (function () {
        var text = <?= json_encode($vjmTicker) ?>;
        var key = 'vjm_ticker_dismissed';
        try {
            var seen = sessionStorage.getItem(key);
            if (seen === text) return; // re-shows when the announcement text changes
        } catch (e) {}
        function mount() {
            var bar = document.createElement('div');
            bar.className = 'vjm-ticker';
            bar.innerHTML = '<span class="vjm-ticker__dot"></span><span></span>' +
                '<button class="vjm-ticker__close" aria-label="Dismiss announcement">&times;</button>';
            bar.children[1].textContent = text;
            bar.querySelector('.vjm-ticker__close').addEventListener('click', function () {
                bar.remove();
                try { sessionStorage.setItem(key, text); } catch (e) {}
            });
            document.body.prepend(bar);
        }
        if (document.body) { mount(); } else { document.addEventListener('DOMContentLoaded', mount); }
    })();
    </script>
    <?php } ?>
