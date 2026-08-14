<?php
    // Meta description for SEO
    $meta_description = "Shri V.J. Modha College, Porbandar — Empowering students through quality education, modern facilities, experienced faculty, and industry-oriented programs."; 
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
         =================================================== -->
    <?php include("nav.html"); ?>


    <!-- ===================================================
         Hero Video Banner Section
         =================================================== -->
    <section class="video-banner" id="video-banner">
        <video autoplay muted loop playsinline class="video-banner__background" preload="metadata">
            <source src="assets/videos/banner-video.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <div class="video-banner__overlay">
            <div class="video-banner__content">
                <span class="video-banner__badge">Welcome to Excellence</span>
                <h1 class="video-banner__title">Shri V.J. Modha College</h1>
                <p class="video-banner__slogan">॥ विद्यार्थी लभते विद्यां ॥</p>
                <p class="video-banner__subtitle">Empowering Minds • Inspiring Futures • Leading Education in Porbandar</p>
                
                <div class="video-banner__actions">
                    <a href="courses.php" class="btn btn--primary">Explore Programs</a>
                    <a href="contact.php" class="btn btn--secondary">Get in Touch</a>
                </div>
            </div>
        </div>

        <button class="scroll-down" aria-label="Scroll to content">
            <span class="scroll-down__text">Explore</span>
            <span class="arrow"></span>
            <span class="arrow"></span>
        </button>
    </section>


    <?php include("components/counter.html"); ?>

    <?php include("components/chairman_pride.php"); ?>

    <?php include("components/events_news.html"); ?>

    <?php include("components/academic_pass_rates.html"); ?>

    <?php include("components/testimonials.html"); ?>

    <?php include("components/photos.php"); ?>


    <!-- Back to Top Button with smooth floating icon -->
    <button id="backToTop" class="back-to-top" aria-label="Back to top">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
            <path d="M12 4l-8 8h6v8h4v-8h6z"></path>
        </svg>
    </button>

    <!-- ===================================================
         Footer Section
         =================================================== -->
    <?php include("footer.html"); ?>

</body>

</html>