<?php
/**
 * Reusable Hero Header Component
 * 
 * Usage:
 *   $hero = [
 *       'title'       => 'Page Title',
 *       'badge'       => 'Badge Text',
 *       'slogan'      => '॥ Sanskrit Slogan ॥',
 *       'subtitle'    => 'Page description text...',
 *       'breadcrumb'  => [
 *           ['label' => 'Home', 'url' => 'index.php'],
 *           ['label' => 'Section', 'url' => null],  // null = no link
 *           ['label' => 'Current Page', 'url' => null],  // current page
 *       ],
 *       'metrics'     => [  // optional metric pills
 *           ['value' => '₹ 2.05+ Crores', 'label' => 'Total Aid Disbursed'],
 *       ],
 *       'specs'       => [  // optional spec pills
 *           ['icon' => 'clock', 'label' => 'Duration:', 'value' => '4 Years'],
 *       ],
 *   ];
 *   include('components/hero.php');
 */

$hero_defaults = [
    'title'      => '',
    'badge'      => '',
    'slogan'     => '॥ विद्यार्थी लभते विद्यां ॥',
    'subtitle'   => '',
    'breadcrumb' => [],
    'metrics'    => [],
    'specs'      => [],
];

$hero = array_merge($hero_defaults, $hero ?? []);
?>

<header class="page-hero" id="page-hero">
    <div class="page-hero__overlay">
        <div class="page-hero__content">
            <?php if (!empty($hero['breadcrumb'])): ?>
            <nav class="page-hero__breadcrumb" aria-label="Breadcrumb">
                <?php foreach ($hero['breadcrumb'] as $i => $crumb): ?>
                    <?php if ($i > 0): ?>
                        <span class="page-hero__breadcrumb-sep">/</span>
                    <?php endif; ?>
                    <?php if (!empty($crumb['url'])): ?>
                        <a href="<?= htmlspecialchars($crumb['url']) ?>"><?= htmlspecialchars($crumb['label']) ?></a>
                    <?php elseif (isset($crumb['current']) && $crumb['current']): ?>
                        <span aria-current="page" id="heroBreadcrumbCurrent"><?= htmlspecialchars($crumb['label']) ?></span>
                    <?php else: ?>
                        <span><?= htmlspecialchars($crumb['label']) ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
            <?php endif; ?>

            <?php if (!empty($hero['badge'])): ?>
                <span class="page-hero__badge" id="heroBadge"><?= htmlspecialchars($hero['badge']) ?></span>
            <?php endif; ?>

            <?php if (!empty($hero['title'])): ?>
                <h1 class="page-hero__title" id="heroTitle"><?= htmlspecialchars($hero['title']) ?></h1>
            <?php endif; ?>

            <?php if (!empty($hero['slogan'])): ?>
                <p class="page-hero__slogan"><?= $hero['slogan'] ?></p>
            <?php endif; ?>

            <?php if (!empty($hero['subtitle'])): ?>
                <p class="page-hero__subtitle" id="heroSubtitle"><?= htmlspecialchars($hero['subtitle']) ?></p>
            <?php endif; ?>

            <?php if (!empty($hero['metrics'])): ?>
            <div class="page-hero__metrics">
                <?php foreach ($hero['metrics'] as $metric): ?>
                    <div class="page-hero__metric-pill">
                        <strong><?= htmlspecialchars($metric['value']) ?></strong>
                        <span><?= htmlspecialchars($metric['label']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($hero['specs'])): ?>
            <div class="page-hero__specs">
                <?php foreach ($hero['specs'] as $spec): ?>
                    <div class="page-hero__spec-pill">
                        <?php if (!empty($spec['icon'])): ?>
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <?php if ($spec['icon'] === 'clock'): ?>
                                <circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline>
                            <?php elseif ($spec['icon'] === 'book'): ?>
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                            <?php elseif ($spec['icon'] === 'graduation'): ?>
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            <?php endif; ?>
                        </svg>
                        <?php endif; ?>
                        <span><strong><?= htmlspecialchars($spec['label']) ?></strong> <?= htmlspecialchars($spec['value']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</header>
