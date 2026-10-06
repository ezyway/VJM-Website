<?php
    include("labs_data.php");

    // Gather lab images dynamically
    $labsPayload = [];
    foreach ($labs as $key => $labData) {
        $fsDir = __DIR__ . '/assets/photos/labs/' . $key . '/';
        $webDir = 'assets/photos/labs/' . $key . '/';
        $images = glob($fsDir . '*.{jpg,jpeg,png,webp}', GLOB_BRACE);
        $imgUrls = [];
        if ($images) {
            sort($images);
            foreach ($images as $p) {
                $imgUrls[] = $webDir . basename($p);
            }
        }
        $labsPayload[$key] = array_merge($labData, [
            "key" => $key,
            "images" => $imgUrls
        ]);
    }

    $selectedLab = isset($_GET["lab"]) ? strtolower(trim($_GET["lab"])) : "computer";
    if (!isset($labsPayload[$selectedLab])) {
        $selectedLab = "computer";
    }

    $currentLab = $labsPayload[$selectedLab];
    $meta_description = "Explore the cutting-edge {$currentLab['name']} at Shri V.J. Modha College, Porbandar. Review modern infrastructure, equipment specifications, and laboratory amenities.";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "EducationalOrganization",
            "name": "Shri V.J. Modha College - Campus Laboratories",
            "url": "https://shrivjmodhacollege.com/labs.php",
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
    <!-- Embed Labs Payload for instant client-side switching -->
    <script id="labsPayload" type="application/json">
        <?= json_encode([
            'labs' => $labsPayload,
            'initialLab' => $selectedLab
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
    </script>

    <!-- ===================================================
         Navigation Section
         =================================================== -->
    <?php include("nav.html"); ?>


    <!-- ===================================================
         1. Hero Header Banner
         =================================================== -->
    <?php
    $hero = [
        'title' => htmlspecialchars($currentLab['name']),
        'badge' => htmlspecialchars($currentLab['badge']),
        'slogan' => '॥ क्रियासिद्धिः सत्त्वे भवति महतां नोपकरणे ॥',
        'subtitle' => htmlspecialchars($currentLab['tagline']),
        'breadcrumb' => [
            ['label' => 'Home', 'url' => 'index.php'],
            ['label' => 'Facilities', 'url' => null],
            ['label' => htmlspecialchars($currentLab['code']), 'current' => true],
        ],
    ];
    include('components/hero.php');
    ?>


    <!-- ===================================================
         2. Main Labs Section with Switcher & Media Showcase
         =================================================== -->
    <main class="labs-section" id="labs-main">
        <div class="labs-section__container">

            <!-- Lab Switcher Bar -->
            <div class="lab-switcher">
                <span class="lab-switcher__label">Explore Laboratories:</span>
                <div class="lab-switcher__links">
                    <?php foreach ($labsPayload as $key => $lData): ?>
                        <a href="labs.php?lab=<?= urlencode($key) ?>" class="lab-switcher__pill <?= $key === $selectedLab ? 'is-active' : '' ?>" data-lab="<?= htmlspecialchars($key) ?>">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                <?php if ($key === 'computer'): ?>
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line>
                                <?php elseif ($key === 'chemistry'): ?>
                                    <path d="M10 2v7.31L4.41 18.9A2 2 0 0 0 6 22h12a2 2 0 0 0 1.59-3.1L14 9.31V2z"></path>
                                <?php else: ?>
                                    <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>
                                <?php endif; ?>
                            </svg>
                            <span><?= htmlspecialchars($lData['code']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Two-Column Lab Detail Layout -->
            <div class="lab-detail-layout" id="labDetailLayout">
                
                <!-- Left: Photo Carousel Showcase -->
                <div class="lab-media-card">
                    <div class="lab-carousel" id="labCarousel">
                        <div class="lab-carousel__main">
                            <img id="labMainImage" src="<?= htmlspecialchars($currentLab['images'][0] ?? 'assets/background.png') ?>" alt="<?= htmlspecialchars($currentLab['name']) ?>" class="lab-carousel__image" loading="lazy" decoding="async" />
                            <div class="lab-carousel__counter" id="labCarouselCounter">1 / <?= count($currentLab['images']) ?></div>
                            
                            <!-- Carousel Navigation Buttons -->
                            <button type="button" class="lab-carousel__nav-btn lab-carousel__nav-btn--prev" id="labPrevBtn" aria-label="Previous lab photo">
                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                            </button>
                            <button type="button" class="lab-carousel__nav-btn lab-carousel__nav-btn--next" id="labNextBtn" aria-label="Next lab photo">
                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                        </div>

                        <!-- Thumbnail Selectors -->
                        <div class="lab-thumbnails-strip" id="labThumbnailsStrip">
                            <?php foreach ($currentLab['images'] as $idx => $imgSrc): ?>
                                <button type="button" class="lab-thumb-btn <?= $idx === 0 ? 'is-active' : '' ?>" data-index="<?= $idx ?>">
                                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="Lab photo thumbnail <?= $idx + 1 ?>" loading="lazy" />
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Right: Lab Description & Features & Specs -->
                <div class="lab-info-col">
                    
                    <!-- Overview Card -->
                    <div class="lab-card">
                        <span class="section__eyebrow">Facility Overview</span>
                        <h2 id="labOverviewTitle" class="lab-card__title">About <?= htmlspecialchars($currentLab['name']) ?></h2>
                        <p id="labDescriptionText" class="lab-card__lead"><?= htmlspecialchars($currentLab['description']) ?></p>

                        <!-- Key Highlights List -->
                        <h4 class="lab-card__subtitle">Key Laboratory Highlights:</h4>
                        <div class="lab-features-list" id="labFeaturesList">
                            <?php foreach ($currentLab['features'] as $feat): ?>
                                <div class="lab-feature-item">
                                    <div class="lab-feature-icon">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    </div>
                                    <span><?= htmlspecialchars($feat) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Infrastructure Specs Card -->
                    <div class="lab-card lab-card--specs">
                        <span class="section__eyebrow">Technical Specifications</span>
                        <h3 class="lab-card__title">Infrastructure &amp; Capacity</h3>
                        
                        <div class="lab-specs-grid" id="labSpecsGrid">
                            <?php foreach ($currentLab['specs'] as $sLabel => $sVal): ?>
                                <div class="lab-spec-box">
                                    <span class="lab-spec-box__label"><?= htmlspecialchars($sLabel) ?></span>
                                    <strong class="lab-spec-box__value"><?= htmlspecialchars($sVal) ?></strong>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </main>


    <!-- ===================================================
         3. Reusable Call to Action
         =================================================== -->
    <?php
    $cta = [
        'badge' => 'Hands-On Learning',
        'title' => 'Experience Our Modern Infrastructure',
        'subtitle' => 'Discover why practical laboratory work at Shri V. J. Modha College builds superior technical competence.',
        'actions' => [
            ['label' => 'Explore Programs', 'url' => 'courses.php', 'class' => 'btn--primary'],
            ['label' => 'Schedule a Campus Visit', 'url' => 'contact.php', 'class' => 'btn--secondary'],
        ],
    ];
    include('components/cta.php');
    ?>


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