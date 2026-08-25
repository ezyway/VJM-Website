<?php
require_once dirname(__DIR__) . '/admin/includes/db.php';

$counterCourses  = getSetting('counter_courses', '8');
$counterPassRate = getSetting('counter_pass_rate', '97.6');
$counterStudents = getSetting('counter_students', '1500');
$counterAlumni   = getSetting('counter_alumni', '7900');
?>
<!-- ===================================================
        Counter Section
        - Dynamic animated counters with glass cards and icons
        =================================================== -->
<section class="counter-section" id="counter-section">
    <div class="counter-section__container">
        <div class="counter-section__box">
            
            <div class="counter-section__item">
                <div class="counter-section__icon-wrap">
                    <svg viewBox="0 0 24 24" class="counter-section__icon" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        <line x1="12" y1="6" x2="16" y2="6"></line>
                        <line x1="12" y1="10" x2="16" y2="10"></line>
                    </svg>
                </div>
                <div class="counter-section__courses counter-section__number" data-target="<?= htmlspecialchars($counterCourses) ?>">0</div>
                <div class="counter-section__label">Academic Programs</div>
            </div>

            <div class="counter-section__item">
                <div class="counter-section__icon-wrap">
                    <svg viewBox="0 0 24 24" class="counter-section__icon" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="7"></circle>
                        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                    </svg>
                </div>
                <div class="counter-section__pass-percentage counter-section__number" data-target="<?= htmlspecialchars($counterPassRate) ?>">0</div>
                <div class="counter-section__label">Academic Pass Rate</div>
            </div>

            <div class="counter-section__item">
                <div class="counter-section__icon-wrap">
                    <svg viewBox="0 0 24 24" class="counter-section__icon" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="counter-section__enrolled counter-section__number" data-target="<?= htmlspecialchars($counterStudents) ?>">0</div>
                <div class="counter-section__label">Active Students</div>
            </div>

            <div class="counter-section__item">
                <div class="counter-section__icon-wrap">
                    <svg viewBox="0 0 24 24" class="counter-section__icon" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                    </svg>
                </div>
                <div class="counter-section__passouts counter-section__number" data-target="<?= htmlspecialchars($counterAlumni) ?>">0</div>
                <div class="counter-section__label">Graduated Alumni</div>
            </div>

        </div>
    </div>
</section>
