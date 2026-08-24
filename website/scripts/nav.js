/**
 * =======================================================
 * NAVBAR & ADMISSIONS INQUIRY - INTERACTIVE SYSTEM
 * Shri V.J. Modha College Portal
 * =======================================================
 */

document.addEventListener("DOMContentLoaded", () => {
    const navbar = document.getElementById("siteNavbar");
    const hamburger = document.getElementById("navbarHamburger");
    const navLinks = document.getElementById("navbarLinks");
    const backdrop = document.getElementById("navbarBackdrop");
    const modalOverlay = document.getElementById("modalOverlay");
    const closeBtn = document.getElementById("closeBtn");
    const dropdownItems = document.querySelectorAll(".navbar_nav__item--dropdown");

    // Inquiry Modal Elements
    const inquiryModal = document.getElementById("inquiryModal");
    const inquiryBackdrop = document.getElementById("inquiryBackdrop");
    const inquiryCloseBtn = document.getElementById("inquiryCloseBtn");
    const inquirySuccessCloseBtn = document.getElementById("inquirySuccessCloseBtn");
    const inquiryForm = document.getElementById("admissionInquiryForm");
    const inquirySuccessBox = document.getElementById("inquirySuccessBox");
    const inquiryProgramSelect = document.getElementById("inquiryProgram");
    const inquiryPhoneInput = document.getElementById("inquiryPhone");
    const inquiryNameInput = document.getElementById("inquiryName");
    const inquiryStreamSelect = document.getElementById("inquiryStream");
    const inquiryMessageInput = document.getElementById("inquiryMessage");
    const inquirySubmitBtn = document.getElementById("inquirySubmitBtn");
    const inquirySubmitText = document.getElementById("inquirySubmitText");
    const inquiryWhatsAppBtn = document.getElementById("inquiryWhatsAppBtn");
    const siteToast = document.getElementById("siteToast");

    // ----------------------------------------------------
    // 0. Site Toast Helper
    // ----------------------------------------------------
    let toastTimeout = null;
    function showToast(message, type = "success") {
        if (!siteToast) return;
        clearTimeout(toastTimeout);

        siteToast.textContent = message;
        siteToast.className = `site-toast site-toast--${type} is-visible`;
        siteToast.style.display = "flex";

        toastTimeout = setTimeout(() => {
            siteToast.classList.remove("is-visible");
            setTimeout(() => {
                siteToast.style.display = "none";
            }, 300);
        }, 4000);
    }

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
        if (navbar) {
            navbar.classList.toggle("navbar_container--mobile-open", isOpen);
        }

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

    const drawerCloseBtn = document.getElementById("drawerCloseBtn");
    if (drawerCloseBtn) {
        drawerCloseBtn.addEventListener("click", () => toggleMobileNav(true));
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
    // 4. College Overview Modal Controls
    // ----------------------------------------------------
    function openOverviewModal() {
        if (modalOverlay) {
            modalOverlay.classList.add("navbar_modal--active");
            document.body.style.overflow = "hidden";
        }
    }

    function closeOverviewModal() {
        if (modalOverlay) {
            modalOverlay.classList.remove("navbar_modal--active");
            document.body.style.overflow = "";
        }
    }

    if (closeBtn) {
        closeBtn.addEventListener("click", closeOverviewModal);
    }

    if (modalOverlay) {
        modalOverlay.addEventListener("click", (e) => {
            if (e.target === modalOverlay) closeOverviewModal();
        });
    }


    // ----------------------------------------------------
    // 5. Quick Admissions & Course Inquiry Modal
    // ----------------------------------------------------
    const courseCodeMap = {
        "bca": "BCA",
        "bsc": "B.Sc.",
        "bba": "BBA",
        "bcom": "B.Com.",
        "bsw": "BSW",
        "mscit": "M.Sc. IT",
        "mscorgchem": "M.Sc. Chem",
        "mcom": "M.Com."
    };

    function openInquiryModal(preselectedCourseKey = null) {
        if (!inquiryModal) return;

        // Reset state
        if (inquirySuccessBox) inquirySuccessBox.style.display = "none";
        if (inquiryForm) {
            inquiryForm.style.display = "block";
        }

        // Prefill program if specified
        if (preselectedCourseKey && inquiryProgramSelect) {
            const mappedVal = courseCodeMap[preselectedCourseKey.toLowerCase()] || preselectedCourseKey;
            for (let option of inquiryProgramSelect.options) {
                if (option.value === mappedVal || option.value.toLowerCase().includes(preselectedCourseKey.toLowerCase())) {
                    option.selected = true;
                    break;
                }
            }
        }

        inquiryModal.style.display = "flex";
        document.body.style.overflow = "hidden";

        // Focus first input
        setTimeout(() => {
            if (inquiryNameInput) inquiryNameInput.focus();
        }, 100);
    }

    function closeInquiryModal() {
        if (!inquiryModal) return;
        inquiryModal.style.display = "none";
        document.body.style.overflow = "";
    }

    // Global click listener for any element with data-open-inquiry="true"
    document.addEventListener("click", (e) => {
        const trigger = e.target.closest("[data-open-inquiry='true'], #openInquiryBtn");
        if (trigger) {
            e.preventDefault();
            const courseKey = trigger.getAttribute("data-course-inquiry");
            openInquiryModal(courseKey);
        }
    });

    if (inquiryCloseBtn) inquiryCloseBtn.addEventListener("click", closeInquiryModal);
    if (inquiryBackdrop) inquiryBackdrop.addEventListener("click", closeInquiryModal);
    if (inquirySuccessCloseBtn) inquirySuccessCloseBtn.addEventListener("click", closeInquiryModal);

    // Form Validation Helper
    function validateInquiryForm() {
        let isValid = true;
        const nameVal = inquiryNameInput ? inquiryNameInput.value.trim() : "";
        const phoneVal = inquiryPhoneInput ? inquiryPhoneInput.value.trim() : "";
        const progVal = inquiryProgramSelect ? inquiryProgramSelect.value : "";

        // Name
        const nameErr = document.getElementById("inquiryNameError");
        if (!nameVal || nameVal.length < 2) {
            if (nameErr) nameErr.style.display = "block";
            if (inquiryNameInput) inquiryNameInput.classList.add("has-error");
            isValid = false;
        } else {
            if (nameErr) nameErr.style.display = "none";
            if (inquiryNameInput) inquiryNameInput.classList.remove("has-error");
        }

        // Phone (10 digit regex)
        const phoneErr = document.getElementById("inquiryPhoneError");
        const phoneRegex = /^[6-9]\d{9}$/;
        if (!phoneRegex.test(phoneVal.replace(/\D/g, ""))) {
            if (phoneErr) phoneErr.style.display = "block";
            if (inquiryPhoneInput) inquiryPhoneInput.classList.add("has-error");
            isValid = false;
        } else {
            if (phoneErr) phoneErr.style.display = "none";
            if (inquiryPhoneInput) inquiryPhoneInput.classList.remove("has-error");
        }

        // Program
        const progErr = document.getElementById("inquiryProgramError");
        if (!progVal) {
            if (progErr) progErr.style.display = "block";
            if (inquiryProgramSelect) inquiryProgramSelect.classList.add("has-error");
            isValid = false;
        } else {
            if (progErr) progErr.style.display = "none";
            if (inquiryProgramSelect) inquiryProgramSelect.classList.remove("has-error");
        }

        return isValid;
    }

    // Direct WhatsApp Button Generator
    if (inquiryWhatsAppBtn) {
        inquiryWhatsAppBtn.addEventListener("click", () => {
            const nameVal = inquiryNameInput ? inquiryNameInput.value.trim() : "Prospective Student";
            const progVal = inquiryProgramSelect && inquiryProgramSelect.value ? inquiryProgramSelect.value : "College Programs";
            const streamVal = inquiryStreamSelect ? inquiryStreamSelect.value : "12th Standard";
            const msgVal = inquiryMessageInput ? inquiryMessageInput.value.trim() : "";

            let waText = `Hello Shri V.J. Modha College,%0A%0AMy name is *${encodeURIComponent(nameVal)}* (Qualification: ${encodeURIComponent(streamVal)}).%0AI would like to inquire about admissions for the *${encodeURIComponent(progVal)}* program for Academic Year 2026-27.`;
            if (msgVal) {
                waText += `%0A%0AQuery: ${encodeURIComponent(msgVal)}`;
            }

            const waUrl = `https://wa.me/919978818009?text=${waText}`;
            window.open(waUrl, "_blank", "noopener,noreferrer");
            showToast("Opening WhatsApp helpline...", "info");
        });
    }

    // Online Form Submit Handler
    if (inquiryForm) {
        inquiryForm.addEventListener("submit", (e) => {
            e.preventDefault();
            if (!validateInquiryForm()) return;

            // Simulate immediate network submission
            if (inquirySubmitBtn) inquirySubmitBtn.disabled = true;
            if (inquirySubmitText) inquirySubmitText.textContent = "Submitting Inquiry...";

            setTimeout(() => {
                if (inquiryForm) inquiryForm.style.display = "none";
                if (inquirySuccessBox) inquirySuccessBox.style.display = "flex";
                if (inquirySubmitBtn) inquirySubmitBtn.disabled = false;
                if (inquirySubmitText) inquirySubmitText.textContent = "Submit Inquiry Online";

                showToast("Inquiry submitted successfully! We'll call you shortly.", "success");
                inquiryForm.reset();
            }, 600);
        });
    }


    // ----------------------------------------------------
    // 6. Keyboard Shortcuts (Escape Key Handler)
    // ----------------------------------------------------
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            closeInquiryModal();
            closeOverviewModal();
            closeAllDropdowns();
            toggleMobileNav(true);
        }
    });


    // ----------------------------------------------------
    // 7. Automatic Active Navigation Link Highlighter
    // ----------------------------------------------------
    const currentPath = window.location.pathname.split("/").pop() || "index.php";
    const currentSearch = window.location.search;

    const allNavLinks = document.querySelectorAll(".navbar_nav__link, .navbar_dropdown__link");
    allNavLinks.forEach(link => {
        const href = link.getAttribute("href");
        if (!href || href.startsWith("#") || href.startsWith("http")) return;

        const [linkPath, linkSearch] = href.split("?");

        // Exact match with query param
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
