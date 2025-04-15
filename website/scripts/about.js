
document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.querySelector('.trustee-section__slider-wrapper');
    const items = document.querySelectorAll('.trustee-section__item');
    const slider = document.querySelector('.trustee-section__slider');
    const totalItems = items.length;
    let currentIndex = 0;
    let itemWidth = items[0].offsetWidth;
    let visibleItems = Math.floor(slider.offsetWidth / itemWidth);
    let maxIndex = totalItems - visibleItems;

    // Make sure we always have at least one visible item
    if (visibleItems <= 0) visibleItems = 1;
    if (maxIndex < 0) maxIndex = 0;

    // Function to update the slider position
    function updateSlider() {
        wrapper.style.transform = `translateX(${-currentIndex * itemWidth}px)`;
    }

    // Auto scroll function
    function autoScroll() {
        currentIndex = (currentIndex + 1) % (maxIndex + 1);
        updateSlider();
    }

    // Set up the auto-scroll interval
    const scrollInterval = setInterval(autoScroll, 3000);

    // Recalculate dimensions on window resize
    window.addEventListener('resize', function () {
        // Clear the interval to prevent scrolling during resize
        clearInterval(scrollInterval);

        // Recalculate dimensions
        itemWidth = items[0].offsetWidth;
        visibleItems = Math.floor(slider.offsetWidth / itemWidth);

        // Make sure we always have at least one visible item
        if (visibleItems <= 0) visibleItems = 1;

        // Update maxIndex
        maxIndex = totalItems - visibleItems;
        if (maxIndex < 0) maxIndex = 0;

        // Reset current index if it's out of bounds
        if (currentIndex > maxIndex) currentIndex = maxIndex;

        // Update slider position
        updateSlider();

        // Restart auto-scroll
        setInterval(autoScroll, 3000);
    });
});
