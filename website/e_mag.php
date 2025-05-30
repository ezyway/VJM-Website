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
				<h2 class="page_title">E-Magazines</h2>
			</div>
			<div class="table-container">
				<table>
					<tr>
						<th>No.</th>
						<th>Name</th>
						<th>Actions</th>
					</tr>
					<tr>
						<td>1</td>
						<td>E-Magazine 2021</td>
						<td>
							<div class="buttons">
								<a href="assets/e_mags/mag_2021.pdf" target="_blank">View</a>
								<a href="assets/e_mags/mag_2021.pdf" download="College_Magazine_2021.pdf">Download</a>
							</div>
						</td>
					</tr>
					<tr>
						<td>2</td>
						<td>E-Magazine 2020</td>
						<td>
							<div class="buttons">
								<a href="assets/e_mags/mag_2020.pdf" target="_blank">View</a>
								<a href="assets/e_mags/mag_2020.pdf" download="College_Magazine_2020.pdf">Download</a>
							</div>
						</td>
					</tr>
				</table>
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