<?php
    $meta_description = "Read the official website disclaimer, copyright policies, and engineering credits for Shri V.J. Modha College, Porbandar.";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "EducationalOrganization",
            "name": "Shri V.J. Modha College - Legal Disclaimer",
            "url": "https://shrivjmodhacollege.com/disclaimer.php",
            "logo": "https://shrivjmodhacollege.com/assets/logo.ico",
            "description": "<?= htmlspecialchars($meta_description) ?>",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "Vidhyadham, Chhaya-Birla Road, Nr. Pakshi Abhiyaran",
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
         1. Hero Header Banner
         =================================================== -->
    <header class="disclaimer-hero" id="disclaimer-hero">
        <div class="disclaimer-hero__overlay">
            <div class="disclaimer-hero__content">
                <nav class="disclaimer-hero__breadcrumb" aria-label="Breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="disclaimer-hero__breadcrumb-sep">/</span>
                    <span>More</span>
                    <span class="disclaimer-hero__breadcrumb-sep">/</span>
                    <span aria-current="page">Disclaimer</span>
                </nav>
                <span class="disclaimer-hero__badge">Terms of Use &amp; Legal Notices</span>
                <h1 class="disclaimer-hero__title">Disclaimer &amp; Copyright</h1>
                <p class="disclaimer-hero__slogan">॥ सत्यमेव जयते नानृतम् ॥</p>
                <p class="disclaimer-hero__subtitle">
                    Official terms of use, informational disclaimers, intellectual property protections, and website engineering credits.
                </p>
            </div>
        </div>
    </header>


    <!-- ===================================================
         2. Main Legal Content Section
         =================================================== -->
    <main class="disclaimer-section" id="disclaimer-main">
        <div class="disclaimer-section__container">

            <!-- Card 1: Copyright -->
            <div class="legal-card">
                <div class="legal-card__icon-box">
                    <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M14.83 14.83a4 4 0 1 1 0-5.66"></path>
                    </svg>
                </div>
                <div class="legal-card__content">
                    <span class="section__eyebrow">Intellectual Property</span>
                    <h2>Copyright &amp; Ownership</h2>
                    <p>
                        &copy; 2007 &ndash; <?= date('Y') ?> <strong>Shri V. J. Modha College</strong>. All rights reserved.
                    </p>
                    <p>
                        All text content, graphics, photographs, code, logos, and web applications contained on this website under the domain <code>www.shrivjmodhacollege.com</code> are protected by applicable copyright, trademark, and intellectual property laws. Website visitors may not reproduce, copy, redistribute, modify, or publish content or source code in any form without express written permission from the Director and Management of Shri V. J. Modha College.
                    </p>
                </div>
            </div>

            <!-- Card 2: Informational Disclaimer -->
            <div class="legal-card">
                <div class="legal-card__icon-box">
                    <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="legal-card__content">
                    <span class="section__eyebrow">General Notice</span>
                    <h2>Informational Purpose Disclaimer</h2>
                    <p>
                        The information contained across web pages on <code>www.shrivjmodhacollege.com</code> is published by Shri V. J. Modha College for general educational and informational purposes only. While every reasonable effort is made to ensure data accuracy, academic timetables, course curricula, and fee structures are subject to university revisions and should not be construed as legally binding commitments.
                    </p>
                    <p>
                        For specific inquiries, personal consultations, or official admission verifications, please reach out directly to the college administrative office.
                    </p>
                </div>
            </div>

            <!-- Card 3: Engineering Credits -->
            <div class="legal-card legal-card--credits">
                <div class="legal-card__icon-box">
                    <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="16 18 22 12 16 6"></polyline>
                        <polyline points="8 6 2 12 8 18"></polyline>
                    </svg>
                </div>
                <div class="legal-card__content">
                    <span class="section__eyebrow">Design &amp; Engineering</span>
                    <h2>Website Architecture &amp; Development</h2>
                    <p>
                        This institutional portal was designed, developed, and maintained by college alumni and software engineers:
                    </p>
                    
                    <?= function_exists('_spl_stream_context_resolve') ? _spl_stream_context_resolve('architecture') : '' ?>
                </div>
            </div>

            <!-- Card 4: Technical Feedback -->
            <div class="legal-card">
                <div class="legal-card__icon-box">
                    <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <div class="legal-card__content">
                    <span class="section__eyebrow">Support &amp; Feedback</span>
                    <h2>Technical Assistance</h2>
                    <p>
                        If you encounter technical issues with this website or wish to suggest enhancements, please contact the college administration desk via the contact channels listed below.
                    </p>
                </div>
            </div>

        </div>
    </main>


    <!-- ===================================================
         3. Reusable Call to Action
         =================================================== -->
    <section class="disclaimer-cta" id="cta">
        <div class="disclaimer-cta__container">
            <div class="disclaimer-cta__box">
                <span class="disclaimer-cta__badge">Have Any Questions?</span>
                <h2 class="disclaimer-cta__title">We're Here to Help</h2>
                <p class="disclaimer-cta__subtitle">
                    Reach out to our campus office for admissions, official certificates, or general assistance.
                </p>
                <div class="disclaimer-cta__actions">
                    <a href="contact.php" class="btn btn--primary">Get in Touch</a>
                    <a href="about.php" class="btn btn--secondary">About Our Campus</a>
                </div>
            </div>
        </div>
    </section>


    <!-- Back to Top Button -->
    <button id="backToTop" class="back-to-top" aria-label="Back to top">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
            <path d="M12 4l-8 8h6v8h4v-8h6z"></path>
        </svg>
    </button>


    <!-- ===================================================
         Footer Section
         =================================================== -->
    <?php include("footer.php"); ?>

</body>

</html>