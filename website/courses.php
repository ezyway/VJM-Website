<?php
    include("courses_data.php");

    // Course metadata definitions
    $courseMeta = [
        "bca" => [
            "code" => "BCA",
            "level" => "Undergraduate (UG)",
            "category" => "ug",
            "dept" => "Information Technology",
            "duration" => "4 Years",
            "medium" => "English",
            "color" => "#10B981",
            "icon" => "laptop"
        ],
        "bsc" => [
            "code" => "B.Sc.",
            "level" => "Undergraduate (UG)",
            "category" => "ug",
            "dept" => "Science & Chemistry",
            "duration" => "4 Years",
            "medium" => "English",
            "color" => "#059669",
            "icon" => "flask"
        ],
        "bba" => [
            "code" => "BBA",
            "level" => "Undergraduate (UG)",
            "category" => "ug",
            "dept" => "Business Administration",
            "duration" => "4 Years",
            "medium" => "English",
            "color" => "#D97706",
            "icon" => "briefcase"
        ],
        "bcom" => [
            "code" => "B.Com",
            "level" => "Undergraduate (UG)",
            "category" => "ug",
            "dept" => "Commerce & Banking",
            "duration" => "4 Years",
            "medium" => "English & Gujarati",
            "color" => "#3B82F6",
            "icon" => "chart"
        ],
        "bsw" => [
            "code" => "BSW",
            "level" => "Undergraduate (UG)",
            "category" => "ug",
            "dept" => "Social Work & Welfare",
            "duration" => "4 Years",
            "medium" => "Gujarati",
            "color" => "#8B5CF6",
            "icon" => "users"
        ],
        "mcom" => [
            "code" => "M.Com",
            "level" => "Postgraduate (PG)",
            "category" => "pg",
            "dept" => "Advanced Commerce",
            "duration" => "2 Years",
            "medium" => "English & Gujarati",
            "color" => "#2563EB",
            "icon" => "trending-up"
        ],
        "mscit" => [
            "code" => "M.Sc. IT",
            "level" => "Postgraduate (PG)",
            "category" => "pg",
            "dept" => "Advanced Information Technology",
            "duration" => "2 Years",
            "medium" => "English",
            "color" => "#059669",
            "icon" => "cpu"
        ],
        "mscorgchem" => [
            "code" => "M.Sc. Chem",
            "level" => "Postgraduate (PG)",
            "category" => "pg",
            "dept" => "Organic Chemistry",
            "duration" => "2 Years",
            "medium" => "English",
            "color" => "#D97706",
            "icon" => "activity"
        ]
    ];

    $selectedCourse = isset($_GET["course"]) ? strtolower(trim($_GET["course"])) : null;
    if ($selectedCourse && !isset($courses[$selectedCourse])) {
        $selectedCourse = null;
    }
    $isSingleCourse = ($selectedCourse !== null);

    // Initial course for rendering detail view template
    $initialCourseKey = $isSingleCourse ? $selectedCourse : "bca";
    $initialMeta = $courseMeta[$initialCourseKey];
    $initialTitle = $fullforms[$initialCourseKey];
    $initialData = $courses[$initialCourseKey];
    $initialFaq = $initialData['faq'];
    $initialRoles = $initialData['job_roles'];
    $initialQuickInfo = $initialData['quick_info'][0];

    // SEO Meta description
    if ($isSingleCourse) {
        $meta_description = "Learn more about the {$initialTitle} program at Shri V.J. Modha College, Porbandar. Check eligibility, syllabus, career opportunities, and course structure.";
    } else {
        $meta_description = "Explore undergraduate and postgraduate academic programs at Shri V.J. Modha College, Porbandar including BCA, B.Sc, BBA, B.Com, BSW, M.Com, and M.Sc IT.";
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "EducationalOrganization",
            "name": "Shri V.J. Modha College - Academic Programs",
            "url": "https://shrivjmodhacollege.com/courses.php",
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
    <!-- Embed Course Data for 0ms Client-Side Instant Switching -->
    <script id="coursesPayload" type="application/json">
        <?= json_encode([
            'courses' => $courses,
            'fullforms' => $fullforms,
            'meta' => $courseMeta,
            'initialCourse' => $selectedCourse
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
    </script>

    <!-- ===================================================
         Navigation Section
         =================================================== -->
    <?php include("nav.html"); ?>


    <!-- ===================================================
         VIEW 1: SINGLE COURSE DETAIL VIEW
         =================================================== -->
    <div id="courseDetailView" class="course-view-wrapper" style="<?= $isSingleCourse ? '' : 'display: none;' ?>">
        
        <!-- Hero Header -->
        <header class="course-hero" id="course-hero">
            <div class="course-hero__overlay">
                <div class="course-hero__content">
                    <nav class="course-hero__breadcrumb" aria-label="Breadcrumb">
                        <a href="index.php">Home</a>
                        <span class="course-hero__breadcrumb-sep">/</span>
                        <a href="courses.php" class="breadcrumb-all-courses" data-course="all">Courses</a>
                        <span class="course-hero__breadcrumb-sep">/</span>
                        <span id="courseHeroBreadcrumb" aria-current="page"><?= htmlspecialchars($initialMeta['code']) ?></span>
                    </nav>
                    <span id="courseHeroBadge" class="course-hero__badge"><?= htmlspecialchars($initialMeta['level']) ?> • <?= htmlspecialchars($initialMeta['dept']) ?></span>
                    <h1 id="courseHeroTitle" class="course-hero__title"><?= htmlspecialchars($initialTitle) ?> (<?= htmlspecialchars($initialMeta['code']) ?>)</h1>
                    <p class="course-hero__slogan">॥ विद्यार्थी लभते विद्यां ॥</p>
                    
                    <!-- Quick Pill Specs in Hero -->
                    <div class="course-hero__specs">
                        <div class="course-spec-pill">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span><strong>Duration:</strong> <span id="courseHeroDuration"><?= htmlspecialchars($initialFaq[2] ?? $initialMeta['duration']) ?></span></span>
                        </div>
                        <div class="course-spec-pill">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                            <span><strong>Medium:</strong> <span id="courseHeroMedium"><?= htmlspecialchars(strip_tags($initialFaq[1] ?? $initialMeta['medium'])) ?></span></span>
                        </div>
                        <div class="course-spec-pill">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                            <span><strong>Eligibility:</strong> <span id="courseHeroEligibility"><?= htmlspecialchars(strip_tags($initialFaq[0] ?? '12th Pass')) ?></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Detail Section -->
        <main class="course-detail-section" id="course-main">
            <div class="course-detail__container">
                
                <!-- Quick Program Switcher Bar -->
                <div class="course-switcher">
                    <div class="course-switcher__header">
                        <a href="courses.php" class="course-switcher__pill course-switcher__pill--all" data-course="all" title="View all degree programs">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                            <span>All Programs</span>
                        </a>
                        <span class="course-switcher__label">Switch Course:</span>
                    </div>

                    <div class="course-switcher__links">
                        <?php foreach ($courseMeta as $key => $cMeta): ?>
                            <a href="courses.php?course=<?= urlencode($key) ?>" class="course-switcher__pill <?= $key === $initialCourseKey ? 'is-active' : '' ?>" data-course="<?= htmlspecialchars($key) ?>">
                                <?= htmlspecialchars($cMeta['code']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Two-Column Grid -->
                <div class="course-detail__grid" id="courseDetailGrid">
                    
                    <!-- Left Column -->
                    <div class="course-detail__left">
                        
                        <!-- Course Overview Card -->
                        <div class="course-card">
                            <span class="section__eyebrow">Program Overview</span>
                            <h2 id="courseAboutTitle" class="course-card__title">About <?= htmlspecialchars($initialMeta['code']) ?></h2>
                            <p id="courseOverviewText" class="course-card__lead"><?= htmlspecialchars($initialQuickInfo) ?></p>

                            <div class="course-highlights-banner">
                                <div class="course-highlight-item">
                                    <div class="course-highlight-icon">
                                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </div>
                                    <div>
                                        <h4>BKNMU Affiliation</h4>
                                        <p>Conforms to standardized university guidelines &amp; credit systems.</p>
                                    </div>
                                </div>

                                <div class="course-highlight-item">
                                    <div class="course-highlight-icon">
                                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </div>
                                    <div>
                                        <h4>Hands-on Practical Labs</h4>
                                        <p>Extensive lab hours, project work, and industry mentor guidance.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Career Opportunities & Job Roles Card -->
                        <div class="course-card">
                            <span class="section__eyebrow">Future Pathways</span>
                            <h2 class="course-card__title">Career Opportunities &amp; Job Roles</h2>
                            <p class="course-card__subtitle">Graduates step into thriving roles across multiple sectors:</p>
                            
                            <div class="job-roles-grid" id="courseJobRolesGrid">
                                <?php foreach ($initialRoles as $role): ?>
                                    <div class="job-role-chip">
                                        <div class="job-role-chip__bullet"></div>
                                        <span><?= htmlspecialchars($role) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Specs & FAQs -->
                    <div class="course-detail__right">
                        
                        <div class="course-card course-card--specs">
                            <span class="section__eyebrow">Key Specifications</span>
                            <h2 class="course-card__title">Curriculum Details</h2>

                            <div class="course-specs-table">
                                <div class="course-spec-row">
                                    <div class="course-spec-row__label">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path></svg>
                                        <span>Eligibility</span>
                                    </div>
                                    <div id="specEligibility" class="course-spec-row__value"><?= $initialFaq[0] ?? '12th Pass' ?></div>
                                </div>

                                <div class="course-spec-row">
                                    <div class="course-spec-row__label">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        <span>Course Duration</span>
                                    </div>
                                    <div id="specDuration" class="course-spec-row__value"><?= $initialFaq[2] ?? $initialMeta['duration'] ?></div>
                                </div>

                                <div class="course-spec-row">
                                    <div class="course-spec-row__label">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                        <span>Medium</span>
                                    </div>
                                    <div id="specMedium" class="course-spec-row__value"><?= $initialFaq[1] ?? 'English' ?></div>
                                </div>

                                <div class="course-spec-row">
                                    <div class="course-spec-row__label">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                        <span>Subjects / Sem</span>
                                    </div>
                                    <div id="specSubjects" class="course-spec-row__value"><?= $initialFaq[3] ?? '5 to 7' ?> Subjects</div>
                                </div>

                                <div class="course-spec-row">
                                    <div class="course-spec-row__label">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                        <span>Higher Studies</span>
                                    </div>
                                    <div id="specHigherStudies" class="course-spec-row__value"><?= $initialFaq[4] ?? 'Post Graduation' ?></div>
                                </div>

                                <div class="course-spec-row">
                                    <div class="course-spec-row__label">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        <span>Session Timing</span>
                                    </div>
                                    <div id="specTiming" class="course-spec-row__value"><?= $initialFaq[5] ?? 'Morning Session' ?></div>
                                </div>
                            </div>

                            <!-- Official Syllabus Link Button -->
                            <div class="course-syllabus-action">
                                <a id="courseSyllabusBtn" href="<?= htmlspecialchars($initialFaq[6] ?? 'https://www.bknmu.edu.in/Academic/page/Syllabus') ?>" target="_blank" rel="noopener noreferrer" class="btn btn--primary btn--syllabus">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                    <span>Download Official Syllabus</span>
                                </a>
                            </div>

                        </div>

                        <!-- Admissions Contact Box -->
                        <div class="course-card course-card--cta">
                            <h3>Interested in Enrolling?</h3>
                            <p>Get in touch with our admissions office for counseling, application assistance, and scholarship details.</p>
                            <div class="course-card-cta-btns">
                                <a href="contact.php" class="btn btn--secondary">Contact Admissions</a>
                                <a href="faculties.php" class="btn btn--outline">Meet Department Faculty</a>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </main>

    </div>


    <!-- ===================================================
         VIEW 2: ALL COURSES DIRECTORY VIEW
         =================================================== -->
    <div id="coursesDirectoryView" class="course-view-wrapper" style="<?= $isSingleCourse ? 'display: none;' : '' ?>">

        <header class="courses-hero" id="courses-hero">
            <div class="courses-hero__overlay">
                <div class="courses-hero__content">
                    <nav class="courses-hero__breadcrumb" aria-label="Breadcrumb">
                        <a href="index.php">Home</a>
                        <span class="courses-hero__breadcrumb-sep">/</span>
                        <span aria-current="page">Courses</span>
                    </nav>
                    <span class="courses-hero__badge">Academic Offerings • Shri V. J. Modha College</span>
                    <h1 class="courses-hero__title">Our Academic Programs</h1>
                    <p class="courses-hero__slogan">॥ विद्यार्थी लभते विद्यां ॥</p>
                    <p class="courses-hero__subtitle">
                        Explore industry-aligned undergraduate and postgraduate degree courses designed to foster critical thinking, technological excellence, and career success.
                    </p>
                </div>
            </div>
        </header>

        <main class="courses-directory-section" id="courses-directory">
            <div class="courses-directory__container">

                <div class="courses-directory__header">
                    <div>
                        <span class="section__eyebrow">Degree Programs</span>
                        <h2 class="courses-directory__title">Explore All Programs</h2>
                        <p class="courses-directory__subtitle">Choose from our diverse undergraduate and postgraduate faculties.</p>
                    </div>

                    <div class="courses-filter-tabs" role="tablist">
                        <button class="courses-filter-btn is-active" data-filter="all" role="tab" aria-selected="true">All Programs (<?= count($courseMeta) ?>)</button>
                        <button class="courses-filter-btn" data-filter="ug" role="tab" aria-selected="false">Undergraduate (5)</button>
                        <button class="courses-filter-btn" data-filter="pg" role="tab" aria-selected="false">Postgraduate (3)</button>
                    </div>
                </div>

                <div class="courses-grid" id="coursesGrid">
                    <?php foreach ($courseMeta as $key => $meta): 
                        $cData = $courses[$key] ?? [];
                        $faq = $cData['faq'] ?? [];
                        $snippet = $cData['quick_info'][0] ?? '';
                        $shortDesc = strlen($snippet) > 180 ? substr($snippet, 0, 180) . '...' : $snippet;
                    ?>
                        <div class="program-card" data-category="<?= htmlspecialchars($meta['category']) ?>">
                            <div class="program-card__header">
                                <span class="program-card__badge"><?= htmlspecialchars($meta['level']) ?></span>
                                <span class="program-card__duration"><?= htmlspecialchars($meta['duration']) ?></span>
                            </div>

                            <div class="program-card__body">
                                <h3 class="program-card__title"><?= htmlspecialchars($fullforms[$key] ?? $meta['code']) ?></h3>
                                <p class="program-card__dept"><?= htmlspecialchars($meta['dept']) ?></p>
                                <p class="program-card__desc"><?= htmlspecialchars($shortDesc) ?></p>

                                <div class="program-card__specs-row">
                                    <span><strong>Eligibility:</strong> <?= strip_tags($faq[0] ?? '12th Pass') ?></span>
                                    <span><strong>Medium:</strong> <?= strip_tags($faq[1] ?? 'English') ?></span>
                                </div>
                            </div>

                            <div class="program-card__footer">
                                <a href="courses.php?course=<?= urlencode($key) ?>" class="btn btn--primary btn--full program-card-link" data-course="<?= htmlspecialchars($key) ?>">
                                    <span>View Program Details</span>
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </main>

    </div>


    <!-- ===================================================
         3. Reusable Call to Action
         =================================================== -->
    <section class="courses-cta" id="cta">
        <div class="courses-cta__container">
            <div class="courses-cta__box">
                <span class="courses-cta__badge">Admissions Open</span>
                <h2 class="courses-cta__title">Start Your Academic Journey Today</h2>
                <p class="courses-cta__subtitle">
                    Need guidance choosing the right course? Reach out to our academic counseling desk.
                </p>
                <div class="courses-cta__actions">
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
    <?php include("footer.html"); ?>

</body>

</html>