<!DOCTYPE html>
<html lang="en">

<head>
	<title>Shri V.J. Modha College</title>

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
            <h1>Shri V.J. Modha College</h1>
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


    <!-- Chairman ---------------------------------------------------------------------------------------- -->
    <section class="chairman-section">
        <div class="chairman-container">
            <!-- Image of the Chairman -->
            <div class="chairman-image">
                <img src="assets/photos/index/chairman.png" alt="Chairman">
            </div>

            <!-- Message Content -->
            <div class="chairman-content">
                <h2>Chairman's Message</h2>
                <p class="chairman-quote">
                    "Knowledge is power & with this power, I can visualize that the future of our nation is presently building in the classrooms, Todays's students are the builders of our nation. For such building our college has recorded the stupendous period in the history of this institute we believe in the quality education time has wings & it constantly files but now the time has come to stop for a while and look back at the Morden system of education & him force the positive factors that are beneficial for the Students."
                </p>
                <h3>- Mr. Vallabhbhai Modha</h3>
                <p class="chairman-title">Chairman, Shri V.J. Modha College</p>
            </div>
        </div>
    </section>


    <!-- Events and News ---------------------------------------------------------------------------------------- -->
    <section class="news-section">
        <!-- Events Section -->
        <div class="news-box">
            <h2>Upcoming Events</h2>
            <div class="news-slider events-slider">
                <div class="news-item">
                    <h3>Annual Tech Fest</h3>
                    <span class="news-date">March 25, 2025</span>
                    <p>Join us for an exciting showcase of innovation and technology.</p>
                </div>
                <div class="news-item">
                    <h3>Guest Lecture on AI</h3>
                    <span class="news-date">April 10, 2025</span>
                    <p>Industry expert will discuss the latest trends in AI.</p>
                </div>
                <div class="news-item">
                    <h3>Sports Meet</h3>
                    <span class="news-date">April 20, 2025</span>
                    <p>Compete and enjoy a variety of sports activities.</p>
                </div>
                <div class="news-item">
                    <h3>Coding Hackathon</h3>
                    <span class="news-date">May 5, 2025</span>
                    <p>Showcase your coding skills and win exciting prizes.</p>
                </div>
                <div class="news-item">
                    <h3>Alumni Meet</h3>
                    <span class="news-date">June 1, 2025</span>
                    <p>Reconnect with old friends and share your experiences.</p>
                </div>
            </div>
        </div>

        <!-- Results Section -->
        <div class="news-box">
            <h2>Recent Results</h2>
            <div class="news-slider results-slider">
                <div class="news-item">
                    <h3>B.Sc. IT Semester 5</h3>
                    <span class="news-date">March 15, 2025</span>
                    <p>Pass percentage: 96%. Congratulations to all!</p>
                </div>
                <div class="news-item">
                    <h3>MCA Final Year</h3>
                    <span class="news-date">April 1, 2025</span>
                    <p>Topper: Raj Patel with 9.8 CGPA. Great job!</p>
                </div>
                <div class="news-item">
                    <h3>Commerce Dept. Results</h3>
                    <span class="news-date">April 10, 2025</span>
                    <p>98% students passed with distinction.</p>
                </div>
                <div class="news-item">
                    <h3>HSC Science Results</h3>
                    <span class="news-date">April 20, 2025</span>
                    <p>Top Scorer: Meera Shah - 97.2%</p>
                </div>
                <div class="news-item">
                    <h3>Engineering Semester 7</h3>
                    <span class="news-date">May 5, 2025</span>
                    <p>75% of students secured first class.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="table-container">
        <div class="heading-container">
            <h2>Academic Pass Rates</h2>
            <!-- <h2>Graduation Performance Data</h2> -->
            <!-- <h2>Historical Passout Rates</h2> -->
            <!-- <h2>Annual Passout Rate</h2> -->
            <!-- <h2>Graduation Success Rates</h2> -->
        </div>
        
        <table class="achievements-table">
            <tr>
                <th>Year</th>
                <th>BCA</th>
                <th>B.Sc.</th>
                <th>BBA</th>
                <th>B.Com.</th>
                <th>BSW</th>
                <th>PGDCA</th>
                <th>M.Sc.<br>(IT)</th>
                <th>M.Com.</th>
                <th>M.Sc.<br>(Chem)</th>
            </tr>
            <!-- <tr>
                <td><b>2007</b></td>
                <td>95.23 %</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>95.00 %</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr> -->
            <!--<tr>
                <td><b>2008</b></td>
                <td>96.19 %</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>96.66 %</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>  -->
            <!-- <tr>
                <td><b>2009</b></td>
                <td>97.22 %</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>98.33 %</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr> -->
            <!-- <tr>
                <td><b>2010</b></td>
                <td>96.14 %</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>98.87 %</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>-->
            <!-- <tr>
                <td><b>2011</b></td>
                <td>95.10 %</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>97.25 %</td>
                <td>95.00 %</td>
                <td>-</td>
                <td>-</td>
            </tr> -->
            <!-- <tr>
                <td><b>2012</b></td>
                <td>96.15 %</td>
                <td>-</td>
                <td>100.00 %</td>
                <td>96.35 %</td>
                <td>96.80 %</td>
                <td>97.40 %</td>
                <td>95.65 %</td>
                <td>-</td>
                <td>-</td>
            </tr> -->
            <!-- <tr>
                <td><b>2013</b></td>
                <td>97.90 %</td>
                <td>-</td>
                <td>98.76 %</td>
                <td>98.20 %</td>
                <td>97.50 %</td>
                <td>99.56 %</td>
                <td>94.86 %</td>
                <td>-</td>
                <td>-</td>
            </tr> -->
            <tr>
                <td><b>2014</b></td>
                <td>96.87 %</td>
                <td>-</td>
                <td>97.00 %</td>
                <td>97.63 %</td>
                <td>98.70 %</td>
                <td>99.80 %</td>
                <td>96.23 %</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td><b>2015</b></td>
                <td>97.33 %</td>
                <td>90.00 %</td>
                <td>95.63 %</td>
                <td>100.00 %</td>
                <td>100.00 %</td>
                <td>98.70 %</td>
                <td>100.00 %</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td><b>2016</b></td>
                <td>95.00 %</td>
                <td>91.00 %</td>
                <td>89.00 %</td>
                <td>99.00 %</td>
                <td>100.00 %</td>
                <td>98.00 %</td>
                <td>95.00 %</td>
                <td>-</td>
                <td>-</td>
            </tr>
            <tr>
                <td><b>2017</b></td>
                <td>94.22 %</td>
                <td>86.23 %</td>
                <td>90.00 %</td>
                <td>96.00 %</td>
                <td>100.00 %</td>
                <td>93.00 %</td>
                <td>100.00 %</td>
                <td>75.00 %</td>
                <td>68.00 %</td>
            </tr>
            <tr>
                <td><b>2018</b></td>
                <td>96.45 %</td>
                <td>92.38 %</td>
                <td>93.89 %</td>
                <td>97.17 %</td>
                <td>99.00 %</td>
                <td>95.00 %</td>
                <td>98.52 %</td>
                <td>97.42 %</td>
                <td>74.13 %</td>
            </tr>
            <tr>
                <td><b>2019</b></td>
                <td>95.55 %</td>
                <td>78.56 %</td>
                <td>94.88 %</td>
                <td>94.28 %</td>
                <td>100.00 %</td>
                <td>98.00 %</td>
                <td>98.74 %</td>
                <td>96.52 %</td>
                <td>75.00 %</td>
            </tr>
            <tr>
                <td><b>2020</b></td>
                <td>97.55 %</td>
                <td>82.45 %</td>
                <td>92.34 %</td>
                <td>90.44 %</td>
                <td>100.00 %</td>
                <td>97.55 %</td>
                <td>95.55 %</td>
                <td>94.55 %</td>
                <td>77.55 %</td>
            </tr>
            <tr>
                <td><b>2021</b></td>
                <td>94.00 %</td>
                <td>93.00 %</td>
                <td>98.00 %</td>
                <td>95.00 %</td>
                <td>98.00 %</td>
                <td>98.00 %</td>
                <td>100.00 %</td>
                <td>98.00 %</td>
                <td>95.00 %</td>
            </tr>
            <tr>
                <td><b>2022</b></td>
                <td>98.00 %</td>
                <td>98.37 %</td>
                <td>96.00 %</td>
                <td>97.17 %</td>
                <td>100.00 %</td>
                <td>- </td>
                <td>100.00 %</td>
                <td>98.33 %</td>
                <td>96.61 %</td>
            </tr>
            <tr>
                <td><b>2023</b></td>
                <td>97.87 %</td>
                <td>98.52 %</td>
                <td>97.57 %</td>
                <td>98.00 %</td>
                <td>100.00 %</td>
                <td>- </td>
                <td>100.00 %</td>
                <td>98.00 %</td>
                <td>97.00 %</td>
            </tr>
        </table>
    </section>


    

	<div class="content">
		<h1>Welcome to taSSashe Page</h1>
		<p>This is some sample content behind the navbar. Scroll to see the overlay effect.</p>
	</div>

    <?php include("footer.html"); ?>

</body>

</html>