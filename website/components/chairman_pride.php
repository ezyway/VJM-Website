<!-- ===================================================
        Chairman Section
        - Displays the chairman's image along with his message.
        =================================================== -->
<section class="chairman-section">
    <div class="chairman-section__profile">
        <img src="assets/photos/index/chairman.png" alt="Chairman Image" class="chairman-section__image" />
        <h3 class="chairman-section__name">Mr. Vallabhbhai Modha</h3>
        <p class="chairman-section__designation">Chairman, Shri V.J. Modha College, Porbandar</p>
        <p class="chairman-section__message">
            "Knowledge is power &amp; with this power, I can visualize that the future of our nation is presently building in the classrooms. Today's students are the builders of our nation. For such building, our college has recorded a stupendous period in the history of this institute. We believe in quality education; time has wings &amp; it constantly flies, but now the time has come to pause and reflect on the modern system of education and harness the positive factors beneficial for the students."
        </p>
    </div>

    <!-- Pride of the College Section with Slider -->
    <div class="pride-section">
        <h2>Pride of the College</h2>
        <div class="pride-section__slider">
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
                        $courseRaw = $parts[0];
                        $nameRaw   = $parts[1];
                        $rankRaw   = $parts[2];

                        // === Course Parsing ===
                        // Normalize underscores to spaces, except rank suffixes
                        $courseRaw = str_replace('_', ' ', $courseRaw);

                        // Split by dash
                        $courseSegments = explode('-', $courseRaw);

                        $courseName = trim($courseSegments[0] ?? '');
                        $semester   = '';
                        $language   = '';

                        // Handle cases like M.Com.-3-Gujarati or B.Sc.-6
                        if (isset($courseSegments[1])) {
                            if (is_numeric($courseSegments[1])) {
                                $semester = $courseSegments[1];
                                $language = $courseSegments[2] ?? '';
                            } else {
                                $courseName .= ' ' . $courseSegments[1]; // Possibly part of name
                                if (isset($courseSegments[2]) && is_numeric($courseSegments[2])) {
                                    $semester = $courseSegments[2];
                                    $language = $courseSegments[3] ?? '';
                                }
                            }
                        }

                        // Final course label
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
                        }

                        $src = $image_dir_web . $filename;

                        echo <<<HTML
                            <div class="pride-section__item">
                                <img src="{$src}" alt="{$fullName}" class="pride-section__image" />
                                <h3 class="pride-section__name">{$fullName}</h3>
                                <p class="pride-section__course">{$courseLabel}</p>
                                <p class="pride-section__award">{$rankDisplay}</p>
                            </div>
                            HTML;
                    }
                } else {
                    echo "<p class='pride-section__no-images-message'>No ranker images available at the moment.</p>";
                }
                ?>



            </div>
        </div>
        <div class="pride-section__nav">
            <button class="pride-section__nav-prev">&#10094;</button>
            <button class="pride-section__nav-next">&#10095;</button>
        </div>
    </div>
</section>