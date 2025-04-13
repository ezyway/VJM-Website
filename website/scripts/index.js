document.addEventListener("DOMContentLoaded", () => {

    // --------------------------
    // Click for Scroll Down Icon
    // --------------------------

    document.querySelector('.scroll-down').addEventListener('click', function () {
        document.getElementById('counter-section').scrollIntoView({ behavior: 'smooth' });
    });


    // --------------------------
    // Animate Counters
    // --------------------------
    const counters = document.querySelectorAll('[data-target]');

    function animateCount(el, duration) {
        const target = parseInt(el.getAttribute('data-target'));
        let startTime = null;

        function updateCount(timestamp) {
            if (!startTime) startTime = timestamp;
            const progress = timestamp - startTime;
            const currentCount = Math.min(Math.floor((progress / duration) * target), target);
            el.textContent = currentCount;
            if (progress < duration) {
                requestAnimationFrame(updateCount);
            }
        }
        requestAnimationFrame(updateCount);
    }

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCount(entry.target, 2000); // 2000 ms duration for the animation
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => {
        observer.observe(counter);
    });

    // --------------------------
    // Testimonials Slider Script
    // --------------------------
    const testimonialsSlider = document.querySelector('.testimonials-section__slider');
    const testimonialBtnLeft = document.querySelector('.testimonials-section__nav-left');
    const testimonialBtnRight = document.querySelector('.testimonials-section__nav-right');

    if (testimonialsSlider && testimonialBtnLeft && testimonialBtnRight) {
        // Calculate scroll amount dynamically
        function getScrollAmount() {
            return Math.min(testimonialsSlider.clientWidth * 0.75, 300); // 75% of container width or max 300px
        }

        // Center the active slide if one exists
        function centerActiveSlide() {
            const activeItem = testimonialsSlider.querySelector('.testimonials-section__item.active');
            if (activeItem) {
                const offsetLeft = activeItem.offsetLeft;
                const itemWidth = activeItem.offsetWidth;
                const centerPosition = offsetLeft - (testimonialsSlider.clientWidth / 2) + (itemWidth / 2);
                testimonialsSlider.scrollTo({
                    left: centerPosition,
                    behavior: 'smooth'
                });
            }
        }

        testimonialBtnLeft.addEventListener('click', () => {
            testimonialsSlider.scrollBy({
                left: -getScrollAmount(),
                behavior: 'smooth'
            });
            setTimeout(centerActiveSlide, 300);
            updateTestimonialNavButtons();
        });

        testimonialBtnRight.addEventListener('click', () => {
            testimonialsSlider.scrollBy({
                left: getScrollAmount(),
                behavior: 'smooth'
            });
            setTimeout(centerActiveSlide, 300);
            updateTestimonialNavButtons();
        });

        function updateTestimonialNavButtons() {
            setTimeout(() => {
                testimonialBtnLeft.disabled = testimonialsSlider.scrollLeft <= 0;
                testimonialBtnRight.disabled = testimonialsSlider.scrollLeft + testimonialsSlider.clientWidth >= testimonialsSlider.scrollWidth;
            }, 300);
        }

        updateTestimonialNavButtons();
        testimonialsSlider.addEventListener('scroll', updateTestimonialNavButtons);
        window.addEventListener('resize', updateTestimonialNavButtons);
    }

    // --------------------------
    // Carousel Button Controls
    // --------------------------
    const carousel = document.getElementById('carousel');
    const carouselBtnLeft = document.querySelector('.photo-carousel-section__nav-left');
    const carouselBtnRight = document.querySelector('.photo-carousel-section__nav-right');

    const carouselItems = carousel.querySelectorAll('.photo-carousel-section__item');
    let itemWidth = carouselItems[0]?.offsetWidth || 300;

    window.addEventListener('resize', () => {
        itemWidth = carouselItems[0]?.offsetWidth || 300;
    });

    carouselBtnLeft.addEventListener('click', () => {
        carousel.scrollBy({
            left: -itemWidth,
            behavior: 'smooth'
        });
        updateCarouselNavButtons();
    });

    carouselBtnRight.addEventListener('click', () => {
        carousel.scrollBy({
            left: itemWidth,
            behavior: 'smooth'
        });
        updateCarouselNavButtons();
    });

    function updateCarouselNavButtons() {
        setTimeout(() => {
            carouselBtnLeft.disabled = carousel.scrollLeft <= 0;
            carouselBtnRight.disabled = carousel.scrollLeft + carousel.clientWidth >= carousel.scrollWidth - 1;
        }, 200);
    }

    updateCarouselNavButtons();
    carousel.addEventListener('scroll', updateCarouselNavButtons);
    window.addEventListener('resize', updateCarouselNavButtons);

    // --------------------------
    // Pride Slider Script
    // --------------------------
    const prideSection = document.querySelector(".pride-section");
    const prideSliderWrapper = document.querySelector(".pride-section__slider-wrapper");
    const prideSlides = document.querySelectorAll(".pride-section__item");
    const pridePrevBtn = document.querySelector(".pride-section__nav-prev");
    const prideNextBtn = document.querySelector(".pride-section__nav-next");

    let prideCurrentIndex = 0;

    function showNextPrideSlide() {
        prideCurrentIndex = (prideCurrentIndex + 1) % prideSlides.length;
        updatePrideSlider();
    }

    function showPrevPrideSlide() {
        prideCurrentIndex = (prideCurrentIndex - 1 + prideSlides.length) % prideSlides.length;
        updatePrideSlider();
    }

    function updatePrideSlider() {
        const offset = -prideCurrentIndex * 100;
        prideSliderWrapper.style.transform = `translateX(${offset}%)`;
    }

    prideNextBtn.addEventListener("click", showNextPrideSlide);
    pridePrevBtn.addEventListener("click", showPrevPrideSlide);

    // Auto slide every 5 seconds
    let prideInterval = setInterval(showNextPrideSlide, 5000);

    prideSection.addEventListener("mouseenter", () => clearInterval(prideInterval));
    prideSection.addEventListener("mouseleave", () => {
        prideInterval = setInterval(showNextPrideSlide, 5000);
    });

});
