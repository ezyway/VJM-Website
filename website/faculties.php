<?php
    // Meta description for SEO
    $meta_description = "Meet the distinguished faculty members of Shri V.J. Modha College, Porbandar across IT, Computer Science, Commerce, Science, Management, and Social Work departments.";

    // Department metadata definitions
    $deptDefs = [
        "admin" => ["name" => "Administrators", "badge" => "Leadership"],
        "data-admin" => ["name" => "Data Administrators", "badge" => "Operations"],
        "bca" => ["name" => "B.C.A. / M.Sc.(IT) & C.A.", "badge" => "IT & Computer Science"],
        "bsc" => ["name" => "B.Sc. / M.Sc.(Chem.)", "badge" => "Chemical Sciences"],
        "bcom" => ["name" => "B.Com. / M.Com.", "badge" => "Commerce & Finance"],
        "bba" => ["name" => "B.B.A.", "badge" => "Business Administration"],
        "bsw" => ["name" => "B.S.W.", "badge" => "Social Work"]
    ];

    require_once __DIR__ . '/admin/includes/db.php';
    $facultyList = [];
    try {
        $db = getDB();
        $dbFaculties = $db->query('SELECT * FROM faculties ORDER BY sort_order ASC, id ASC')->fetchAll();
        if (!empty($dbFaculties)) {
            foreach ($dbFaculties as $f) {
                $depts = array_values(array_filter(array_map('trim', explode(',', $f['depts']))));
                $facultyList[] = [
                    "name" => $f['name'],
                    "designation" => $f['designation'],
                    "depts" => $depts,
                    "badge" => $f['badge'],
                    "dept_label" => $f['dept_label'],
                    "image" => $f['image'],
                    "featured" => (bool)$f['featured']
                ];
            }
        }
    } catch (Exception $e) {
        $facultyList = [];
    }

    // Fallback if database is empty
    if (empty($facultyList)) {
        $facultyList = [
        // Administrators & BCA
        [
            "name" => "Prof. Paresh Savjani",
            "designation" => "Incharge Principal & Associate Professor",
            "depts" => ["admin", "bca"],
            "badge" => "Leadership • IT",
            "dept_label" => "Administration & IT",
            "image" => "assets/photos/faculties/Paresh_Savjani.jpg",
            "featured" => true
        ],
        [
            "name" => "Prof. Vishal Pandya",
            "designation" => "Director & Associate Professor",
            "depts" => ["admin", "bca"],
            "badge" => "Leadership • IT",
            "dept_label" => "Administration & IT",
            "image" => "assets/photos/faculties/Vishal_Pandya.jpg",
            "featured" => true
        ],
        // Data Administrators
        [
            "name" => "Dr. Nilesh Pratapsinh Gohil",
            "designation" => "Head of Data Administration",
            "depts" => ["data-admin"],
            "badge" => "Operations",
            "dept_label" => "Data Administration",
            "image" => "assets/photos/faculties/Nileshsinh_Gohil.jpg",
            "featured" => false
        ],
        // BCA / M.Sc. IT
        [
            "name" => "Prof. Jyotsna Salet",
            "designation" => "Associate Professor",
            "depts" => ["bca"],
            "badge" => "IT & Computer Science",
            "dept_label" => "B.C.A. / M.Sc.(IT)",
            "image" => "assets/photos/faculties/Salet_Jyotsna.jpeg",
            "featured" => false
        ],
        [
            "name" => "Prof. Zalak Thakrar",
            "designation" => "Associate Professor",
            "depts" => ["bca"],
            "badge" => "IT & Computer Science",
            "dept_label" => "B.C.A. / M.Sc.(IT)",
            "image" => "assets/photos/faculties/Thakrar_Zalak.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Jaydip Rathod",
            "designation" => "Assistant Professor",
            "depts" => ["bca"],
            "badge" => "IT & Computer Science",
            "dept_label" => "B.C.A. / M.Sc.(IT)",
            "image" => "assets/photos/faculties/Rathod_Jaydip.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Krunal Madlani",
            "designation" => "Assistant Professor",
            "depts" => ["bca"],
            "badge" => "IT & Computer Science",
            "dept_label" => "B.C.A. / M.Sc.(IT)",
            "image" => "assets/photos/faculties/Madlani_Krunal.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Nirav Maheta",
            "designation" => "Assistant Professor",
            "depts" => ["bca"],
            "badge" => "IT & Computer Science",
            "dept_label" => "B.C.A. / M.Sc.(IT)",
            "image" => "assets/photos/faculties/Maheta_Nirav.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Keyur Joshi",
            "designation" => "Assistant Professor",
            "depts" => ["bca"],
            "badge" => "IT & Computer Science",
            "dept_label" => "B.C.A. / M.Sc.(IT)",
            "image" => "assets/photos/faculties/Keyur_Joshi.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Nirali Dasani",
            "designation" => "Assistant Professor",
            "depts" => ["bca"],
            "badge" => "IT & Computer Science",
            "dept_label" => "B.C.A. / M.Sc.(IT)",
            "image" => "assets/photos/faculties/Nirali_Dasani.jpeg",
            "featured" => false
        ],
        [
            "name" => "Prof. Dhruv Monani",
            "designation" => "Assistant Professor",
            "depts" => ["bca"],
            "badge" => "IT & Computer Science",
            "dept_label" => "B.C.A. / M.Sc.(IT)",
            "image" => "assets/photos/faculties/Dhruv_Monani.jpg",
            "featured" => false
        ],
        // B.Sc. / M.Sc. Chem
        [
            "name" => "Prof. Manan Purohit",
            "designation" => "Lecturer",
            "depts" => ["bsc"],
            "badge" => "Chemical Sciences",
            "dept_label" => "B.Sc. / M.Sc.(Chem.)",
            "image" => "assets/photos/faculties/Manan_Purohit.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Riddhi Bamaniya",
            "designation" => "Lecturer",
            "depts" => ["bsc"],
            "badge" => "Chemical Sciences",
            "dept_label" => "B.Sc. / M.Sc.(Chem.)",
            "image" => "assets/photos/faculties/Bsc Bamaniya Riddhi Vijaybhai - Photo.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Pratima Parmar",
            "designation" => "Lecturer",
            "depts" => ["bsc"],
            "badge" => "Chemical Sciences",
            "dept_label" => "B.Sc. / M.Sc.(Chem.)",
            "image" => "assets/photos/faculties/Bsc Parmar Pratima Ashok - Photo.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Madhvi Thanki",
            "designation" => "Lecturer",
            "depts" => ["bsc"],
            "badge" => "Chemical Sciences",
            "dept_label" => "B.Sc. / M.Sc.(Chem.)",
            "image" => "assets/photos/faculties/Thanki Madhvi.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Nidhi Devani",
            "designation" => "Lecturer",
            "depts" => ["bsc"],
            "badge" => "Chemical Sciences",
            "dept_label" => "B.Sc. / M.Sc.(Chem.)",
            "image" => "assets/photos/faculties/Devani Nidhi.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Vishal Bhavnani",
            "designation" => "Lecturer",
            "depts" => ["bsc"],
            "badge" => "Chemical Sciences",
            "dept_label" => "B.Sc. / M.Sc.(Chem.)",
            "image" => "assets/photos/faculties/ViShal Bhavnani.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Sunny Pala",
            "designation" => "Lecturer",
            "depts" => ["bsc"],
            "badge" => "Chemical Sciences",
            "dept_label" => "B.Sc. / M.Sc.(Chem.)",
            "image" => "assets/photos/faculties/Pala Sunny Ashokbhai.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Shivani Kotecha",
            "designation" => "Lecturer",
            "depts" => ["bsc"],
            "badge" => "Chemical Sciences",
            "dept_label" => "B.Sc. / M.Sc.(Chem.)",
            "image" => "assets/photos/faculties/Kotecha Shivani Maheshbhai.jpeg",
            "featured" => false
        ],
        [
            "name" => "Prof. Neel Keshwala",
            "designation" => "Lecturer",
            "depts" => ["bsc"],
            "badge" => "Chemical Sciences",
            "dept_label" => "B.Sc. / M.Sc.(Chem.)",
            "image" => "assets/photos/faculties/Keshwala Neel Harishbhai.jpg",
            "featured" => false
        ],
        // B.B.A.
        [
            "name" => "Prof. Monti Daredi",
            "designation" => "Lecturer",
            "depts" => ["bba"],
            "badge" => "Business Administration",
            "dept_label" => "B.B.A.",
            "image" => "assets/photos/faculties/26 Daredi Monti - Photo.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Vaishnavi Ghediya",
            "designation" => "Lecturer",
            "depts" => ["bba"],
            "badge" => "Business Administration",
            "dept_label" => "B.B.A.",
            "image" => "assets/photos/faculties/Ghediya Vaishnavi.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Nidhi Hathi",
            "designation" => "Lecturer",
            "depts" => ["bba"],
            "badge" => "Business Administration",
            "dept_label" => "B.B.A.",
            "image" => "assets/photos/faculties/Hathi Nidhi.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Nandita Ghediya",
            "designation" => "Lecturer",
            "depts" => ["bba"],
            "badge" => "Business Administration",
            "dept_label" => "B.B.A.",
            "image" => "assets/photos/faculties/Ghediya Nandita.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Jalpa Somnani",
            "designation" => "Lecturer",
            "depts" => ["bba"],
            "badge" => "Business Administration",
            "dept_label" => "B.B.A.",
            "image" => "assets/photos/faculties/Somnani Jalpa Mulchandbhai.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Mahek Jogia",
            "designation" => "Lecturer",
            "depts" => ["bba"],
            "badge" => "Business Administration",
            "dept_label" => "B.B.A.",
            "image" => "assets/photos/faculties/Jogia Mahek Kishorbhai.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Parita Davda",
            "designation" => "Lecturer",
            "depts" => ["bba"],
            "badge" => "Business Administration",
            "dept_label" => "B.B.A.",
            "image" => "assets/photos/faculties/Parita Yatish Davda.jpg",
            "featured" => false
        ],
        // B.Com. / M.Com.
        [
            "name" => "Prof. Huzef Dor",
            "designation" => "Lecturer",
            "depts" => ["bcom"],
            "badge" => "Commerce & Finance",
            "dept_label" => "B.Com. / M.Com.",
            "image" => "assets/photos/faculties/30-Huzef-Dor---Photo.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Vinodray Raythatha",
            "designation" => "Lecturer",
            "depts" => ["bcom"],
            "badge" => "Commerce & Finance",
            "dept_label" => "B.Com. / M.Com.",
            "image" => "assets/photos/faculties/Raythatha Vinodray.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Jatin Raninga",
            "designation" => "Lecturer",
            "depts" => ["bcom"],
            "badge" => "Commerce & Finance",
            "dept_label" => "B.Com. / M.Com.",
            "image" => "assets/photos/faculties/Raninga Jatin Bharatbhai - Photo.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Mit Raval",
            "designation" => "Lecturer",
            "depts" => ["bcom"],
            "badge" => "Commerce & Finance",
            "dept_label" => "B.Com. / M.Com.",
            "image" => "assets/photos/faculties/Raval_Mit.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Chirag Khatwani",
            "designation" => "Lecturer",
            "depts" => ["bcom"],
            "badge" => "Commerce & Finance",
            "dept_label" => "B.Com. / M.Com.",
            "image" => "assets/photos/faculties/BCom Khatwani Chirag Jagdishbhai - Photo.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Rahul Pandya",
            "designation" => "Lecturer",
            "depts" => ["bcom"],
            "badge" => "Commerce & Finance",
            "dept_label" => "B.Com. / M.Com.",
            "image" => "assets/photos/faculties/Pandya-Rahul-Nathalal.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Hiren Majithia",
            "designation" => "Lecturer",
            "depts" => ["bcom"],
            "badge" => "Commerce & Finance",
            "dept_label" => "B.Com. / M.Com.",
            "image" => "assets/photos/faculties/Majithia Hiren Hasmukhlal.jpeg",
            "featured" => false
        ],
        [
            "name" => "Prof. Deep Thanki",
            "designation" => "Lecturer",
            "depts" => ["bcom"],
            "badge" => "Commerce & Finance",
            "dept_label" => "B.Com. / M.Com.",
            "image" => "assets/photos/faculties/Thanki Deep Naranjibhai.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Ronak Jogiya",
            "designation" => "Lecturer",
            "depts" => ["bcom"],
            "badge" => "Commerce & Finance",
            "dept_label" => "B.Com. / M.Com.",
            "image" => "assets/photos/faculties/Jogiya Ronak Amrutlal.jpeg",
            "featured" => false
        ],
        [
            "name" => "Prof. Ankita Baraiya",
            "designation" => "Lecturer",
            "depts" => ["bcom"],
            "badge" => "Commerce & Finance",
            "dept_label" => "B.Com. / M.Com.",
            "image" => "assets/photos/faculties/Baraiya Ankita.jpeg",
            "featured" => false
        ],
        [
            "name" => "Prof. Radhika Hindicha",
            "designation" => "Lecturer",
            "depts" => ["bcom"],
            "badge" => "Commerce & Finance",
            "dept_label" => "B.Com. / M.Com.",
            "image" => "assets/photos/faculties/Hindicah radhika.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Sweta Salet",
            "designation" => "Lecturer",
            "depts" => ["bcom"],
            "badge" => "Commerce & Finance",
            "dept_label" => "B.Com. / M.Com.",
            "image" => "assets/photos/faculties/Salet Sweta.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Shweta Shiyal",
            "designation" => "Lecturer",
            "depts" => ["bcom"],
            "badge" => "Commerce & Finance",
            "dept_label" => "B.Com. / M.Com.",
            "image" => "assets/photos/faculties/Shiyal Shweta Gopalbhai.jpeg",
            "featured" => false
        ],
        [
            "name" => "Prof. Bansi Raiyarela",
            "designation" => "Lecturer",
            "depts" => ["bcom"],
            "badge" => "Commerce & Finance",
            "dept_label" => "B.Com. / M.Com.",
            "image" => "assets/photos/faculties/Bansi Rajeshbhai Raiyarela.jpeg",
            "featured" => false
        ],
        [
            "name" => "Prof. Ravina Bhadreshvara",
            "designation" => "Lecturer",
            "depts" => ["bcom"],
            "badge" => "Commerce & Finance",
            "dept_label" => "B.Com. / M.Com.",
            "image" => "assets/photos/faculties/Bhadreshvara Ravina Jitendrabhai.jpg",
            "featured" => false
        ],
        // B.S.W.
        [
            "name" => "Prof. Puja Goraniya",
            "designation" => "Lecturer",
            "depts" => ["bsw"],
            "badge" => "Social Work",
            "dept_label" => "B.S.W.",
            "image" => "assets/photos/faculties/Goraniya_Puja.jpg",
            "featured" => false
        ],
        [
            "name" => "Prof. Kavita Aditya",
            "designation" => "Lecturer",
            "depts" => ["bsw"],
            "badge" => "Social Work",
            "dept_label" => "B.S.W.",
            "image" => "assets/photos/faculties/Aditya Kavita.jpg",
            "featured" => false
        ]
    ];
    }

    // Accurate count computation
    $deptCounts = [
        "all" => count($facultyList),
        "admin" => 0,
        "data-admin" => 0,
        "bca" => 0,
        "bsc" => 0,
        "bcom" => 0,
        "bba" => 0,
        "bsw" => 0
    ];
    foreach ($facultyList as $fac) {
        foreach ($fac['depts'] as $d) {
            if (isset($deptCounts[$d])) {
                $deptCounts[$d]++;
            }
        }
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
            "name": "Shri V.J. Modha College of Information Technology - Faculties",
            "url": "https://shrivjmodhacollege.com/faculties.php",
            "logo": "https://shrivjmodhacollege.com/assets/logo.ico",
            "description": "Meet our highly qualified and experienced educators and IT mentors at Shri V.J. Modha College, Porbandar.",
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
    <header class="faculties-hero" id="faculties-hero">
        <div class="faculties-hero__overlay">
            <div class="faculties-hero__content">
                <nav class="faculties-hero__breadcrumb" aria-label="Breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="faculties-hero__breadcrumb-sep">/</span>
                    <span aria-current="page">Faculties</span>
                </nav>
                <span class="faculties-hero__badge">Academic Mentors • Shri V. J. Modha College</span>
                <h1 class="faculties-hero__title">Our Esteemed Faculty</h1>
                <p class="faculties-hero__slogan">॥ विद्या विनयेन शोभते ॥</p>
                <p class="faculties-hero__subtitle">
                    Dedicated educators, industry specialists, and researchers shaping competent young professionals across IT, Commerce, Sciences, and Management.
                </p>
            </div>
        </div>
    </header>


    <!-- ===================================================
         2. Main Faculties Section with Interactive Filters
         =================================================== -->
    <main class="faculties-section" id="faculties-main">
        <div class="faculties-section__container">

            <!-- Section Header -->
            <div class="faculties-section__header">
                <div>
                    <span class="section__eyebrow">Academic Department Directory</span>
                    <h2 class="faculties-section__title">Meet Our Professors &amp; Lecturers</h2>
                    <p class="faculties-section__subtitle">Explore faculty profiles by department or search by name and specialization.</p>
                </div>

                <!-- Quick Search Input -->
                <div class="faculty-search-wrapper">
                    <svg class="faculty-search-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="facultySearch" class="faculty-search-input" placeholder="Search faculty by name..." aria-label="Search faculty by name" />
                    <button type="button" id="facultySearchClear" class="faculty-search-clear" aria-label="Clear search" style="display: none;">&times;</button>
                </div>
            </div>

            <!-- Department Filter Tabs -->
            <div class="faculty-tabs-container" role="tablist" aria-label="Faculty Department Filter">
                <button class="faculty-tab-btn is-active" data-dept="all" role="tab" aria-selected="true">
                    <span>All Departments</span>
                    <span class="faculty-tab-count"><?= $deptCounts['all'] ?></span>
                </button>
                <?php foreach ($deptDefs as $deptKey => $dept): ?>
                    <button class="faculty-tab-btn" data-dept="<?= htmlspecialchars($deptKey) ?>" role="tab" aria-selected="false">
                        <span><?= htmlspecialchars($dept['name']) ?></span>
                        <span class="faculty-tab-count"><?= $deptCounts[$deptKey] ?? 0 ?></span>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Faculty Cards Grid (Zero Duplicates in All View) -->
            <div class="faculty-grid" id="facultyGrid">
                <?php foreach ($facultyList as $member): ?>
                    <?php 
                        $deptsAttr = implode(' ', $member['depts']);
                    ?>
                    <div class="faculty-card <?= !empty($member['featured']) ? 'faculty-card--featured' : '' ?>" data-depts="<?= htmlspecialchars($deptsAttr) ?>" data-name="<?= htmlspecialchars(strtolower($member['name'])) ?>" data-designation="<?= htmlspecialchars(strtolower($member['designation'])) ?>">
                        <div class="faculty-card__image-box">
                            <img src="<?= htmlspecialchars($member['image']) ?>" alt="<?= htmlspecialchars($member['name']) ?>" class="faculty-card__image" width="148" height="184" loading="lazy" decoding="async" />
                            <?php if (!empty($member['featured'])): ?>
                                <span class="faculty-card__ribbon">Head</span>
                            <?php endif; ?>
                        </div>

                        <span class="faculty-card__badge"><?= htmlspecialchars($member['badge']) ?></span>

                        <div class="faculty-card__info">
                            <h3 class="faculty-card__name"><?= htmlspecialchars($member['name']) ?></h3>
                            <p class="faculty-card__designation"><?= htmlspecialchars($member['designation']) ?></p>
                            <span class="faculty-card__dept-tag"><?= htmlspecialchars($member['dept_label']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Empty State when search returns no match -->
            <div class="faculty-empty-state" id="facultyEmptyState" style="display: none;">
                <div class="faculty-empty-state__icon">
                    <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        <line x1="8" y1="11" x2="14" y2="11"></line>
                    </svg>
                </div>
                <h3>No Faculty Members Found</h3>
                <p>No professors match your current search query. Try searching with a different name or switch to "All Departments".</p>
                <button type="button" id="resetFiltersBtn" class="btn btn--secondary">Reset All Filters</button>
            </div>

        </div>
    </main>


    <!-- ===================================================
         3. Call to Action Banner
         =================================================== -->
    <section class="faculties-cta" id="cta">
        <div class="faculties-cta__container">
            <div class="faculties-cta__box">
                <span class="faculties-cta__badge">Learn With Experts</span>
                <h2 class="faculties-cta__title">Empowering Your Academic Journey</h2>
                <p class="faculties-cta__subtitle">
                    Experience personalized mentorship and high-standard curricula at Shri V. J. Modha College.
                </p>
                <div class="faculties-cta__actions">
                    <a href="courses.php" class="btn btn--primary">Explore Programs</a>
                    <a href="contact.php" class="btn btn--secondary">Connect With Us</a>
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