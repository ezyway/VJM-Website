<?php
    $currentFileName = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
    $title_data = [
        "index" => "Shri V.J. Modha College",
        "about" => "About Us",
        "anti_ragging" => "Anti-Ragging Committee",
        "contact" => "Contact Us",
        "courses" => "Courses",
        "disclaimer" => "Disclaimer",
        "e_mag" => "E-Magazines",
        "faculties" => "Faculties",
        "gallery" => "Gallery",
        "labs" => isset($lab["name"]) ? $lab["name"] : null." - Facilities",
        "online_courses" => "Free Online Courses",
        "placement" => "Placement",
        "scholarship" => "Scholarships"
    ];
?>

<!-- Document Title -->
<title> <?= $title_data[$currentFileName]; ?> </title>

<!-- ===================================================
Metadata & Document Setup
=================================================== -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<!-- Meta description for SEO -->
<meta name="description" content="<?= isset($meta_description) ? htmlspecialchars($meta_description) : 'Shri V.J. Modha College - Empowering students for a better future.' ?>">



<!-- Favicon -->
<link rel="shortcut icon" type="image/x-icon" href="assets/logo.ico">

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
Scripts
=================================================== -->
<script src='scripts/nav.js?v=<?= filemtime('scripts/nav.js') ?>' defer></script>
<?php
    $path = "scripts/".$currentFileName.".js";
    if(file_exists($path)){
        echo "<script src='{$path}?v=" . filemtime($path) . "' defer></script>";
    }
?>



<!-- ===================================================
Google Analytics
=================================================== -->
<?php
    $host = $_SERVER['HTTP_HOST'];
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
