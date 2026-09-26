(function() {
    'use strict';

    let backToTopBtn = null;

    function initBackToTop() {
        backToTopBtn = document.getElementById('backToTop');
        if (!backToTopBtn) return;

        // Throttled scroll handler
        let ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    toggleBackToTop();
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });

        backToTopBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        toggleBackToTop();
    }

    function toggleBackToTop() {
        if (!backToTopBtn) return;
        if (window.scrollY > 400) {
            backToTopBtn.classList.add('is-visible');
        } else {
            backToTopBtn.classList.remove('is-visible');
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBackToTop);
    } else {
        initBackToTop();
    }
})();
