<?php
/**
 * Database Helper & Connection Manager
 * Shri V.J. Modha College Portal
 */

define('DB_FILE_PATH', dirname(__DIR__, 2) . '/data/database.sqlite');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dbDir = dirname(DB_FILE_PATH);
        if (!is_dir($dbDir)) {
            @mkdir($dbDir, 0777, true);
        }
        if (is_dir($dbDir) && !is_writable($dbDir)) {
            @chmod($dbDir, 0777);
        }
        if (file_exists(DB_FILE_PATH) && !is_writable(DB_FILE_PATH)) {
            @chmod(DB_FILE_PATH, 0666);
        }

        $dsn = 'sqlite:' . DB_FILE_PATH;
        $pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 10,
        ]);

        // Enable SQLite Write-Ahead Logging for high concurrency and foreign keys (gracefully fallback if host disallows WAL)
        try {
            $pdo->exec('PRAGMA journal_mode = WAL;');
        } catch (Throwable $e) {}
        try {
            $pdo->exec('PRAGMA foreign_keys = ON;');
        } catch (Throwable $e) {}

        initSchema($pdo);
    }
    return $pdo;
}

function initSchema(PDO $pdo): void {
    $schema = "
    CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password_hash TEXT NOT NULL,
        name TEXT NOT NULL,
        email TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS faculties (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        designation TEXT NOT NULL,
        depts TEXT NOT NULL,
        badge TEXT,
        dept_label TEXT,
        image TEXT,
        featured INTEGER DEFAULT 0,
        sort_order INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS events (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        badge TEXT,
        badge_type TEXT DEFAULT 'confirmed',
        description TEXT,
        event_type TEXT DEFAULT 'event',
        sort_order INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS rankers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        course TEXT NOT NULL,
        semester TEXT,
        language TEXT,
        rank_text TEXT NOT NULL,
        image TEXT NOT NULL,
        sort_order INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS pass_rates (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        year TEXT NOT NULL,
        bca TEXT DEFAULT '—',
        bsc TEXT DEFAULT '—',
        bba TEXT DEFAULT '—',
        bcom TEXT DEFAULT '—',
        bsw TEXT DEFAULT '—',
        pgdca TEXT DEFAULT '—',
        msc_it TEXT DEFAULT '—',
        mcom TEXT DEFAULT '—',
        msc_chem TEXT DEFAULT '—',
        is_latest INTEGER DEFAULT 0,
        sort_order INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS courses (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        slug TEXT UNIQUE NOT NULL,
        name TEXT NOT NULL,
        quick_info TEXT,
        job_roles TEXT,
        eligibility TEXT,
        medium TEXT,
        duration TEXT,
        seats_or_intake TEXT,
        next_step TEXT,
        timings TEXT,
        syllabus_url TEXT,
        sort_order INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS labs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        slug TEXT UNIQUE NOT NULL,
        name TEXT NOT NULL,
        code TEXT NOT NULL,
        tagline TEXT,
        badge TEXT,
        description TEXT,
        features TEXT,
        specs TEXT,
        sort_order INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS gallery_albums (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        slug TEXT UNIQUE NOT NULL,
        title TEXT NOT NULL,
        category TEXT NOT NULL,
        category_label TEXT,
        description TEXT,
        cover_image TEXT,
        sort_order INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS gallery_photos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        album_id INTEGER NOT NULL,
        image_path TEXT NOT NULL,
        caption TEXT,
        sort_order INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (album_id) REFERENCES gallery_albums(id) ON DELETE CASCADE
    );

    CREATE TABLE IF NOT EXISTS testimonials (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        course TEXT NOT NULL,
        avatar_text TEXT,
        stars INTEGER DEFAULT 5,
        text TEXT NOT NULL,
        sort_order INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS magazines (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        year TEXT NOT NULL,
        title TEXT NOT NULL,
        edition TEXT,
        theme TEXT,
        file_path TEXT NOT NULL,
        file_size TEXT,
        pages TEXT,
        badge TEXT,
        sort_order INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS scholarships (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        year TEXT NOT NULL,
        amount_str TEXT NOT NULL,
        amount_numeric INTEGER DEFAULT 0,
        status TEXT DEFAULT 'Completed',
        sort_order INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS scholarship_portals (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        provider TEXT,
        badge TEXT,
        description TEXT,
        link TEXT,
        icon TEXT DEFAULT 'shield',
        sort_order INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS site_settings (
        key TEXT PRIMARY KEY,
        value TEXT
    );

    CREATE TABLE IF NOT EXISTS activity_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        admin_id INTEGER,
        admin_username TEXT NOT NULL,
        action TEXT NOT NULL,
        entity_type TEXT NOT NULL,
        entity_id INTEGER DEFAULT 0,
        details TEXT,
        ip_address TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE INDEX IF NOT EXISTS idx_activity_logs_created ON activity_logs(created_at DESC);
    CREATE INDEX IF NOT EXISTS idx_faculties_sort ON faculties(sort_order, id);
    CREATE INDEX IF NOT EXISTS idx_courses_sort ON courses(sort_order, id);
    CREATE INDEX IF NOT EXISTS idx_events_sort ON events(event_type, sort_order, id);
    CREATE INDEX IF NOT EXISTS idx_rankers_sort ON rankers(sort_order, id);
    CREATE INDEX IF NOT EXISTS idx_pass_rates_sort ON pass_rates(sort_order, id);
    CREATE INDEX IF NOT EXISTS idx_gallery_albums_sort ON gallery_albums(sort_order, id);
    CREATE INDEX IF NOT EXISTS idx_gallery_photos_album ON gallery_photos(album_id, sort_order, id);
    CREATE INDEX IF NOT EXISTS idx_testimonials_sort ON testimonials(sort_order, id);
    CREATE INDEX IF NOT EXISTS idx_magazines_sort ON magazines(sort_order, id);
    CREATE INDEX IF NOT EXISTS idx_scholarships_sort ON scholarships(sort_order, id);
    CREATE INDEX IF NOT EXISTS idx_scholarship_portals_sort ON scholarship_portals(sort_order, id);
    ";

    $pdo->exec($schema);
}

/**
 * Get setting value with fallback
 */
function getSetting(string $key, string $default = ''): string {
    try {
        $db = getDB();
        $stmt = $db->prepare('SELECT "value" FROM site_settings WHERE "key" = :key LIMIT 1');
        $stmt->execute([':key' => $key]);
        $val = $stmt->fetchColumn();
        return $val !== false ? (string)$val : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Set setting key-value pair
 */
function setSetting(string $key, string $value): bool {
    try {
        $db = getDB();
        $stmt = $db->prepare('INSERT INTO site_settings ("key", "value") VALUES (:key, :val) ON CONFLICT("key") DO UPDATE SET "value" = :val');
        return $stmt->execute([':key' => $key, ':val' => $value]);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Recursively count files + total bytes inside the given directories.
 * Shared by the dashboard and the settings diagnostics.
 */
function getStorageStats(array $dirs): array {
    $count = 0;
    $bytes = 0;
    foreach ($dirs as $d) {
        if (!is_dir($d)) continue;
        try {
            $it = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($d, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($it as $item) {
                if ($item->isFile()) {
                    $count++;
                    $bytes += $item->getSize();
                }
            }
        } catch (Exception $e) {}
    }
    $sizeStr = ($bytes > 1048576)
        ? round($bytes / 1048576, 2) . ' MB'
        : round($bytes / 1024, 1) . ' KB';
    return ['count' => $count, 'bytes' => $bytes, 'size' => $sizeStr];
}
