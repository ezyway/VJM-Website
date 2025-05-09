<?php

if (isset($_GET["course"])) {
     $courses = [
          "bca" => [
               "quick_info" => [
                    "Affiliated to Bhakta Kavi Narsinh Mehta University",
                    "3 Year Graduation Course",
                    "6 Semesters",
                    "Eligibility : Std. 12th Pass (Any stream)",
                    "Medium : English",
                    "Syllabus: To be included"
               ],
               "job_roles" => [
                    "Computer Programmer",
                    "Software Developer",
                    "Software Tester",
                    "Web Developer/Designer",
                    "System Analyst",
                    "Lab Assistant",
                    "Network Administrator",
                    "Academic Work",
                    "Government Work"
               ],
               "faq" => [
                    "To be added"
               ]
          ],
          "bsc" => [
               "quick_info" => [
                    "Affiliated to Bhakta Kavi Narsinh Mehta University",
                    "3 Year Graduation Course",
                    "6 Semesters",
                    "Eligibility : Std. 12th Pass (Science stream)",
                    "Medium : English",
                    "Syllabus: To be included"
               ],
               "job_roles" => [
                    "Lab Technician",
                    "Chemical Industry",
                    "Research Centre",
                    "Radiologist",
                    "Health Care Providers",
                    "Academic Sector",
                    "Research & Develop Dept.",
                    "Government Sector"
               ],
               "faq" => [
                    "To be added"
               ]
          ],
          "bba" => [
               "quick_info" => [
                    "Affiliated to Bhakta Kavi Narsinh Mehta University",
                    "3 Year Graduation Course",
                    "6 Semesters",
                    "Eligibility : Std. 12th Pass (Any stream)",
                    "Medium : English",
                    "Syllabus: To be included"
               ],
               "job_roles" => [
                    "Finance / HR Department",
                    "Healthcare Management",
                    "Business Consultant",
                    "Marketing Department",
                    "Business Operation",
                    "Project Analyst",
                    "E-Commerce",
                    "Banking Sector",
                    "Event Management"
               ],
               "faq" => [
                    "To be added"
               ]
          ],
          "bcom" => [
               "quick_info" => [
                    "Affiliated to Bhakta Kavi Narsinh Mehta University",
                    "3 Year Graduation Course",
                    "6 Semesters",
                    "Eligibility : Std. 12th Pass (Any stream)",
                    "Medium : Gujarati & English",
                    "Syllabus: To be included"
               ],
               "job_roles" => [
                    "Asst. to CA / CS / CFA",
                    "Financial Risk Manager",
                    "General Manager",
                    "Business Accountant",
                    "Insurance Agent",
                    "Certified Public",
                    "Certified Mgmt. Accountant",
                    "Marketing Manager",
                    "Accountant"
               ],
               "faq" => [
                    "To be added"
               ]
          ],
          "bsw" => [
               "quick_info" => [
                    "Affiliated to Bhakta Kavi Narsinh Mehta University",
                    "3 Year Graduation Course",
                    "6 Semesters",
                    "Eligibility : Std. 12th Pass (Any stream)",
                    "Medium : Gujarati",
                    "Syllabus: To be included"
               ],
               "job_roles" => [
                    "Education Dept.",
                    "Health Dept.",
                    "Municipal Dept.",
                    "Human Resource Dept.",
                    "Social Defence",
                    "Labour Dept.",
                    "Social Welfare Dept.",
                    "Government Dept.",
                    "NGO"
               ],
               "faq" => [
                    "To be added"
               ]
          ],
          "mcom" => [
               "quick_info" => [
                    "Affiliated to Bhakta Kavi Narsinh Mehta University",
                    "2 Year Master Course",
                    "4 Semesters",
                    "Eligibility : Any Commerce or Bussiness Administration Graduate",
                    "Medium : Gujarati & English",
                    "Syllabus: To be included"
               ],
               "job_roles" => [
                    "Accountant",
                    "Banking Sector",
                    "Wealth Management",
                    "Financial Analyst",
                    "Lecturer",
                    "Stock Broker",
                    "Tax Consultant",
                    "Insurance",
                    "RBI Grade B Officer"
               ],
               "faq" => [
                    "To be added"
               ]
          ],
          "mscit" => [
               "quick_info" => [
                    "Affiliated to Bhakta Kavi Narsinh Mehta University",
                    "2 Year Master Course",
                    "4 Semesters",
                    "Eligibility : BCA / B. Sc. (Computer / IT) / B. Com. (With Computer Science) / B. E. (Computer / IT) / B. Pharm. / B. Arch. / PGDCA",
                    "Medium : English",
                    "Syllabus: To be included"
               ],
               "job_roles" => [
                    "Software Developer",
                    "Network Administrator",
                    "Software Tester",
                    "Computer Programmer",
                    "System Analyst",
                    "Lab Assistant",
                    "Web Developer/Designer",
                    "Academic Work",
                    "Government Sector"
               ],
               "faq" => [
                    "To be added"
               ]
          ],
          "mscorgchem" => [
               "quick_info" => [
                    "Affiliated to Bhakta Kavi Narsinh Mehta University",
                    "2 Year Master Course",
                    "4 Semesters",
                    "Eligibility : Science Stream",
                    "Medium : English",
                    "Syllabus: To be included"
               ],
               "job_roles" => [
                    "Lab Technician",
                    "Academic Sector",
                    "Government Sector",
                    "Pharma Industry",
                    "Chemical Industry",
                    "Research Centre",
                    "Agrochemical Industries",
                    "Forensic Science Dept.",
                    "Health Care Providers"
               ],
               "faq" => [
                    "To be added"
               ]
          ]
     ];
     $fullforms = [
          'bca'=>'Bachelor in Computer Applications',
          'bsc'=>'Bachelor in Science',
          'bba'=>'Bachelor in Business Administration',
          'bcom'=>'Bachelor in Commerse',
          'bsw'=>'Bachelor in Social Work',
          'mcom'=>'Master in Commerse',
          'mscit'=>'Master in Information Technology',
          'mscorgchem'=>'Master in Science (Chemistry)',
     ];
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
                    echo "<div class='info-snippet'>";
                    echo "<h3 class='course_title'>Quick Info</h3>";
                    echo "<ul>";
                    foreach ($courses[$_GET["course"]]['quick_info'] as $info) {
                         echo "<li>$info</li>";
                    }
                    echo "</ul>";
                    echo "</div>";

                    
                    echo "<div class='info-snippet'>";
                    echo "<h3 class='course_title'>Job Roles</h3>";
                    echo "<ul>";
                    foreach ($courses[$_GET["course"]]['job_roles'] as $role) {
                         echo "<li>$role</li>";
                    }
                    echo "</ul>";
                    echo "</div>";

                    
                    echo "<div class='info-snippet'>";
                    echo "<h3 class='course_title'>FAQs</h3>";
                    echo "<ul>";
                    foreach ($courses[$_GET["course"]]['faq'] as $faq) {
                         echo "<li>$faq</li>";
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