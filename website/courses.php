<?php

if (isset($_GET["course"])) {
	include("courses_data.php");
?>


	<!DOCTYPE html>
	<html lang="en">

	<head>

		<!-- ===================================================
         Metadata & Document Setup
         =================================================== -->
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<!-- Meta description for SEO -->
		<meta name="description" content="Shri V.J. Modha College - Empowering students for a better future.">

		<!-- Document Title -->
		<title>Courses</title>

		<!-- Favicon -->
		<link rel="shortcut icon" type="image/x-icon" href="assets/logo.ico">

		<!-- ===================================================
         Fonts & Stylesheets
         =================================================== -->

		<!-- Fonts -->
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

		<!-- Custom Styles -->
		<link href="style/global.css" rel="stylesheet">
		<link href="style/courses.css" rel="stylesheet">

	</head>

	<body>
		<!-- ===================================================
		Navigation Section
		- Included via PHP to allow reusability across pages.
		=================================================== -->
		<?php include("nav.html"); ?>

		<div class="container">
			<div class="wrapper">

				<div class="page_title_wrapper">
					<h2 class="page_title"><?php echo $fullforms[$_GET['course']]; ?></h2>
				</div>
				<?php
				if (isset($courses[$_GET["course"]])) {
					echo "<div class='quick-info-snippet snippet'>";
						echo "<h3 class='snippet_title'>Quick Info</h3>";
						echo "<ul>";
							foreach ($courses[$_GET["course"]]['quick_info'] as $info) {
								echo "<li>$info</li>";
							}
						echo "</ul>";
					echo "</div>";


					echo "<div class='job-roles-snippet snippet'>";
						echo "<h3 class='snippet_title'>Job Roles</h3>";
						echo "<ul>";
							foreach ($courses[$_GET["course"]]['job_roles'] as $role) {
								echo "<li>$role</li>";
							}
						echo "</ul>";
					echo "</div>";


					echo "<div class='faq-snippet snippet'>";
						echo "<h3 class='snippet_title'>FAQs</h3>";
						echo "<ul>";
							$faq = $courses[$_GET["course"]]['faq'];
							for($i = 0; $i < count($faq); $i++){
								echo "<li><b>$faq_questions[$i]</b>$faq[$i]</li>";
							}
						echo "</ul>";
					echo "</div>";
				}
				?>
			</div>
		</div>

		<!-- ===================================================
		Footer Section
		- Included via PHP for consistency across pages.
		=================================================== -->
		<?php include("footer.html"); ?>

	</body>

	</html>
<?php
} else {
	header("Location:index.php");
}

?>