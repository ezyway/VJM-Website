// Wait for the DOM to be fully loaded
document.addEventListener("DOMContentLoaded", () => {
    // Hamburger menu functionality
    const hamburger = document.querySelector(".hamburger");
    const navLinks = document.querySelector(".nav-links");

    hamburger.addEventListener("click", () => {
        hamburger.classList.toggle("active");
        navLinks.classList.toggle("active");
    });

    const modalOverlay = document.getElementById("modalOverlay");
    const logoTrigger = document.getElementById("logoTrigger");
    const closeBtn = document.getElementById("closeBtn");

    // Show Modal
    logoTrigger.addEventListener("click", () => {
        modalOverlay.classList.add("active");
    });

    // Close Modal (Button Click)
    closeBtn.addEventListener("click", () => {
        modalOverlay.classList.remove("active");
    });

    // Close Modal (Click Outside)
    modalOverlay.addEventListener("click", (e) => {
        if (e.target === modalOverlay) {
            modalOverlay.classList.remove("active");
        }
    });


    // Nav Image Shrink on Scroll
    const logo = document.querySelector('.logo');
    const mobileLogo = document.querySelector('.mobile-logo');

    window.addEventListener('scroll', function () {
        if (window.scrollY > 50) { // Adjust the scroll threshold as needed
            if (logo) {
                logo.classList.add('scrolled');
            }
            if (mobileLogo) {
                mobileLogo.classList.add('scrolled');
            }
        } else {
            if (logo) {
                logo.classList.remove('scrolled');
            }
            if (mobileLogo) {
                mobileLogo.classList.remove('scrolled');
            }
        }
    });
});
