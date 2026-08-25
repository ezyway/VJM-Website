<?php
require_once dirname(__DIR__) . '/admin/includes/db.php';

$testimonials = [];
try {
    $db = getDB();
    $testimonials = $db->query("SELECT * FROM testimonials ORDER BY sort_order ASC, id ASC")->fetchAll();
} catch (Exception $e) {
    $testimonials = [];
}
?>
<!-- ===================================================
    Testimonials Section
    - Interactive student voices with glass cards and star ratings
    =================================================== -->
<section class="testimonials-section" id="testimonials">
    <div class="testimonials-section__wrapper">
        
        <div class="testimonials-section__header">
            <div class="testimonials-section__header-text">
                <span class="section__eyebrow">Student Experiences</span>
                <h2 class="testimonials-section__title">What Our Students Say</h2>
            </div>
            <div class="testimonials-section__controls">
                <button class="testimonials-section__nav-left" aria-label="Previous testimonial">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <button class="testimonials-section__nav-right" aria-label="Next testimonial">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>

        <div class="testimonials-section__container">
            <div class="testimonials-section__slider">

                <?php if (empty($testimonials)): ?>
                    <div class="testimonials-section__item">
                        <div class="testimonials-section__stars">★★★★★</div>
                        <p class="testimonials-section__text">
                            "The faculty are so supportive and truly committed to our success. The guidance I received here shaped my career path completely."
                        </p>
                        <div class="testimonials-section__author">
                            <div class="testimonials-section__avatar">OR</div>
                            <div class="testimonials-section__info">
                                <h3 class="testimonials-section__name">Odedra Ranjitji</h3>
                                <span class="testimonials-section__course">B.S.W. Graduate</span>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($testimonials as $t): ?>
                        <div class="testimonials-section__item">
                            <div class="testimonials-section__stars"><?= str_repeat('★', (int)$t['stars']) ?></div>
                            <p class="testimonials-section__text">
                                "<?= htmlspecialchars($t['text']) ?>"
                            </p>
                            <div class="testimonials-section__author">
                                <div class="testimonials-section__avatar"><?= htmlspecialchars($t['avatar_text']) ?></div>
                                <div class="testimonials-section__info">
                                    <h3 class="testimonials-section__name"><?= htmlspecialchars($t['name']) ?></h3>
                                    <span class="testimonials-section__course"><?= htmlspecialchars($t['course']) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </div>

    </div>
</section>
