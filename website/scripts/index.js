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

    function animateCount(element, duration) {
        const targetString = element.getAttribute('data-target');
        const isFloat = targetString.includes('.');
        // Check if the element's class indicates it needs a '+' suffix
        const percentSuffix = element.classList.contains('counter-section__pass-percentage');
        const plusSuffix = element.classList.contains('counter-section__enrolled') || 
                                element.classList.contains('counter-section__passouts');

        const target = isFloat ? parseFloat(targetString) : parseInt(targetString, 10);
        let startTime = null;

        function updateCount(timestamp) {
            if (!startTime) startTime = timestamp;
            const progress = timestamp - startTime;
            let displayValue;

            if (progress < duration) {
                if (isFloat) {
                    const currentValue = (progress / duration) * target;
                    // Determine decimal places from the target string, e.g., "97.63" has 2
                    const decimalPlaces = (targetString.split('.')[1] || '').length;
                    displayValue = Math.min(currentValue, target).toFixed(decimalPlaces);
                } else {
                    const currentValue = Math.floor((progress / duration) * target);
                    displayValue = Math.min(currentValue, target);
                }
                element.textContent = displayValue;
                requestAnimationFrame(updateCount);
            } else {
                // Animation finished, set the final target value
                if (isFloat) {
                    const decimalPlaces = (targetString.split('.')[1] || '').length;
                    displayValue = target.toFixed(decimalPlaces);
                } else {
                    displayValue = target;
                }
                element.textContent = displayValue;

                // Add '+ and %' suffix if needed
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


    // --------------------------
    // Back to top button
    // --------------------------
    const backToTopButton = document.getElementById("backToTop");
    const section1 = document.getElementById("video-banner");

    // Function to check if section1 is out of view
    function toggleBackToTop() {
        const rect = section1.getBoundingClientRect();
        // Check if the bottom of section1 is above the viewport
        if (rect.bottom <= 0) {
            backToTopButton.style.display = "block";
        } else {
            backToTopButton.style.display = "none";
        }
    }

    // Listen for scroll events
    window.addEventListener("scroll", toggleBackToTop);

    // Smooth scroll to top when button is clicked
    backToTopButton.addEventListener("click", () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    });
});
