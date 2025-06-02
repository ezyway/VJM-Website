document.addEventListener("DOMContentLoaded", () => {
  const hamburger     = document.querySelector(".navbar_hamburger");
  const navLinks      = document.querySelector(".navbar_nav");
  const modalOverlay  = document.getElementById("modalOverlay");
  const logoTrigger   = document.getElementById("logoTrigger");
  const closeBtn      = document.getElementById("closeBtn");
  const dropdownItems = document.querySelectorAll(".navbar_nav__item--dropdown");

  // Helper to close all dropdowns (unless you pass a reference to skip one)
  const closeAllDropdowns = (except = null) => {
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
      closeAllDropdowns();
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
      // if (window.innerWidth > 1150) return; // Removed to allow JS handling on desktop
      if (link.getAttribute("href") === "#") {
        e.preventDefault();
      }

      const wasActive = item.classList.contains("js-dropdown-active");
      if (wasActive) {
        // If already open, close everything (including this one)
        closeAllDropdowns();
      } else {
        // Otherwise, close others and open this one
        closeAllDropdowns(item);
        item.classList.add("js-dropdown-active");
      }
    });
  });

  // Close any open dropdown if clicking outside
  document.addEventListener("click", (e) => {
    const target = e.target;

    const isMobileNavActive = navLinks.classList.contains("navbar_nav--active");
    // Assuming 1150px is the breakpoint for desktop-like layout where nav items are always visible
    const isLikelyDesktopLayout = window.innerWidth > 1150;

    // This listener should only proceed if:
    // 1. We are on a desktop-like layout OR
    // 2. We are on a mobile-like layout AND the mobile nav is currently open.
    // Otherwise (e.g., mobile layout with nav closed), clicks outside shouldn't affect hidden dropdowns.
    if (!isLikelyDesktopLayout && !isMobileNavActive) {
      return;
    }

    // 1. Ignore clicks on the hamburger itself (it has its own toggle logic)
    if (hamburger.contains(target)) {
      return;
    }

    // 2. Determine if the click is on any dropdown trigger OR inside the content of an OPEN dropdown.
    let clickIsInsideInteractiveDropdownArea = false;
    for (const item of dropdownItems) {
      const trigger = item.querySelector(".navbar_nav__link");
      const content = item.querySelector(".navbar_dropdown"); // Selector for the dropdown content area

      if (trigger && trigger.contains(target)) {
        // Click is on a dropdown trigger. Its own event listener will handle opening/closing.
        clickIsInsideInteractiveDropdownArea = true;
        break;
      }
      if (item.classList.contains("js-dropdown-active") && content && content.contains(target)) {
        // Click is inside the content of an OPEN dropdown. Allow interaction.
        clickIsInsideInteractiveDropdownArea = true;
        break;
      }
    }

    // 3. If the click was not on a trigger and not inside open content, then it's an "outside" click.
    if (!clickIsInsideInteractiveDropdownArea) {
      closeAllDropdowns();
    }
  });
});
