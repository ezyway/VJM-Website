/**
 * ====================================================
 * COURSES PAGE SCRIPTS - CLIENT-SIDE INSTANT SWITCHER
 * Shri V.J. Modha College Portal
 * ====================================================
 */

document.addEventListener("DOMContentLoaded", () => {

    // ----------------------------------------------------
    // 0. Load Embedded Course Data Payload
    // ----------------------------------------------------
    let payload = null;
    const payloadEl = document.getElementById("coursesPayload");
    if (payloadEl) {
        try {
            payload = JSON.parse(payloadEl.textContent);
        } catch (e) {
            console.error("Failed to parse courses payload", e);
        }
    }

    if (!payload || !payload.courses) return;

    const { courses, fullforms, meta } = payload;

    // View Containers
    const detailView = document.getElementById("courseDetailView");
    const directoryView = document.getElementById("coursesDirectoryView");
    const detailGrid = document.getElementById("courseDetailGrid");

    // Single Course Detail DOM Elements
    const heroBreadcrumb = document.getElementById("courseHeroBreadcrumb");
    const heroBadge = document.getElementById("courseHeroBadge");
    const heroTitle = document.getElementById("courseHeroTitle");
    const heroDuration = document.getElementById("courseHeroDuration");
    const heroMedium = document.getElementById("courseHeroMedium");
    const heroEligibility = document.getElementById("courseHeroEligibility");
    
    const aboutTitle = document.getElementById("courseAboutTitle");
    const overviewText = document.getElementById("courseOverviewText");
    const jobRolesGrid = document.getElementById("courseJobRolesGrid");

    const specEligibility = document.getElementById("specEligibility");
    const specDuration = document.getElementById("specDuration");
    const specMedium = document.getElementById("specMedium");
    const specSubjects = document.getElementById("specSubjects");
    const specHigherStudies = document.getElementById("specHigherStudies");
    const specTiming = document.getElementById("specTiming");
    const syllabusBtn = document.getElementById("courseSyllabusBtn");

    const switcherPills = document.querySelectorAll(".course-switcher__pill");

    let currentActiveCourse = payload.initialCourse || null;
    let isTransitioning = false;

    /**
     * Strip HTML helper
     */
    function stripHtml(html) {
        if (!html) return "";
        const tmp = document.createElement("DIV");
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || "";
    }

    /**
     * Switch course view dynamically without page reload
     */
    function renderCourse(courseKey, updateHistory = true) {
        if (!courseKey || courseKey === "all") {
            // Switch to directory view
            if (directoryView && detailView) {
                detailView.style.display = "none";
                directoryView.style.display = "block";
                directoryView.style.animation = "heroFadeIn 0.35s ease forwards";
                currentActiveCourse = null;
                document.title = "Academic Programs - Shri V.J. Modha College";

                if (updateHistory) {
                    window.history.pushState({ course: "all" }, "", "courses.php");
                }

                window.scrollTo({ top: 0, behavior: "smooth" });
            }
            return;
        }

        const cData = courses[courseKey];
        const cMeta = meta[courseKey];
        const cTitle = fullforms[courseKey] || courseKey.toUpperCase();

        if (!cData || !cMeta) return;

        // Switch to detail view if previously on directory
        if (directoryView && detailView) {
            directoryView.style.display = "none";
            detailView.style.display = "block";
        }

        // Animate content transition
        if (detailGrid) {
            detailGrid.style.opacity = "0.4";
            detailGrid.style.transform = "translateY(8px) scale(0.995)";
        }

        setTimeout(() => {
            // 1. Update Hero
            if (heroBreadcrumb) heroBreadcrumb.textContent = cMeta.code;
            if (heroBadge) heroBadge.textContent = `${cMeta.level} • ${cMeta.dept}`;
            if (heroTitle) heroTitle.textContent = `${cTitle} (${cMeta.code})`;

            const faq = cData.faq || [];
            if (heroDuration) heroDuration.textContent = faq[2] || cMeta.duration;
            if (heroMedium) heroMedium.textContent = stripHtml(faq[1] || cMeta.medium);
            if (heroEligibility) heroEligibility.innerHTML = faq[0] || "12<sup>th</sup> Pass";

            // 2. Update Overview
            if (aboutTitle) aboutTitle.textContent = `About ${cMeta.code}`;
            if (overviewText) overviewText.textContent = (cData.quick_info && cData.quick_info[0]) || "";

            // 3. Update Job Roles
            if (jobRolesGrid && cData.job_roles) {
                jobRolesGrid.innerHTML = "";
                cData.job_roles.forEach((role, idx) => {
                    const chip = document.createElement("div");
                    chip.className = "job-role-chip";
                    chip.style.animation = `facultyCardPop 0.35s cubic-bezier(0.16, 1, 0.3, 1) ${Math.min(idx * 30, 200)}ms forwards`;
                    chip.innerHTML = `
                        <div class="job-role-chip__bullet"></div>
                        <span>${role}</span>
                    `;
                    jobRolesGrid.appendChild(chip);
                });
            }

            // 4. Update Specs Table
            if (specEligibility) specEligibility.innerHTML = faq[0] || "12th Pass";
            if (specDuration) specDuration.textContent = faq[2] || cMeta.duration;
            if (specMedium) specMedium.innerHTML = faq[1] || cMeta.medium;
            if (specSubjects) specSubjects.textContent = `${faq[3] || '5 to 7'} Subjects`;
            if (specHigherStudies) specHigherStudies.textContent = faq[4] || "Post Graduation";
            if (specTiming) specTiming.innerHTML = faq[5] || "Morning Session";

            // 5. Update Syllabus Button & Detail Inquiry Button
            if (syllabusBtn) {
                syllabusBtn.href = faq[6] || "https://www.bknmu.edu.in/Academic/page/Syllabus";
            }

            const detailInquireBtn = document.getElementById("detailInquireBtn");
            if (detailInquireBtn) {
                detailInquireBtn.setAttribute("data-course-inquiry", courseKey);
                const btnSpan = detailInquireBtn.querySelector("span");
                if (btnSpan) btnSpan.textContent = `Apply / Inquire for ${cMeta.code}`;
            }

            // 6. Update Active Switcher Pills
            switcherPills.forEach(pill => {
                const pCourse = pill.getAttribute("data-course");
                const isMatch = pCourse === courseKey;
                pill.classList.toggle("is-active", isMatch);
                if (isMatch) {
                    pill.scrollIntoView({ behavior: "smooth", inline: "center", block: "nearest" });
                }
            });

            // 7. Update Document Title and History
            currentActiveCourse = courseKey;
            document.title = `${cTitle} (${cMeta.code}) - Shri V.J. Modha College`;

            if (updateHistory) {
                window.history.pushState({ course: courseKey }, "", `courses.php?course=${courseKey}`);
            }

            // Fade detail grid back in
            if (detailGrid) {
                detailGrid.style.opacity = "1";
                detailGrid.style.transform = "translateY(0) scale(1)";
            }

            // Scroll up to main container if user is scrolled past hero
            const detailContainer = document.querySelector(".course-detail__container");
            if (detailContainer) {
                const rect = detailContainer.getBoundingClientRect();
                if (rect.top < 0) {
                    window.scrollTo({
                        top: detailContainer.offsetTop - 80,
                        behavior: "smooth"
                    });
                }
            }
        }, 120);
    }


    // ----------------------------------------------------
    // 1. Intercept Course Switcher & Card Links
    // ----------------------------------------------------
    document.addEventListener("click", (e) => {
        const link = e.target.closest("a[data-course], a[href*='courses.php?course=']");
        if (!link) return;

        const href = link.getAttribute("href") || "";
        const dataCourse = link.getAttribute("data-course");

        let targetCourse = null;
        if (dataCourse) {
            targetCourse = dataCourse;
        } else if (href.includes("courses.php?course=")) {
            const match = href.match(/courses\.php\?course=([a-zA-Z0-9]+)/);
            if (match && match[1]) {
                targetCourse = match[1].toLowerCase();
            }
        } else if (href === "courses.php" || href.endsWith("/courses.php")) {
            targetCourse = "all";
        }

        if (targetCourse) {
            e.preventDefault();
            renderCourse(targetCourse, true);
        }
    });


    // ----------------------------------------------------
    // 2. Handle Browser Back & Forward Navigation (popstate)
    // ----------------------------------------------------
    window.addEventListener("popstate", (e) => {
        const urlParams = new URLSearchParams(window.location.search);
        const courseParam = urlParams.get("course");
        if (courseParam && courses[courseParam]) {
            renderCourse(courseParam, false);
        } else {
            renderCourse("all", false);
        }
    });


    // ----------------------------------------------------
    // 3. Interactive Stream Advisor Filtering
    // ----------------------------------------------------
    const streamChips = document.querySelectorAll(".stream-chip");
    const programCards = document.querySelectorAll(".program-card");
    const coursesGrid = document.getElementById("coursesGrid");
    const streamAdvisorStatus = document.getElementById("streamAdvisorStatus");
    const filterButtons = document.querySelectorAll(".courses-filter-btn");

    function applyStreamFilter(stream) {
        let matchCount = 0;

        if (coursesGrid) {
            coursesGrid.style.opacity = "0.5";
            coursesGrid.style.transform = "scale(0.99)";
        }

        setTimeout(() => {
            programCards.forEach((card, idx) => {
                const streams = (card.getAttribute("data-streams") || "").split(" ");
                const shouldShow = (stream === "all" || streams.includes(stream));

                if (shouldShow) {
                    card.style.display = "flex";
                    card.style.animation = `facultyCardPop 0.35s cubic-bezier(0.16, 1, 0.3, 1) ${Math.min(matchCount * 40, 250)}ms forwards`;
                    matchCount++;
                } else {
                    card.style.display = "none";
                }
            });

            if (coursesGrid) {
                coursesGrid.style.opacity = "1";
                coursesGrid.style.transform = "scale(1)";
            }

            if (streamAdvisorStatus) {
                const streamLabels = {
                    "all": "All Programs",
                    "science": "12th Science Stream",
                    "commerce": "12th Commerce Stream",
                    "arts": "12th Arts / Humanities",
                    "any12": "Any 12th Pass",
                    "graduate": "Graduate / PG Degree"
                };
                const label = streamLabels[stream] || "Selected Stream";
                streamAdvisorStatus.innerHTML = `<span>Showing <strong>${matchCount} Eligible Programs</strong> for <em>${label}</em>.</span>`;
            }
        }, 120);
    }

    if (streamChips.length > 0) {
        streamChips.forEach(chip => {
            chip.addEventListener("click", () => {
                const stream = chip.getAttribute("data-stream") || "all";

                streamChips.forEach(c => {
                    const isMatch = c === chip;
                    c.classList.toggle("is-active", isMatch);
                    c.setAttribute("aria-selected", isMatch ? "true" : "false");
                });

                // Reset general UG/PG filter buttons to "All"
                filterButtons.forEach((b, idx) => {
                    b.classList.toggle("is-active", idx === 0);
                    b.setAttribute("aria-selected", idx === 0 ? "true" : "false");
                });

                applyStreamFilter(stream);
            });
        });
    }


    // ----------------------------------------------------
    // 4. Directory Filter Tabs (All / UG / PG)
    // ----------------------------------------------------
    if (filterButtons.length > 0 && programCards.length > 0) {
        filterButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                const category = btn.getAttribute("data-filter") || "all";

                filterButtons.forEach(b => {
                    const isMatch = b === btn;
                    b.classList.toggle("is-active", isMatch);
                    b.setAttribute("aria-selected", isMatch ? "true" : "false");
                });

                // Reset stream chips to "All"
                streamChips.forEach((c, idx) => {
                    c.classList.toggle("is-active", idx === 0);
                    c.setAttribute("aria-selected", idx === 0 ? "true" : "false");
                });

                if (coursesGrid) {
                    coursesGrid.style.opacity = "0.6";
                    coursesGrid.style.transform = "scale(0.99)";
                }

                setTimeout(() => {
                    let visibleIndex = 0;
                    programCards.forEach(card => {
                        const cardCat = card.getAttribute("data-category") || "";
                        const shouldShow = (category === "all" || cardCat === category);

                        if (shouldShow) {
                            card.style.display = "flex";
                            card.style.animation = `facultyCardPop 0.35s cubic-bezier(0.16, 1, 0.3, 1) ${Math.min(visibleIndex * 40, 250)}ms forwards`;
                            visibleIndex++;
                        } else {
                            card.style.display = "none";
                        }
                    });

                    if (coursesGrid) {
                        coursesGrid.style.opacity = "1";
                        coursesGrid.style.transform = "scale(1)";
                    }

                    if (streamAdvisorStatus) {
                        const catLabels = { "all": "All Programs", "ug": "Undergraduate", "pg": "Postgraduate" };
                        streamAdvisorStatus.innerHTML = `<span>Showing <strong>${visibleIndex} Programs</strong> in <em>${catLabels[category] || category}</em>.</span>`;
                    }
                }, 120);
            });
        });
    }


    // ----------------------------------------------------
    // 4. Back To Top Button Handler
    // ----------------------------------------------------
    const backToTopBtn = document.getElementById("backToTop");
    const heroSection = document.getElementById("course-hero") || document.getElementById("courses-hero");

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
