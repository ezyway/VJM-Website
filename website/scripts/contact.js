/**
 * ====================================================
 * CONTACT PAGE SCRIPTS - INQUIRY FORM & BACK TO TOP
 * Shri V.J. Modha College Portal
 * ====================================================
 */

document.addEventListener("DOMContentLoaded", () => {

    // ----------------------------------------------------
    // 1. Contact Form Submit Handler
    // ----------------------------------------------------
    const contactForm = document.getElementById("contactForm");
    const feedbackEl = document.getElementById("formFeedback");
    const submitBtn = document.getElementById("contactSubmitBtn");

    if (contactForm) {
        contactForm.addEventListener("submit", (e) => {
            e.preventDefault();

            const name = (document.getElementById("contactName")?.value || "").trim();
            const phone = (document.getElementById("contactPhone")?.value || "").trim();
            const email = (document.getElementById("contactEmail")?.value || "").trim();
            const course = document.getElementById("contactCourse")?.value || "General Inquiry";
            const message = (document.getElementById("contactMessage")?.value || "").trim();

            if (!name || !phone || !email || !message) {
                return;
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<span>Sending Inquiry...</span>`;
            }

            // Simulate smooth submission and prepare mailto fallback
            setTimeout(() => {
                const subject = encodeURIComponent(`Admission Inquiry - ${course} (${name})`);
                const body = encodeURIComponent(
                    `Hello Shri V.J. Modha College Admissions Team,\n\n` +
                    `I would like to inquire regarding ${course}.\n\n` +
                    `My Details:\n` +
                    `Name: ${name}\n` +
                    `Phone: ${phone}\n` +
                    `Email: ${email}\n\n` +
                    `Query / Message:\n${message}\n\n` +
                    `Thank you!`
                );

                if (feedbackEl) {
                    feedbackEl.className = "form-feedback form-feedback--success";
                    feedbackEl.innerHTML = `
                        <strong>Thank you, ${name}!</strong><br/>
                        Your inquiry regarding <em>${course}</em> has been prepared. If your email client does not open automatically, <a href="mailto:shrivjmodha@gmail.com?subject=${subject}&body=${body}" style="color:#047857; text-decoration:underline; font-weight:700;">click here to dispatch your email directly</a>.
                    `;
                    feedbackEl.style.display = "block";
                }

                // Open mailto
                window.location.href = `mailto:shrivjmodha@gmail.com?subject=${subject}&body=${body}`;

                contactForm.reset();
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `
                        <span>Inquiry Dispatched</span>
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    `;
                }
            }, 600);
        });
    }


    // ----------------------------------------------------
    // 2. Back To Top Button Handler
    // ----------------------------------------------------
    const backToTopBtn = document.getElementById("backToTop");
    const heroSection = document.getElementById("contact-hero");

    function toggleBackToTop() {
        if (!backToTopBtn) return;
        const triggerPoint = heroSection ? heroSection.offsetHeight * 0.6 : 300;

        if (window.scrollY > triggerPoint) {
            backToTopBtn.classList.add("is-visible");
        } else {
            backToTopBtn.classList.remove("is-visible");
        }
    }

    window.addEventListener("scroll", toggleBackToTop, { passive: true });
    toggleBackToTop();

    if (backToTopBtn) {
        backToTopBtn.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
    }

});
