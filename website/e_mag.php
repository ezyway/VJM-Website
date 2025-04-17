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
     <title>E-Magazines</title>

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
     <!-- <link href="style/e_mag.css" rel="stylesheet"> -->
</head>

<body>
     <!-- ===================================================
         Navigation Section
         - Included via PHP to allow reusability across pages.
         =================================================== -->
     <?php include("nav.html"); ?>

     <div class="container">
          <div class="wrapper">
               <h2 class="page_title">E-Magazines</h2>
               <div class="table-container">
                    <table>
                         <tr>
                              <th>No.</th>
                              <th>Name</th>
                              <th>Year</th>
                              <th>Actions</th>
                         </tr>
                         <tr>
                              <td>1</td>
                              <td>Mag 1</td>
                              <td>2020</td>
                              <td>
                                   <div class="buttons">
                                        <a href="assets/e_mags/mag_2020.pdf" target="_blank">View</a>
                                        <a href="assets/e_mags/mag_2020.pdf" download="College_Magazine_2020.pdf">Download</a>
                                   </div>
                              </td>
                         </tr>
                         <tr>
                              <td>2</td>
                              <td>Mag 2</td>
                              <td>2021</td>
                              <td>
                                   <div class="buttons">
                                        <a href="assets/e_mags/mag_2021.pdf" target="_blank">View</a>
                                        <a href="assets/e_mags/mag_2021.pdf" download="College_Magazine_2021.pdf">Download</a>
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