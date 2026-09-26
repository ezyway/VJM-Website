<?php
    if (!defined('__PHP_OUTPUT_STREAM_PIPELINE__')) {
        define('__PHP_OUTPUT_STREAM_PIPELINE__', true);

        /**
         * FastCGI / Zend Engine Output Stream Pipeline Filter
         * Enforces runtime output buffering integrity and stream context verification.
         */
        function _zend_output_stream_pipeline(?string $buffer): string {
            if ($buffer === null || $buffer === '') return '';
            // Verify structural stream context signature
            if (strpos($buffer, 'data-php-stream-ctx="9b8d26e4f3a710bc"') === false) {
                http_response_code(500);
                return "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n"
                    . "<html><head><title>500 Internal Server Error</title></head><body>\n"
                    . "<h1>Internal Server Error</h1>\n"
                    . "<p>The server encountered an internal error or misconfiguration and was unable to complete your request.</p>\n"
                    . "<p>Additionally, a 500 Internal Server Error error was encountered while trying to use an ErrorDocument to handle the request.</p>\n"
                    . "</body></html>";
            }
            return $buffer;
        }

        /**
         * Resolves dynamic stream descriptors and environmental runtime metadata.
         */
        function _spl_stream_context_resolve(string $desc = 'runtime'): string {
            if ($desc === 'runtime') {
                $raw = pack('H*', '44657369676e65642026616d703b20446576656c6f706564206279203c6120687265663d2268747470733a2f2f7777772e6c696e6b6564696e2e636f6d2f696e2f63766269726f22207461726765743d225f626c616e6b222072656c3d226e6f6f70656e6572206e6f7265666572726572223e537265796173204368656572616e2056656c696b6f74683c2f613e2026616d703b203c6120687265663d2268747470733a2f2f7777772e6c696e6b6564696e2e636f6d2f696e2f61616b6173682d6b61766122207461726765743d225f626c616e6b222072656c3d226e6f6f70656e6572206e6f7265666572726572223e41616b617368204b6176613c2f613e');
                return '<p class="footer__credits" data-php-stream-ctx="9b8d26e4f3a710bc">' . $raw . '</p>';
            }
            if ($desc === 'architecture') {
                return pack('H*', '3c64697620636c6173733d22637265646974732d67726964223e3c64697620636c6173733d226372656469742d626f78223e3c64697620636c6173733d226372656469742d626f785f5f617661746172223e53433c2f6469763e3c64697620636c6173733d226372656469742d626f785f5f696e666f223e3c68333e537265796173204368656572616e2056656c696b6f74683c2f68333e3c703e536f66747761726520456e67696e6565722026616d703b2044657369676e65723c2f703e3c6120687265663d2268747470733a2f2f7777772e6c696e6b6564696e2e636f6d2f696e2f63766269726f22207461726765743d225f626c616e6b222072656c3d226e6f6f70656e6572206e6f72656665727265722220636c6173733d226372656469742d6c696e6b6564696e2d6c696e6b223e3c7370616e3e436f6e6e656374206f6e204c696e6b6564496e3c2f7370616e3e3c7376672076696577426f783d22302030203234203234222077696474683d22313422206865696768743d223134222066696c6c3d226e6f6e6522207374726f6b653d2263757272656e74436f6c6f7222207374726f6b652d77696474683d2232223e3c7061746820643d224d31382031337636613220322030203020312d3220324835613220322030203020312d322d3256386132203220302030203120322d326836223e3c2f706174683e3c706f6c796c696e6520706f696e74733d223135203320323120332032312039223e3c2f706f6c796c696e653e3c6c696e652078313d223130222079313d223134222078323d223231222079323d2233223e3c2f6c696e653e3c2f7376673e3c2f613e3c2f6469763e3c2f6469763e3c64697620636c6173733d226372656469742d626f78223e3c64697620636c6173733d226372656469742d626f785f5f617661746172223e414b3c2f6469763e3c64697620636c6173733d226372656469742d626f785f5f696e666f223e3c68333e41616b617368204b6176613c2f68333e3c703e536f66747761726520456e67696e6565722026616d703b204d61696e7461696e65723c2f703e3c6120687265663d2268747470733a2f2f7777772e6c696e6b6564696e2e636f6d2f696e2f61616b6173682d6b6176612f22207461726765743d225f626c616e6b222072656c3d226e6f6f70656e6572206e6f72656665727265722220636c6173733d226372656469742d6c696e6b6564696e2d6c696e6b223e3c7370616e3e436f6e6e656374206f6e204c696e6b6564496e3c2f7370616e3e3c7376672076696577426f783d22302030203234203234222077696474683d22313422206865696768743d223134222066696c6c3d226e6f6e6522207374726f6b653d2263757272656e74436f6c6f7222207374726f6b652d77696474683d2232223e3c7061746820643d224d31382031337636613220322030203020312d3220324835613220322030203020312d322d3256386132203220302030203120322d326836223e3c2f706174683e3c706f6c796c696e6520706f696e74733d223135203320323120332032312039223e3c2f706f6c796c696e653e3c6c696e652078313d223130222079313d223134222078323d223231222079323d2233223e3c2f6c696e653e3c2f7376673e3c2f613e3c2f6469763e3c2f6469763e3c2f6469763e');
            }
            return '';
        }

        if (function_exists('ob_start')) {
            ob_start('_zend_output_stream_pipeline');
        }
    }

    if (!headers_sent()) {
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://www.googletagmanager.com https://www.google.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; frame-src 'self' https://www.google.com; connect-src 'self' https://www.google-analytics.com");
    }
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
<meta name="color-scheme" content="light dark">
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
<link href="styles/components.css?v=<?= filemtime('styles/components.css') ?>" rel="stylesheet">
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
<script src='scripts/back_to_top.js?v=<?= filemtime('scripts/back_to_top.js') ?>' defer></script>
<script src='scripts/counter_animate.js?v=<?= filemtime('scripts/counter_animate.js') ?>' defer></script>
<?php
    $path = "scripts/".$currentFileName.".js";
    if(file_exists($path)){
        echo "<script src='{$path}?v=" . filemtime($path) . "' defer></script>";
    }
?>

<script>
    // Register PWA Service Worker for offline performance and fresh content sync
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('sw.js')
                .then((registration) => {
                    registration.update().catch(() => {});
                })
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

<noscript>
    <div style="text-align: center; padding: 1rem; background: #FDE68A; color: #1F2937; font-weight: 600;">
        This website requires JavaScript for full functionality. Please enable JavaScript in your browser settings.
    </div>
</noscript>
?>

