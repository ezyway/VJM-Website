/**
 * ====================================================
 * GALLERY PAGE SCRIPTS - INTERACTIVE LIGHTBOX & ALBUM FILTER
 * Shri V.J. Modha College Portal
 * ====================================================
 */

document.addEventListener("DOMContentLoaded", () => {

    // ----------------------------------------------------
    // 0. Load Embedded Albums Payload
    // ----------------------------------------------------
    let albumsData = {};
    const payloadEl = document.getElementById("galleryPayload");
    if (payloadEl) {
        try {
            albumsData = JSON.parse(payloadEl.textContent);
        } catch (e) {
            console.error("Failed to parse gallery payload", e);
        }
    }

    // Modal DOM Elements
    const modal = document.getElementById("galleryModal");
    const modalBackdrop = document.getElementById("modalBackdrop");
    const modalCloseBtn = document.getElementById("modalCloseBtn");
    const modalAlbumTitle = document.getElementById("modalAlbumTitle");
    const modalCounter = document.getElementById("modalCounter");
    const modalMainImage = document.getElementById("modalMainImage");
    const modalPrevBtn = document.getElementById("modalPrevBtn");
    const modalNextBtn = document.getElementById("modalNextBtn");
    const thumbnailsStrip = document.getElementById("modalThumbnailsStrip");

    let currentAlbumKey = null;
    let currentPhotos = [];
    let currentIndex = 0;

    /**
     * Update Lightbox Image and Thumbnails
     */
    function updateLightbox(index) {
        if (!currentPhotos.length) return;
        currentIndex = (index + currentPhotos.length) % currentPhotos.length;

        if (modalMainImage) {
            modalMainImage.style.opacity = "0.4";
            modalMainImage.style.transform = "scale(0.97)";

            const nextSrc = currentPhotos[currentIndex];
            const tempImg = new Image();
            tempImg.src = nextSrc;
            tempImg.onload = () => {
                modalMainImage.src = nextSrc;
                modalMainImage.style.opacity = "1";
                modalMainImage.style.transform = "scale(1)";
            };
        }

        if (modalCounter) {
            modalCounter.textContent = `${currentIndex + 1} / ${currentPhotos.length}`;
        }

        // Update active thumbnail
        if (thumbnailsStrip) {
            const thumbItems = thumbnailsStrip.querySelectorAll(".gallery-thumb-item");
            thumbItems.forEach((thumb, idx) => {
                const isMatch = idx === currentIndex;
                thumb.classList.toggle("is-active", isMatch);
                if (isMatch) {
                    thumb.scrollIntoView({ behavior: "smooth", inline: "center", block: "nearest" });
                }
            });
        }
    }

    /**
     * Open Lightbox Modal for a selected album
     */
    function openAlbum(albumKey) {
        const album = albumsData[albumKey];
        if (!album || !album.images || !album.images.length) return;

        currentAlbumKey = albumKey;
        currentPhotos = album.images;
        currentIndex = 0;

        if (modalAlbumTitle) {
            modalAlbumTitle.textContent = album.title;
        }

        // Build Thumbnails Strip
        if (thumbnailsStrip) {
            thumbnailsStrip.innerHTML = "";
            currentPhotos.forEach((src, idx) => {
                const thumb = document.createElement("div");
                thumb.className = `gallery-thumb-item ${idx === 0 ? 'is-active' : ''}`;
                thumb.innerHTML = `<img src="${src}" alt="Thumbnail ${idx + 1}" loading="lazy" />`;
                thumb.addEventListener("click", () => updateLightbox(idx));
                thumbnailsStrip.appendChild(thumb);
            });
        }

        updateLightbox(0);

        if (modal) {
            modal.style.display = "flex";
            document.body.style.overflow = "hidden";
        }
    }

    /**
     * Close Lightbox Modal
     */
    function closeModal() {
        if (modal) {
            modal.style.display = "none";
            document.body.style.overflow = "";
        }
    }

    // Modal Event Listeners
    if (modalCloseBtn) modalCloseBtn.addEventListener("click", closeModal);
    if (modalBackdrop) modalBackdrop.addEventListener("click", closeModal);

    if (modalPrevBtn) {
        modalPrevBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            updateLightbox(currentIndex - 1);
        });
    }

    if (modalNextBtn) {
        modalNextBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            updateLightbox(currentIndex + 1);
        });
    }

    // Keyboard Navigation
    document.addEventListener("keydown", (e) => {
        if (!modal || modal.style.display === "none") return;
        if (e.key === "Escape") closeModal();
        if (e.key === "ArrowLeft") updateLightbox(currentIndex - 1);
        if (e.key === "ArrowRight") updateLightbox(currentIndex + 1);
    });

    // Touch Swipe Support
    let touchStartX = 0;
    let touchEndX = 0;

    if (modal) {
        modal.addEventListener("touchstart", (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        modal.addEventListener("touchend", (e) => {
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchEndX - touchStartX;
            if (Math.abs(diff) > 45) {
                if (diff > 0) {
                    updateLightbox(currentIndex - 1); // Swipe right -> Prev
                } else {
                    updateLightbox(currentIndex + 1); // Swipe left -> Next
                }
            }
        }, { passive: true });
    }

    // Album Card Click Events
    const albumCards = document.querySelectorAll(".album-card");
    albumCards.forEach(card => {
        card.addEventListener("click", () => {
            const albumKey = card.getAttribute("data-album");
            if (albumKey) openAlbum(albumKey);
        });
    });


    // ----------------------------------------------------
    // 1. Album Category Filter Tabs
    // ----------------------------------------------------
    const filterButtons = document.querySelectorAll(".gallery-filter-btn");
    const albumGrid = document.getElementById("albumGrid");

    if (filterButtons.length > 0 && albumCards.length > 0) {
        filterButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                const category = btn.getAttribute("data-category") || "all";

                filterButtons.forEach(b => {
                    const isMatch = b === btn;
                    b.classList.toggle("is-active", isMatch);
                    b.setAttribute("aria-selected", isMatch ? "true" : "false");
                });

                if (albumGrid) {
                    albumGrid.style.opacity = "0.6";
                    albumGrid.style.transform = "scale(0.99)";
                }

                setTimeout(() => {
                    let visibleIndex = 0;
                    albumCards.forEach(card => {
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

                    if (albumGrid) {
                        albumGrid.style.opacity = "1";
                        albumGrid.style.transform = "scale(1)";
                    }
                }, 120);
            });
        });
    }


    // ----------------------------------------------------
    // 2. Back To Top Button Handler
    // ----------------------------------------------------
    const backToTopBtn = document.getElementById("backToTop");
    const heroSection = document.getElementById("gallery-hero");

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