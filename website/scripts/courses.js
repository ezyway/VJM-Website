/**
 * ====================================================
 * COURSES PAGE SCRIPTS
 * Shri V.J. Modha College Portal
 * ====================================================
 */

document.addEventListener("DOMContentLoaded", () => {

    // ----------------------------------------------------
    // 1. Directory Filter Tabs (All / UG / PG)
    // ----------------------------------------------------
    const filterButtons = document.querySelectorAll(".courses-filter-btn");
    const programCards = document.querySelectorAll(".program-card");
    const coursesGrid = document.getElementById("coursesGrid");

    if (filterButtons.length > 0 && programCards.length > 0) {
        filterButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                const category = btn.getAttribute("data-filter") || "all";

                filterButtons.forEach(b => {
                    const isMatch = b === btn;
                    b.classList.toggle("is-active", isMatch);
                    b.setAttribute("aria-selected", isMatch ? "true" : "false");
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
                }, 120);
            });
        });
    }


    // ----------------------------------------------------
    // 2. Auto-scroll active switcher pill into view
    // ----------------------------------------------------
    const activeSwitcherPill = document.querySelector(".course-switcher__pill.is-active");
    if (activeSwitcherPill) {
        const switcherContainer = document.querySelector(".course-switcher");
        if (switcherContainer) {
            const containerLeft = switcherContainer.getBoundingClientRect().left;
            const pillLeft = activeSwitcherPill.getBoundingClientRect().left;
            if (pillLeft < containerLeft || pillLeft > containerLeft + switcherContainer.offsetWidth) {
                activeSwitcherPill.scrollIntoView({ behavior: "smooth", inline: "center", block: "nearest" });
            }
        }
    }


    // ----------------------------------------------------
    // 3. Back To Top Button Handler
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
