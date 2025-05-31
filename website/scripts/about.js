document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.querySelector('.trustee-section__slider-wrapper');
    const items = document.querySelectorAll('.trustee-section__item');
    const slider = document.querySelector('.trustee-section__slider');
    const prevButton = document.getElementById('trustee-prev');
    const nextButton = document.getElementById('trustee-next');

    const totalItems = items.length;
    let currentIndex = 0;
    let itemWidth = 0;
    let visibleItems = 0;
    let maxIndex = 0;

    function calculateDimensions() {
        if (items.length > 0 && items[0].offsetWidth > 0) {
            itemWidth = items[0].offsetWidth;
            visibleItems = Math.floor(slider.offsetWidth / itemWidth);
        } else {
            itemWidth = 0;
            visibleItems = 0;
        }

        // Ensure we always have at least one visible item if possible, or handle no items
        if (visibleItems <= 0 && totalItems > 0) visibleItems = 1;

        maxIndex = totalItems - visibleItems;
        if (maxIndex < 0) maxIndex = 0;

        // Reset current index if it's out of bounds after recalculation
        if (currentIndex > maxIndex) currentIndex = maxIndex;
    }

    function updateSlider() {
        // Ensure wrapper and slider exist, and itemWidth is positive for calculations
        if (wrapper && slider && itemWidth > 0) {
            const wrapperWidth = totalItems * itemWidth;

            // Calculate the leftmost position the wrapper can be translated to.
            const maxNegativeScroll = Math.min(0, slider.offsetWidth - wrapperWidth);

            // Intended translation based on current index and item width
            let targetTranslateX = -currentIndex * itemWidth;

            // It cannot be further left than maxNegativeScroll.
            const finalTranslateX = Math.max(targetTranslateX, maxNegativeScroll);

            wrapper.style.transform = `translateX(${finalTranslateX}px)`;
        } else if (wrapper) {
            // Fallback: if itemWidth is not valid, or other elements missing, reset transform.
            wrapper.style.transform = `translateX(0px)`;
        }
    }

    function updateButtonStates() {
        if (maxIndex <= 0) {
            prevButton.disabled = true;
            nextButton.disabled = true;
        } else {
            prevButton.disabled = false;
            nextButton.disabled = false;
        }
    }

    function showNextItem() {
        if (maxIndex <= 0) return; // No scrolling if all items fit or no scrollable items
        currentIndex = (currentIndex + 1) % (maxIndex + 1);
        updateSlider();
    }

    function showPrevItem() {
        if (maxIndex <= 0) return; // No scrolling
        currentIndex = (currentIndex - 1 + (maxIndex + 1)) % (maxIndex + 1);
        updateSlider();
    }

    // Event Listeners for Nav Buttons
    nextButton.addEventListener('click', showNextItem);
    prevButton.addEventListener('click', showPrevItem);

    // Initial Setup
    calculateDimensions();
    updateSlider();
    updateButtonStates();

    // Recalculate dimensions on window resize
    window.addEventListener('resize', function () {
        calculateDimensions();
        updateSlider();
        updateButtonStates();
    });
});
