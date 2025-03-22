document.addEventListener("DOMContentLoaded", () => {

	// Select all elements that should animate (they have the data-target attribute)
	const counters = document.querySelectorAll('[data-target]');

	// Function to animate count from 0 to the target value over a given duration
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

	// Use Intersection Observer to trigger the count-up when the element is in view
	const observer = new IntersectionObserver((entries, observer) => {
		entries.forEach(entry => {
			if (entry.isIntersecting) {
				animateCount(entry.target, 2000); // 2000 ms duration for the animation
				observer.unobserve(entry.target); // Stop observing once the animation starts
			}
		});
	}, { threshold: 0.5 }); // Trigger when 50% of the element is visible

	// Observe each counter element
	counters.forEach(counter => {
		observer.observe(counter);
	});

	// --------------------------
	// Testimonial Slider Script
	// --------------------------

	// Select the testimonials slider and navigation buttons
const slider = document.querySelector('.testimonials-slider');
const btnLeft = document.querySelector('.testimonial-nav.left');
const btnRight = document.querySelector('.testimonial-nav.right');

// Check if slider and buttons exist
if (slider && btnLeft && btnRight) {
    // Dynamically calculate scroll amount based on viewport width
    function getScrollAmount() {	
        return Math.min(slider.clientWidth * 0.75, 300); // 80% of container width or max 300px
    }

    // Function to center the active slide
    function centerActiveSlide() {
        const activeItem = slider.querySelector('.testimonial-item.active');
        if (activeItem) {
            const offsetLeft = activeItem.offsetLeft;
            const itemWidth = activeItem.offsetWidth;
            const centerPosition = offsetLeft - (slider.clientWidth / 2) + (itemWidth / 2);
            slider.scrollTo({
                left: centerPosition,
                behavior: 'smooth'
            });
        }
    }

    // Scroll left on clicking the left button
    btnLeft.addEventListener('click', () => {
        slider.scrollBy({
            left: -getScrollAmount(),
            behavior: 'smooth'
        });
        setTimeout(centerActiveSlide, 300); // Center after the scroll
        updateNavButtons();
    });

    // Scroll right on clicking the right button
    btnRight.addEventListener('click', () => {
        slider.scrollBy({
            left: getScrollAmount(),
            behavior: 'smooth'
        });
        setTimeout(centerActiveSlide, 300); // Center after the scroll
        updateNavButtons();
    });

    // Disable buttons when at the start or end
    function updateNavButtons() {
        setTimeout(() => {
            btnLeft.disabled = slider.scrollLeft <= 0;
            btnRight.disabled = slider.scrollLeft + slider.clientWidth >= slider.scrollWidth;
        }, 300);
    }

    // Initialize button states
    updateNavButtons();

    // Update button state on scroll
    slider.addEventListener('scroll', updateNavButtons);

    // Resize event to adjust scroll amount dynamically
    window.addEventListener('resize', updateNavButtons);
}



	
	const carousel = document.getElementById('carousel');
	let scrollAmount = 0;
	const scrollSpeed = 1; // Speed of auto-scroll
	const scrollInterval = 30; // Interval in milliseconds

	function autoScroll() {
		scrollAmount += scrollSpeed;
		if (scrollAmount >= carousel.scrollWidth - carousel.clientWidth) {
			scrollAmount = 0;
		}
		carousel.scrollLeft = scrollAmount;
	}

	// Auto scroll every 30ms
	let autoScrollInterval = setInterval(autoScroll, scrollInterval);

	// Pause auto-scroll on hover
	carousel.addEventListener('mouseenter', () => clearInterval(autoScrollInterval));
	carousel.addEventListener('mouseleave', () => {
		autoScrollInterval = setInterval(autoScroll, scrollInterval);
	});

	
});
