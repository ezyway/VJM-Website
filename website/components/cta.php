<?php
/**
 * Reusable Call-to-Action Component
 * 
 * Usage:
 *   $cta = [
 *       'badge'     => 'Admissions Open',
 *       'title'     => 'Start Your Academic Journey Today',
 *       'subtitle'  => 'Need guidance? Reach out to our counseling desk.',
 *       'actions'   => [
 *           ['label' => 'Get in Touch', 'url' => 'contact.php', 'class' => 'btn--primary'],
 *           ['label' => 'About Campus', 'url' => 'about.php', 'class' => 'btn--secondary'],
 *       ],
 *   ];
 *   include('components/cta.php');
 */

$cta_defaults = [
    'badge'    => '',
    'title'    => '',
    'subtitle' => '',
    'actions'  => [],
];

$cta = array_merge($cta_defaults, $cta ?? []);
?>

<section class="page-cta" id="cta">
    <div class="page-cta__container">
        <div class="page-cta__box">
            <?php if (!empty($cta['badge'])): ?>
                <span class="page-cta__badge"><?= htmlspecialchars($cta['badge']) ?></span>
            <?php endif; ?>
            <?php if (!empty($cta['title'])): ?>
                <h2 class="page-cta__title"><?= htmlspecialchars($cta['title']) ?></h2>
            <?php endif; ?>
            <?php if (!empty($cta['subtitle'])): ?>
                <p class="page-cta__subtitle"><?= htmlspecialchars($cta['subtitle']) ?></p>
            <?php endif; ?>
            <?php if (!empty($cta['actions'])): ?>
            <div class="page-cta__actions">
                <?php foreach ($cta['actions'] as $action): ?>
                    <a href="<?= htmlspecialchars($action['url']) ?>" class="btn <?= htmlspecialchars($action['class'] ?? 'btn--primary') ?>">
                        <?= htmlspecialchars($action['label']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
