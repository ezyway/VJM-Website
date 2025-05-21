<?php
if (isset($_GET["lab"])) {
    include("labs_data.php");
    $lab_type = $_GET["lab"];
    $lab = $labs[$lab_type];
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <?php include("header.php"); ?>
    </head>

    <body>

        <!-- Navigation -->
        <?php include("nav.html"); ?>

        <!-- Main Content -->
        <div class="container">
            <div class="wrapper">
                <div class="page_title_wrapper">
                    <h2 class="page_title"><?php echo $lab["name"]; ?></h2>
                </div>

                <div class="lab-section">
                    <div class="lab-section__image-wrapper">
                        <div class="carousel-track">
                            <!-- <?php foreach ($lab["images"] as $image): ?>
                                <img src="<?php echo $image; ?>" alt="<?php echo $lab["name"]; ?>" class="lab-section__image" />
                            <?php endforeach; ?> -->


                            <?php
                            $image_dir_fs = __DIR__ . '/assets/photos/labs/'.$lab_type . '/';
                            $image_dir_web = 'assets/photos/labs/'.$lab_type . '/';

                            $images = glob($image_dir_fs . '*.{jpg,jpeg,png,webp}', GLOB_BRACE);

                            if ($images) {
                                sort($images);
                                foreach ($images as $path) {
                                    $src = $image_dir_web . basename($path);
                                    echo "<img src='$src' alt='$lab_type' class='lab-section__image' />";
                                }
                            } else {
                                echo "<p class='photo-carousel-section__no-images-message'>No photos available at the moment.</p>";
                            }
                            ?>
                        </div>
                    </div>

                    <div class="lab-section__description">
                        <p><?php echo $lab["description"]; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php include("footer.html"); ?>

    </body>

    </html>

<?php
} else {
    header("Location: index.php");
    exit();
}
?>