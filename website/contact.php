<?php
    $meta_description = "Get in touch with Shri V.J. Modha College, Porbandar. Find our campus address, phone numbers, WhatsApp helpline, email, interactive map, and admissions inquiry desk.";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "EducationalOrganization",
            "name": "Shri V.J. Modha College of Information Technology - Contact",
            "url": "https://shrivjmodhacollege.com/contact.php",
            "logo": "https://shrivjmodhacollege.com/assets/logo.ico",
            "description": "<?= htmlspecialchars($meta_description) ?>",
            "telephone": "+919978818009",
            "email": "shrivjmodha@gmail.com",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "Vidhyadham, Chhaya-Birla Road, Nr. Pakshi Abhiyaran",
                "addressLocality": "Porbandar",
                "addressRegion": "Gujarat",
                "postalCode": "360575",
                "addressCountry": "IN"
            }
        }
    </script>

    <?php include("header.php"); ?>
</head>

<body>
    <!-- ===================================================
         Navigation Section
         =================================================== -->
    <?php include("nav.html"); ?>


    <!-- ===================================================
         1. Hero Header Banner
         =================================================== -->
    <header class="contact-hero" id="contact-hero">
        <div class="contact-hero__overlay">
            <div class="contact-hero__content">
                <nav class="contact-hero__breadcrumb" aria-label="Breadcrumb">
                    <a href="index.php">Home</a>
                    <span class="contact-hero__breadcrumb-sep">/</span>
                    <span aria-current="page">Contact Us</span>
                </nav>
                <span class="contact-hero__badge">Admissions &amp; Campus Helpdesk</span>
                <h1 class="contact-hero__title">Get in Touch with Us</h1>
                <p class="contact-hero__slogan">॥ सर्वस्य लोचनं शास्त्रम् ॥</p>
                <p class="contact-hero__subtitle">
                    Have questions regarding admissions, degree programs, fee structures, or campus visits? Our counselors and administrative staff are here to assist you.
                </p>
            </div>
        </div>
    </header>


    <!-- ===================================================
         2. Main Contact Grid Section
         =================================================== -->
    <main class="contact-section" id="contact-main">
        <div class="contact-section__container">

            <div class="contact-grid">
                
                <!-- Left Column: Information Cards & Social Hub -->
                <div class="contact-info-col">
                    
                    <div class="contact-card">
                        <span class="section__eyebrow">Direct Channels</span>
                        <h2 class="contact-card__title">Campus Communication</h2>
                        
                        <div class="contact-channels-list">
                            
                            <!-- Phone -->
                            <a href="tel:+919978818009" class="contact-channel-item">
                                <div class="contact-channel-icon">
                                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                </div>
                                <div class="contact-channel-text">
                                    <span class="contact-channel-label">Call Office Hotline</span>
                                    <strong class="contact-channel-val">+91 997 8818 009</strong>
                                </div>
                            </a>

                            <!-- WhatsApp -->
                            <a href="https://wa.me/919978818009?text=Hello%20Shri%20VJ%20Modha%20College,%20I%20would%20like%20to%20inquire%20about%20admissions." target="_blank" rel="noopener noreferrer" class="contact-channel-item contact-channel-item--whatsapp">
                                <div class="contact-channel-icon">
                                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                </div>
                                <div class="contact-channel-text">
                                    <span class="contact-channel-label">WhatsApp Helpdesk</span>
                                    <strong class="contact-channel-val">+91 997 8818 009 / +91 9825 673 093</strong>
                                </div>
                            </a>

                            <!-- Email -->
                            <a href="mailto:shrivjmodha@gmail.com" class="contact-channel-item">
                                <div class="contact-channel-icon">
                                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                </div>
                                <div class="contact-channel-text">
                                    <span class="contact-channel-label">Official Email</span>
                                    <strong class="contact-channel-val">shrivjmodha@gmail.com</strong>
                                </div>
                            </a>

                            <!-- Address -->
                            <div class="contact-channel-item contact-channel-item--static">
                                <div class="contact-channel-icon">
                                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                </div>
                                <div class="contact-channel-text">
                                    <span class="contact-channel-label">Campus Location</span>
                                    <strong class="contact-channel-val">"Vidhyadham", Chhaya-Birla Road, Nr. Pakshi Abhiyaran, Porbandar, Gujarat, 360575</strong>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Social Media Hub Card -->
                    <div class="contact-card">
                        <span class="section__eyebrow">Connect Online</span>
                        <h3 class="contact-card__title">Follow Our Community</h3>
                        <p class="contact-card__desc">Stay updated with campus activities, student achievements, and admission announcements.</p>
                        
                        <div class="contact-social-grid">
                            <a href="https://www.instagram.com/vjmodhacollege/" target="_blank" rel="noopener noreferrer" class="contact-social-btn contact-social-btn--instagram">
                                <img src="assets/icons/footer/instagram.png" alt="Instagram" width="22" height="22" />
                                <span>Instagram</span>
                            </a>
                            <a href="https://www.facebook.com/vjmodhacollege/" target="_blank" rel="noopener noreferrer" class="contact-social-btn contact-social-btn--facebook">
                                <img src="assets/icons/footer/facebook.png" alt="Facebook" width="22" height="22" />
                                <span>Facebook</span>
                            </a>
                            <a href="https://www.linkedin.com/school/vjmodhacollege/" target="_blank" rel="noopener noreferrer" class="contact-social-btn contact-social-btn--linkedin">
                                <img src="assets/icons/footer/linkedin.png" alt="LinkedIn" width="22" height="22" />
                                <span>LinkedIn</span>
                            </a>
                        </div>
                    </div>

                </div>


                <!-- Right Column: Inquiry Form & Interactive Google Map -->
                <div class="contact-form-col">
                    
                    <!-- Quick Inquiry Form -->
                    <div class="contact-card contact-card--form">
                        <span class="section__eyebrow">Online Inquiry Desk</span>
                        <h2 class="contact-card__title">Send Us a Message</h2>
                        <p class="contact-card__desc">Fill out the quick form below and our admissions team will respond promptly.</p>

                        <form id="contactForm" class="contact-form" onsubmit="handleContactSubmit(event)">
                            <div class="form-group-row">
                                <div class="form-group">
                                    <label for="contactName">Full Name *</label>
                                    <input type="text" id="contactName" name="name" class="form-input" placeholder="e.g. Rahul Sharma" required />
                                </div>

                                <div class="form-group">
                                    <label for="contactPhone">Phone Number *</label>
                                    <input type="tel" id="contactPhone" name="phone" class="form-input" placeholder="e.g. +91 98765 43210" required />
                                </div>
                            </div>

                            <div class="form-group-row">
                                <div class="form-group">
                                    <label for="contactEmail">Email Address *</label>
                                    <input type="email" id="contactEmail" name="email" class="form-input" placeholder="e.g. rahul@example.com" required />
                                </div>

                                <div class="form-group">
                                    <label for="contactCourse">Program of Interest</label>
                                    <select id="contactCourse" name="course" class="form-select">
                                        <option value="BCA">B.C.A. (Computer Applications)</option>
                                        <option value="B.Sc.">B.Sc. (Chemistry / Physics / Maths)</option>
                                        <option value="BBA">B.B.A. (Business Administration)</option>
                                        <option value="B.Com">B.Com. (Commerce)</option>
                                        <option value="BSW">B.S.W. (Social Work)</option>
                                        <option value="M.Sc. IT">M.Sc. (IT &amp; CA)</option>
                                        <option value="M.Sc. Chem">M.Sc. (Organic Chemistry)</option>
                                        <option value="M.Com">M.Com. (Commerce)</option>
                                        <option value="General">General Inquiry / Campus Visit</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="contactMessage">Your Message / Query *</label>
                                <textarea id="contactMessage" name="message" class="form-textarea" rows="4" placeholder="Tell us what you'd like to know..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn--primary btn--submit" id="contactSubmitBtn">
                                <span>Send Inquiry</span>
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                            </button>

                            <div id="formFeedback" class="form-feedback" style="display: none;"></div>
                        </form>
                    </div>

                    <!-- Interactive Google Map Card -->
                    <div class="contact-card contact-card--map">
                        <div class="map-card__header">
                            <div>
                                <span class="section__eyebrow">Location Guide</span>
                                <h3 class="contact-card__title" style="margin-bottom: 0;">Find Us on Google Maps</h3>
                            </div>
                            <a href="https://maps.app.goo.gl/1KuyRCNiCoc7pfun9" target="_blank" rel="noopener noreferrer" class="btn btn--outline btn--sm">
                                <span>Open in Maps</span>
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </a>
                        </div>

                        <div class="contact-map-frame">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3530.0519910131816!2d69.6194120749665!3d21.63553296666266!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395634f5f4455d49%3A0xdadec407c49c6b5c!2sShri%20V.%20J.%20Modha%20College%20of%20Information%20Technology!5e1!3m2!1sen!2sin!4v1742061188891!5m2!1sen!2sin"
                                width="100%" height="280" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade" title="College Google Map Location"></iframe>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </main>


    <!-- Back to Top Button -->
    <button id="backToTop" class="back-to-top" aria-label="Back to top">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
            <path d="M12 4l-8 8h6v8h4v-8h6z"></path>
        </svg>
    </button>


    <!-- ===================================================
         Footer Section
         =================================================== -->
    <?php include("footer.html"); ?>

</body>

</html>