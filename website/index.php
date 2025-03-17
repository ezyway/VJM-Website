<!DOCTYPE html>
<html lang="en">

<head>
	<title>Shri V.J. Modha College, Porbandar</title>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/navLogo.png">

	<!-- Import Roboto for Material Design Feel -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,500&display=swap" rel="stylesheet">
	
    <link href="style/global.css" rel="stylesheet">
    <link href="style/index.css" rel="stylesheet">
    <script src="scripts/index.js"></script>
</head>

<body>
    <?php include("nav.html"); ?>

    <!-- Banner Video ---------------------------------------------------------------------------------------- -->
	<section class="video-banner">
        <video autoplay muted loop playsinline class="background-video">
            <source src="assets/videos/banner-video.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="video-overlay">
            <h1>Welcome to Shri V.J. Modha College</h1>
            <p>Empowering students for a better future</p>
        </div>
    </section>

    <!-- Counter ---------------------------------------------------------------------------------------- -->
    <section class="counter-container">
        <div class="counter-item">
            <div class="counter-courses" data-target="13">0</div>
            <div class="counter-courses-label">Courses</div>
        </div>
        
        <div class="counter-item">
            <div class="counter-enrolled" data-target="1200">0</div>
            <div class="counter-enrolled-label">Students Enrolled Currently</div>
        </div>
        
        <div class="counter-item">
            <div class="counter-pass-percentage" data-target="98">0</div>
            <div class="counter-pass-percentage-label">Pass Percentage</div>
        </div>
        
        <div class="counter-item">
            <div class="counter-passouts" data-target="10607">0</div>
            <div class="counter-passouts-label">Total Students Passed Out</div>
        </div>
</section>
    

	<div class="content">
		<h1>Welcome to taSSashe Page</h1>
		<p>This is some sample content behind the navbar. Scroll to see the overlay effect.</p>
	</div>

    <?php include("footer.html"); ?>

</body>

</html>