document.addEventListener("DOMContentLoaded", () => {
    const track = document.querySelector('.carousel-track');
    const slides = document.querySelectorAll('.lab-section__image');
    let index = 0;

    function showNextSlide() {
        index = (index + 1) % slides.length;
        track.style.transform = `translateX(-${index * 100}%)`;
    }

    setInterval(showNextSlide, 3000);
});