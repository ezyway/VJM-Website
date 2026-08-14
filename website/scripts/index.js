document.addEventListener("DOMContentLoaded", () => {

    // ----------------------------------------------------
    // 1. Hero Video Banner Setup & Scroll Down
    // ----------------------------------------------------
    const overlay = document.querySelector(".video-banner__overlay");
    const scrollDownBtn = document.querySelector('.scroll-down');

    // Ensure overlay is smoothly visible once DOM is ready
    if (overlay) {
        overlay.classList.add("is-visible");
    }

    if (scrollDownBtn) {
        scrollDownBtn.addEventListener('click', () => {
            const counterSection = document.getElementById('counter-section');
            if (counterSection) {
                counterSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }


    // ----------------------------------------------------
    // 2. Animated Stats Counters (IntersectionObserver)
    // ----------------------------------------------------
    const counters = document.querySelectorAll('[data-target]');

    function animateCount(element, duration = 2000) {
        const targetString = element.getAttribute('data-target') || '0';
        const isFloat = targetString.includes('.');
        const percentSuffix = element.classList.contains('counter-section__pass-percentage');
        const plusSuffix = element.classList.contains('counter-section__enrolled') ||
                           element.classList.contains('counter-section__passouts');

        const target = isFloat ? parseFloat(targetString) : parseInt(targetString, 10);
        const decimalPlaces = isFloat ? (targetString.split('.')[1] || '').length : 0;
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
                    element.textContent += '+';
                }
                if (percentSuffix) {
                    element.textContent += '%';
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
    // 3. Pride of College Slider (Fixed Auto & Manual Slide)
    // ----------------------------------------------------
    const prideSliderWrapper = document.querySelector(".pride-section__slider-wrapper");
    const prideSlides = document.querySelectorAll(".pride-section__item");
    const pridePrevBtn = document.querySelector(".pride-section__nav-prev");
    const prideNextBtn = document.querySelector(".pride-section__nav-next");
    const prideSection = document.querySelector(".pride-section");

    if (prideSliderWrapper && prideSlides.length > 0) {
        let prideCurrentIndex = 0;
        let prideInterval = null;
        const totalSlides = prideSlides.length;

        function updatePrideSlider() {
            // Wrapper width is 100% of container width (1 slide).
            // Translating by - (prideCurrentIndex * 100)% translates exactly 1 full slide.
            const offset = -prideCurrentIndex * 100;
            prideSliderWrapper.style.transform = `translateX(${offset}%)`;
        }

        function showNextPrideSlide() {
            prideCurrentIndex = (prideCurrentIndex + 1) % totalSlides;
            updatePrideSlider();
        }

        function showPrevPrideSlide() {
            prideCurrentIndex = (prideCurrentIndex - 1 + totalSlides) % totalSlides;
            updatePrideSlider();
        }

        function resetPrideTimer() {
            if (prideInterval) {
                clearInterval(prideInterval);
            }
            prideInterval = setInterval(showNextPrideSlide, 4500);
        }

        if (prideNextBtn) {
            prideNextBtn.addEventListener("click", () => {
                showNextPrideSlide();
                resetPrideTimer();
            });
        }

        if (pridePrevBtn) {
            pridePrevBtn.addEventListener("click", () => {
                showPrevPrideSlide();
                resetPrideTimer();
            });
        }

        function startPrideAutoSlide() {
            if (!prideInterval) {
                prideInterval = setInterval(showNextPrideSlide, 4500);
            }
        }

        function stopPrideAutoSlide() {
            if (prideInterval) {
                clearInterval(prideInterval);
                prideInterval = null;
            }
        }

        startPrideAutoSlide();
        updatePrideSlider();

        if (prideSection) {
            prideSection.addEventListener("mouseenter", stopPrideAutoSlide);
            prideSection.addEventListener("mouseleave", startPrideAutoSlide);
            prideSection.addEventListener("touchstart", stopPrideAutoSlide, { passive: true });
            prideSection.addEventListener("touchend", startPrideAutoSlide, { passive: true });
        }
    }


    // ----------------------------------------------------
    // 4. Testimonials Horizontal Slider
    // ----------------------------------------------------
    const testimonialsSlider = document.querySelector('.testimonials-section__slider');
    const testimonialBtnLeft = document.querySelector('.testimonials-section__nav-left');
    const testimonialBtnRight = document.querySelector('.testimonials-section__nav-right');

    if (testimonialsSlider && testimonialBtnLeft && testimonialBtnRight) {
        function getScrollStep() {
            const item = testimonialsSlider.querySelector('.testimonials-section__item');
            return item ? item.offsetWidth + 24 : 340;
        }

        testimonialBtnLeft.addEventListener('click', () => {
            testimonialsSlider.scrollBy({
                left: -getScrollStep(),
                behavior: 'smooth'
            });
            updateNavState();
        });

        testimonialBtnRight.addEventListener('click', () => {
            testimonialsSlider.scrollBy({
                left: getScrollStep(),
                behavior: 'smooth'
            });
            updateNavState();
        });

        function updateNavState() {
            setTimeout(() => {
                const maxScroll = testimonialsSlider.scrollWidth - testimonialsSlider.clientWidth;
                testimonialBtnLeft.disabled = testimonialsSlider.scrollLeft <= 4;
                testimonialBtnRight.disabled = testimonialsSlider.scrollLeft >= maxScroll - 4;
            }, 250);
        }

        testimonialsSlider.addEventListener('scroll', updateNavState, { passive: true });
        window.addEventListener('resize', updateNavState);
        updateNavState();
    }


    // ----------------------------------------------------
    // 5. Campus Life Photo Carousel
    // ----------------------------------------------------
    const carousel = document.getElementById('carousel');
    const carouselBtnLeft = document.querySelector('.photo-carousel-section__nav-left');
    const carouselBtnRight = document.querySelector('.photo-carousel-section__nav-right');

    if (carousel && carouselBtnLeft && carouselBtnRight) {
        function getPhotoStep() {
            const item = carousel.querySelector('.photo-carousel-section__item');
            return item ? item.offsetWidth + 20 : 320;
        }

        carouselBtnLeft.addEventListener('click', () => {
            carousel.scrollBy({
                left: -getPhotoStep(),
                behavior: 'smooth'
            });
            updateCarouselNavState();
        });

        carouselBtnRight.addEventListener('click', () => {
            carousel.scrollBy({
                left: getPhotoStep(),
                behavior: 'smooth'
            });
            updateCarouselNavState();
        });

        function updateCarouselNavState() {
            setTimeout(() => {
                const maxScroll = carousel.scrollWidth - carousel.clientWidth;
                carouselBtnLeft.disabled = carousel.scrollLeft <= 4;
                carouselBtnRight.disabled = carousel.scrollLeft >= maxScroll - 4;
            }, 200);
        }

        carousel.addEventListener('scroll', updateCarouselNavState, { passive: true });
        window.addEventListener('resize', updateCarouselNavState);
        updateCarouselNavState();
    }


    // ----------------------------------------------------
    // 6. Back To Top Button
    // ----------------------------------------------------
    const backToTopButton = document.getElementById("backToTop");
    const heroBanner = document.getElementById("video-banner");

    function toggleBackToTop() {
        if (!backToTopButton) return;
        const triggerPoint = heroBanner ? heroBanner.offsetHeight * 0.7 : 400;

        if (window.scrollY > triggerPoint) {
            backToTopButton.classList.add("is-visible");
        } else {
            backToTopButton.classList.remove("is-visible");
        }
    }

    window.addEventListener("scroll", toggleBackToTop, { passive: true });
    toggleBackToTop();

    if (backToTopButton) {
        backToTopButton.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
    }

});
