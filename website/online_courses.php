<?php
    $meta_description = "Access free government-certified online courses and MOOC portals including SWAYAM, AICTE, and ATAL Academy at Shri V.J. Modha College, Porbandar.";

    $platforms = [
        [
            "name" => "SWAYAM National MOOCs Portal",
            "provider" => "Ministry of Education, Government of India",
            "badge" => "National MOOCs",
            "desc" => "Comprehensive online courses from school to post-graduate level created by premier faculties across India with academic credit transfer.",
            "logo" => "assets/photos/online_courses/Swayam.png",
            "link" => "https://swayam.gov.in/"
        ],
        [
            "name" => "AICTE Free Learning Support",
            "provider" => "All India Council for Technical Education",
            "badge" => "Technical Upskilling",
            "desc" => "Free access to 40+ curated ed-tech learning products and certificate modules for computing, business, and foundational engineering.",
            "logo" => "assets/photos/online_courses/AICTE.png",
            "link" => "https://free.aicte-india.org/"
        ],
        [
            "name" => "AICTE ATAL Academy",
            "provider" => "AICTE Training & Learning Academy",
            "badge" => "Emerging Technologies",
            "desc" => "Advanced faculty and student development programs in Artificial Intelligence, IoT, Data Sciences, Cybersecurity, and Robotics.",
            "logo" => "assets/photos/online_courses/ATAL.png",
            "link" => "https://atalacademy.aicte.gov.in/"
        ],
        [
            "name" => "CEC Higher Education MOOCs",
            "provider" => "Consortium for Educational Communication",
            "badge" => "Undergraduate Arts & Science",
            "desc" => "High-quality multimedia lectures and courseware for undergraduate humanities, commerce, physical, and chemical sciences.",
            "logo" => "assets/photos/online_courses/CEC.png",
            "link" => "https://swayam.gov.in/CEC"
        ],
        [
            "name" => "NCERT Online Courses",
            "provider" => "National Council of Educational Research & Training",
            "badge" => "Foundational & Academic",
            "desc" => "Standardized subject modules and pedagogical resources for academic enrichment and fundamental subject mastery.",
            "logo" => "assets/photos/online_courses/NCERT.png",
            "link" => "https://swayam.gov.in/NCERT"
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
            "name": "Shri V.J. Modha College - Online Courses & MOOCs",
            "url": "https://shrivjmodhacollege.com/online_courses.php",
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
    <header class="online-hero" id="online-hero">
        <div class="online-hero__overlay">
            <div class="online-hero__content">
                <nav class="online-hero__breadcrumb" aria-label="Breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="online-hero__breadcrumb-sep">/</span>
                    <span>More</span>
                    <span class="online-hero__breadcrumb-sep">/</span>
                    <span aria-current="page">Online Courses</span>
                </nav>
                <span class="online-hero__badge">Value Added E-Learning &amp; Certifications</span>
                <h1 class="online-hero__title">Free Online Courses &amp; MOOCs</h1>
                <p class="online-hero__slogan">॥ विद्या सर्वधनं प्रधानम् ॥</p>
                <p class="online-hero__subtitle">
                    Expand your horizons, gain industry-ready skills, and earn certified academic credits with premier government-backed digital learning platforms.
                </p>
            </div>
        </div>
    </header>


    <!-- ===================================================
         2. Main Online Courses Grid
         =================================================== -->
    <main class="online-section" id="online-main">
        <div class="online-section__container">

            <div class="section-header-glass">
                <span class="section__eyebrow">National Learning Initiatives</span>
                <h2 class="online-section__title">Recognized E-Learning Platforms</h2>
                <p class="online-section__subtitle">Explore and enroll in thousands of free online courses from leading professors and top institutions.</p>
            </div>

            <div class="online-grid">
                <?php foreach ($platforms as $p): ?>
                    <div class="online-card">
                        <div class="online-card__header">
                            <div class="online-card__logo-box">
                                <img src="<?= htmlspecialchars($p['logo']) ?>" alt="<?= htmlspecialchars($p['name']) ?> Logo" class="online-card__logo" loading="lazy" />
                            </div>
                            <span class="online-card__badge"><?= htmlspecialchars($p['badge']) ?></span>
                        </div>

                        <div class="online-card__body">
                            <h3 class="online-card__title"><?= htmlspecialchars($p['name']) ?></h3>
                            <p class="online-card__provider"><?= htmlspecialchars($p['provider']) ?></p>
                            <p class="online-card__desc"><?= htmlspecialchars($p['desc']) ?></p>
                        </div>

                        <div class="online-card__footer">
                            <a href="<?= htmlspecialchars($p['link']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn--primary btn--online">
                                <span>Explore &amp; Enroll Free</span>
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </main>


    <!-- ===================================================
         3. Reusable Call to Action
         =================================================== -->
    <section class="online-cta" id="cta">
        <div class="online-cta__container">
            <div class="online-cta__box">
                <span class="online-cta__badge">Continuous Learning</span>
                <h2 class="online-cta__title">Empower Your Career with In-Demand Skills</h2>
                <p class="online-cta__subtitle">
                    Complement your college degree with verified MOOC certificates and real-world project portfolios.
                </p>
                <div class="online-cta__actions">
                    <a href="courses.php" class="btn btn--primary">View College Programs</a>
                    <a href="contact.php" class="btn btn--secondary">Contact Career Counseling</a>
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