<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Schema.org Structured data for SEO benefits -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "College",
            "name": "Shri V.J. Modha College",
            "url": "https://shrivjmodhacollege.com",
            "logo": "https://shrivjmodhacollege.com/assets/logo.ico",
            "description": "Shri V.J. Modha College - Empowering students for a better future.",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "Vidhyadham, Chhaya-Birla Road, Nr. Pakshi Abhiyaran, Porbandar, Gujarat, 360575",
                "addressLocality": "Porbandar",
                "addressRegion": "Gujarat",
                "postalCode": "360575",
                "addressCountry": "IN"
            }
        }
    </script>

    <?php include("header.php"); ?>
    
    <!-- Document Title -->
    <title>Shri V.J. Modha College</title>
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
    <section class="video-banner" id="video-banner">
        <video autoplay muted loop playsinline class="video-banner__background" preload="metadata">
            <source src="assets/videos/banner-video.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <!-- <div class="video-banner__overlay">
            <h1>Shri V.J. Modha College</h1>
            <p>॥ विद्यार्थी लभते विद्यां ॥</p>
        </div> -->

        <div class="scroll-down">
            <span class="arrow"></span>
            <span class="arrow"></span>
        </div>

    </section>


    <!-- ===================================================
         Counter Section
         - Displays dynamic counters for courses, enrolled students,
         - pass percentage, and total students passed out.
         =================================================== -->
    <section class="counter-section" id="counter-section">
        <div class="counter-section__box">
            <div class="counter-section__item">
                <div class="counter-section__courses" data-target="13">0</div>
                <div class="counter-section__label">Courses</div>
            </div>
            <div class="counter-section__item">
                <div class="counter-section__pass-percentage" data-target="98">0</div>
                <div class="counter-section__label">Pass Percentage</div>
            </div>
            <div class="counter-section__item">
                <div class="counter-section__enrolled" data-target="1200">0</div>
                <div class="counter-section__label">Students Enrolled</div>
            </div>
            <div class="counter-section__item">
                <div class="counter-section__passouts" data-target="10607">0</div>
                <div class="counter-section__label">Total Students Passed Out</div>
            </div>
        </div>
    </section>


    <!-- ===================================================
         Chairman Section
         - Displays the chairman's image along with his message.
         =================================================== -->
    <section class="chairman-section">
        <div class="chairman-section__profile">
            <img src="assets/photos/index/chairman.png" alt="Chairman Image" class="chairman-section__image" />
            <h3 class="chairman-section__name">Mr. Vallabhbhai Modha</h3>
            <p class="chairman-section__designation">Chairman, Shri V.J. Modha College, Porbandar</p>
            <p class="chairman-section__message">
                "Knowledge is power &amp; with this power, I can visualize that the future of our nation is presently building in the classrooms. Today's students are the builders of our nation. For such building, our college has recorded a stupendous period in the history of this institute. We believe in quality education; time has wings &amp; it constantly flies, but now the time has come to pause and reflect on the modern system of education and harness the positive factors beneficial for the students."
            </p>
        </div>

        <!-- Pride of the College Section with Slider -->
        <div class="pride-section">
            <h2>Pride of the College</h2>
            <div class="pride-section__slider">
                <div class="pride-section__slider-wrapper">
                    <div class="pride-section__item">
                        <img src="assets/photos/index/pride_of_college/1-CHAMADIYA CHIRAG-MUKESHBHAI.jpg" alt="Student 1" class="pride-section__image" />
                        <h3 class="pride-section__name">Mr. Chirag Mukeshbhai</h3>
                        <p class="pride-section__course">B.Sc. Computer Science</p>
                        <p class="pride-section__award">Gold Medalist</p>
                    </div>
                    <div class="pride-section__item">
                        <img src="assets/photos/index/pride_of_college/1-Karavadara Ram Bhima.jpg" alt="Student 2" class="pride-section__image" />
                        <h3 class="pride-section__name">Mr. Ram Bhima Karavadara</h3>
                        <p class="pride-section__course">B.Com</p>
                        <p class="pride-section__award">University Rank 2</p>
                    </div>
                    <div class="pride-section__item">
                        <img src="assets/photos/index/pride_of_college/1-Sonigra Jayesh harishkumar.jpg" alt="Student 3" class="pride-section__image" />
                        <h3 class="pride-section__name">Mr. Jayesh Harishkumar Sonigra</h3>
                        <p class="pride-section__course">M.Sc. Mathematics</p>
                        <p class="pride-section__award">Best Research Paper Award</p>
                    </div>
                    <div class="pride-section__item">
                        <img src="assets/photos/index/pride_of_college/1-Sonigra Jayesh harishkumar.jpg" alt="Student 3" class="pride-section__image" />
                        <h3 class="pride-section__name">XXXX</h3>
                        <p class="pride-section__course">M.Sc. Mathematics</p>
                        <p class="pride-section__award">Best Research Paper Award</p>
                    </div>
                </div>
            </div>
            <div class="pride-section__nav">
                <button class="pride-section__nav-prev">&#10094;</button>
                <button class="pride-section__nav-next">&#10095;</button>
            </div>
        </div>
    </section>


    <!-- ===================================================
         Events and News Section
         - Displays upcoming events and recent results in separate news boxes.
         =================================================== -->
    <section class="news-section">
        <div class="news-section__box news-section__events">
            <h2 class="component_title">Upcoming Events</h2>
            <div class="news-section__slider news-section__slider--events">
                <div class="news-section__item">
                    <h3>Annual Tech Fest</h3>
                    <span class="news-section__date">March 25, 2025</span>
                    <p>Join us for an exciting showcase of innovation and technology.</p>
                </div>
                <div class="news-section__item">
                    <h3>Guest Lecture on AI</h3>
                    <span class="news-section__date">April 10, 2025</span>
                    <p>Industry expert will discuss the latest trends in AI.</p>
                </div>
                <div class="news-section__item">
                    <h3>Sports Meet</h3>
                    <span class="news-section__date">April 20, 2025</span>
                    <p>Compete and enjoy a variety of sports activities.</p>
                </div>
                <div class="news-section__item">
                    <h3>Coding Hackathon</h3>
                    <span class="news-section__date">May 5, 2025</span>
                    <p>Showcase your coding skills and win exciting prizes.</p>
                </div>
                <div class="news-section__item">
                    <h3>Alumni Meet</h3>
                    <span class="news-section__date">June 1, 2025</span>
                    <p>Reconnect with old friends and share your experiences.</p>
                </div>
            </div>
        </div>
        <div class="news-section__box news-section__results">
            <h2 class="component_title">Recent Results</h2>
            <div class="news-section__slider news-section__slider--results">
                <div class="news-section__item">
                    <h3>B.Sc. IT Semester 5</h3>
                    <span class="news-section__date">March 15, 2025</span>
                    <p>Pass percentage: 96%. Congratulations to all!</p>
                </div>
                <div class="news-section__item">
                    <h3>MCA Final Year</h3>
                    <span class="news-section__date">April 1, 2025</span>
                    <p>Topper: Raj Patel with 9.8 CGPA. Great job!</p>
                </div>
                <div class="news-section__item">
                    <h3>Commerce Dept. Results</h3>
                    <span class="news-section__date">April 10, 2025</span>
                    <p>98% students passed with distinction.</p>
                </div>
                <div class="news-section__item">
                    <h3>HSC Science Results</h3>
                    <span class="news-section__date">April 20, 2025</span>
                    <p>Top Scorer: Meera Shah - 97.2%</p>
                </div>
                <div class="news-section__item">
                    <h3>Engineering Semester 7</h3>
                    <span class="news-section__date">May 5, 2025</span>
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
            <h2 class="component_title">Academic Pass Rates</h2>
            <!-- Additional headings can be uncommented if needed -->
            <!-- <h2 class="component_title">Graduation Performance Data</h2> -->
            <!-- <h2 class="component_title">Historical Passout Rates</h2> -->
            <!-- <h2 class="component_title">Annual Passout Rate</h2> -->
            <!-- <h2 class="component_title">Graduation Success Rates</h2> -->
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
        <h2 class="testimonials-section__title component_title">Student Testimonials</h2>

        <div class="testimonials-section__container">
            <button class="testimonials-section__nav-left">&lt;</button>

            <div class="testimonials-section__slider">

                <div class="testimonials-section__item">
                    <p class="testimonials-section__text">
                        "The practical approach and supportive faculty have transformed my learning experience!"
                    </p>
                    <h3 class="testimonials-section__name">Aisha Kumar</h3>
                    <span class="testimonials-section__course">B.Sc. IT</span>
                </div>

                <div class="testimonials-section__item">
                    <p class="testimonials-section__text">
                        "The vibrant campus life and quality education have set me on the path to success."
                    </p>
                    <h3 class="testimonials-section__name">Rahul Sharma</h3>
                    <span class="testimonials-section__course">BBA</span>
                </div>

                <div class="testimonials-section__item">
                    <p class="testimonials-section__text">
                        "I appreciate the focus on both academic and personal growth at Shri V.J. Modha College."
                    </p>
                    <h3 class="testimonials-section__name">Sneha Patel</h3>
                    <span class="testimonials-section__course">MCA</span>
                </div>

                <div class="testimonials-section__item">
                    <p class="testimonials-section__text">
                        "The practical approach and supportive faculty have transformed my learning experience!"
                    </p>
                    <h3 class="testimonials-section__name">Aisha Kumar</h3>
                    <span class="testimonials-section__course">B.Sc. IT</span>
                </div>

                <div class="testimonials-section__item">
                    <p class="testimonials-section__text">
                        "The vibrant campus life and quality education have set me on the path to success."
                    </p>
                    <h3 class="testimonials-section__name">Rahul Sharma</h3>
                    <span class="testimonials-section__course">BBA</span>
                </div>

                <div class="testimonials-section__item">
                    <p class="testimonials-section__text">
                        "I appreciate the focus on both academic and personal growth at Shri V.J. Modha College."
                    </p>
                    <h3 class="testimonials-section__name">Sneha Patel</h3>
                    <span class="testimonials-section__course">MCA</span>
                </div>

            </div>

            <button class="testimonials-section__nav-right">&gt;</button>
        </div>
    </section>


    <!-- ===================================================
     Photo Carousel Section
     =================================================== -->
    <section class="photo-carousel-section">
        <div class="photo-carousel-section__wrapper">
            <h2 class="photo-carousel-section__title component_title">Photos</h2>
            <button class="photo-carousel-section__nav-left">&lt;</button>

            <div class="photo-carousel-section__carousel" id="carousel">
                <div class="photo-carousel-section__item">
                    <img src="assets/photos/index/photo_carousel/129.jpg" alt="Image 1">
                </div>

                <div class="photo-carousel-section__item">
                    <img src="assets/photos/index/photo_carousel/130.jpg" alt="Image 2">
                </div>

                <div class="photo-carousel-section__item">
                    <img src="assets/photos/index/photo_carousel/131.jpg" alt="Image 3">
                </div>

                <div class="photo-carousel-section__item">
                    <img src="assets/photos/index/photo_carousel/132.jpg" alt="Image 4">
                </div>

                <div class="photo-carousel-section__item">
                    <img src="assets/photos/index/photo_carousel/133.jpg" alt="Image 5">
                </div>

                <div class="photo-carousel-section__item">
                    <img src="assets/photos/index/photo_carousel/129.jpg" alt="Image 1">
                </div>

                <div class="photo-carousel-section__item">
                    <img src="assets/photos/index/photo_carousel/130.jpg" alt="Image 2">
                </div>

                <div class="photo-carousel-section__item">
                    <img src="assets/photos/index/photo_carousel/131.jpg" alt="Image 3">
                </div>

                <div class="photo-carousel-section__item">
                    <img src="assets/photos/index/photo_carousel/132.jpg" alt="Image 4">
                </div>

                <div class="photo-carousel-section__item">
                    <img src="assets/photos/index/photo_carousel/133.jpg" alt="Image 5">
                </div>
            </div>

            <button class="photo-carousel-section__nav-right">&gt;</button>
        </div>
    </section>


    <!-- Back to Top Button with a modern arrow icon -->
    <button id="backToTop" aria-label="Back to top">
        <!-- Inline SVG arrow icon -->
        <svg viewBox="0 0 24 24">
            <path d="M12 4l-8 8h6v8h4v-8h6z"></path>
        </svg>
    </button>

    <!-- ===================================================
         Footer Section
         - Included via PHP for consistency across pages.
         =================================================== -->
    <?php include("footer.html"); ?>

</body>

</html>