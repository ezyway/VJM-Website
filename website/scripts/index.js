document.addEventListener("DOMContentLoaded", () => {

    // ----------------------------------------------------
    // 1. Hero Video Banner Setup & Scroll Down
    // ----------------------------------------------------
    const overlay = document.querySelector(".video-banner__overlay");
    const scrollDownBtn = document.querySelector('.scroll-down');
    const bannerVideo = document.querySelector('.video-banner__background');

    // Ensure overlay is smoothly visible once DOM is ready
    if (overlay) {
        overlay.classList.add("is-visible");
    }

    // Explicit video playback trigger (handles mobile & strict browser policies)
    if (bannerVideo) {
        bannerVideo.muted = true;
        bannerVideo.playsInline = true;
        const playPromise = bannerVideo.play();
        if (playPromise !== undefined) {
            playPromise.catch(error => {
                // Autoplay blocked by browser power saving / policy, trigger on first touch/click
                const resumePlay = () => {
                    bannerVideo.play();
                    document.removeEventListener('touchstart', resumePlay);
                    document.removeEventListener('click', resumePlay);
                };
                document.addEventListener('touchstart', resumePlay, { passive: true });
                document.addEventListener('click', resumePlay, { passive: true });
            });
        }
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
    // 2. Pride of College Slider (Fixed Auto & Manual Slide)
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


});
