<?php
/**
 * Authentication & Security Manager
 * Shri V.J. Modha College Admin Portal
 */

require_once __DIR__ . '/db.php';

function startAdminSession(): void {
    if (!headers_sent()) {
        header('X-Frame-Options: SAMEORIGIN');
        header("Content-Security-Policy: frame-ancestors 'self'");
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('X-XSS-Protection: 1; mode=block');
    }

    if (session_status() === PHP_SESSION_NONE) {
        $lifetime = 3600 * 8; // 8 hours
        ini_set('session.cookie_lifetime', (string)$lifetime);
        ini_set('session.cookie_secure', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_strict_mode', '1');
        session_start();
    }
}

function isLoggedIn(): bool {
    startAdminSession();
    return !empty($_SESSION['admin_logged_in']) && !empty($_SESSION['admin_id']);
}

function getCurrentAdmin(): ?array {
    if (!isLoggedIn()) return null;
    return [
        'id' => $_SESSION['admin_id'],
        'username' => $_SESSION['admin_username'] ?? 'admin',
        'name' => $_SESSION['admin_name'] ?? 'Administrator',
        'email' => $_SESSION['admin_email'] ?? ''
    ];
}

function requireAuth(): void {
    if (!isLoggedIn()) {
        $returnUrl = urlencode($_SERVER['REQUEST_URI'] ?? 'index.php');
        header("Location: login.php?return={$returnUrl}");
        exit;
    }
}

function attemptLogin(string $username, string $password): array {
    startAdminSession();
    $db = getDB();

    $stmt = $db->prepare('SELECT * FROM admins WHERE username = :u LIMIT 1');
    $stmt->execute([':u' => trim($username)]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_name'] = $user['name'];
        $_SESSION['admin_email'] = $user['email'];
        return ['success' => true];
    }

    return ['success' => false, 'error' => 'Invalid username or password.'];
}

function logoutAdmin(): void {
    startAdminSession();
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * CSRF Protection
 */
function getCSRFToken(): string {
    startAdminSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken(?string $token): bool {
    startAdminSession();
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrfField(): string {
    $token = htmlspecialchars(getCSRFToken(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Flash Messages
 */
function setFlash(string $type, string $message): void {
    startAdminSession();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    startAdminSession();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Safe File Uploader
 */
function handleAdminUpload(array $file, string $subDirectory, array $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'pdf']): array {
    if (empty($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'error' => 'No file was uploaded.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'File upload error code: ' . $file['error']];
    }

    // Size limit: 50MB
    if ($file['size'] > 50 * 1024 * 1024) {
        return ['success' => false, 'error' => 'File size exceeds maximum allowed limit (50MB).'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) {
        return ['success' => false, 'error' => 'Invalid file format. Allowed: ' . implode(', ', $allowedExts)];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'application/pdf'];
    if (!in_array($mimeType, $allowedMimes, true)) {
        return ['success' => false, 'error' => 'Invalid file type'];
    }

    // Base uploads directory: website/assets/uploads/
    $baseUploadDir = dirname(__DIR__, 2) . '/assets/uploads/' . trim($subDirectory, '/') . '/';
    if (!is_dir($baseUploadDir)) {
        mkdir($baseUploadDir, 0777, true);
    }

    $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
    $filename = $cleanName . '_' . substr(md5(uniqid((string)mt_rand(), true)), 0, 8) . '.' . $ext;
    $targetPath = $baseUploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Automatically optimize and downscale oversized images if GD is available
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            optimizeUploadedImage($targetPath, $ext);
        }

        // Return web-relative path
        $webPath = 'assets/uploads/' . trim($subDirectory, '/') . '/' . $filename;
        return ['success' => true, 'path' => $webPath, 'filename' => $filename];
    }

    return ['success' => false, 'error' => 'Failed to move uploaded file to target folder. Check write permissions.'];
}

/**
 * Proportional image resizer & compressor for uploaded assets.
 * Scales down images larger than $maxDimension (default 1920px) while maintaining
 * aspect ratio and preserving PNG/WebP alpha channels.
 */
function optimizeUploadedImage(string $filePath, string $ext, int $maxDimension = 1920, int $quality = 85): void {
    if (!function_exists('imagecreatetruecolor') || !file_exists($filePath)) {
        return;
    }

    $info = @getimagesize($filePath);
    if (!$info) return;

    [$width, $height, $type] = $info;

    $src = null;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $src = imagecreatefromjpeg($filePath);
            if ($src === false) return;
            break;
        case IMAGETYPE_PNG:
            $src = imagecreatefrompng($filePath);
            if ($src === false) return;
            break;
        case IMAGETYPE_WEBP:
            if (function_exists('imagecreatefromwebp')) {
                $src = imagecreatefromwebp($filePath);
                if ($src === false) return;
            }
            break;
    }
    if (!$src) return;

    // Check if resizing or re-encoding is needed
    $newWidth = $width;
    $newHeight = $height;
    if ($width > $maxDimension || $height > $maxDimension) {
        $ratio = min($maxDimension / $width, $maxDimension / $height);
        $newWidth = (int)round($width * $ratio);
        $newHeight = (int)round($height * $ratio);
    }

    $dst = imagecreatetruecolor($newWidth, $newHeight);

    // Preserve transparency for PNG and WebP
    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);
    }

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($dst, $filePath, $quality);
            break;
        case IMAGETYPE_PNG:
            imagepng($dst, $filePath, 8);
            break;
        case IMAGETYPE_WEBP:
            if (function_exists('imagewebp')) {
                imagewebp($dst, $filePath, $quality);
            }
            break;
    }

    // After optimizing, also create a WebP version
    if (function_exists('imagewebp')) {
        $webPPath = $filePath . '.webp';
        imagewebp($dst, $webPPath, 80);
    }

    imagedestroy($src);
    imagedestroy($dst);
}

/**
 * Record an administrative activity audit log entry.
 */
function logAdminActivity(string $action, string $entityType, int $entityId = 0, string $details = ''): void {
    try {
        $db = getDB();
        $admin = getCurrentAdmin();
        $adminId = $admin ? (int)$admin['id'] : 0;
        $adminUsername = $admin ? $admin['username'] : 'system';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $stmt = $db->prepare('INSERT INTO activity_logs (admin_id, admin_username, action, entity_type, entity_id, details, ip_address) VALUES (:aid, :u, :act, :ent, :eid, :det, :ip)');
        $stmt->execute([
            ':aid' => $adminId,
            ':u'   => $adminUsername,
            ':act' => $action,
            ':ent' => $entityType,
            ':eid' => $entityId,
            ':det' => $details,
            ':ip'  => $ip
        ]);
    } catch (Exception $e) {
        error_log('Activity log failed: ' . $e->getMessage());
    }
}

/**
 * Fetch recent activity audit trail entries.
 */
function getRecentActivityLogs(int $limit = 10): array {
    try {
        $db = getDB();
        $stmt = $db->prepare('SELECT * FROM activity_logs ORDER BY created_at DESC, id DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    } catch (Exception $e) {
        return [];
    }
}
