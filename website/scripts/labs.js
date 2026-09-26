/**
 * ====================================================
 * LABS PAGE SCRIPTS - INSTANT SWITCHER & PHOTO CAROUSEL
 * Shri V.J. Modha College Portal
 * ====================================================
 */

document.addEventListener("DOMContentLoaded", () => {

    // ----------------------------------------------------
    // 0. Load Embedded Labs Payload
    // ----------------------------------------------------
    let labsPayload = null;
    const payloadEl = document.getElementById("labsPayload");
    if (payloadEl) {
        try {
            labsPayload = JSON.parse(payloadEl.textContent);
        } catch (e) {
            console.error("Failed to parse labs payload", e);
        }
    }

    if (!labsPayload || !labsPayload.labs) return;

    const { labs } = labsPayload;

    // DOM Elements
    const heroBreadcrumb = document.getElementById("labHeroBreadcrumb");
    const heroBadge = document.getElementById("labHeroBadge");
    const heroTitle = document.getElementById("labHeroTitle");
    const heroSubtitle = document.getElementById("labHeroSubtitle");

    const detailLayout = document.getElementById("labDetailLayout");
    const mainImage = document.getElementById("labMainImage");
    const carouselCounter = document.getElementById("labCarouselCounter");
    const prevBtn = document.getElementById("labPrevBtn");
    const nextBtn = document.getElementById("labNextBtn");
    const thumbnailsStrip = document.getElementById("labThumbnailsStrip");

    const overviewTitle = document.getElementById("labOverviewTitle");
    const descriptionText = document.getElementById("labDescriptionText");
    const featuresList = document.getElementById("labFeaturesList");
    const specsGrid = document.getElementById("labSpecsGrid");

    const switcherPills = document.querySelectorAll(".lab-switcher__pill");

    let currentLabKey = labsPayload.initialLab || "computer";
    let currentPhotos = (labs[currentLabKey] && labs[currentLabKey].images) || [];
    let currentPhotoIndex = 0;
    let autoPlayTimer = null;
    let renderTimer = null;

    /**
     * Update Carousel Image
     */
    function updatePhoto(index) {
        if (!currentPhotos.length) return;
        currentPhotoIndex = (index + currentPhotos.length) % currentPhotos.length;

        if (mainImage) {
            mainImage.style.opacity = "0.4";
            mainImage.style.transform = "scale(0.98)";

            const nextSrc = currentPhotos[currentPhotoIndex];
            const tempImg = new Image();
            tempImg.src = nextSrc;
            tempImg.onload = () => {
                mainImage.src = nextSrc;
                mainImage.style.opacity = "1";
                mainImage.style.transform = "scale(1)";
            };
        }

        if (carouselCounter) {
            carouselCounter.textContent = `${currentPhotoIndex + 1} / ${currentPhotos.length}`;
        }

        if (thumbnailsStrip) {
            const thumbBtns = thumbnailsStrip.querySelectorAll(".lab-thumb-btn");
            thumbBtns.forEach((btn, idx) => {
                const isMatch = idx === currentPhotoIndex;
                btn.classList.toggle("is-active", isMatch);
                if (isMatch) {
                    btn.scrollIntoView({ behavior: "smooth", inline: "center", block: "nearest" });
                }
            });
        }
    }

    /**
     * Switch Lab View dynamically without reload
     */
    function renderLab(labKey, updateHistory = true) {
        const lab = labs[labKey];
        if (!lab) return;

        currentLabKey = labKey;
        currentPhotos = lab.images || [];
        currentPhotoIndex = 0;

        if (detailLayout) {
            detailLayout.style.opacity = "0.4";
            detailLayout.style.transform = "translateY(8px) scale(0.995)";
        }

        if (renderTimer) {
            clearTimeout(renderTimer);
            renderTimer = null;
        }

        renderTimer = setTimeout(() => {
            // 1. Update Hero
            if (heroBreadcrumb) heroBreadcrumb.textContent = lab.code;
            if (heroBadge) heroBadge.textContent = lab.badge;
            if (heroTitle) heroTitle.textContent = lab.name;
            if (heroSubtitle) heroSubtitle.textContent = lab.tagline;

            // 2. Update Thumbnails Strip
            if (thumbnailsStrip) {
                thumbnailsStrip.innerHTML = "";
                currentPhotos.forEach((src, idx) => {
                    const btn = document.createElement("button");
                    btn.type = "button";
                    btn.className = `lab-thumb-btn ${idx === 0 ? 'is-active' : ''}`;
                    btn.setAttribute("data-index", idx);
                    const img = document.createElement('img');
                    img.src = src;
                    img.alt = `Lab photo thumbnail ${idx + 1}`;
                    img.loading = 'lazy';
                    btn.appendChild(img);
                    btn.addEventListener("click", () => updatePhoto(idx));
                    thumbnailsStrip.appendChild(btn);
                });
            }

            updatePhoto(0);

            // 3. Update Overview & Features
            if (overviewTitle) overviewTitle.textContent = `About ${lab.name}`;
            if (descriptionText) descriptionText.textContent = lab.description;

            if (featuresList && lab.features) {
                featuresList.innerHTML = "";
                lab.features.forEach((feat, idx) => {
                    const item = document.createElement("div");
                    item.className = "lab-feature-item";
                    item.style.animation = `facultyCardPop 0.35s cubic-bezier(0.16, 1, 0.3, 1) ${idx * 30}ms forwards`;
                    const iconDiv = document.createElement('div');
                    iconDiv.className = 'lab-feature-icon';
                    iconDiv.innerHTML = `<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>`;
                    const span = document.createElement('span');
                    span.textContent = feat;
                    item.appendChild(iconDiv);
                    item.appendChild(span);
                    featuresList.appendChild(item);
                });
            }

            // 4. Update Specs Grid
            if (specsGrid && lab.specs) {
                specsGrid.innerHTML = "";
                Object.entries(lab.specs).forEach(([label, val]) => {
                    const box = document.createElement("div");
                    box.className = "lab-spec-box";
                    const labelSpan = document.createElement('span');
                    labelSpan.className = 'lab-spec-box__label';
                    labelSpan.textContent = label;
                    const valueStrong = document.createElement('strong');
                    valueStrong.className = 'lab-spec-box__value';
                    valueStrong.textContent = val;
                    box.appendChild(labelSpan);
                    box.appendChild(valueStrong);
                    specsGrid.appendChild(box);
                });
            }

            // 5. Update Switcher Pills
            switcherPills.forEach(pill => {
                const pLab = pill.getAttribute("data-lab");
                const isMatch = pLab === labKey;
                pill.classList.toggle("is-active", isMatch);
            });

            // 6. Update URL & Title
            document.title = `${lab.name} - Shri V.J. Modha College`;
            if (updateHistory) {
                window.history.pushState({ lab: labKey }, "", `labs.php?lab=${labKey}`);
            }

            if (detailLayout) {
                detailLayout.style.opacity = "1";
                detailLayout.style.transform = "translateY(0) scale(1)";
            }

            renderTimer = null;
        }, 120);
    }

    // Carousel Navigation
    if (prevBtn) {
        prevBtn.addEventListener("click", () => updatePhoto(currentPhotoIndex - 1));
    }
    if (nextBtn) {
        nextBtn.addEventListener("click", () => updatePhoto(currentPhotoIndex + 1));
    }

    // Thumbnail Buttons Initial Bind
    if (thumbnailsStrip) {
        const thumbBtns = thumbnailsStrip.querySelectorAll(".lab-thumb-btn");
        thumbBtns.forEach(btn => {
            btn.addEventListener("click", () => {
                const idx = parseInt(btn.getAttribute("data-index"), 10);
                if (!isNaN(idx)) updatePhoto(idx);
            });
        });
    }

    // Intercept Lab Switcher & Nav Links
    document.addEventListener("click", (e) => {
        const link = e.target.closest("a[data-lab], a[href*='labs.php?lab=']");
        if (!link) return;

        const href = link.getAttribute("href") || "";
        const dataLab = link.getAttribute("data-lab");

        let targetLab = null;
        if (dataLab) {
            targetLab = dataLab;
        } else if (href.includes("labs.php?lab=")) {
            const match = href.match(/labs\.php\?lab=([a-zA-Z0-9]+)/);
            if (match && match[1]) {
                targetLab = match[1].toLowerCase();
            }
        }

        if (targetLab && labs[targetLab]) {
            e.preventDefault();
            renderLab(targetLab, true);
        }
    });

    // Handle Browser History Navigation
    window.addEventListener("popstate", () => {
        const urlParams = new URLSearchParams(window.location.search);
        const labParam = urlParams.get("lab") || "computer";
        if (labs[labParam]) {
            renderLab(labParam, false);
        }
    });


});