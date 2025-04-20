<?php
if (isset($_GET["lab"])) {
    $labs = [
        "computer" => [
            "name" => "Computer Lab",
            "images" => [
                "assets/photos/computer_lab/115.jpg",
                "assets/photos/computer_lab/116.jpg",
                "assets/photos/computer_lab/117.jpg",
                "assets/photos/computer_lab/118.jpg",
                "assets/photos/computer_lab/136.jpg"
            ],
            "description" => "Our Computer Lab is equipped with modern PCs, high-speed internet, and the latest software to support IT and computer science education."
        ],
        "chemistry" => [
            "name" => "Chemistry Lab",
            "images" => [
                "assets/photos/chemistry_lab/130.jpg",
                "assets/photos/chemistry_lab/135.jpg",
                "assets/photos/chemistry_lab/137.jpg"
            ],
            "description" => "The Chemistry Lab provides all necessary chemicals, safety gear, and modern equipment to ensure hands-on learning for budding scientists."
        ],
        "biology" => [
            "name" => "Biology Lab",
            "images" => [
                "assets/photos/biology_lab/lab.jpg",
                "assets/photos/biology_lab/specimen.jpg",
                "assets/photos/biology_lab/students.jpg"
            ],
            "description" => "In the Biology Lab, students explore anatomy, microbiology, and ecosystems through microscopes and preserved specimens."
        ]
    ];

    $lab = $labs[$_GET["lab"]];
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>

        <!-- ===================================================
         Metadata & Document Setup
         =================================================== -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="description" content="Shri V.J. Modha College - Empowering students for a better future.">

        <!-- Page Title -->
        <title><?php echo $lab["name"]; ?> - Facilities</title>

        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="assets/logo.ico">

        <!-- ===================================================
         Fonts & Stylesheets
         =================================================== -->

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link href="style/global.css" rel="stylesheet">
        <link href="style/labs.css" rel="stylesheet">
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
    <script>
        const track = document.querySelector('.carousel-track');
        const slides = document.querySelectorAll('.lab-section__image');
        let index = 0;

        function showNextSlide() {
            index = (index + 1) % slides.length;
            track.style.transform = `translateX(-${index * 100}%)`;
        }

        setInterval(showNextSlide, 3000);
    </script>

<?php
} else {
    header("Location: index.php");
    exit();
}
?>