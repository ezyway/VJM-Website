// Wait for the DOM to be fully loaded
document.addEventListener("DOMContentLoaded", () => {
  // Hamburger menu functionality
  const hamburger = document.querySelector(".hamburger");
  const navLinks = document.querySelector(".nav-links");

  hamburger.addEventListener("click", () => {
      hamburger.classList.toggle("active");
      navLinks.classList.toggle("active");
  });
});
