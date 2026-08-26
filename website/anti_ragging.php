<?php
    $meta_description = "Learn about the Anti-Ragging Committee, zero-tolerance policies, and student helpline contacts at Shri V.J. Modha College, Porbandar.";

    $committeeMembers = [
        [
            "name" => "Prof. Paresh Savjani",
            "role" => "Head of Committee",
            "designation" => "Incharge Principal & Assoc. Professor",
            "image" => "assets/photos/faculties/Paresh_Savjani.jpg",
            "contact" => "Principal Desk"
        ],
        [
            "name" => "Prof. Krunal Madlani",
            "role" => "Committee Member",
            "designation" => "Assistant Professor, Dept. of IT",
            "image" => "assets/photos/faculties/Madlani_Krunal.jpg",
            "contact" => "Faculty Coordinator"
        ],
        [
            "name" => "Prof. Jyotsna Salet",
            "role" => "Committee Member",
            "designation" => "Associate Professor, Dept. of IT",
            "image" => "assets/photos/faculties/Salet_Jyotsna.jpeg",
            "contact" => "Faculty Coordinator"
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
            "name": "Shri V.J. Modha College - Anti-Ragging Cell",
            "url": "https://shrivjmodhacollege.com/anti_ragging.php",
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
    <header class="anti-ragging-hero" id="anti-ragging-hero">
        <div class="anti-ragging-hero__overlay">
            <div class="anti-ragging-hero__content">
                <nav class="anti-ragging-hero__breadcrumb" aria-label="Breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="anti-ragging-hero__breadcrumb-sep">/</span>
                    <span>More</span>
                    <span class="anti-ragging-hero__breadcrumb-sep">/</span>
                    <span aria-current="page">Anti-Ragging</span>
                </nav>
                <span class="anti-ragging-hero__badge">Campus Discipline &amp; Student Safety</span>
                <h1 class="anti-ragging-hero__title">Anti-Ragging Committee</h1>
                <p class="anti-ragging-hero__slogan">॥ अहिंसा परमो धर्मः ॥</p>
                <p class="anti-ragging-hero__subtitle">
                    Shri V. J. Modha College maintains a strict Zero-Tolerance Policy against ragging in any form, ensuring a safe, supportive, and dignified educational atmosphere.
                </p>
            </div>
        </div>
    </header>


    <!-- ===================================================
         2. Main Policy & Committee Content
         =================================================== -->
    <main class="anti-ragging-section" id="anti-ragging-main">
        <div class="anti-ragging-section__container">

            <!-- Section 1: Zero Tolerance Mandate -->
            <div class="anti-ragging-policy-card">
                <div class="policy-card__icon-box">
                    <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        <path d="M9 12l2 2 4-4"></path>
                    </svg>
                </div>
                <div class="policy-card__content">
                    <span class="section__eyebrow">UGC &amp; University Regulations</span>
                    <h2>Zero Tolerance Campus Policy</h2>
                    <p>
                        In accordance with the Supreme Court of India directives and UGC Regulations on Curbing the Menace of Ragging in Higher Educational Institutions, any disorderly conduct, teasing, handling with rudeness, or psychological harassment is strictly prohibited on campus.
                    </p>
                    <div class="policy-badges">
                        <span class="policy-badge">100% Ragging-Free Campus</span>
                        <span class="policy-badge">Confidential Grievance Redressal</span>
                        <span class="policy-badge">Immediate Legal Compliance</span>
                    </div>
                </div>
            </div>

            <!-- Section 2: Committee Members -->
            <div class="committee-block">
                <div class="section-header-glass">
                    <span class="section__eyebrow">Institutional Cell</span>
                    <h2 class="committee-block__title">Anti-Ragging Committee Members</h2>
                    <p class="committee-block__subtitle">Senior administrators and faculty members dedicated to student counseling and immediate grievance response.</p>
                </div>

                <div class="committee-grid">
                    <?php foreach ($committeeMembers as $member): ?>
                        <div class="committee-card">
                            <div class="committee-card__image-box">
                                <img src="<?= htmlspecialchars($member['image']) ?>" alt="<?= htmlspecialchars($member['name']) ?>" class="committee-card__image" width="148" height="184" loading="lazy" decoding="async" />
                                <span class="committee-card__role-badge"><?= htmlspecialchars($member['role']) ?></span>
                            </div>

                            <div class="committee-card__info">
                                <h3 class="committee-card__name"><?= htmlspecialchars($member['name']) ?></h3>
                                <p class="committee-card__designation"><?= htmlspecialchars($member['designation']) ?></p>
                                <span class="committee-card__contact-tag"><?= htmlspecialchars($member['contact']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Section 3: National Helpline & Emergency Desk -->
            <div class="helpline-block">
                <div class="helpline-box">
                    <div class="helpline-box__icon">
                        <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </div>
                    <div class="helpline-box__details">
                        <h3>National Anti-Ragging 24x7 Toll-Free Helpline</h3>
                        <p class="helpline-number">1800-180-5522</p>
                        <p class="helpline-email">Email: <a href="mailto:helpline@antiragging.in">helpline@antiragging.in</a> | Web: <a href="https://www.antiragging.in" target="_blank" rel="noopener noreferrer">www.antiragging.in</a></p>
                    </div>
                </div>
            </div>

        </div>
    </main>


    <!-- ===================================================
         3. Reusable Call to Action
         =================================================== -->
    <section class="anti-ragging-cta" id="cta">
        <div class="anti-ragging-cta__container">
            <div class="anti-ragging-cta__box">
                <span class="anti-ragging-cta__badge">Confidential Assistance</span>
                <h2 class="anti-ragging-cta__title">Report a Concern or Seek Guidance</h2>
                <p class="anti-ragging-cta__subtitle">
                    Students can submit grievances in person or connect directly with college authorities with complete confidentiality.
                </p>
                <div class="anti-ragging-cta__actions">
                    <a href="contact.php" class="btn btn--primary">Contact College Administration</a>
                    <a href="about.php" class="btn btn--secondary">About Our Institution</a>
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