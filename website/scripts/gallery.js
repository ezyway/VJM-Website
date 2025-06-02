let currentSlideIndex = 0;
let slideContainer; // Will hold the slide elements
let modalCounter; // Will hold the counter element
// Open the modal and fetch images from the selected album
function openModal(album) {
    modalCounter = document.getElementById("modal-counter"); // Get the counter element earlier

    fetch(`?album=${album}&action=json`)
        .then(response => response.json())
        .then(imageURLs => {
            if (imageURLs.length === 0) return;
            currentSlideIndex = 0;
            buildSlides(imageURLs);
            document.getElementById("modal").classList.add("gallery_modal--active");
            // Add keydown listener for Escape key
            document.addEventListener("keydown", handleKeyDown);
        })
        .catch(err => {
            console.error("Error fetching images: ", err);
            if (modalCounter) modalCounter.textContent = ""; // Clear counter on error
        });
}
// Build the slide elements inside the modal content container
function buildSlides(imageURLs) {
    const modalContent = document.getElementById("modal-content");
    modalContent.innerHTML = ""; // Clear any existing content
    
    // Create slides only for valid image URLs
    imageURLs.forEach((url, index) => {
        if (!url) return; // Skip empty URLs
        
        const slide = document.createElement("div");
        slide.className = "gallery_slide";
        
        const img = document.createElement("img");
        img.src = url;
        img.className = "gallery_slide__image";
        
        // Add error handling for images that fail to load
        img.onerror = function() {
            console.warn(`Failed to load image: ${url}`);
            this.src = 'assets/placeholder.png'; // Use a placeholder or remove this slide
        };
        
        slide.appendChild(img);
        modalContent.appendChild(slide);
    });
    
    // Cache the slide elements for navigation
    slideContainer = document.querySelectorAll(".gallery_modal__content .gallery_slide");
    
    updateCounter(); // Always update counter based on actual slides

    if (slideContainer.length > 0) {
        updateSlidePosition();
    } else {
        console.warn("No valid slides found");
    }
}
// Update slide positions using a slide effect
function updateSlidePosition() {
    slideContainer.forEach((slide, index) => {
        slide.style.transform = `translateX(${(index - currentSlideIndex) * 100}%)`;
    });
}
// Update the counter text
function updateCounter() {
    if (modalCounter && slideContainer && slideContainer.length > 0) {
        modalCounter.textContent = `${currentSlideIndex + 1} / ${slideContainer.length}`;
    } else if (modalCounter) {
        modalCounter.textContent = ""; // Clear counter if no slides
    }
}
// Move to the next slide
function nextSlide() {
    if (!slideContainer || slideContainer.length === 0) return;
    currentSlideIndex = (currentSlideIndex + 1) % slideContainer.length;
    updateSlidePosition();
    updateCounter();
}
// Move to the previous slide
function prevSlide() {
    if (!slideContainer || slideContainer.length === 0) return;
    currentSlideIndex = (currentSlideIndex - 1 + slideContainer.length) % slideContainer.length;
    updateSlidePosition();
    updateCounter();
}
// Close the modal and remove content
function closeModal() {
    document.getElementById("modal").classList.remove("gallery_modal--active");
    document.getElementById("modal-content").innerHTML = "";
    if (modalCounter) modalCounter.textContent = ""; // Clear counter text
    document.removeEventListener("keydown", handleKeyDown);
}
// Handle Escape key to close the modal
function handleKeyDown(e) {
    if (e.key === "Escape") {
        closeModal();
    }
}