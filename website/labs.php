<?php
if (isset($_GET["lab"])) {
    include("labs_data.php");

    $lab = $labs[$_GET["lab"]];
?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <?php include("header.php"); ?>
        
        <!-- Document Title -->
        <title><?php echo $lab["name"]; ?> - Facilities</title>

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
                            <?php foreach ($lab["images"] as $image): ?>
                                <img src="<?php echo $image; ?>" alt="<?php echo $lab["name"]; ?>" class="lab-section__image" />
                            <?php endforeach; ?>
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