// Wait for the DOM to be fully loaded
document.addEventListener("DOMContentLoaded", () => {
    // Hamburger menu functionality
    const hamburger = document.querySelector(".navbar_hamburger");
    const navLinks = document.querySelector(".navbar_nav");
    hamburger.addEventListener("click", () => {
        hamburger.classList.toggle("navbar_hamburger--active");
        navLinks.classList.toggle("navbar_nav--active");
    });
    const modalOverlay = document.getElementById("modalOverlay");
    const logoTrigger = document.getElementById("logoTrigger");
    const closeBtn = document.getElementById("closeBtn");
    // Show Modal
    logoTrigger.addEventListener("click", () => {
        modalOverlay.classList.add("navbar_modal--active");
    });
    // Close Modal (Button Click)
    closeBtn.addEventListener("click", () => {
        modalOverlay.classList.remove("navbar_modal--active");
    });
    // Close Modal (Click Outside)
    modalOverlay.addEventListener("click", (e) => {
        if (e.target === modalOverlay) {
            modalOverlay.classList.remove("navbar_modal--active");
        }
    });
});
