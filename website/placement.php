<?php
    $meta_description = "Explore career opportunities, campus placement drives, and government employment platforms at Shri V.J. Modha College, Porbandar.";

    $platforms = [
        [
            "name" => "National Career Service (NCS)",
            "authority" => "Ministry of Labour & Employment, GoI",
            "jurisdiction" => "Central",
            "link" => "https://www.ncs.gov.in/",
            "desc" => "Official digital employment platform connecting jobseekers with national public and private vacancies."
        ],
        [
            "name" => "Directorate of Employment & Training (DET)",
            "authority" => "Government of Gujarat",
            "jurisdiction" => "State",
            "link" => "https://employment.gujarat.gov.in/",
            "desc" => "State employment exchange facilitating job fairs, apprenticeship programs, and industrial placements in Gujarat."
        ],
        [
            "name" => "Gujarat Public Service Commission (GPSC)",
            "authority" => "State Examination Body",
            "jurisdiction" => "State",
            "link" => "https://gpsc.gujarat.gov.in/",
            "desc" => "Recruitment notifications and competitive examinations for Class 1, 2, and 3 administrative state services."
        ],
        [
            "name" => "Staff Selection Commission (SSC)",
            "authority" => "Government of India",
            "jurisdiction" => "Central",
            "link" => "https://ssc.nic.in/",
            "desc" => "Central government recruitments for technical, administrative, and subordinate ministerial posts (CGL, CHSL)."
        ],
        [
            "name" => "Employment News (Rozgar Samachar)",
            "authority" => "Ministry of Information & Broadcasting",
            "jurisdiction" => "National",
            "link" => "https://www.employmentnews.gov.in/",
            "desc" => "Weekly premier publication featuring public sector, banking, defense, and academic job openings."
        ],
        [
            "name" => "NARI (National Repository for Women)",
            "authority" => "Ministry of Women & Child Development",
            "jurisdiction" => "Central",
            "link" => "https://www.nari.nic.in/",
            "desc" => "Comprehensive schemes, skill training programs, and financial incentives for women's career advancement."
        ],
        [
            "name" => "Empowering Youth Portal (Rojgar Setu)",
            "authority" => "Department of Labour & Employment, Gujarat",
            "jurisdiction" => "State",
            "link" => "https://empower.gujarat.gov.in/",
            "desc" => "Youth skill mapping, vocational training initiatives, and district-level Rojgar Melas."
        ],
        [
            "name" => "Commissionerate Of Employment & Training",
            "authority" => "Talim Rojgar Gujarat",
            "jurisdiction" => "State",
            "link" => "https://talimrojgar.gujarat.gov.in/employment.asp",
            "desc" => "Career counseling, campus recruitment drives, and employer-student linkage programs."
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
            "name": "Shri V.J. Modha College - Placement & Career Cell",
            "url": "https://shrivjmodhacollege.com/placement.php",
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
    <header class="placement-hero" id="placement-hero">
        <div class="placement-hero__overlay">
            <div class="placement-hero__content">
                <nav class="placement-hero__breadcrumb" aria-label="Breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="placement-hero__breadcrumb-sep">/</span>
                    <span>More</span>
                    <span class="placement-hero__breadcrumb-sep">/</span>
                    <span aria-current="page">Placements</span>
                </nav>
                <span class="placement-hero__badge">Career Guidance &amp; Placements</span>
                <h1 class="placement-hero__title">Training &amp; Placement Cell</h1>
                <p class="placement-hero__slogan">॥ उद्यमेन हि सिध्यन्ति कार्याणि न मनोरथैः ॥</p>
                <p class="placement-hero__subtitle">
                    Empowering graduates with technical skills, mock interviews, corporate networking, and direct access to state and national career platforms.
                </p>
            </div>
        </div>
    </header>


    <!-- ===================================================
         2. Main Placement Directory Section
         =================================================== -->
    <main class="placement-section" id="placement-main">
        <div class="placement-section__container">

            <!-- Section 1: Career Cell Pillars -->
            <div class="placement-pillars-block">
                <div class="section-header-glass">
                    <span class="section__eyebrow">Student Career Services</span>
                    <h2 class="placement-block__title">How We Prepare Our Graduates</h2>
                    <p class="placement-block__subtitle">A structured 360-degree approach to career readiness, soft skills development, and recruitment.</p>
                </div>

                <div class="placement-services-grid">
                    <div class="placement-service-card">
                        <div class="placement-service-card__icon">
                            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                        </div>
                        <h3>Technical Workshops</h3>
                        <p>Specialized bootcamps in full-stack web development, Python, accounting tools, and data analytics.</p>
                    </div>

                    <div class="placement-service-card">
                        <div class="placement-service-card__icon">
                            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </div>
                        <h3>Mock Interviews &amp; Soft Skills</h3>
                        <p>Simulated technical and HR interview rounds, group discussions, and professional resume building sessions.</p>
                    </div>

                    <div class="placement-service-card">
                        <div class="placement-service-card__icon">
                            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polygon points="12 6 12 12 14 14"></polygon></svg>
                        </div>
                        <h3>Government Job Guidance</h3>
                        <p>Guidance for GPSC, Banking (IBPS), SSC, and public examinations with access to library resources.</p>
                    </div>
                </div>
            </div>

            <!-- Section 2: Official Career & Employment Portals -->
            <div class="placement-platforms-block">
                <div class="section-header-glass">
                    <span class="section__eyebrow">Employment Directory</span>
                    <h2 class="placement-block__title">Government &amp; Public Employment Platforms</h2>
                    <p class="placement-block__subtitle">Direct links to verified state and national employment exchanges and recruitment portals.</p>
                </div>

                <div class="placement-platforms-grid">
                    <?php foreach ($platforms as $idx => $p): ?>
                        <div class="platform-card">
                            <div class="platform-card__header">
                                <span class="platform-jurisdiction-badge jurisdiction--<?= strtolower($p['jurisdiction']) ?>">
                                    <?= htmlspecialchars($p['jurisdiction']) ?> Jurisdiction
                                </span>
                                <span class="platform-card__number">#<?= $idx + 1 ?></span>
                            </div>

                            <h3 class="platform-card__title"><?= htmlspecialchars($p['name']) ?></h3>
                            <p class="platform-card__authority"><?= htmlspecialchars($p['authority']) ?></p>
                            <p class="platform-card__desc"><?= htmlspecialchars($p['desc']) ?></p>

                            <div class="platform-card__action">
                                <a href="<?= htmlspecialchars($p['link']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn--primary btn--portal">
                                    <span>Visit Career Portal</span>
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </main>


    <!-- ===================================================
         3. Reusable Call to Action
         =================================================== -->
    <section class="placement-cta" id="cta">
        <div class="placement-cta__container">
            <div class="placement-cta__box">
                <span class="placement-cta__badge">Are You a Recruiter?</span>
                <h2 class="placement-cta__title">Hire Our Industry-Ready Graduates</h2>
                <p class="placement-cta__subtitle">
                    Partner with Shri V. J. Modha College for on-campus and virtual hiring drives across BCA, B.Sc., BBA, B.Com, and M.Sc IT.
                </p>
                <div class="placement-cta__actions">
                    <a href="contact.php" class="btn btn--primary">Partner With Us</a>
                    <a href="courses.php" class="btn btn--secondary">Explore Student Programs</a>
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