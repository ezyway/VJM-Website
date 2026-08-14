<?php
    $meta_description = "Explore government and institutional scholarship programs at Shri V.J. Modha College, Porbandar. Review Digital Gujarat, MYSY portals, and yearly disbursement records.";

    $scholarshipRecords = [
        ["year" => "2024 - 2025", "amount" => "₹ 21,22,000", "numeric" => 2122000, "status" => "Latest"],
        ["year" => "2023 - 2024", "amount" => "₹ 19,38,000", "numeric" => 1938000, "status" => "Completed"],
        ["year" => "2022 - 2023", "amount" => "₹ 17,20,000", "numeric" => 1720000, "status" => "Completed"],
        ["year" => "2021 - 2022", "amount" => "₹ 16,50,000", "numeric" => 1650000, "status" => "Completed"],
        ["year" => "2020 - 2021", "amount" => "₹ 14,82,000", "numeric" => 1482000, "status" => "Completed"],
        ["year" => "2019 - 2020", "amount" => "₹ 16,98,500", "numeric" => 1698500, "status" => "Completed"],
        ["year" => "2018 - 2019", "amount" => "₹ 17,35,800", "numeric" => 1735800, "status" => "Completed"],
        ["year" => "2017 - 2018", "amount" => "₹ 22,13,100", "numeric" => 2213100, "status" => "Peak"],
        ["year" => "2016 - 2017", "amount" => "₹ 20,38,090", "numeric" => 2038090, "status" => "Completed"],
        ["year" => "2015 - 2016", "amount" => "₹ 15,53,850", "numeric" => 1553850, "status" => "Completed"],
        ["year" => "2014 - 2015", "amount" => "₹ 11,13,140", "numeric" => 1113140, "status" => "Completed"],
        ["year" => "2013 - 2014", "amount" => "₹ 7,00,774", "numeric" => 700774, "status" => "Completed"],
        ["year" => "2012 - 2013", "amount" => "₹ 5,42,500", "numeric" => 542500, "status" => "Completed"]
    ];

    $portals = [
        [
            "name" => "Digital Gujarat Portal",
            "provider" => "Government of Gujarat",
            "badge" => "State Government",
            "desc" => "Post-matric and merit scholarships for SC, ST, SEBC, and EWS students pursuing higher education.",
            "link" => "https://www.digitalgujarat.gov.in/",
            "icon" => "shield"
        ],
        [
            "name" => "MYSY (Mukhyamantri Yuva Swavalamban Yojana)",
            "provider" => "Education Department, Gujarat",
            "badge" => "Merit & Financial Aid",
            "desc" => "Fee waiver and financial assistance for bright and needy students across degree programs.",
            "link" => "https://mysy.guj.nic.in/",
            "icon" => "award"
        ]
    ];

    $totalDisbursed = 0;
    foreach ($scholarshipRecords as $r) {
        $totalDisbursed += $r['numeric'];
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
            "name": "Shri V.J. Modha College - Scholarships & Financial Aid",
            "url": "https://shrivjmodhacollege.com/scholarship.php",
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
    <header class="scholarship-hero" id="scholarship-hero">
        <div class="scholarship-hero__overlay">
            <div class="scholarship-hero__content">
                <nav class="scholarship-hero__breadcrumb" aria-label="Breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="scholarship-hero__breadcrumb-sep">/</span>
                    <span>Facilities</span>
                    <span class="scholarship-hero__breadcrumb-sep">/</span>
                    <span aria-current="page">Scholarships</span>
                </nav>
                <span class="scholarship-hero__badge">Financial Aid &amp; Student Support</span>
                <h1 class="scholarship-hero__title">Scholarships &amp; Grants</h1>
                <p class="scholarship-hero__slogan">॥ विद्यादानं महत्पुण्यम् ॥</p>
                <p class="scholarship-hero__subtitle">
                    Dedicated to ensuring equal educational opportunities for every meritorious and deserving student through government schemes and institutional aid.
                </p>

                <!-- Hero Metric Badges -->
                <div class="scholarship-hero__metrics">
                    <div class="scholarship-metric-pill">
                        <strong>₹ 2.05+ Crores</strong>
                        <span>Total Aid Disbursed</span>
                    </div>
                    <div class="scholarship-metric-pill">
                        <strong>13+ Years</strong>
                        <span>Continuous Student Support</span>
                    </div>
                    <div class="scholarship-metric-pill">
                        <strong>100% Direct</strong>
                        <span>Government Direct Benefit Transfer (DBT)</span>
                    </div>
                </div>
            </div>
        </div>
    </header>


    <!-- ===================================================
         2. Main Scholarship Content Section
         =================================================== -->
    <main class="scholarship-section" id="scholarship-main">
        <div class="scholarship-section__container">

            <!-- Section 1: Government Scholarship Portals -->
            <div class="scholarship-portals-block">
                <div class="section-header-glass">
                    <span class="section__eyebrow">Application Portals</span>
                    <h2 class="scholarship-block__title">Official Government Scholarship Schemes</h2>
                    <p class="scholarship-block__subtitle">Eligible students can apply online through official Gujarat state scholarship portals with college guidance.</p>
                </div>

                <div class="portals-grid">
                    <?php foreach ($portals as $portal): ?>
                        <div class="portal-card">
                            <div class="portal-card__top">
                                <div class="portal-card__icon-box">
                                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                                    </svg>
                                </div>
                                <span class="portal-card__badge"><?= htmlspecialchars($portal['badge']) ?></span>
                            </div>

                            <h3 class="portal-card__title"><?= htmlspecialchars($portal['name']) ?></h3>
                            <p class="portal-card__provider"><?= htmlspecialchars($portal['provider']) ?></p>
                            <p class="portal-card__desc"><?= htmlspecialchars($portal['desc']) ?></p>

                            <div class="portal-card__action">
                                <a href="<?= htmlspecialchars($portal['link']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn--primary btn--portal">
                                    <span>Apply on Official Portal</span>
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Section 2: Yearly Disbursement History Table -->
            <div class="scholarship-history-block">
                <div class="section-header-glass">
                    <span class="section__eyebrow">Financial Aid Transparency</span>
                    <h2 class="scholarship-block__title">Year-wise Scholarship Disbursement Record</h2>
                    <p class="scholarship-block__subtitle">A transparent overview of total scholarship funds received and disbursed to students since 2012.</p>
                </div>

                <div class="scholarship-table-wrapper">
                    <table class="scholarship-table" id="scholarshipTable">
                        <thead>
                            <tr>
                                <th>Academic Year</th>
                                <th>Disbursed Amount</th>
                                <th>Status / Highlight</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($scholarshipRecords as $rec): ?>
                                <tr>
                                    <td>
                                        <div class="table-year-cell">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                            <span><?= htmlspecialchars($rec['year']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="table-amount-cell"><?= htmlspecialchars($rec['amount']) ?></span>
                                    </td>
                                    <td>
                                        <span class="table-status-pill <?= $rec['status'] === 'Latest' ? 'status--latest' : ($rec['status'] === 'Peak' ? 'status--peak' : '') ?>">
                                            <?= htmlspecialchars($rec['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-total-row">
                                <td><strong>Grand Total (2012 – 2025)</strong></td>
                                <td><strong>₹ <?= number_format($totalDisbursed) ?></strong></td>
                                <td><span class="table-status-pill status--total">Disbursed</span></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </main>


    <!-- ===================================================
         3. Reusable Call to Action
         =================================================== -->
    <section class="scholarship-cta" id="cta">
        <div class="scholarship-cta__container">
            <div class="scholarship-cta__box">
                <span class="scholarship-cta__badge">Scholarship Desk</span>
                <h2 class="scholarship-cta__title">Need Help with Your Application?</h2>
                <p class="scholarship-cta__subtitle">
                    Our administrative cell provides full assistance with portal registration, document verification, and scholarship tracking.
                </p>
                <div class="scholarship-cta__actions">
                    <a href="contact.php" class="btn btn--primary">Contact Scholarship Cell</a>
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