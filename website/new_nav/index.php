

<!DOCTYPE html>
<html lang="en">

<head>


    <!-- ===================================================
    Metadata & Document Setup
    =================================================== -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">



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
    <link href="../styles/global.css" rel="stylesheet">
    <link href="nav.css" rel="stylesheet">
    <link href="../styles/footer.css" rel="stylesheet">
    <link href="../styles/index.css" rel="stylesheet">


    <!-- ===================================================
    Scripts
    =================================================== -->
    <script src='../scripts/nav.js' defer></script>
    <script src='../index.js' defer></script>

</head>

<body>
    <!-- ===================================================
         Navigation Section
         - Included via PHP to allow reusability across pages.
         =================================================== -->
    <?php include("nav.html"); ?>


    <!-- ===================================================
         Banner Video Section
         - Fullscreen video banner with an overlay message.
         =================================================== -->
    <section class="video-banner" id="video-banner">
        <video autoplay muted loop playsinline class="video-banner__background" preload="metadata">
            <source src="../assets/videos/banner-video.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <!-- <div class="video-banner__overlay">
            <h1>Shri V.J. Modha College</h1>
            <p>॥ विद्यार्थी लभते विद्यां ॥</p>
        </div> -->

        <div class="scroll-down">
            <span class="arrow"></span>
            <span class="arrow"></span>
        </div>
    </section>


    <!-- Back to Top Button with a modern arrow icon -->
    <button id="backToTop" aria-label="Back to top">
        <!-- Inline SVG arrow icon -->
        <svg viewBox="0 0 24 24">
            <path d="M12 4l-8 8h6v8h4v-8h6z"></path>
        </svg>
    </button>

    <?php include("../components/counter.html"); ?>
    <?php include("../components/academic_pass_rates.html"); ?>

</body>

</html>