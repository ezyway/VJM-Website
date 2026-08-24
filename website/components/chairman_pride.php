<!-- ===================================================
        Chairman & Pride of the College Section
        =================================================== -->
<section class="chairman-pride-section">
    <div class="chairman-pride__container">
        
        <!-- Chairman Executive Profile Card -->
        <div class="chairman-section__profile">
            <div class="chairman-section__header">
                <img src="assets/photos/index/chairman.png" alt="Mr. Vallabhbhai Modha" class="chairman-section__image" width="100" height="100" loading="lazy" decoding="async" />
                <div class="chairman-section__header-info">
                    <span class="chairman-section__badge">Chairman's Desk</span>
                    <h3 class="chairman-section__name">Mr. Vallabhbhai Modha</h3>
                    <p class="chairman-section__designation">Chairman, Shri V.J. Modha College, Porbandar</p>
                </div>
            </div>
            
            <div class="chairman-section__body">
                <div class="chairman-section__quote-icon">
                    <svg viewBox="0 0 24 24" width="30" height="30" fill="currentColor">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                    </svg>
                </div>
                <blockquote class="chairman-section__message">
                    "Knowledge is power &amp; with this power, I can visualize that the future of our nation is presently building in the classrooms. Today's students are the builders of our nation. For such building, our college has recorded a stupendous period in the history of this institute. We believe in quality education; time has wings &amp; it constantly flies, but now the time has come to pause and reflect on the modern system of education and harness the positive factors beneficial for the students."
                </blockquote>
            </div>
        </div>

        <!-- Pride of the College Section with Slider -->
        <div class="pride-section">
            <div class="pride-section__header">
                <div>
                    <span class="section__eyebrow">Academic Laurels</span>
                    <h2 class="pride-section__title">Pride of the College</h2>
                </div>
                <div class="pride-section__nav">
                    <button class="pride-section__nav-prev" aria-label="Previous ranker" type="button">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </button>
                    <button class="pride-section__nav-next" aria-label="Next ranker" type="button">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="pride-section__slider" id="prideSlider">
                <div class="pride-section__slider-wrapper">
                    <?php
                    $image_dir_fs = dirname(__DIR__) . '/assets/photos/index/pride_of_college/';
                    $image_dir_web = 'assets/photos/index/pride_of_college/';
                    $images = glob($image_dir_fs . '*.{jpg,jpeg,png,webp}', GLOB_BRACE);

                    if ($images) {
                        sort($images);
                        foreach ($images as $path) {
                            $filename = basename($path);
                            $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);

                            // Split into Course__Name__Rank
                            $parts = explode('__', $filenameWithoutExt);
                            $courseRaw = $parts[0] ?? '';
                            $nameRaw   = $parts[1] ?? '';
                            $rankRaw   = $parts[2] ?? '';

                            // === Course Parsing ===
                            $courseRaw = str_replace('_', ' ', $courseRaw);
                            $courseSegments = explode('-', $courseRaw);

                            $courseName = trim($courseSegments[0] ?? '');
                            $semester   = '';
                            $language   = '';

                            if (isset($courseSegments[1])) {
                                if (is_numeric($courseSegments[1])) {
                                    $semester = $courseSegments[1];
                                    $language = $courseSegments[2] ?? '';
                                } else {
                                    $courseName .= ' ' . $courseSegments[1];
                                    if (isset($courseSegments[2]) && is_numeric($courseSegments[2])) {
                                        $semester = $courseSegments[2];
                                        $language = $courseSegments[3] ?? '';
                                    }
                                }
                            }

                            $courseLabel = trim(
                                $courseName .
                                    ($language ? ' ' . $language : '') .
                                    ($semester ? " (Sem $semester)" : '')
                            );

                            // === Name Parsing ===
                            $fullName = str_replace('_', ' ', $nameRaw);

                            // === Rank Parsing ===
                            $rankDisplay = '';
                            if (preg_match('/(.*?)-(\d+)(?:_(\w+))?$/', $rankRaw, $matches)) {
                                $scope = $matches[1] ?? '';
                                $number = $matches[2] ?? '';
                                $suffix = isset($matches[3]) ? "<sup>{$matches[3]}</sup>" : '';
                                $rankDisplay = "{$scope} Rank {$number}{$suffix}";
                            } else {
                                $rankDisplay = str_replace('_', ' ', $rankRaw);
                            }

                            $src = $image_dir_web . $filename;

                            echo <<<HTML
                                <div class="pride-section__item">
                                    <div class="pride-section__card">
                                        <div class="pride-section__image-box">
                                            <img src="{$src}" alt="{$fullName}" class="pride-section__image" loading="lazy" />
                                            <span class="pride-section__award">{$rankDisplay}</span>
                                        </div>
                                        <div class="pride-section__details">
                                            <h3 class="pride-section__name">{$fullName}</h3>
                                            <p class="pride-section__course">{$courseLabel}</p>
                                        </div>
                                    </div>
                                </div>
                            HTML;
                        }
                    } else {
                        echo "<p class='pride-section__no-images-message'>No ranker images available at the moment.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>

    </div>
</section>