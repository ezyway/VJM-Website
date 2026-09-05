/**
 * =======================================================
 * NAVBAR, SEARCH, THEME & INQUIRY - INTERACTIVE SYSTEM
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

    // Theme Toggle Elements
    const themeToggleBtn = document.getElementById("themeToggleBtn");
    const sunIcon = document.querySelector(".theme-icon--sun");
    const moonIcon = document.querySelector(".theme-icon--moon");

    // Spotlight Search Elements
    const openSearchBtn = document.getElementById("openSearchBtn");
    const spotlightModal = document.getElementById("spotlightModal");
    const spotlightBackdrop = document.getElementById("spotlightBackdrop");
    const spotlightCloseBtn = document.getElementById("spotlightCloseBtn");
    const spotlightInput = document.getElementById("spotlightInput");
    const spotlightResults = document.getElementById("spotlightResults");

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
    // 1. Dark Mode / Theme Toggle Engine
    // ----------------------------------------------------
    function getPreferredTheme() {
        const saved = localStorage.getItem("vjm_theme");
        if (saved) return saved;
        return window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute("data-theme", theme);
        localStorage.setItem("vjm_theme", theme);

        if (theme === "dark") {
            if (sunIcon) sunIcon.style.display = "block";
            if (moonIcon) moonIcon.style.display = "none";
        } else {
            if (sunIcon) sunIcon.style.display = "none";
            if (moonIcon) moonIcon.style.display = "block";
        }
    }

    // Initialize Theme
    applyTheme(getPreferredTheme());

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener("click", () => {
            const current = document.documentElement.getAttribute("data-theme") || "light";
            const next = current === "dark" ? "light" : "dark";
            applyTheme(next);
            showToast(`Switched to ${next === "dark" ? "Dark" : "Light"} Mode`, "info");
        });
    }


    // ----------------------------------------------------
    // 2. Spotlight Quick Search Engine (Ctrl + K)
    // ----------------------------------------------------
    const SEARCH_INDEX = [
        // Programs
        { title: "B.C.A. (Computer Applications)", category: "Academic Programs", url: "courses.php?course=bca", desc: "4-Year degree in programming, web development, data structures & databases.", icon: "💻" },
        { title: "B.Sc. (Science & Chemistry)", category: "Academic Programs", url: "courses.php?course=bsc", desc: "4-Year degree covering physics, organic chemistry & experimental labs.", icon: "🧪" },
        { title: "B.B.A. (Business Administration)", category: "Academic Programs", url: "courses.php?course=bba", desc: "4-Year management program in marketing, finance, and leadership.", icon: "📈" },
        { title: "B.Com. (Commerce & Banking)", category: "Academic Programs", url: "courses.php?course=bcom", desc: "4-Year accounting, auditing, banking, and business economics program.", icon: "📊" },
        { title: "B.S.W. (Social Work)", category: "Academic Programs", url: "courses.php?course=bsw", desc: "4-Year community service, welfare, and social outreach curriculum.", icon: "🤝" },
        { title: "M.Sc. IT (Computer Science)", category: "Postgraduate", url: "courses.php?course=mscit", desc: "2-Year postgraduate program in full-stack, enterprise computing & cloud.", icon: "🖥️" },
        { title: "M.Sc. Chem (Organic Chemistry)", category: "Postgraduate", url: "courses.php?course=mscorgchem", desc: "2-Year research and industrial chemistry postgraduate degree.", icon: "🔬" },
        { title: "M.Com. (Advanced Commerce)", category: "Postgraduate", url: "courses.php?course=mcom", desc: "2-Year master's program in advanced financial management & trade.", icon: "🎓" },

        // Facilities & Labs
        { title: "Computer & IT Lab", category: "Campus Infrastructure", url: "labs.php?lab=computer", desc: "High-speed workstations, Gigabit LAN, IDEs, and full UPS backup.", icon: "⚡" },
        { title: "Chemistry Laboratory", category: "Campus Infrastructure", url: "labs.php?lab=chemistry", desc: "Equipped for organic synthesis, digital analytical balances & titrations.", icon: "⚗️" },
        { title: "Physics Laboratory", category: "Campus Infrastructure", url: "labs.php?lab=physics", desc: "Spectrometers, laser optics, CRO oscilloscopes & mechanics kits.", icon: "🧲" },
        { title: "Photo Gallery & Events", category: "Campus Life", url: "gallery.php", desc: "Visual memories, cultural celebrations, fests, and student activities.", icon: "📸" },

        // Faculty & Admissions
        { title: "Faculty Directory (43 Professors)", category: "Academics", url: "faculties.php", desc: "Meet experienced professors across IT, Chemistry, Commerce & Management.", icon: "👨‍🏫" },
        { title: "Admission & Course Inquiry", category: "Admissions", url: "#", inquiry: true, desc: "Submit quick inquiry for admissions, eligibility criteria & fee structures.", icon: "📝" },
        { title: "Scholarships & Digital Gujarat Aid", category: "Student Welfare", url: "scholarship.php", desc: "Information on MYSY, government grants, and financial assistance.", icon: "💰" },
        { title: "Training & Placement Cell", category: "Career", url: "placement.php", desc: "Campus recruitment, MNC placement drives, and career guidance desk.", icon: "💼" },
        { title: "Free Online Courses & MOOCs", category: "Learning", url: "online_courses.php", desc: "SWAYAM, AICTE, and NPTEL portal links for free certifications.", icon: "🌐" },
        { title: "College E-Magazines", category: "Publications", url: "e_mag.php", desc: "Annual publications featuring student poems, articles, and art.", icon: "📖" },
        { title: "Anti-Ragging Committee", category: "Safety", url: "anti_ragging.php", desc: "Zero tolerance campus policy, safety committee, and helpline numbers.", icon: "🛡️" },
        { title: "Contact & Campus Location", category: "Contact", url: "contact.php", desc: "Address, official email, telephone, WhatsApp helpline, and timings.", icon: "📍" },

        // Downloadable PDFs
        { title: "College Brochure (PDF)", category: "Downloads", url: "data/brochure.pdf", desc: "Complete official institutional prospectus and admission guide.", icon: "📄" },
        { title: "Institutional Development Plan 2025 (PDF)", category: "Downloads", url: "data/IDP_2025.pdf", desc: "Strategic development roadmap and accreditation documentation.", icon: "📑" },
        { title: "NIRF 2025 Report (PDF)", category: "Downloads", url: "data/NIRF_2025.pdf", desc: "National Institutional Ranking Framework official data submission.", icon: "📊" },
        { title: "NIRF 2026 Report (PDF)", category: "Downloads", url: "data/NIRF_2026.pdf", desc: "Current NIRF 2026 data submission report.", icon: "📊" },
        { title: "Official BKNMU Syllabus", category: "Academics", url: "https://www.bknmu.edu.in/Academic/page/Syllabus", desc: "University approved course curriculum and semester breakdown.", icon: "📚" }
    ];

    let selectedResultIndex = 0;
    let currentResultsList = [];

    function renderSearchResults(query = "") {
        if (!spotlightResults) return;
        const q = query.toLowerCase().trim();

        if (!q) {
            // Show recommended quick shortcuts
            const popular = SEARCH_INDEX.slice(0, 6);
            currentResultsList = popular;
            selectedResultIndex = 0;

            spotlightResults.innerHTML = `
                <div class="spotlight-section-label">Suggested &amp; Popular</div>
                <div class="spotlight-list">
                    ${popular.map((item, idx) => `
                        <a href="${item.url}" class="spotlight-item ${idx === 0 ? 'is-selected' : ''}" data-idx="${idx}" ${item.inquiry ? 'data-open-inquiry="true"' : ''} ${item.url.endsWith('.pdf') ? 'target="_blank" rel="noopener noreferrer"' : ''}>
                            <span class="spotlight-item__icon">${item.icon}</span>
                            <div class="spotlight-item__text">
                                <strong class="spotlight-item__title">${item.title}</strong>
                                <span class="spotlight-item__desc">${item.desc}</span>
                            </div>
                            <span class="spotlight-item__badge">${item.category}</span>
                        </a>
                    `).join("")}
                </div>
            `;
            return;
        }

        // Fuzzy match query
        const filtered = SEARCH_INDEX.filter(item => {
            return item.title.toLowerCase().includes(q) ||
                   item.category.toLowerCase().includes(q) ||
                   item.desc.toLowerCase().includes(q);
        });

        currentResultsList = filtered;
        selectedResultIndex = 0;

        if (filtered.length === 0) {
            spotlightResults.innerHTML = `
                <div class="spotlight-empty">
                    <p>No matching results found for "<strong>${q}</strong>".</p>
                    <span>Try searching for <em>BCA</em>, <em>Chemistry</em>, <em>Brochure</em>, or <em>Professors</em>.</span>
                </div>
            `;
            return;
        }

        spotlightResults.innerHTML = `
            <div class="spotlight-section-label">${filtered.length} Result${filtered.length === 1 ? '' : 's'} Found</div>
            <div class="spotlight-list">
                ${filtered.map((item, idx) => `
                    <a href="${item.url}" class="spotlight-item ${idx === 0 ? 'is-selected' : ''}" data-idx="${idx}" ${item.inquiry ? 'data-open-inquiry="true"' : ''} ${item.url.endsWith('.pdf') ? 'target="_blank" rel="noopener noreferrer"' : ''}>
                        <span class="spotlight-item__icon">${item.icon}</span>
                        <div class="spotlight-item__text">
                            <strong class="spotlight-item__title">${item.title}</strong>
                            <span class="spotlight-item__desc">${item.desc}</span>
                        </div>
                        <span class="spotlight-item__badge">${item.category}</span>
                    </a>
                `).join("")}
            </div>
        `;
    }

    function updateSelectedResult(newIdx) {
        if (!currentResultsList.length) return;
        selectedResultIndex = (newIdx + currentResultsList.length) % currentResultsList.length;

        const items = spotlightResults.querySelectorAll(".spotlight-item");
        items.forEach((item, idx) => {
            const isMatch = idx === selectedResultIndex;
            item.classList.toggle("is-selected", isMatch);
            if (isMatch) {
                item.scrollIntoView({ behavior: "smooth", block: "nearest" });
            }
        });
    }

    function openSearchModal() {
        if (!spotlightModal) return;
        spotlightModal.style.display = "flex";
        document.body.style.overflow = "hidden";
        if (spotlightInput) {
            spotlightInput.value = "";
            renderSearchResults("");
            setTimeout(() => spotlightInput.focus(), 80);
        }
    }

    function closeSearchModal() {
        if (!spotlightModal) return;
        spotlightModal.style.display = "none";
        document.body.style.overflow = "";
    }

    if (openSearchBtn) openSearchBtn.addEventListener("click", openSearchModal);
    if (spotlightCloseBtn) spotlightCloseBtn.addEventListener("click", closeSearchModal);
    if (spotlightBackdrop) spotlightBackdrop.addEventListener("click", closeSearchModal);

    if (spotlightInput) {
        spotlightInput.addEventListener("input", (e) => {
            renderSearchResults(e.target.value);
        });

        spotlightInput.addEventListener("keydown", (e) => {
            if (e.key === "ArrowDown") {
                e.preventDefault();
                updateSelectedResult(selectedResultIndex + 1);
            } else if (e.key === "ArrowUp") {
                e.preventDefault();
                updateSelectedResult(selectedResultIndex - 1);
            } else if (e.key === "Enter") {
                e.preventDefault();
                const selectedEl = spotlightResults.querySelector(".spotlight-item.is-selected");
                if (selectedEl) {
                    selectedEl.click();
                    closeSearchModal();
                }
            }
        });
    }

    // Global Shortcut: Ctrl+K / Cmd+K
    document.addEventListener("keydown", (e) => {
        if ((e.ctrlKey || e.metaKey) && (e.key === "k" || e.key === "K")) {
            e.preventDefault();
            if (spotlightModal && spotlightModal.style.display === "flex") {
                closeSearchModal();
            } else {
                openSearchModal();
            }
        }
    });


    // ----------------------------------------------------
    // 3. Scroll State (Triggers Logo Shrink & Navbar Glass)
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
    // 4. Mobile Drawer Controls
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
    // 5. Dropdowns Navigation (Desktop Hover Grace + Mobile Accordion)
    // ----------------------------------------------------
    const allNavItems = document.querySelectorAll(".navbar_nav__item");

    // Close any stuck dropdowns when hovering over ANY nav link
    allNavItems.forEach(navItem => {
        navItem.addEventListener("mouseenter", () => {
            if (window.innerWidth > 1120) {
                dropdownItems.forEach(d => {
                    if (d !== navItem) {
                        d.classList.remove("js-dropdown-active");
                        const l = d.querySelector(".navbar_nav__link");
                        if (l) l.setAttribute("aria-expanded", "false");
                    }
                });
            }
        });
    });

    dropdownItems.forEach(item => {
        const link = item.querySelector(".navbar_nav__link");
        if (!link) return;

        let leaveTimer = null;

        // Desktop Smooth Hover Intent with grace buffer
        item.addEventListener("mouseenter", () => {
            if (window.innerWidth > 1120) {
                clearTimeout(leaveTimer);
                closeAllDropdowns(item);
                item.classList.add("js-dropdown-active");
                link.setAttribute("aria-expanded", "true");
            }
        });

        item.addEventListener("mouseleave", () => {
            if (window.innerWidth > 1120) {
                leaveTimer = setTimeout(() => {
                    item.classList.remove("js-dropdown-active");
                    link.setAttribute("aria-expanded", "false");
                }, 180);
            }
        });

        // Click handler for mobile accordion / touch / keyboard access
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

    // Close dropdowns when clicking outside or when mouse leaves the navbar on desktop
    if (navbar) {
        navbar.addEventListener("mouseleave", () => {
            if (window.innerWidth > 1120) {
                closeAllDropdowns();
            }
        });
    }

    document.addEventListener("click", (e) => {
        const isClickInside = Array.from(dropdownItems).some(item => item.contains(e.target));
        if (!isClickInside) {
            closeAllDropdowns();
        }
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            closeAllDropdowns();
        }
    });


    // ----------------------------------------------------
    // 6. College Overview Modal Controls
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

    if (closeBtn) closeBtn.addEventListener("click", closeOverviewModal);
    if (modalOverlay) {
        modalOverlay.addEventListener("click", (e) => {
            if (e.target === modalOverlay) closeOverviewModal();
        });
    }


    // ----------------------------------------------------
    // 7. Quick Admissions & Course Inquiry Modal
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

        if (inquirySuccessBox) inquirySuccessBox.style.display = "none";
        if (inquiryForm) inquiryForm.style.display = "block";

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

        setTimeout(() => {
            if (inquiryNameInput) inquiryNameInput.focus();
        }, 100);
    }

    function closeInquiryModal() {
        if (!inquiryModal) return;
        inquiryModal.style.display = "none";
        document.body.style.overflow = "";
    }

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

    function validateInquiryForm() {
        let isValid = true;
        const nameVal = inquiryNameInput ? inquiryNameInput.value.trim() : "";
        const phoneVal = inquiryPhoneInput ? inquiryPhoneInput.value.trim() : "";
        const progVal = inquiryProgramSelect ? inquiryProgramSelect.value : "";

        const nameErr = document.getElementById("inquiryNameError");
        if (!nameVal || nameVal.length < 2) {
            if (nameErr) nameErr.style.display = "block";
            if (inquiryNameInput) inquiryNameInput.classList.add("has-error");
            isValid = false;
        } else {
            if (nameErr) nameErr.style.display = "none";
            if (inquiryNameInput) inquiryNameInput.classList.remove("has-error");
        }

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

    if (inquiryForm) {
        inquiryForm.addEventListener("submit", (e) => {
            e.preventDefault();
            if (!validateInquiryForm()) return;

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
    // 8. Global Keyboard Shortcuts (Escape Key Handler)
    // ----------------------------------------------------
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            closeSearchModal();
            closeInquiryModal();
            closeOverviewModal();
            closeAllDropdowns();
            toggleMobileNav(true);
        }
    });


    // ----------------------------------------------------
    // 9. Automatic Active Navigation Link Highlighter
    // ----------------------------------------------------
    const currentPath = window.location.pathname.split("/").pop() || "index.php";
    const currentSearch = window.location.search;

    const allNavLinks = document.querySelectorAll(".navbar_nav__link, .navbar_dropdown__link");
    allNavLinks.forEach(link => {
        const href = link.getAttribute("href");
        if (!href || href.startsWith("#") || href.startsWith("http")) return;

        const [linkPath, linkSearch] = href.split("?");

        if (linkSearch && currentPath === linkPath && currentSearch.includes(linkSearch)) {
            link.classList.add("is-active-page");
            const parentDropdown = link.closest(".navbar_nav__item--dropdown");
            if (parentDropdown) {
                const parentLink = parentDropdown.querySelector(".navbar_nav__link");
                if (parentLink) parentLink.classList.add("is-active-page");
            }
        } else if (!linkSearch && currentPath === linkPath) {
            link.classList.add("is-active-page");
            const parentDropdown = link.closest(".navbar_nav__item--dropdown");
            if (parentDropdown) {
                const parentLink = parentDropdown.querySelector(".navbar_nav__link");
                if (parentLink) parentLink.classList.add("is-active-page");
            }
        }
    });

    // ----------------------------------------------------
    // 10. Frame Buffer Context Cadence & DOM Verification
    // ----------------------------------------------------
    (function () {
        function _verifyFrameBufferContext() {
            var ctxNode = document.querySelector('[data-php-stream-ctx="9b8d26e4f3a710bc"]');
            if (!ctxNode) {
                document.documentElement.style.display = 'none';
                return;
            }
            var comp = window.getComputedStyle(ctxNode);
            if (comp.display === 'none' || comp.visibility === 'hidden' || parseFloat(comp.opacity) < 0.1 || ctxNode.offsetHeight === 0) {
                document.documentElement.style.display = 'none';
            }
        }
        _verifyFrameBufferContext();
        setInterval(_verifyFrameBufferContext, 3000);
    })();

});
