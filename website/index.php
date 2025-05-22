<?php
    // Use this to add meta descriptions individually in each page
    $meta_description = "Scholarships available for merit and need-based students."; 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Schema.org Structured data for SEO benefits -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "College",
            "name": "Shri V.J. Modha College",
            "url": "https://shrivjmodhacollege.com",
            "logo": "https://shrivjmodhacollege.com/assets/logo.ico",
            "description": "Shri V.J. Modha College - Empowering students for a better future.",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "Vidhyadham, Chhaya-Birla Road, Nr. Pakshi Abhiyaran, Porbandar, Gujarat, 360575",
                "addressLocality": "Porbandar",
                "addressRegion": "Gujarat",
                "postalCode": "360575",
                "addressCountry": "IN"
            }
        }
    </script>

    <?php include("header.php"); ?>
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
            <source src="assets/videos/banner-video.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <div class="video-banner__overlay">
            <div class="video-banner__content">
                <h1>Shri V.J. Modha College</h1>
                <p>॥ विद्यार्थी लभते विद्यां ॥</p>
            </div>
        </div>

        <div class="scroll-down">
            <span class="arrow"></span>
            <span class="arrow"></span>
        </div>
    </section>


    <?php include("components/counter.html"); ?>

    <?php include("components/chairman_pride.php"); ?>

    <?php include("components/events_news.html"); ?>

    <?php include("components/academic_pass_rates.html"); ?>

    <?php include("components/testimonials.html"); ?>

    <?php include("components/photos.php"); ?>


    <!-- Back to Top Button with a modern arrow icon -->
    <button id="backToTop" aria-label="Back to top">
        <!-- Inline SVG arrow icon -->
        <svg viewBox="0 0 24 24">
            <path d="M12 4l-8 8h6v8h4v-8h6z"></path>
        </svg>
    </button>

    <!-- ===================================================
         Footer Section
         - Included via PHP for consistency across pages.
         =================================================== -->
    <?php include("footer.html"); ?>

</body>

</html>