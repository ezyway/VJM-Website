document.addEventListener("DOMContentLoaded", () => {
  const hamburger     = document.querySelector(".navbar_hamburger");
  const navLinks      = document.querySelector(".navbar_nav");
  const modalOverlay  = document.getElementById("modalOverlay");
  const logoTrigger   = document.getElementById("logoTrigger");
  const closeBtn      = document.getElementById("closeBtn");
  const dropdownItems = document.querySelectorAll(".navbar_nav__item--dropdown");

  // Helper to close all dropdowns (unless you pass a reference to skip one)
  const closeAllMobileDropdowns = (except = null) => {
    dropdownItems.forEach(item => {
      if (item !== except) {
        item.classList.remove("js-dropdown-active");
      }
    });
  };

  // Toggle mobile nav & close dropdowns when collapsing
  hamburger.addEventListener("click", () => {
    hamburger.classList.toggle("navbar_hamburger--active");
    navLinks.classList.toggle("navbar_nav--active");
    if (!navLinks.classList.contains("navbar_nav--active")) {
      closeAllMobileDropdowns();
    }
  });

  // Open modal
  logoTrigger.addEventListener("click", () => {
    modalOverlay.classList.add("navbar_modal--active");
  });

  // Close modal (button or click-outside)
  closeBtn.addEventListener("click", () => {
    modalOverlay.classList.remove("navbar_modal--active");
  });
  modalOverlay.addEventListener("click", (e) => {
    if (e.target === modalOverlay) {
      modalOverlay.classList.remove("navbar_modal--active");
    }
  });

  // Handle each dropdown link in mobile view
  dropdownItems.forEach(item => {
    const link = item.querySelector(".navbar_nav__link");
    link.addEventListener("click", (e) => {
      if (window.innerWidth > 1150) return;         // desktop: let CSS hover handle it
      if (link.getAttribute("href") === "#") {
        e.preventDefault();
      }

      const wasActive = item.classList.contains("js-dropdown-active");

      if (wasActive) {
        // If already open, close everything (including this one)
        closeAllMobileDropdowns();
      } else {
        // Otherwise, close others and open this one
        closeAllMobileDropdowns(item);
        item.classList.add("js-dropdown-active");
      }
    });
  });

  // Close any open dropdown if clicking outside (mobile only)
  document.addEventListener("click", (e) => {
    if (window.innerWidth > 1150) return;                          // desktop: ignore
    if (!navLinks.classList.contains("navbar_nav--active")) return; // nav closed: nothing to do
    if (hamburger.contains(e.target)) return;                       // clicking hamburger itself

    // Check if click is on a dropdown trigger or inside an open dropdown’s content
    const clickedInsideDropdown = Array.from(dropdownItems).some(item => {
      const trigger = item.querySelector(".navbar_nav__link");
      const content = item.querySelector(".navbar_dropdown");
      return (
        trigger.contains(e.target) ||
        (item.classList.contains("js-dropdown-active") && content.contains(e.target))
      );
    });

    if (!clickedInsideDropdown) {
      closeAllMobileDropdowns();
    }
  });
});
