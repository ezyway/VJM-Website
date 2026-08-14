<?php
    $meta_description = "Read and download annual college e-magazines, student creative writing, poems, and artistic publications from Shri V.J. Modha College, Porbandar.";

    $magazines = [
        [
            "year" => "2021",
            "title" => "Shri V.J. Modha College Annual E-Magazine 2021",
            "edition" => "Edition 2021",
            "theme" => "Resilience, Innovation & Digital Transformation",
            "file" => "assets/e_mags/mag_2021.pdf",
            "size" => "~31.6 MB",
            "pages" => "Full Edition",
            "badge" => "Latest Edition"
        ],
        [
            "year" => "2020",
            "title" => "Shri V.J. Modha College Annual E-Magazine 2020",
            "edition" => "Edition 2020",
            "theme" => "Academic Excellence, Creativity & Cultural Heritage",
            "file" => "assets/e_mags/mag_2020.pdf",
            "size" => "~21.6 MB",
            "pages" => "Full Edition",
            "badge" => "Archive"
        ]
    ];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "EducationalOrganization",
            "name": "Shri V.J. Modha College - E-Magazines",
            "url": "https://shrivjmodhacollege.com/e_mag.php",
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
    <header class="emag-hero" id="emag-hero">
        <div class="emag-hero__overlay">
            <div class="emag-hero__content">
                <nav class="emag-hero__breadcrumb" aria-label="Breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="emag-hero__breadcrumb-sep">/</span>
                    <span>More</span>
                    <span class="emag-hero__breadcrumb-sep">/</span>
                    <span aria-current="page">E-Magazine</span>
                </nav>
                <span class="emag-hero__badge">Annual Publications &amp; Creative Expressions</span>
                <h1 class="emag-hero__title">College E-Magazines</h1>
                <p class="emag-hero__slogan">॥ साहित्य संगीत कला विहीनः ॥</p>
                <p class="emag-hero__subtitle">
                    A vibrant chronicle showcasing student literature, original artwork, faculty articles, event photo retrospectives, and academic achievements.
                </p>
            </div>
        </div>
    </header>


    <!-- ===================================================
         2. Main E-Magazines Grid
         =================================================== -->
    <main class="emag-section" id="emag-main">
        <div class="emag-section__container">

            <div class="section-header-glass">
                <span class="section__eyebrow">College Publications</span>
                <h2 class="emag-section__title">Explore Published Editions</h2>
                <p class="emag-section__subtitle">Read our annual magazines online or download the full PDF to your device.</p>
            </div>

            <div class="emag-grid">
                <?php foreach ($magazines as $mag): ?>
                    <div class="emag-card">
                        <div class="emag-card__book-cover">
                            <div class="emag-card__spine"></div>
                            <div class="emag-card__cover-content">
                                <span class="emag-card__year"><?= htmlspecialchars($mag['year']) ?></span>
                                <div class="emag-card__icon-box">
                                    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                    </svg>
                                </div>
                                <span class="emag-card__cover-text">V.J. Modha Magazine</span>
                            </div>
                        </div>

                        <div class="emag-card__body">
                            <div class="emag-card__tags">
                                <span class="emag-tag emag-tag--badge"><?= htmlspecialchars($mag['badge']) ?></span>
                                <span class="emag-tag emag-tag--format">PDF • <?= htmlspecialchars($mag['size']) ?></span>
                            </div>

                            <h3 class="emag-card__title"><?= htmlspecialchars($mag['title']) ?></h3>
                            <p class="emag-card__theme"><strong>Theme:</strong> <?= htmlspecialchars($mag['theme']) ?></p>

                            <div class="emag-card__actions">
                                <a href="<?= htmlspecialchars($mag['file']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn--primary btn--emag">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <span>Read Online</span>
                                </a>
                                <a href="<?= htmlspecialchars($mag['file']) ?>" download="VJ_Modha_Magazine_<?= htmlspecialchars($mag['year']) ?>.pdf" class="btn btn--outline btn--emag">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    <span>Download PDF</span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </main>


    <!-- ===================================================
         3. Reusable Call to Action
         =================================================== -->
    <section class="emag-cta" id="cta">
        <div class="emag-cta__container">
            <div class="emag-cta__box">
                <span class="emag-cta__badge">Student Editorial Board</span>
                <h2 class="emag-cta__title">Submit Your Creative Work</h2>
                <p class="emag-cta__subtitle">
                    Students and faculty are invited to contribute articles, poems, technology research, and artwork for the upcoming edition.
                </p>
                <div class="emag-cta__actions">
                    <a href="contact.php" class="btn btn--primary">Contact Editorial Team</a>
                    <a href="gallery.php" class="btn btn--secondary">View Photo Gallery</a>
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
    <?php include("footer.html"); ?>

</body>

</html>