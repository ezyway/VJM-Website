/**
 * ====================================================
 * ABOUT US PAGE SCRIPTS
 * Shri V.J. Modha College Portal
 * ====================================================
 */

document.addEventListener("DOMContentLoaded", () => {

    // ----------------------------------------------------
    // 1. Animated Stats Counters (IntersectionObserver)
    // ----------------------------------------------------
    const counters = document.querySelectorAll("[data-target]");

    function animateCount(element, duration = 2000) {
        const targetString = element.getAttribute("data-target") || "0";
        const isFloat = targetString.includes(".");
        const percentSuffix = element.classList.contains("counter-section__pass-percentage");
        const plusSuffix = element.classList.contains("counter-section__enrolled") ||
                           element.classList.contains("counter-section__passouts");

        const target = isFloat ? parseFloat(targetString) : parseInt(targetString, 10);
        const decimalPlaces = isFloat ? (targetString.split(".")[1] || "").length : 0;
        let startTime = null;

        function easeOutQuad(t) {
            return t * (2 - t);
        }

        function updateCount(timestamp) {
            if (!startTime) startTime = timestamp;
            const elapsed = timestamp - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const easedProgress = easeOutQuad(progress);

            if (progress < 1) {
                const current = easedProgress * target;
                element.textContent = isFloat ? current.toFixed(decimalPlaces) : Math.floor(current).toLocaleString();
                requestAnimationFrame(updateCount);
            } else {
                element.textContent = isFloat ? target.toFixed(decimalPlaces) : target.toLocaleString();
                if (plusSuffix) {
                    element.textContent += "+";
                }
                if (percentSuffix) {
                    element.textContent += "%";
                }
            }
        }

        requestAnimationFrame(updateCount);
    }

    if (counters.length > 0) {
        const counterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCount(entry.target, 2000);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.2 });

        counters.forEach(counter => counterObserver.observe(counter));
    }


    // ----------------------------------------------------
    // 2. Board of Trustees Responsive Carousel
    // ----------------------------------------------------
    const trusteeSlider = document.querySelector(".trustee-section__slider");
    const trusteeWrapper = document.getElementById("trusteeSliderWrapper");
    const trusteeItems = document.querySelectorAll(".trustee-section__item");
    const prevBtn = document.getElementById("trustee-prev");
    const nextBtn = document.getElementById("trustee-next");
    const dotsContainer = document.getElementById("trusteeDots");

    if (trusteeWrapper && trusteeItems.length > 0) {
        const totalItems = trusteeItems.length;
        let currentIndex = 0;
        let visibleCount = 3;
        let maxIndex = 0;
        let autoPlayTimer = null;
        let touchStartX = 0;
        let touchEndX = 0;

        function getVisibleCount() {
            const width = window.innerWidth;
            if (width <= 768) return 1;
            if (width <= 1024) return 2;
            return 3;
        }

        function setupDots() {
            if (!dotsContainer) return;
            dotsContainer.innerHTML = "";
            const numDots = totalItems;
            
            for (let i = 0; i < numDots; i++) {
                const dot = document.createElement("button");
                dot.className = `trustee-dot ${i === currentIndex ? "is-active" : ""}`;
                dot.setAttribute("aria-label", `Go to slide ${i + 1}`);
                dot.addEventListener("click", () => {
                    currentIndex = Math.min(i, maxIndex);
                    updateSlider();
                    resetAutoPlay();
                });
                dotsContainer.appendChild(dot);
            }
        }

        function updateDots() {
            if (!dotsContainer) return;
            const dots = dotsContainer.querySelectorAll(".trustee-dot");
            dots.forEach((dot, idx) => {
                dot.classList.toggle("is-active", idx === currentIndex);
            });
        }

        function calculateBounds() {
            visibleCount = getVisibleCount();
            maxIndex = Math.max(0, totalItems - visibleCount);
            if (currentIndex > maxIndex) {
                currentIndex = maxIndex;
            }
        }

        function updateSlider() {
            calculateBounds();
            if (trusteeItems[0]) {
                const itemWidth = trusteeItems[0].offsetWidth;
                const computedGap = parseFloat(window.getComputedStyle(trusteeWrapper).gap) || 24;
                const step = itemWidth + computedGap;
                const offset = -currentIndex * step;
                trusteeWrapper.style.transform = `translateX(${Math.round(offset)}px)`;
            }

            // Update button states
            if (prevBtn && nextBtn) {
                prevBtn.disabled = currentIndex === 0 && maxIndex > 0 ? false : false; // Allow loop or enable
            }

            updateDots();
        }

        function nextSlide() {
            if (currentIndex >= maxIndex) {
                currentIndex = 0; // Loop back
            } else {
                currentIndex++;
            }
            updateSlider();
        }

        function prevSlide() {
            if (currentIndex <= 0) {
                currentIndex = maxIndex; // Loop to end
            } else {
                currentIndex--;
            }
            updateSlider();
        }

        function startAutoPlay() {
            if (!autoPlayTimer) {
                autoPlayTimer = setInterval(nextSlide, 4500);
            }
        }

        function stopAutoPlay() {
            if (autoPlayTimer) {
                clearInterval(autoPlayTimer);
                autoPlayTimer = null;
            }
        }

        function resetAutoPlay() {
            stopAutoPlay();
            startAutoPlay();
        }

        // Navigation button events
        if (nextBtn) {
            nextBtn.addEventListener("click", () => {
                nextSlide();
                resetAutoPlay();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener("click", () => {
                prevSlide();
                resetAutoPlay();
            });
        }

        // Pause autoplay on user interaction
        if (trusteeSlider) {
            trusteeSlider.addEventListener("mouseenter", stopAutoPlay);
            trusteeSlider.addEventListener("mouseleave", startAutoPlay);
            
            // Touch Swipe handling
            trusteeSlider.addEventListener("touchstart", (e) => {
                touchStartX = e.changedTouches[0].screenX;
                stopAutoPlay();
            }, { passive: true });

            trusteeSlider.addEventListener("touchend", (e) => {
                touchEndX = e.changedTouches[0].screenX;
                const diffX = touchStartX - touchEndX;
                if (Math.abs(diffX) > 40) {
                    if (diffX > 0) {
                        nextSlide();
                    } else {
                        prevSlide();
                    }
                }
                startAutoPlay();
            }, { passive: true });
        }

        // Debounced resize handler using requestAnimationFrame
        let resizeRaf = null;
        window.addEventListener("resize", () => {
            if (resizeRaf) cancelAnimationFrame(resizeRaf);
            resizeRaf = requestAnimationFrame(() => {
                calculateBounds();
                updateSlider();
            });
        });

        // Initialize Slider
        setupDots();
        calculateBounds();
        updateSlider();
        startAutoPlay();
    }


    // ----------------------------------------------------
    // 3. Back To Top Button Handler
    // ----------------------------------------------------
    const backToTopBtn = document.getElementById("backToTop");
    const aboutHero = document.getElementById("about-hero");

    function toggleBackToTop() {
        if (!backToTopBtn) return;
        const triggerPoint = aboutHero ? aboutHero.offsetHeight * 0.6 : 300;

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
