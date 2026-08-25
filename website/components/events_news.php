<?php
require_once dirname(__DIR__) . '/admin/includes/db.php';

$upcomingEvents = [];
$academicNews = [];

try {
    $db = getDB();
    $upcomingEvents = $db->query("SELECT * FROM events WHERE event_type = 'event' ORDER BY sort_order ASC, id ASC")->fetchAll();
    $academicNews   = $db->query("SELECT * FROM events WHERE event_type = 'news' ORDER BY sort_order ASC, id ASC")->fetchAll();
} catch (Exception $e) {
    $upcomingEvents = [];
    $academicNews = [];
}
?>
<!-- ===================================================
        Events and Results Hub Section
        =================================================== -->
<section class="news-section" id="news-section">
    <div class="news-section__container">
        
        <!-- Upcoming Events Column -->
        <div class="news-section__box news-section__events">
            <div class="news-section__header">
                <div class="news-section__header-icon">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div>
                    <span class="section__eyebrow">Campus Life</span>
                    <h2 class="news-section__title">Upcoming Events</h2>
                </div>
            </div>

            <div class="news-section__list news-section__slider--events">
                <?php if (empty($upcomingEvents)): ?>
                    <div class="news-section__item">
                        <div class="news-section__item-header">
                            <h3 class="news-section__item-title">Independence Day Celebration</h3>
                            <span class="news-section__badge news-section__badge--confirmed">15 Aug 2025</span>
                        </div>
                        <p class="news-section__item-desc">Patriotic celebration featuring ceremonial flag hoisting, cultural performances, and tributes to freedom heroes.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($upcomingEvents as $ev): 
                        $badgeClass = ($ev['badge_type'] === 'confirmed') ? 'news-section__badge--confirmed' : 'news-section__badge--tba';
                    ?>
                        <div class="news-section__item">
                            <div class="news-section__item-header">
                                <h3 class="news-section__item-title"><?= htmlspecialchars($ev['title']) ?></h3>
                                <span class="news-section__badge <?= $badgeClass ?>"><?= htmlspecialchars($ev['badge']) ?></span>
                            </div>
                            <p class="news-section__item-desc"><?= htmlspecialchars($ev['description']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Academic News & Results Column -->
        <div class="news-section__box news-section__results">
            <div class="news-section__header">
                <div class="news-section__header-icon">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <div>
                    <span class="section__eyebrow">Academic Updates</span>
                    <h2 class="news-section__title">Recent University Results &amp; Notices</h2>
                </div>
            </div>

            <div class="news-section__list news-section__slider--results">
                <?php if (empty($academicNews)): ?>
                    <div class="news-section__item news-section__item--result">
                        <div class="news-section__item-header">
                            <h3 class="news-section__item-title">B.C.A. Semester 6</h3>
                            <span class="news-section__badge news-section__badge--result">May 2025</span>
                        </div>
                        <div class="news-section__result-footer">
                            <span class="news-section__university">BKNMU University</span>
                            <a href="https://bknmuerp.in/" target="_blank" rel="noopener noreferrer" class="news-section__link">
                                View Result 
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($academicNews as $nw): ?>
                        <div class="news-section__item news-section__item--result">
                            <div class="news-section__item-header">
                                <h3 class="news-section__item-title"><?= htmlspecialchars($nw['title']) ?></h3>
                                <span class="news-section__badge news-section__badge--result"><?= htmlspecialchars($nw['badge']) ?></span>
                            </div>
                            <?php if (!empty($nw['description'])): ?>
                                <p class="news-section__item-desc" style="margin-top: 4px; font-size: 12.5px;"><?= htmlspecialchars($nw['description']) ?></p>
                            <?php endif; ?>
                            <div class="news-section__result-footer">
                                <span class="news-section__university">BKNMU Notice Board</span>
                                <a href="https://bknmuerp.in/" target="_blank" rel="noopener noreferrer" class="news-section__link">
                                    View Details 
                                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>
