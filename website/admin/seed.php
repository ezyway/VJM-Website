<?php
/**
 * Data Seeder and Migration Tool
 * Shri V.J. Modha College Portal
 * Automatically populates the SQLite database with all existing website data.
 */

require_once __DIR__ . '/includes/db.php';

function runSeeder(): array {
    $db = getDB();
    $logs = [];

    // 1. Seed Admin User
    $adminCount = $db->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    if ($adminCount == 0) {
        $defaultPassword = 'Admin@vjm2025!';
        $hash = password_hash($defaultPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare('INSERT INTO admins (username, password_hash, name, email) VALUES (:u, :p, :n, :e)');
        $stmt->execute([
            ':u' => 'admin',
            ':p' => $hash,
            ':n' => 'System Administrator',
            ':e' => 'admin@shrivjmodhacollege.com'
        ]);
        $logs[] = "Created default admin user: <strong>admin</strong> (Password: <code>{$defaultPassword}</code>)";
    } else {
        $logs[] = "Admin account already exists ({$adminCount} found).";
    }

    // 2. Seed Faculty Members
    $facultyCount = $db->query('SELECT COUNT(*) FROM faculties')->fetchColumn();
    if ($facultyCount == 0) {
        $facultiesFile = dirname(__DIR__) . '/faculties.php';
        if (file_exists($facultiesFile)) {
            // Include $deptDefs and $facultyList safely
            ob_start();
            include $facultiesFile;
            ob_end_clean();

            if (isset($facultyList) && is_array($facultyList)) {
                $stmt = $db->prepare('INSERT INTO faculties (name, designation, depts, badge, dept_label, image, featured, sort_order) VALUES (:name, :desig, :depts, :badge, :label, :img, :feat, :sort)');
                $i = 1;
                foreach ($facultyList as $f) {
                    $depts = is_array($f['depts'] ?? '') ? implode(',', $f['depts']) : ($f['depts'] ?? '');
                    $stmt->execute([
                        ':name'  => $f['name'] ?? '',
                        ':desig' => $f['designation'] ?? '',
                        ':depts' => $depts,
                        ':badge' => $f['badge'] ?? '',
                        ':label' => $f['dept_label'] ?? '',
                        ':img'   => $f['image'] ?? '',
                        ':feat'  => !empty($f['featured']) ? 1 : 0,
                        ':sort'  => $i++
                    ]);
                }
                $logs[] = "Seeded " . count($facultyList) . " faculty members successfully.";
            }
        }
    } else {
        $logs[] = "Faculties already seeded ({$facultyCount} found).";
    }

    // 3. Seed Courses
    $courseCount = $db->query('SELECT COUNT(*) FROM courses')->fetchColumn();
    if ($courseCount == 0) {
        $coursesFile = dirname(__DIR__) . '/courses_data.php';
        if (file_exists($coursesFile)) {
            include_once $coursesFile;
            if (isset($courses) && is_array($courses)) {
                $courseNames = [
                    'bca' => 'Bachelor in Computer Applications (BCA)',
                    'bsc' => 'Bachelor in Science (B.Sc.)',
                    'bba' => 'Bachelor in Business Administration (BBA)',
                    'bcom' => 'Bachelor in Commerce (B.Com.)',
                    'bsw' => 'Bachelor in Social Work (BSW)',
                    'pgdca' => 'Post Graduate Diploma in Computer Applications (PGDCA)',
                    'msc_it' => 'Master in Science (Information Technology) (M.Sc. IT & CA)',
                    'mcom' => 'Master in Commerce (M.Com.)',
                    'msc_chem' => 'Master in Science (Chemistry) (M.Sc. Chem.)'
                ];

                $stmt = $db->prepare('INSERT INTO courses (slug, name, quick_info, job_roles, eligibility, medium, duration, seats_or_intake, next_step, timings, syllabus_url, sort_order) VALUES (:slug, :name, :info, :roles, :elig, :med, :dur, :seats, :next, :time, :syl, :sort)');
                $i = 1;
                foreach ($courses as $slug => $c) {
                    $faq = $c['faq'] ?? [];
                    $stmt->execute([
                        ':slug'      => $slug,
                        ':name'      => $courseNames[$slug] ?? strtoupper($slug),
                        ':info'      => implode("\n\n", $c['quick_info'] ?? []),
                        ':roles'     => json_encode($c['job_roles'] ?? []),
                        ':elig'      => $faq[0] ?? '',
                        ':med'       => $faq[1] ?? '',
                        ':dur'       => $faq[2] ?? '',
                        ':seats'     => $faq[3] ?? '',
                        ':next'      => $faq[4] ?? '',
                        ':time'      => $faq[5] ?? '',
                        ':syl'       => $faq[6] ?? '',
                        ':sort'      => $i++
                    ]);
                }
                $logs[] = "Seeded " . count($courses) . " academic courses successfully.";
            }
        }
    } else {
        $logs[] = "Courses already seeded ({$courseCount} found).";
    }

    // 4. Seed Labs
    $labCount = $db->query('SELECT COUNT(*) FROM labs')->fetchColumn();
    if ($labCount == 0) {
        $labsFile = dirname(__DIR__) . '/labs_data.php';
        if (file_exists($labsFile)) {
            include_once $labsFile;
            if (isset($labs) && is_array($labs)) {
                $stmt = $db->prepare('INSERT INTO labs (slug, name, code, tagline, badge, description, features, specs, sort_order) VALUES (:slug, :name, :code, :tag, :badge, :desc, :feat, :specs, :sort)');
                $i = 1;
                foreach ($labs as $slug => $l) {
                    $stmt->execute([
                        ':slug'  => $slug,
                        ':name'  => $l['name'] ?? '',
                        ':code'  => $l['code'] ?? '',
                        ':tag'   => $l['tagline'] ?? '',
                        ':badge' => $l['badge'] ?? '',
                        ':desc'  => $l['description'] ?? '',
                        ':feat'  => json_encode($l['features'] ?? []),
                        ':specs' => json_encode($l['specs'] ?? []),
                        ':sort'  => $i++
                    ]);
                }
                $logs[] = "Seeded " . count($labs) . " laboratories successfully.";
            }
        }
    } else {
        $logs[] = "Labs already seeded ({$labCount} found).";
    }

    // 5. Seed Events & News
    $eventCount = $db->query('SELECT COUNT(*) FROM events')->fetchColumn();
    if ($eventCount == 0) {
        $defaultEvents = [
            ['title' => 'Independence Day Celebration', 'badge' => '15 Aug 2025', 'badge_type' => 'confirmed', 'description' => 'Patriotic celebration featuring ceremonial flag hoisting, cultural performances, and tributes to freedom heroes.', 'event_type' => 'event', 'sort' => 1],
            ['title' => 'Welcome Party (Freshers\' Day)', 'badge' => 'TBA', 'badge_type' => 'tba', 'description' => 'A vibrant campus gathering to welcome first-year students with music, interactive games, and cultural performances.', 'event_type' => 'event', 'sort' => 2],
            ['title' => 'Teacher’s Day Celebration', 'badge' => 'TBA', 'badge_type' => 'tba', 'description' => 'Student-led initiatives to honor and appreciate the invaluable mentorship and wisdom of faculty members.', 'event_type' => 'event', 'sort' => 3],
            ['title' => 'Welcome Navratri & Raas-Garba', 'badge' => 'TBA', 'badge_type' => 'tba', 'description' => 'Traditional folk dance, devotional music, and cultural unity during the auspicious Navratri festival.', 'event_type' => 'event', 'sort' => 4],
            ['title' => 'Aavishkar Technical Innovation Fest', 'badge' => 'TBA', 'badge_type' => 'tba', 'description' => 'Annual inter-college IT fest featuring coding contests, web design, quizzes, and project expos.', 'event_type' => 'event', 'sort' => 5],
            ['title' => 'B.C.A. Sem-6 (Winter 2025) Results Declared', 'badge' => 'Latest', 'badge_type' => 'latest', 'description' => 'BKNMU university academic performance results have been published on the official notice portal.', 'event_type' => 'news', 'sort' => 6],
            ['title' => 'M.Sc.(IT) Sem-4 Final Dissertation Submissions', 'badge' => 'Notice', 'badge_type' => 'confirmed', 'description' => 'Final semester postgraduate project documentation and practical reviews commence next week.', 'event_type' => 'news', 'sort' => 7],
            ['title' => 'B.Sc. Chemistry Practical Examination Schedule', 'badge' => 'Update', 'badge_type' => 'confirmed', 'description' => 'Laboratory sessions and viva voce examinations schedule released for all scientific batches.', 'event_type' => 'news', 'sort' => 8],
            ['title' => 'B.Com & B.B.A Sem-2 Internal Assessments', 'badge' => 'Schedule', 'badge_type' => 'confirmed', 'description' => 'Internal mid-semester evaluation tests scheduled across Commerce and Management wings.', 'event_type' => 'news', 'sort' => 9]
        ];

        $stmt = $db->prepare('INSERT INTO events (title, badge, badge_type, description, event_type, sort_order) VALUES (:title, :badge, :btype, :desc, :etype, :sort)');
        foreach ($defaultEvents as $ev) {
            $stmt->execute([
                ':title' => $ev['title'],
                ':badge' => $ev['badge'],
                ':btype' => $ev['badge_type'],
                ':desc'  => $ev['description'],
                ':etype' => $ev['event_type'],
                ':sort'  => $ev['sort']
            ]);
        }
        $logs[] = "Seeded " . count($defaultEvents) . " events and announcements.";
    } else {
        $logs[] = "Events already seeded ({$eventCount} found).";
    }

    // 6. Seed Academic Pass Rates
    $ratesCount = $db->query('SELECT COUNT(*) FROM pass_rates')->fetchColumn();
    if ($ratesCount == 0) {
        $rates = [
            ['year' => '2026', 'bca' => '90.80%', 'bsc' => '100.00%', 'bba' => '91.94%', 'bcom' => '95.59%', 'bsw' => '95.00%', 'pgdca' => '—', 'msc_it' => '—', 'mcom' => '—', 'msc_chem' => '—', 'is_latest' => 1, 'sort' => 1],
            ['year' => '2025', 'bca' => '97.60%', 'bsc' => '98.23%', 'bba' => '98.86%', 'bcom' => '98.00%', 'bsw' => '100.00%', 'pgdca' => '—', 'msc_it' => '100.00%', 'mcom' => '100.00%', 'msc_chem' => '97.53%', 'is_latest' => 0, 'sort' => 2],
            ['year' => '2024', 'bca' => '93.30%', 'bsc' => '97.22%', 'bba' => '92.30%', 'bcom' => '96.20%', 'bsw' => '100.00%', 'pgdca' => '—', 'msc_it' => '100.00%', 'mcom' => '97.50%', 'msc_chem' => '92.30%', 'is_latest' => 0, 'sort' => 3],
            ['year' => '2023', 'bca' => '98.11%', 'bsc' => '84.00%', 'bba' => '96.87%', 'bcom' => '90.00%', 'bsw' => '100.00%', 'pgdca' => '—', 'msc_it' => '100.00%', 'mcom' => '100.00%', 'msc_chem' => '84.00%', 'is_latest' => 0, 'sort' => 4],
            ['year' => '2022', 'bca' => '96.00%', 'bsc' => '98.38%', 'bba' => '97.29%', 'bcom' => '88.33%', 'bsw' => '100.00%', 'pgdca' => '—', 'msc_it' => '100.00%', 'mcom' => '100.00%', 'msc_chem' => '98.38%', 'is_latest' => 0, 'sort' => 5],
            ['year' => '2021', 'bca' => '98.40%', 'bsc' => '97.10%', 'bba' => '97.67%', 'bcom' => '97.77%', 'bsw' => '100.00%', 'pgdca' => '—', 'msc_it' => '100.00%', 'mcom' => '100.00%', 'msc_chem' => '97.10%', 'is_latest' => 0, 'sort' => 6]
        ];

        $stmt = $db->prepare('INSERT INTO pass_rates (year, bca, bsc, bba, bcom, bsw, pgdca, msc_it, mcom, msc_chem, is_latest, sort_order) VALUES (:yr, :bca, :bsc, :bba, :bcom, :bsw, :pg, :it, :mc, :ch, :lat, :sort)');
        foreach ($rates as $r) {
            $stmt->execute([
                ':yr'   => $r['year'],
                ':bca'  => $r['bca'],
                ':bsc'  => $r['bsc'],
                ':bba'  => $r['bba'],
                ':bcom' => $r['bcom'],
                ':bsw'  => $r['bsw'],
                ':pg'   => $r['pgdca'],
                ':it'   => $r['msc_it'],
                ':mc'   => $r['mcom'],
                ':ch'   => $r['msc_chem'],
                ':lat'  => $r['is_latest'],
                ':sort' => $r['sort']
            ]);
        }
        $logs[] = "Seeded " . count($rates) . " academic pass rate records.";
    } else {
        $logs[] = "Pass rates already seeded ({$ratesCount} found).";
    }

    // 7. Seed Rankers (Pride of the College)
    $rankersCount = $db->query('SELECT COUNT(*) FROM rankers')->fetchColumn();
    if ($rankersCount == 0) {
        $rankerDir = dirname(__DIR__) . '/assets/photos/index/pride_of_college/';
        $images = glob($rankerDir . '*.{jpg,jpeg,png,webp}', GLOB_BRACE);
        if ($images) {
            sort($images);
            $stmt = $db->prepare('INSERT INTO rankers (name, course, semester, language, rank_text, image, sort_order) VALUES (:name, :course, :sem, :lang, :rank, :img, :sort)');
            $i = 1;
            foreach ($images as $path) {
                $filename = basename($path);
                $filenameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
                $parts = explode('__', $filenameWithoutExt);
                $courseRaw = $parts[0] ?? '';
                $nameRaw   = $parts[1] ?? '';
                $rankRaw   = $parts[2] ?? '';

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

                $fullName = str_replace('_', ' ', $nameRaw);
                $rankDisplay = '';
                if (preg_match('/(.*?)-(\d+)(?:_(\w+))?$/', $rankRaw, $matches)) {
                    $scope = $matches[1] ?? '';
                    $number = $matches[2] ?? '';
                    $suffix = isset($matches[3]) ? "^{$matches[3]}" : '';
                    $rankDisplay = "{$scope} Rank {$number}{$suffix}";
                } else {
                    $rankDisplay = str_replace('_', ' ', $rankRaw);
                }

                $relPath = 'assets/photos/index/pride_of_college/' . $filename;
                $stmt->execute([
                    ':name'   => $fullName,
                    ':course' => $courseName,
                    ':sem'    => $semester,
                    ':lang'   => $language,
                    ':rank'   => $rankDisplay,
                    ':img'    => $relPath,
                    ':sort'   => $i++
                ]);
            }
            $logs[] = "Seeded " . count($images) . " student rankers.";
        }
    } else {
        $logs[] = "Rankers already seeded ({$rankersCount} found).";
    }

    // 8. Seed Gallery Albums & Photos
    $albumCount = $db->query('SELECT COUNT(*) FROM gallery_albums')->fetchColumn();
    if ($albumCount == 0) {
        $albumMeta = [
            "aavishkar_event" => [
                "title" => "Aavishkar Tech & Science Fest",
                "category" => "events",
                "category_label" => "Tech & Academic",
                "description" => "Annual technical exhibition and science project competitions showcasing student innovations."
            ],
            "campus" => [
                "title" => "Campus Infrastructure & Grounds",
                "category" => "campus",
                "category_label" => "Campus Life",
                "description" => "Lush green campus environment, seminar halls, sports arena, and modern academic blocks."
            ],
            "freshers_party" => [
                "title" => "Freshers Welcome Celebration",
                "category" => "events",
                "category_label" => "Cultural & Social",
                "description" => "Welcoming the incoming batch of bright minds with music, performances, and student bonding."
            ],
            "ganesh_mahotsav" => [
                "title" => "Ganesh Mahotsav Celebrations",
                "category" => "cultural",
                "category_label" => "Tradition & Festivity",
                "description" => "Traditional cultural celebrations and devotional festivities uniting students and staff."
            ],
            "labs" => [
                "title" => "High-Tech Computer & Science Labs",
                "category" => "campus",
                "category_label" => "Facilities",
                "description" => "State-of-the-art computer labs, chemistry setups, and hands-on scientific research equipment."
            ],
            "talent_show" => [
                "title" => "Annual Talent & Cultural Showcase",
                "category" => "cultural",
                "category_label" => "Arts & Performances",
                "description" => "Celebrating extraordinary artistic talents in dance, drama, music, and public speaking."
            ]
        ];

        $galleryDir = dirname(__DIR__) . '/assets/photos/gallery';
        $stmtAlbum = $db->prepare('INSERT INTO gallery_albums (slug, title, category, category_label, description, cover_image, sort_order) VALUES (:slug, :title, :cat, :clabel, :desc, :cov, :sort)');
        $stmtPhoto = $db->prepare('INSERT INTO gallery_photos (album_id, image_path, caption, sort_order) VALUES (:aid, :path, :cap, :sort)');

        $sort = 1;
        foreach ($albumMeta as $slug => $meta) {
            $folderPath = $galleryDir . '/' . $slug;
            $cover = '';
            $photos = [];

            if (is_dir($folderPath)) {
                $files = array_diff(scandir($folderPath), ['.', '..']);
                foreach ($files as $file) {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $photos[] = "assets/photos/gallery/{$slug}/{$file}";
                    }
                }
            }

            if (!empty($photos)) {
                $cover = $photos[0];
            }

            $stmtAlbum->execute([
                ':slug'   => $slug,
                ':title'  => $meta['title'],
                ':cat'    => $meta['category'],
                ':clabel' => $meta['category_label'],
                ':desc'   => $meta['description'],
                ':cov'    => $cover,
                ':sort'   => $sort++
            ]);

            $albumId = $db->lastInsertId();
            $pSort = 1;
            foreach ($photos as $p) {
                $stmtPhoto->execute([
                    ':aid'  => $albumId,
                    ':path' => $p,
                    ':cap'  => '',
                    ':sort' => $pSort++
                ]);
            }
        }
        $logs[] = "Seeded " . count($albumMeta) . " gallery albums and associated photos.";
    } else {
        $logs[] = "Gallery albums already seeded ({$albumCount} found).";
    }

    // 9. Seed Testimonials
    $testimonialCount = $db->query('SELECT COUNT(*) FROM testimonials')->fetchColumn();
    if ($testimonialCount == 0) {
        $testimonials = [
            ['name' => 'Odedra Ranjitji', 'course' => 'B.S.W. Graduate', 'avatar' => 'OR', 'stars' => 5, 'text' => 'The faculty are so supportive and truly committed to our success. The guidance I received here shaped my career path completely.', 'sort' => 1],
            ['name' => 'Sida Jay', 'course' => 'B.B.A. Graduate', 'avatar' => 'SJ', 'stars' => 5, 'text' => 'This college has supported me in my growth both academically and as a person with excellent corporate exposure.', 'sort' => 2],
            ['name' => 'Dave Hiren', 'course' => 'B.C.A. Graduate', 'avatar' => 'DH', 'stars' => 5, 'text' => 'State of the art computer labs, seasoned professors, and incredible placement training helped me land my dream software job.', 'sort' => 3],
            ['name' => 'Jethwa Priya', 'course' => 'B.Sc. Chemistry Graduate', 'avatar' => 'JP', 'stars' => 5, 'text' => 'Practical laboratory experiments and insightful seminars made chemistry truly thrilling. Highly proud of my alma mater.', 'sort' => 4]
        ];

        $stmt = $db->prepare('INSERT INTO testimonials (name, course, avatar_text, stars, text, sort_order) VALUES (:name, :course, :av, :stars, :text, :sort)');
        foreach ($testimonials as $t) {
            $stmt->execute([
                ':name'   => $t['name'],
                ':course' => $t['course'],
                ':av'     => $t['avatar'],
                ':stars'  => $t['stars'],
                ':text'   => $t['text'],
                ':sort'   => $t['sort']
            ]);
        }
        $logs[] = "Seeded " . count($testimonials) . " student testimonials.";
    } else {
        $logs[] = "Testimonials already seeded ({$testimonialCount} found).";
    }

    // 10. Seed E-Magazines
    $magCount = $db->query('SELECT COUNT(*) FROM magazines')->fetchColumn();
    if ($magCount == 0) {
        $mags = [
            ['year' => '2021', 'title' => 'Shri V.J. Modha College Annual E-Magazine 2021', 'edition' => 'Edition 2021', 'theme' => 'Resilience, Innovation & Digital Transformation', 'file' => 'assets/e_mags/mag_2021.pdf', 'size' => '~31.6 MB', 'pages' => 'Full Edition', 'badge' => 'Latest Edition', 'sort' => 1],
            ['year' => '2020', 'title' => 'Shri V.J. Modha College Annual E-Magazine 2020', 'edition' => 'Edition 2020', 'theme' => 'Academic Excellence, Creativity & Cultural Heritage', 'file' => 'assets/e_mags/mag_2020.pdf', 'size' => '~21.6 MB', 'pages' => 'Full Edition', 'badge' => 'Archive', 'sort' => 2]
        ];

        $stmt = $db->prepare('INSERT INTO magazines (year, title, edition, theme, file_path, file_size, pages, badge, sort_order) VALUES (:yr, :title, :ed, :theme, :file, :sz, :pg, :badge, :sort)');
        foreach ($mags as $m) {
            $stmt->execute([
                ':yr'    => $m['year'],
                ':title' => $m['title'],
                ':ed'    => $m['edition'],
                ':theme' => $m['theme'],
                ':file'  => $m['file'],
                ':sz'    => $m['size'],
                ':pg'    => $m['pages'],
                ':badge' => $m['badge'],
                ':sort'  => $m['sort']
            ]);
        }
        $logs[] = "Seeded " . count($mags) . " e-magazines.";
    } else {
        $logs[] = "Magazines already seeded ({$magCount} found).";
    }

    // 11. Seed Scholarships
    $scholarshipCount = $db->query('SELECT COUNT(*) FROM scholarships')->fetchColumn();
    if ($scholarshipCount == 0) {
        $scholarshipRecords = [
            ["year" => "2024 - 2025", "amount" => "₹ 21,22,000", "numeric" => 2122000, "status" => "Latest"],
            ["year" => "2023 - 2024", "amount" => "₹ 19,38,000", "numeric" => 1938000, "status" => "Completed"],
            ["year" => "2022 - 2023", "amount" => "₹ 17,20,000", "numeric" => 1720000, "status" => "Completed"],
            ["year" => "2021 - 2022", "amount" => "₹ 16,50,000", "numeric" => 1650000, "status" => "Completed"],
            ["year" => "2020 - 2021", "amount" => "₹ 14,82,000", "numeric" => 1482000, "status" => "Completed"],
            ["year" => "2019 - 2020", "amount" => "₹ 16,98,500", "numeric" => 1698500, "status" => "Completed"],
            ["year" => "2018 - 2019", "amount" => "₹ 17,35,800", "numeric" => 1735800, "status" => "Completed"],
            ["year" => "2017 - 2018", "amount" => "₹ 22,13,100", "numeric" => 2213100, "status" => "Peak"],
            ["year" => "2016 - 2017", "amount" => "₹ 20,38,090", "numeric" => 2038090, "status" => "Completed"],
            ["year" => "2015 - 2016", "amount" => "₹ 15,53,850", "numeric" => 1553850, "status" => "Completed"],
            ["year" => "2014 - 2015", "amount" => "₹ 11,13,140", "numeric" => 1113140, "status" => "Completed"],
            ["year" => "2013 - 2014", "amount" => "₹ 7,00,774", "numeric" => 700774, "status" => "Completed"],
            ["year" => "2012 - 2013", "amount" => "₹ 5,42,500", "numeric" => 542500, "status" => "Completed"]
        ];

        $stmt = $db->prepare('INSERT INTO scholarships (year, amount_str, amount_numeric, status, sort_order) VALUES (:yr, :amt, :num, :st, :sort)');
        $i = 1;
        foreach ($scholarshipRecords as $s) {
            $stmt->execute([
                ':yr'   => $s['year'],
                ':amt'  => $s['amount'],
                ':num'  => $s['numeric'],
                ':st'   => $s['status'],
                ':sort' => $i++
            ]);
        }

        $portals = [
            [
                "name" => "Digital Gujarat Portal",
                "provider" => "Government of Gujarat",
                "badge" => "State Government",
                "desc" => "Post-matric and merit scholarships for SC, ST, SEBC, and EWS students pursuing higher education.",
                "link" => "https://www.digitalgujarat.gov.in/",
                "icon" => "shield"
            ],
            [
                "name" => "MYSY (Mukhyamantri Yuva Swavalamban Yojana)",
                "provider" => "Education Department, Gujarat",
                "badge" => "Merit & Financial Aid",
                "desc" => "Fee waiver and financial assistance for bright and needy students across degree programs.",
                "link" => "https://mysy.guj.nic.in/",
                "icon" => "award"
            ]
        ];

        $stmtP = $db->prepare('INSERT INTO scholarship_portals (name, provider, badge, description, link, icon, sort_order) VALUES (:name, :prov, :badge, :desc, :link, :icon, :sort)');
        $j = 1;
        foreach ($portals as $p) {
            $stmtP->execute([
                ':name'  => $p['name'],
                ':prov'  => $p['provider'],
                ':badge' => $p['badge'],
                ':desc'  => $p['desc'],
                ':link'  => $p['link'],
                ':icon'  => $p['icon'],
                ':sort'  => $j++
            ]);
        }
        $logs[] = "Seeded " . count($scholarshipRecords) . " scholarship records and " . count($portals) . " portals.";
    } else {
        $logs[] = "Scholarships already seeded ({$scholarshipCount} found).";
    }

    // 12. Site Settings & Counter Stats
    $settings = [
        'counter_courses'       => '8',
        'counter_pass_rate'     => '97.6',
        'counter_students'      => '1500',
        'counter_alumni'        => '7900',
        'college_name'          => 'Shri V.J. Modha College',
        'college_slogan'        => '॥ विद्यार्थी लभते विद्यां ॥',
        'contact_phone_primary' => '+91 286 2221234',
        'contact_email_primary' => 'info@shrivjmodhacollege.com',
        'contact_address'       => 'Vidhyadham, Chhaya-Birla Road, Nr. Pakshi Abhiyaran, Porbandar, Gujarat, 360575',
        'announcement_banner'   => 'Admissions Open for Academic Year 2025-26 | Apply Online or Visit Campus'
    ];

    foreach ($settings as $k => $v) {
        setSetting($k, $v);
    }
    $logs[] = "Seeded site settings and statistics counters.";

    return $logs;
}

// If run from CLI or directly accessed
if (php_sapi_name() === 'cli' || (isset($_GET['run']) && $_GET['run'] === '1')) {
    $results = runSeeder();
    if (php_sapi_name() === 'cli') {
        echo "=== Seeder Finished ===\n" . implode("\n", array_map('strip_tags', $results)) . "\n";
    }
}
