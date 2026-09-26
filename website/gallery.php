<?php
    require_once __DIR__ . '/admin/includes/db.php';
    $galleryDir = 'assets/photos/gallery';
    
    function getImageURLs($folderPath) {
        $result = [];
        if (!is_dir($folderPath)) return $result;
        $images = array_diff(scandir($folderPath), ['.', '..']);
        foreach ($images as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $result[] = "$folderPath/$file";
            }
        }
        return $result;
    }

    $albumsPayload = [];

    // Try loading from Database
    try {
        $db = getDB();
        $dbAlbums = $db->query('SELECT * FROM gallery_albums ORDER BY sort_order ASC, id ASC')->fetchAll();
        if (!empty($dbAlbums)) {
            $stmtPhotos = $db->prepare('SELECT image_path FROM gallery_photos WHERE album_id = :aid ORDER BY sort_order ASC, id ASC');
            foreach ($dbAlbums as $alb) {
                $stmtPhotos->execute([':aid' => $alb['id']]);
                $photos = $stmtPhotos->fetchAll(PDO::FETCH_COLUMN);

                // Fallback to disk scan if no photos in DB yet
                if (empty($photos)) {
                    $folder = "$galleryDir/{$alb['slug']}";
                    if (is_dir($folder)) {
                        $photos = getImageURLs($folder);
                    }
                }

                $cover = !empty($alb['cover_image']) ? $alb['cover_image'] : ($photos[0] ?? 'assets/background.png');

                $albumsPayload[$alb['slug']] = [
                    "key" => $alb['slug'],
                    "title" => $alb['title'],
                    "category" => $alb['category'],
                    "category_label" => $alb['category_label'] ?: $alb['category'],
                    "description" => $alb['description'],
                    "images" => $photos,
                    "cover" => $cover,
                    "count" => count($photos)
                ];
            }
        }
    } catch (Exception $e) {
        $albumsPayload = [];
    }

    // Fallback if DB empty or error
    if (empty($albumsPayload)) {
        $rawAlbums = is_dir($galleryDir) ? array_diff(scandir($galleryDir), ['.', '..']) : [];
        $albumMeta = [
            "aavishkar_event" => [
                "title" => "Aavishkar Tech & Science Fest",
                "category" => "events",
                "category_label" => "Tech & Academic",
                "description" => "Annual technical exhibition and science project competitions showcasing student innovations."
            ],
            "campus" => [
                "title" => "Campus Infrastructure & Grounds",
                "category" => "campus",
                "category_label" => "Campus Life",
                "description" => "Lush green campus environment, seminar halls, sports arena, and modern academic blocks."
            ],
            "freshers_party" => [
                "title" => "Freshers Welcome Celebration",
                "category" => "events",
                "category_label" => "Cultural & Social",
                "description" => "Welcoming the incoming batch of bright minds with music, performances, and student bonding."
            ],
            "ganesh_mahotsav" => [
                "title" => "Ganesh Mahotsav Celebrations",
                "category" => "cultural",
                "category_label" => "Tradition & Festivity",
                "description" => "Traditional cultural celebrations and devotional festivities uniting students and staff."
            ],
            "labs" => [
                "title" => "High-Tech Computer & Science Labs",
                "category" => "campus",
                "category_label" => "Facilities",
                "description" => "State-of-the-art computer labs, chemistry setups, and hands-on scientific research equipment."
            ],
            "talent_show" => [
                "title" => "Annual Talent & Cultural Showcase",
                "category" => "cultural",
                "category_label" => "Arts & Performances",
                "description" => "Celebrating extraordinary artistic talents in dance, drama, music, and public speaking."
            ]
        ];

        foreach ($rawAlbums as $albumKey) {
            $folder = "$galleryDir/$albumKey";
            if (is_dir($folder)) {
                $imgs = getImageURLs($folder);
                $meta = $albumMeta[$albumKey] ?? [
                    "title" => ucwords(str_replace('_', ' ', $albumKey)),
                    "category" => "events",
                    "category_label" => "Events",
                    "description" => "Memorable moments and student activities at Shri V.J. Modha College."
                ];
                $albumsPayload[$albumKey] = [
                    "key" => $albumKey,
                    "title" => $meta['title'],
                    "category" => $meta['category'],
                    "category_label" => $meta['category_label'],
                    "description" => $meta['description'],
                    "images" => $imgs,
                    "cover" => $imgs[0] ?? 'assets/background.png',
                    "count" => count($imgs)
                ];
            }
        }
    }

    // AJAX endpoint support
    if (isset($_GET['album']) && isset($_GET['action']) && $_GET['action'] === 'json') {
        $album = basename($_GET['album']);
        $path = "$galleryDir/$album";
        if (is_dir($path)) {
            $images = getImageURLs($path);
            header('Content-Type: application/json');
            echo json_encode($images);
        }
        exit;
    }

    $meta_description = "Browse the vibrant photo gallery of Shri V.J. Modha College, Porbandar. Explore campus infrastructure, cultural fests, tech exhibitions, and student life.";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "EducationalOrganization",
            "name": "Shri V.J. Modha College - Photo Gallery",
            "url": "https://shrivjmodhacollege.com/gallery.php",
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
    <!-- Embed Gallery Data Payload for instant lightbox -->
    <script id="galleryPayload" type="application/json">
        <?= json_encode($albumsPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
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
        'title' => 'Our Photo Gallery',
        'badge' => 'Campus Life & Events',
        'slogan' => '॥ स्मरणीयाः सुखदाः क्षणाः ॥',
        'subtitle' => 'Immerse yourself in the vibrant student life, state-of-the-art campus amenities, festive celebrations, and academic milestones at Shri V. J. Modha College.',
        'breadcrumb' => [
            ['label' => 'Home', 'url' => 'index.php'],
            ['label' => 'Facilities', 'url' => null],
            ['label' => 'Photo Gallery', 'current' => true],
        ],
    ];
    include('components/hero.php');
    ?>


    <!-- ===================================================
         2. Main Gallery Section with Album Cards & Filter
         =================================================== -->
    <main class="gallery-section" id="gallery-main">
        <div class="gallery-section__container">

            <!-- Section Header & Filter Tabs -->
            <div class="gallery-section__header">
                <div>
                    <span class="section__eyebrow">Visual Memories</span>
                    <h2 class="gallery-section__title">Explore Photo Albums</h2>
                    <p class="gallery-section__subtitle">Click on any album card to launch the interactive photo slideshow.</p>
                </div>

                <div class="gallery-filter-tabs" role="tablist">
                    <button class="gallery-filter-btn is-active" data-category="all" role="tab" aria-selected="true">All Albums (<?= count($albumsPayload) ?>)</button>
                    <button class="gallery-filter-btn" data-category="events" role="tab" aria-selected="false">Events</button>
                    <button class="gallery-filter-btn" data-category="cultural" role="tab" aria-selected="false">Cultural</button>
                    <button class="gallery-filter-btn" data-category="campus" role="tab" aria-selected="false">Campus &amp; Labs</button>
                </div>
            </div>

            <!-- Album Cards Grid -->
            <div class="gallery-grid" id="albumGrid">
                <?php foreach ($albumsPayload as $key => $album): ?>
                    <div class="album-card" data-category="<?= htmlspecialchars($album['category']) ?>" data-album="<?= htmlspecialchars($key) ?>">
                        <div class="album-card__cover-box">
                            <img src="<?= htmlspecialchars($album['cover']) ?>" alt="<?= htmlspecialchars($album['title']) ?>" class="album-card__image" loading="lazy" decoding="async" />
                            <div class="album-card__overlay">
                                <span class="album-card__view-btn">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                    <span>Play Slideshow</span>
                                </span>
                            </div>
                            <span class="album-card__count-badge" title="<?= $album['count'] ?> photos in this album">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                <span><?= $album['count'] ?> <?= $album['count'] === 1 ? 'Slide' : 'Slides' ?></span>
                            </span>
                        </div>

                        <div class="album-card__content">
                            <div class="album-card__header">
                                <span class="album-card__badge"><?= htmlspecialchars($album['category_label']) ?></span>
                                <span class="album-card__slide-indicator">
                                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect><line x1="7" y1="2" x2="7" y2="22"></line><line x1="17" y1="2" x2="17" y2="22"></line></svg>
                                    <?= $album['count'] ?> Photos
                                </span>
                            </div>
                            <h3 class="album-card__title"><?= htmlspecialchars($album['title']) ?></h3>
                            <p class="album-card__desc"><?= htmlspecialchars($album['description']) ?></p>
                            
                            <div class="album-card__footer">
                                <span class="album-card__cta-text">
                                    View Album &amp; Slideshow &rarr;
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </main>


    <!-- ===================================================
         3. Interactive Fullscreen Lightbox & Slideshow Modal
         =================================================== -->
    <div class="gallery-modal" id="galleryModal" role="dialog" aria-modal="true" aria-label="Photo Lightbox & Slideshow" style="display: none;">
        <div class="gallery-modal__backdrop" id="modalBackdrop"></div>
        
        <!-- Slideshow Top Progress Bar -->
        <div class="gallery-modal__progress-track">
            <div class="gallery-modal__progress-bar" id="modalProgressBar"></div>
        </div>

        <div class="gallery-modal__container">
            
            <!-- Top Controls -->
            <div class="gallery-modal__header">
                <div class="gallery-modal__info">
                    <h3 id="modalAlbumTitle" class="gallery-modal__title">Album Title</h3>
                    <span id="modalCounter" class="gallery-modal__counter">Slide 1 / 1</span>
                    <span id="modalPlayStateBadge" class="gallery-modal__play-badge" style="display: none;">
                        <span class="gallery-modal__pulse-dot"></span> Slideshow Active
                    </span>
                </div>

                <div class="gallery-modal__actions">
                    <!-- Slideshow Play/Pause Button -->
                    <button type="button" class="gallery-modal__action-btn" id="modalPlayBtn" title="Play Slideshow (Space)" aria-label="Play Slideshow">
                        <svg id="playIcon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        <svg id="pauseIcon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="display: none;"><rect x="6" y="4" width="4" height="16"></rect><rect x="14" y="4" width="4" height="16"></rect></svg>
                        <span id="modalPlayBtnLabel" class="gallery-modal__btn-text">Slideshow</span>
                    </button>

                    <!-- Fullscreen Toggle Button -->
                    <button type="button" class="gallery-modal__action-btn" id="modalFullscreenBtn" title="Toggle Fullscreen (F)" aria-label="Toggle Fullscreen">
                        <svg id="fullscreenExpandIcon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg>
                        <svg id="fullscreenCompressIcon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="display: none;"><path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"></path></svg>
                    </button>

                    <!-- Close Button -->
                    <button type="button" class="gallery-modal__close-btn" id="modalCloseBtn" title="Close Lightbox (Esc)" aria-label="Close Lightbox">&times;</button>
                </div>
            </div>

            <!-- Main Stage -->
            <div class="gallery-modal__stage">
                <button type="button" class="gallery-modal__nav-btn gallery-modal__nav-btn--prev" id="modalPrevBtn" title="Previous Slide (Left Arrow)" aria-label="Previous Photo">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </button>

                <div class="gallery-modal__image-wrapper">
                    <img id="modalMainImage" src="" alt="Album photo" class="gallery-modal__main-image" loading="lazy" />
                    <div id="modalLoadingSpinner" class="gallery-modal__spinner" style="display: none;"></div>
                </div>

                <button type="button" class="gallery-modal__nav-btn gallery-modal__nav-btn--next" id="modalNextBtn" title="Next Slide (Right Arrow)" aria-label="Next Photo">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>

            <!-- Bottom Controls & Thumbnails Strip -->
            <div class="gallery-modal__footer">
                <div class="gallery-modal__thumbnails-strip" id="modalThumbnailsStrip"></div>
                <div class="gallery-modal__hints">
                    <span><kbd>Space</kbd> Play / Pause</span>
                    <span><kbd>&larr;</kbd> <kbd>&rarr;</kbd> Navigate</span>
                    <span><kbd>F</kbd> Fullscreen</span>
                    <span><kbd>Esc</kbd> Close</span>
                </div>
            </div>

        </div>
    </div>


    <!-- ===================================================
         4. Reusable Call to Action
         =================================================== -->
    <?php
    $cta = [
        'badge' => 'Experience Campus Life',
        'title' => 'Want to Experience Our Vibrant Campus?',
        'subtitle' => 'Schedule a campus tour or connect with our admissions counselors to learn more about our thriving student community.',
        'actions' => [
            ['label' => 'Schedule a Campus Visit', 'url' => 'contact.php', 'class' => 'btn--primary'],
            ['label' => 'About Our Campus', 'url' => 'about.php', 'class' => 'btn--secondary'],
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