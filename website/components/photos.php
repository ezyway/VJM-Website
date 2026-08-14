<!-- ===================================================
    Photo Carousel Section
    - Campus life highlights with smooth horizontal scrolling
    =================================================== -->
<section class="photo-carousel-section" id="campus-life">
    <div class="photo-carousel-section__wrapper">
        <div class="photo-carousel-section__header">
            <div>
                <span class="section__eyebrow">Campus Memories</span>
                <h2 class="photo-carousel-section__title">Life at Shri V.J. Modha</h2>
            </div>
            <div class="photo-carousel-section__controls">
                <a href="gallery.php" class="photo-carousel-section__view-all">
                    View Full Gallery 
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </a>
                <button class="photo-carousel-section__nav-left" aria-label="Previous photo">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <button class="photo-carousel-section__nav-right" aria-label="Next photo">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>

        <div class="photo-carousel-section__carousel" id="carousel">
            <?php
            $image_dir_fs = dirname(__DIR__) . '/assets/photos/index/photo_carousel/';
            $image_dir_web = 'assets/photos/index/photo_carousel/';

            $images = glob($image_dir_fs . '*.{jpg,jpeg,png,webp}', GLOB_BRACE);

            if ($images) {
                sort($images);
                foreach ($images as $path) {
                    $src = $image_dir_web . basename($path);
                    echo <<<HTML
                        <div class="photo-carousel-section__item">
                            <div class="photo-carousel-section__img-box">
                                <img src="{$src}" alt="Campus Life Photo" loading="lazy">
                            </div>
                        </div>
                    HTML;
                }
            } else {
                echo "<p class='photo-carousel-section__no-images-message'>No photos available at the moment.</p>";
            }
            ?>
        </div>
    </div>
</section>