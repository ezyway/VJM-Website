<?php
    require_once __DIR__ . '/admin/includes/db.php';
    $labs = [];
    try {
        $db = getDB();
        $dbLabs = $db->query('SELECT * FROM labs ORDER BY sort_order ASC, id ASC')->fetchAll();
        if (!empty($dbLabs)) {
            foreach ($dbLabs as $l) {
                $labs[$l['slug']] = [
                    "name"        => $l['name'],
                    "code"        => $l['code'],
                    "tagline"     => $l['tagline'],
                    "badge"       => $l['badge'],
                    "description" => $l['description'],
                    "features"    => json_decode($l['features'] ?? '[]', true) ?: [],
                    "specs"       => json_decode($l['specs'] ?? '{}', true) ?: []
                ];
            }
        }
    } catch (Exception $e) {
        $labs = [];
    }

    if (empty($labs)) {
        $labs = [
        "computer" => [
            "name" => "Computer & Advanced IT Lab",
            "code" => "Computer Lab",
            "tagline" => "High-Speed Computing, Modern IDEs & Software Innovation",
            "badge" => "IT & Computer Applications",
            "description" => "Our air-conditioned Computer Laboratory is equipped with modern high-performance desktop systems, gigabit networking, continuous high-speed Wi-Fi, and licensed development environments for BCA and M.Sc. IT students. Students gain intensive hands-on experience in full-stack web development, database management, cybersecurity, and data structures.",
            "features" => [
                "High-Speed Gigabit LAN & Enterprise Wi-Fi",
                "Latest Development IDEs, Python, Java & Oracle Environments",
                "High-Resolution LED Monitors & Ergonomic Seating",
                "Centralized Server Architecture with Full UPS Backup"
            ],
            "specs" => [
                "Capacity" => "120+ Workstations",
                "Networking" => "High-Speed Fiber Optic",
                "Operating Systems" => "Windows 11 & Linux Ubuntu",
                "Dedicated Supervisor" => "Full-time Lab Technicians & Faculty"
            ]
        ],
        "chemistry" => [
            "name" => "Modern Chemistry Laboratory",
            "code" => "Chemistry Lab",
            "tagline" => "Hands-On Organic Synthesis & Analytical Chemistry",
            "badge" => "Chemical & Physical Sciences",
            "description" => "The Chemistry Laboratory offers an advanced learning environment for B.Sc. and M.Sc. Chemistry students. Featuring individual chemical workstations, digital analytical balances, spectrophotometers, safety fume hoods, and eyewash stations, students conduct precise organic preparations, quantitative assays, and reaction mechanism investigations.",
            "features" => [
                "Advanced Digital Balances & Heating Mantles",
                "Fume Hoods, Safety Showers & Fire Suppression Systems",
                "Glassware & Reagent Supply for Organic Synthesis",
                "Spectrophotometry & Chemical Titration Stations"
            ],
            "specs" => [
                "Capacity" => "60+ Student Stations",
                "Safety Standards" => "OSHA & University Compliant",
                "Specialization" => "Organic & Analytical Chemistry",
                "Supervision" => "Certified Lab Demonstrators"
            ]
        ],
        "physics" => [
            "name" => "Physics & Experimental Lab",
            "code" => "Physics Lab",
            "tagline" => "Mechanics, Optics, Laser & Circuit Investigations",
            "badge" => "Applied Physics",
            "description" => "The Physics Laboratory enables students to observe physical laws in action. Equipped with optical spectrometers, laser sources, CRO oscilloscopes, semiconductor kits, and mechanics apparatus, students gain a deep empirical understanding of optics, electromagnetism, and modern physics.",
            "features" => [
                "Optical Benches, Spectrometers & He-Ne Laser Kits",
                "Cathode Ray Oscilloscopes (CRO) & Signal Generators",
                "Semiconductor & Electronic Logic Circuit Boards",
                "Precision Vernier, Micrometer & Spherometer Sets"
            ],
            "specs" => [
                "Capacity" => "50+ Student Stations",
                "Apparatus" => "Standardized University Experimental Sets",
                "Specialization" => "Optics, Circuits & Mechanics",
                "Supervision" => "Senior Physics Faculty & Lab Assistants"
            ]
        ]
    ];
    }
?>