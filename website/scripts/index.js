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
		// Define the scroll amount (in pixels)
		const scrollAmount = 300;

		// Scroll left on clicking the left button
		btnLeft.addEventListener('click', () => {
			slider.scrollBy({
				left: -scrollAmount,
				behavior: 'smooth'
			});
		});

		// Scroll right on clicking the right button
		btnRight.addEventListener('click', () => {
			slider.scrollBy({
				left: scrollAmount,
				behavior: 'smooth'
			});
		});
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
