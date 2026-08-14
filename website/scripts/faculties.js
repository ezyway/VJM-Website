/**
 * ====================================================
 * FACULTIES PAGE SCRIPTS - ANIMATED & FILTERABLE DIRECTORY
 * Shri V.J. Modha College Portal
 * ====================================================
 */

document.addEventListener("DOMContentLoaded", () => {

    const tabButtons = document.querySelectorAll(".faculty-tab-btn");
    const facultyCards = document.querySelectorAll(".faculty-card");
    const searchInput = document.getElementById("facultySearch");
    const searchClearBtn = document.getElementById("facultySearchClear");
    const emptyState = document.getElementById("facultyEmptyState");
    const resetFiltersBtn = document.getElementById("resetFiltersBtn");
    const facultyGrid = document.getElementById("facultyGrid");

    let currentDept = "all";
    let currentQuery = "";
    let isTransitioning = false;

    /**
     * Filter and smoothly animate faculty cards
     */
    function filterFaculties() {
        let visibleIndex = 0;
        const normalizedQuery = currentQuery.trim().toLowerCase();

        facultyCards.forEach(card => {
            const deptsAttr = card.getAttribute("data-depts") || "";
            const deptsList = deptsAttr.split(/\s+/).filter(Boolean);
            const cardName = card.getAttribute("data-name") || "";
            const cardDesignation = card.getAttribute("data-designation") || "";

            const matchesDept = (currentDept === "all" || deptsList.includes(currentDept));
            const matchesQuery = !normalizedQuery || 
                                 cardName.includes(normalizedQuery) || 
                                 cardDesignation.includes(normalizedQuery);

            if (matchesDept && matchesQuery) {
                // Show card with staggered entrance animation
                card.classList.remove("is-animating-out");
                card.style.display = "flex";
                
                // Add staggered animation delay
                const delayMs = Math.min(visibleIndex * 35, 300);
                card.style.animationDelay = `${delayMs}ms`;
                card.classList.remove("is-animating-in");
                
                // Force reflow to restart animation smoothly
                void card.offsetWidth;
                card.classList.add("is-animating-in");

                visibleIndex++;
            } else {
                // Hide card smoothly
                card.classList.remove("is-animating-in");
                card.classList.add("is-animating-out");
                card.style.display = "none";
            }
        });

        // Toggle Empty State
        if (emptyState) {
            if (visibleIndex === 0) {
                emptyState.style.display = "block";
                if (facultyGrid) facultyGrid.style.display = "none";
            } else {
                emptyState.style.display = "none";
                if (facultyGrid) facultyGrid.style.display = "grid";
            }
        }
    }

    /**
     * Set active department tab
     */
    function setActiveDepartment(deptKey) {
        if (isTransitioning && currentDept === deptKey) return;
        currentDept = deptKey;

        tabButtons.forEach(btn => {
            const isMatch = btn.getAttribute("data-dept") === deptKey;
            btn.classList.toggle("is-active", isMatch);
            btn.setAttribute("aria-selected", isMatch ? "true" : "false");
        });

        // Smooth fade out of grid before animating new cards in
        if (facultyGrid) {
            facultyGrid.style.opacity = "0.6";
            facultyGrid.style.transform = "scale(0.99)";
            
            setTimeout(() => {
                filterFaculties();
                facultyGrid.style.opacity = "1";
                facultyGrid.style.transform = "scale(1)";
            }, 120);
        } else {
            filterFaculties();
        }
    }

    // Department Tab Click Events
    tabButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            const dept = btn.getAttribute("data-dept") || "all";
            setActiveDepartment(dept);
        });
    });

    // Search Input Event (Debounced)
    let searchTimeout = null;
    if (searchInput) {
        searchInput.addEventListener("input", (e) => {
            currentQuery = e.target.value;

            if (searchClearBtn) {
                searchClearBtn.style.display = currentQuery.length > 0 ? "block" : "none";
            }

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(filterFaculties, 150);
        });
    }

    // Clear Search Event
    if (searchClearBtn && searchInput) {
        searchClearBtn.addEventListener("click", () => {
            searchInput.value = "";
            currentQuery = "";
            searchClearBtn.style.display = "none";
            searchInput.focus();
            filterFaculties();
        });
    }

    // Reset All Filters Event
    if (resetFiltersBtn && searchInput) {
        resetFiltersBtn.addEventListener("click", () => {
            searchInput.value = "";
            currentQuery = "";
            if (searchClearBtn) searchClearBtn.style.display = "none";
            setActiveDepartment("all");
        });
    }

    // Check for URL Hash (e.g., #bca, #bsc) or Query param (?dept=bca)
    const urlParams = new URLSearchParams(window.location.search);
    const deptParam = urlParams.get("dept");
    const hashParam = window.location.hash.replace("#", "");

    if (deptParam) {
        setActiveDepartment(deptParam);
    } else if (hashParam) {
        setActiveDepartment(hashParam);
    } else {
        filterFaculties();
    }


    // ----------------------------------------------------
    // Back To Top Button Handler
    // ----------------------------------------------------
    const backToTopBtn = document.getElementById("backToTop");
    const facultiesHero = document.getElementById("faculties-hero");

    function toggleBackToTop() {
        if (!backToTopBtn) return;
        const triggerPoint = facultiesHero ? facultiesHero.offsetHeight * 0.6 : 300;

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
