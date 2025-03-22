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
    <title>Shri V.J. Modha College</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/logo.ico">

    <!-- ===================================================
         Fonts & Stylesheets
         =================================================== -->

    <!-- GOOGLE FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Import Roboto font for a Material Design look -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500&display=swap" rel="stylesheet">

    <!-- Global styles -->
    <link href="style/global.css" rel="stylesheet">
    <!-- Page-specific styles -->
    <link href="style/index.css" rel="stylesheet">

    <!-- ===================================================
         Scripts
         =================================================== -->
    <!-- Main JavaScript for page interactions -->
    <script src="scripts/index.js"></script>
</head>

<body>
    <!-- ===================================================
         Navigation Section
         - Included via PHP to allow reusability across pages.
         =================================================== -->
    <?php include("nav.html"); ?>

    <!-- ===================================================
         Banner Video Section
         - Fullscreen video banner with an overlay message.
         =================================================== -->
    <section class="video-banner">
        <!-- Background Video -->
        <video autoplay muted loop playsinline class="background-video">
            <source src="assets/videos/banner-video.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <!-- Overlay text displayed over the video -->
        <div class="video-overlay">
            <h1>Shri V.J. Modha College</h1>
            <p>Empowering students for a better future</p>
        </div>
    </section>

    <!-- ===================================================
         Counter Section
         - Displays dynamic counters for courses, enrolled students,
         - pass percentage, and total students passed out.
         =================================================== -->
    <section class="counter-container">
        <div class="counter-box">
            <!-- Counter for Courses -->
            <div class="counter-item">
                <div class="counter-courses" data-target="13">0</div>
                <div class="counter-courses-label">Courses</div>
            </div>
            <!-- Counter for Currently Enrolled Students -->
            <div class="counter-item">
                <div class="counter-enrolled" data-target="1200">0</div>
                <div class="counter-enrolled-label">Students Enrolled</div>
            </div>
            <!-- Counter for Pass Percentage -->
            <div class="counter-item">
                <div class="counter-pass-percentage" data-target="98">0</div>
                <div class="counter-pass-percentage-label">Pass Percentage</div>
            </div>
            <!-- Counter for Total Students Passed Out -->
            <div class="counter-item">
                <div class="counter-passouts" data-target="10607">0</div>
                <div class="counter-passouts-label">Total Students Passed Out</div>
            </div>
        </div>
    </section>

    <!-- ===================================================
         Chairman Section
         - Displays the chairman's image along with his message.
         =================================================== -->
    <section class="chairman-section">
        <div class="chairman-box">
            <!-- Chairman's Profile Picture -->
            <img src="assets/photos/index/chairman.png" alt="Chairman Image" class="chairman-image" />

            <!-- Chairman's Name and Title -->
            <h2>Chairman's Message</h2>
            <h3 class="chairman-name">Mr. Vallabhbhai Modha</h3>
            <p class="chairman-designation">Chairman, Shri V.J. Modha College, Porbandar</p>

            <!-- Chairman's Message -->
            <p class="chairman-message">
                "Knowledge is power &amp; with this power, I can visualize that the future of our nation is presently building in the classrooms, Today's students are the builders of our nation. For such building, our college has recorded a stupendous period in the history of this institute. We believe in quality education; time has wings &amp; it constantly flies, but now the time has come to pause and reflect on the modern system of education and harness the positive factors beneficial for the students."
            </p>

            <!-- Read More Button -->
            <!-- <a href="#" class="read-more-btn">Read Full Message</a> -->
        </div>
    </section>

    <!-- ===================================================
         Events and News Section
         - Displays upcoming events and recent results in separate news boxes.
         =================================================== -->
    <section class="news-section">
        <!-- Upcoming Events News Box -->
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

        <!-- Recent Results News Box -->
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

    <!-- ===================================================
         Academic Pass Rates Table Section
         - Displays a responsive table showing pass rates for various courses over the years.
         =================================================== -->
    <section class="table-section">
        <!-- Table Heading Container -->
        <div class="heading-container">
            <h2>Academic Pass Rates</h2>
            <!-- Additional headings can be uncommented if needed -->
            <!-- <h2>Graduation Performance Data</h2> -->
            <!-- <h2>Historical Passout Rates</h2> -->
            <!-- <h2>Annual Passout Rate</h2> -->
            <!-- <h2>Graduation Success Rates</h2> -->
        </div>
        <!-- Table Container with Horizontal Scroll -->
        <div class="table-container">
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
                <!-- Table rows: Some older rows are commented out -->
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
                    <td>-</td>
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
                    <td>-</td>
                    <td>100.00 %</td>
                    <td>98.00 %</td>
                    <td>97.00 %</td>
                </tr>
            </table>
        </div>
    </section>

    <!-- ===================================================
        Testimonials Section
        - Displays student testimonials with horizontal scroll.
        =================================================== -->
    <section class="testimonials-section">
        <h2 class="section-title">Student Testimonials</h2>

        <div class="testimonial-slider-container">
            <!-- Left navigation button -->
            <button class="testimonial-nav left">&lt;</button>

            <div class="testimonials-slider">
                <div class="testimonial-item">
                    <p class="testimonial-text">
                        "The practical approach and supportive faculty have transformed my learning experience!"
                    </p>
                    <h3 class="testimonial-name">Aisha Kumar</h3>
                    <span class="testimonial-course">B.Sc. IT</span>
                </div>
                <div class="testimonial-item">
                    <p class="testimonial-text">
                        "The vibrant campus life and quality education have set me on the path to success."
                    </p>
                    <h3 class="testimonial-name">Rahul Sharma</h3>
                    <span class="testimonial-course">BBA</span>
                </div>
                <div class="testimonial-item">
                    <p class="testimonial-text">
                        "I appreciate the focus on both academic and personal growth at Shri V.J. Modha College."
                    </p>
                    <h3 class="testimonial-name">Sneha Patel</h3>
                    <span class="testimonial-course">MCA</span>
                </div>
                <div class="testimonial-item">
                    <p class="testimonial-text">
                        "I appreciate the focus on both academic and personal growth at Shri V.J. Modha College."
                    </p>
                    <h3 class="testimonial-name">Sneha Patel</h3>
                    <span class="testimonial-course">MCA</span>
                </div>
                <div class="testimonial-item">
                    <p class="testimonial-text">
                        "I appreciate the focus on both academic and personal growth at Shri V.J. Modha College."
                    </p>
                    <h3 class="testimonial-name">Sneha Patel</h3>
                    <span class="testimonial-course">MCA</span>
                </div>
                <div class="testimonial-item">
                    <p class="testimonial-text">
                        "I appreciate the focus on both academic and personal growth at Shri V.J. Modha College."
                    </p>
                    <h3 class="testimonial-name">Sneha Patel</h3>
                    <span class="testimonial-course">MCA</span>
                </div>
                <div class="testimonial-item">
                    <p class="testimonial-text">
                        "I appreciate the focus on both academic and personal growth at Shri V.J. Modha College."
                    </p>
                    <h3 class="testimonial-name">Sneha Patel</h3>
                    <span class="testimonial-course">MCA</span>
                </div>
                <!-- Add more testimonial items as needed -->
            </div>

            <!-- Right navigation button -->
            <button class="testimonial-nav right">&gt;</button>
        </div>
        </div>
    </section>


    <!-- ===================================================
     Photo Carousel Section
     =================================================== -->
    <section class="photo-carousel-container">
        <div class="carousel-wrapper">
        <h2 class="carousel-title">Photos</h2>
            <button class="carousel-nav left">&lt;</button>
            <div class="carousel" id="carousel">
                <div class="carousel-item">
                    <img src="assets/photos/index/photo_carousel/129.jpg" alt="Image 1">
                </div>
                <div class="carousel-item">
                    <img src="assets/photos/index/photo_carousel/130.jpg" alt="Image 2">
                </div>
                <div class="carousel-item">
                    <img src="assets/photos/index/photo_carousel/131.jpg" alt="Image 3">
                </div>
                <div class="carousel-item">
                    <img src="assets/photos/index/photo_carousel/132.jpg" alt="Image 4">
                </div>
                <div class="carousel-item">
                    <img src="assets/photos/index/photo_carousel/133.jpg" alt="Image 5">
                </div>
            </div>
            <button class="carousel-nav right">&gt;</button>
        </div>
    </section>


    <!-- ===================================================
         Footer Section
         - Included via PHP for consistency across pages.
         =================================================== -->
    <?php include("footer.html"); ?>

</body>

</html>