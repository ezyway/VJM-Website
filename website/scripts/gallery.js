/**
 * ====================================================
 * GALLERY PAGE SCRIPTS - INTERACTIVE LIGHTBOX & SLIDESHOW
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
    const modalLoadingSpinner = document.getElementById("modalLoadingSpinner");
    const modalPrevBtn = document.getElementById("modalPrevBtn");
    const modalNextBtn = document.getElementById("modalNextBtn");
    const thumbnailsStrip = document.getElementById("modalThumbnailsStrip");
    const modalProgressBar = document.getElementById("modalProgressBar");
    const modalPlayBtn = document.getElementById("modalPlayBtn");
    const modalPlayBtnLabel = document.getElementById("modalPlayBtnLabel");
    const modalPlayStateBadge = document.getElementById("modalPlayStateBadge");
    const playIcon = document.getElementById("playIcon");
    const pauseIcon = document.getElementById("pauseIcon");
    const modalFullscreenBtn = document.getElementById("modalFullscreenBtn");
    const fullscreenExpandIcon = document.getElementById("fullscreenExpandIcon");
    const fullscreenCompressIcon = document.getElementById("fullscreenCompressIcon");

    let currentAlbumKey = null;
    let currentPhotos = [];
    let currentCaptions = [];
    let currentIndex = 0;

    // Slideshow state
    let isPlaying = false;
    let slideshowTimer = null;
    const SLIDE_DURATION = 3800; // 3.8 seconds per slide

    /**
     * Preload adjacent images in background for instant responsiveness
     */
    function preloadAdjacentImages() {
        if (!currentPhotos.length) return;
        const nextIdx = (currentIndex + 1) % currentPhotos.length;
        const prevIdx = (currentIndex - 1 + currentPhotos.length) % currentPhotos.length;

        const imgNext = new Image();
        imgNext.src = currentPhotos[nextIdx];

        const imgPrev = new Image();
        imgPrev.src = currentPhotos[prevIdx];
    }

    /**
     * Reset and start the progress bar animation
     */
    function triggerProgressBar() {
        if (!modalProgressBar) return;
        modalProgressBar.style.transition = "none";
        modalProgressBar.style.width = "0%";

        if (isPlaying) {
            // Trigger reflow
            void modalProgressBar.offsetWidth;
            modalProgressBar.style.transition = `width ${SLIDE_DURATION}ms linear`;
            modalProgressBar.style.width = "100%";
        }
    }

    /**
     * Update Lightbox Image and Thumbnails
     */
    function updateLightbox(index, isAutoSlide = false) {
        if (!currentPhotos.length) return;
        currentIndex = (index + currentPhotos.length) % currentPhotos.length;

        if (modalMainImage) {
            if (modalLoadingSpinner) modalLoadingSpinner.style.display = "block";
            modalMainImage.style.opacity = "0.35";
            modalMainImage.style.transform = "scale(0.98)";

            const nextSrc = currentPhotos[currentIndex];
            const tempImg = new Image();
            tempImg.src = nextSrc;
            tempImg.onload = () => {
                modalMainImage.src = nextSrc;
                var cap = currentCaptions[currentIndex] || '';
                modalMainImage.alt = cap || ('Photo ' + (currentIndex + 1));
                var capEl = document.getElementById('modalPhotoCaption');
                if (capEl) { capEl.textContent = cap; capEl.style.display = cap ? 'block' : 'none'; }
                modalMainImage.style.opacity = "1";
                modalMainImage.style.transform = "scale(1)";
                if (modalLoadingSpinner) modalLoadingSpinner.style.display = "none";
                preloadAdjacentImages();
            };
            tempImg.onerror = () => {
                if (modalLoadingSpinner) modalLoadingSpinner.style.display = "none";
                modalMainImage.style.opacity = "1";
            };
        }

        if (modalCounter) {
            modalCounter.textContent = `Slide ${currentIndex + 1} / ${currentPhotos.length}`;
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

        // Sync slideshow progress if running
        if (isPlaying) {
            triggerProgressBar();
        }
    }

    /**
     * Slideshow Playback Controls
     */
    function startSlideshow() {
        if (isPlaying || currentPhotos.length <= 1) return;
        isPlaying = true;

        if (playIcon) playIcon.style.display = "none";
        if (pauseIcon) pauseIcon.style.display = "block";
        if (modalPlayBtnLabel) modalPlayBtnLabel.textContent = "Pause";
        if (modalPlayStateBadge) modalPlayStateBadge.style.display = "inline-flex";
        if (modalPlayBtn) {
            modalPlayBtn.classList.add("is-active");
            modalPlayBtn.setAttribute("title", "Pause Slideshow (Space)");
        }

        triggerProgressBar();

        slideshowTimer = setInterval(() => {
            updateLightbox(currentIndex + 1, true);
        }, SLIDE_DURATION);
    }

    function pauseSlideshow() {
        if (!isPlaying) return;
        isPlaying = false;
        clearInterval(slideshowTimer);
        slideshowTimer = null;

        if (playIcon) playIcon.style.display = "block";
        if (pauseIcon) pauseIcon.style.display = "none";
        if (modalPlayBtnLabel) modalPlayBtnLabel.textContent = "Slideshow";
        if (modalPlayStateBadge) modalPlayStateBadge.style.display = "none";
        if (modalPlayBtn) {
            modalPlayBtn.classList.remove("is-active");
            modalPlayBtn.setAttribute("title", "Play Slideshow (Space)");
        }

        if (modalProgressBar) {
            modalProgressBar.style.transition = "none";
            modalProgressBar.style.width = "0%";
        }
    }

    function toggleSlideshow() {
        if (isPlaying) {
            pauseSlideshow();
        } else {
            startSlideshow();
        }
    }

    /**
     * Fullscreen Toggle
     */
    function toggleFullscreen() {
        if (!document.fullscreenElement && !document.webkitFullscreenElement) {
            if (modal.requestFullscreen) {
                modal.requestFullscreen();
            } else if (modal.webkitRequestFullscreen) {
                modal.webkitRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        }
    }

    function updateFullscreenIcons() {
        const isFull = !!(document.fullscreenElement || document.webkitFullscreenElement);
        if (fullscreenExpandIcon) fullscreenExpandIcon.style.display = isFull ? "none" : "block";
        if (fullscreenCompressIcon) fullscreenCompressIcon.style.display = isFull ? "block" : "none";
    }

    document.addEventListener("fullscreenchange", updateFullscreenIcons);
    document.addEventListener("webkitfullscreenchange", updateFullscreenIcons);

    /**
     * Open Lightbox Modal for a selected album
     */
    function openAlbum(albumKey, autoStartSlideshow = true) {
        const album = albumsData[albumKey];
        if (!album || !album.images || !album.images.length) return;

        currentAlbumKey = albumKey;
        currentPhotos = album.images;
        currentCaptions = album.captions || [];
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
                thumb.setAttribute("title", `Slide ${idx + 1}`);
                thumb.innerHTML = `<img src="${src}" alt="${(album.captions && album.captions[idx]) ? album.captions[idx] : 'Thumbnail ' + (idx + 1)}" loading="lazy" />`;
                thumb.addEventListener("click", () => {
                    pauseSlideshow();
                    updateLightbox(idx);
                });
                thumbnailsStrip.appendChild(thumb);
            });
        }

        updateLightbox(0);

        if (modal) {
            modal.style.display = "flex";
            document.body.style.overflow = "hidden";
        }

        // Auto start slideshow on launch if more than 1 image
        if (autoStartSlideshow && currentPhotos.length > 1) {
            startSlideshow();
        } else {
            pauseSlideshow();
        }
    }

    /**
     * Close Lightbox Modal
     */
    function closeModal() {
        pauseSlideshow();
        if (document.fullscreenElement || document.webkitFullscreenElement) {
            if (document.exitFullscreen) document.exitFullscreen();
            else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
        }
        if (modal) {
            modal.style.display = "none";
            document.body.style.overflow = "";
        }
    }

    // Modal Event Listeners
    if (modalCloseBtn) modalCloseBtn.addEventListener("click", closeModal);
    if (modalBackdrop) modalBackdrop.addEventListener("click", closeModal);
    if (modalPlayBtn) modalPlayBtn.addEventListener("click", toggleSlideshow);
    if (modalFullscreenBtn) modalFullscreenBtn.addEventListener("click", toggleFullscreen);

    if (modalPrevBtn) {
        modalPrevBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            pauseSlideshow();
            updateLightbox(currentIndex - 1);
        });
    }

    if (modalNextBtn) {
        modalNextBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            pauseSlideshow();
            updateLightbox(currentIndex + 1);
        });
    }

    // Keyboard Navigation
    document.addEventListener("keydown", (e) => {
        if (!modal || modal.style.display === "none") return;
        
        if (e.key === "Escape") {
            closeModal();
        } else if (e.key === "ArrowLeft") {
            pauseSlideshow();
            updateLightbox(currentIndex - 1);
        } else if (e.key === "ArrowRight") {
            pauseSlideshow();
            updateLightbox(currentIndex + 1);
        } else if (e.key === " " || e.code === "Space") {
            e.preventDefault();
            toggleSlideshow();
        } else if (e.key === "f" || e.key === "F") {
            toggleFullscreen();
        }
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
                pauseSlideshow();
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
            if (albumKey) openAlbum(albumKey, true);
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


});