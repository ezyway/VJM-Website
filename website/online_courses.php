<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("header.php"); ?>
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
				<h2 class='page_title'>Free Online Courses</h2>
			</div>
			<div class="img-container">
				<a href='https://free.aicte-india.org/' target='_blank'>
					<img src="assets/photos/online_courses/AICTE.png">
				</a>
				<a href='https://atalacademy.aicte.gov.in/' target='_blank'>
					<img src="assets/photos/online_courses/ATAL.png">
				</a>
				<a href='https://swayam.gov.in/CEC' target='_blank'>
					<img src="assets/photos/online_courses/CEC.png">
				</a>
				<a href='https://swayam.gov.in/NCERT' target='_blank'>
					<img src="assets/photos/online_courses/NCERT.png">
				</a>
				<a href='https://swayam.gov.in/' target='_blank'>
					<img src="assets/photos/online_courses/Swayam.png">
				</a>
			</div>

		</div>
	</div>

	<!-- ===================================================
         Footer Section
         - Included via PHP for consistency across pages.
         =================================================== -->
	<?php include("footer.html"); ?>

</body>

</html>