<!-- ===================================================
    Photo Carousel Section
    =================================================== -->
<section class="photo-carousel-section">
    <div class="photo-carousel-section__wrapper">
        <h2 class="photo-carousel-section__title component_title">Photos</h2>
        <button class="photo-carousel-section__nav-left" aria-label="Previous photo">&lt;</button>

        <div class="photo-carousel-section__carousel" id="carousel">
            <?php
            $image_dir_fs = dirname(__DIR__) . '/assets/photos/index/photo_carousel/';
            $image_dir_web = 'assets/photos/index/photo_carousel/';

            $images = glob($image_dir_fs . '*.{jpg,jpeg,png,webp}', GLOB_BRACE);

            if ($images) {
                sort($images);
                foreach ($images as $path) {
                    $src = $image_dir_web . basename($path);
                    echo "<div class='photo-carousel-section__item'><img src='$src' alt='Gallery image'></div>";
                }
            } else {
                echo "<p class='photo-carousel-section__no-images-message'>No photos available at the moment.</p>";
            }
            ?>
        </div>


        <button class="photo-carousel-section__nav-right" aria-label="Next photo">&gt;</button>
    </div>
</section>