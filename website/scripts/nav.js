/**
 * =======================================================
 * NAVBAR - INTERACTIVE NAVIGATION SYSTEM & LOGO SHRINK
 * Shri V.J. Modha College Portal
 * =======================================================
 */

document.addEventListener("DOMContentLoaded", () => {
    const navbar = document.getElementById("siteNavbar");
    const hamburger = document.getElementById("navbarHamburger");
    const navLinks = document.getElementById("navbarLinks");
    const backdrop = document.getElementById("navbarBackdrop");
    const modalOverlay = document.getElementById("modalOverlay");
    const logoTrigger = document.getElementById("logoTrigger");
    const closeBtn = document.getElementById("closeBtn");
    const dropdownItems = document.querySelectorAll(".navbar_nav__item--dropdown");

    // ----------------------------------------------------
    // 1. Scroll State (Triggers Logo Shrink & Navbar Glass)
    // ----------------------------------------------------
    function handleScroll() {
        if (!navbar) return;
        if (window.scrollY > 25) {
            navbar.classList.add("navbar_container--scrolled");
        } else {
            navbar.classList.remove("navbar_container--scrolled");
        }
    }
    window.addEventListener("scroll", handleScroll, { passive: true });
    handleScroll();


    // ----------------------------------------------------
    // 2. Mobile Drawer Controls
    // ----------------------------------------------------
    const closeAllDropdowns = (except = null) => {
        dropdownItems.forEach(item => {
            if (item !== except) {
                item.classList.remove("js-dropdown-active");
                const link = item.querySelector(".navbar_nav__link");
                if (link) link.setAttribute("aria-expanded", "false");
            }
        });
    };

    function toggleMobileNav(forceClose = false) {
        if (!hamburger || !navLinks) return;

        const isOpen = forceClose ? false : !navLinks.classList.contains("navbar_nav--active");

        hamburger.classList.toggle("navbar_hamburger--active", isOpen);
        hamburger.setAttribute("aria-expanded", isOpen ? "true" : "false");
        navLinks.classList.toggle("navbar_nav--active", isOpen);

        if (backdrop) {
            backdrop.classList.toggle("is-active", isOpen);
        }

        if (isOpen) {
            document.body.style.overflow = "hidden";
        } else {
            document.body.style.overflow = "";
            closeAllDropdowns();
        }
    }

    if (hamburger) {
        hamburger.addEventListener("click", (e) => {
            e.stopPropagation();
            toggleMobileNav();
        });
    }

    if (backdrop) {
        backdrop.addEventListener("click", () => toggleMobileNav(true));
    }

    // Close mobile drawer when clicking regular destination links
    if (navLinks) {
        const directLinks = navLinks.querySelectorAll("a:not([aria-haspopup='true'])");
        directLinks.forEach(link => {
            link.addEventListener("click", () => {
                if (window.innerWidth <= 1120) {
                    toggleMobileNav(true);
                }
            });
        });
    }


    // ----------------------------------------------------
    // 3. Dropdowns Navigation (Desktop & Mobile Accordion)
    // ----------------------------------------------------
    dropdownItems.forEach(item => {
        const link = item.querySelector(".navbar_nav__link");
        if (!link) return;

        link.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();

            const wasActive = item.classList.contains("js-dropdown-active");
            if (wasActive) {
                closeAllDropdowns();
            } else {
                closeAllDropdowns(item);
                item.classList.add("js-dropdown-active");
                link.setAttribute("aria-expanded", "true");
            }
        });
    });

    // Close dropdowns on outside click
    document.addEventListener("click", (e) => {
        const isClickInside = Array.from(dropdownItems).some(item => item.contains(e.target));
        if (!isClickInside) {
            closeAllDropdowns();
        }
    });


    // ----------------------------------------------------
    // 4. Modal Popup Controls (College Overview)
    // ----------------------------------------------------
    function openModal() {
        if (modalOverlay) {
            modalOverlay.classList.add("navbar_modal--active");
            document.body.style.overflow = "hidden";
        }
    }

    function closeModal() {
        if (modalOverlay) {
            modalOverlay.classList.remove("navbar_modal--active");
            document.body.style.overflow = "";
        }
    }

    if (closeBtn) {
        closeBtn.addEventListener("click", closeModal);
    }

    if (modalOverlay) {
        modalOverlay.addEventListener("click", (e) => {
            if (e.target === modalOverlay) closeModal();
        });
    }

    // Keyboard navigation (Escape key)
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            closeModal();
            closeAllDropdowns();
            toggleMobileNav(true);
        }
    });


    // ----------------------------------------------------
    // 5. Automatic Active Navigation Link Highlighter
    // ----------------------------------------------------
    const currentPath = window.location.pathname.split("/").pop() || "index.php";
    const currentSearch = window.location.search;

    const allNavLinks = document.querySelectorAll(".navbar_nav__link, .navbar_dropdown__link");
    allNavLinks.forEach(link => {
        const href = link.getAttribute("href");
        if (!href || href.startsWith("#") || href.startsWith("http")) return;

        const [linkPath, linkSearch] = href.split("?");

        // Exact match with query param (e.g. courses.php?course=bca, labs.php?lab=computer)
        if (linkSearch && currentPath === linkPath && currentSearch.includes(linkSearch)) {
            link.classList.add("is-active-page");
            const parentDropdown = link.closest(".navbar_nav__item--dropdown");
            if (parentDropdown) {
                const parentLink = parentDropdown.querySelector(".navbar_nav__link");
                if (parentLink) parentLink.classList.add("is-active-page");
            }
        }
        // General page match without query
        else if (!linkSearch && currentPath === linkPath) {
            link.classList.add("is-active-page");
            const parentDropdown = link.closest(".navbar_nav__item--dropdown");
            if (parentDropdown) {
                const parentLink = parentDropdown.querySelector(".navbar_nav__link");
                if (parentLink) parentLink.classList.add("is-active-page");
            }
        }
    });

});
