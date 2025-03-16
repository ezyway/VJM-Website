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
  });
