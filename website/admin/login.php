<?php
/**
 * Admin Login Portal
 */

require_once __DIR__ . '/includes/auth.php';

// Auto-run schema/seeder if DB hasn't been created yet
getDB();

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$username = '';

// Check login attempt throttling
$clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$ipHash = substr(md5($clientIp), 0, 12);
$rateKey = "login_throttle_{$ipHash}";
$throttleData = json_decode(getSetting($rateKey, '{}'), true);
if (!is_array($throttleData)) $throttleData = [];

$attempts = (int)($throttleData['attempts'] ?? 0);
$lockedUntil = (int)($throttleData['locked_until'] ?? 0);
$now = time();
$isLocked = ($lockedUntil > $now);

if ($isLocked) {
    $waitMins = (int)ceil(($lockedUntil - $now) / 60);
    $error = "Too many failed login attempts. Access temporarily locked. Please retry in {$waitMins} minute(s).";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if ($isLocked) {
        // blocked - ignore request
    } elseif (!verifyCSRFToken($token)) {
        $error = 'Session expired or invalid security token. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
        } else {
            $res = attemptLogin($username, $password);
            if ($res['success']) {
                setSetting($rateKey, ''); // Clear failed attempts on success
                logAdminActivity('login', 'admin', (int)$_SESSION['admin_id'], "Admin signed in as {$username}");
                $return = $_GET['return'] ?? 'index.php';
                if (!preg_match('/^[a-z_][a-z0-9_]*\.php$/i', $return)) {
                    $return = 'index.php';
                }
                header('Location: ' . $return);
                exit;
            } else {
                $attempts++;
                $newLockedUntil = ($attempts >= 5) ? ($now + 900) : 0; // 15 mins lockout after 5 attempts
                setSetting($rateKey, json_encode(['attempts' => $attempts, 'locked_until' => $newLockedUntil]));

                if ($attempts >= 5) {
                    $error = 'Too many failed login attempts. Account locked for 15 minutes.';
                } else {
                    $remaining = 5 - $attempts;
                    $error = $res['error'] . " ({$remaining} attempt(s) remaining).";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Shri V.J. Modha College</title>
    <link rel="icon" type="image/x-icon" href="../assets/logo.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css?v=<?= @filemtime(__DIR__ . '/assets/css/admin.css') ?: 1 ?>">
</head>
<body class="login-body">

<div class="login-card">
    <div class="login-header">
        <img src="../assets/logo.ico" alt="Logo" class="login-logo" loading="lazy">
        <h1 class="login-title">VJM College Admin</h1>
        <p class="login-subtitle">Content Management &amp; Administration</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="login-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php<?= isset($_GET['return']) ? '?return=' . urlencode($_GET['return']) : '' ?>">
        <?= csrfField() ?>

        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" required autofocus autocomplete="username" value="<?= htmlspecialchars($username) ?>" placeholder="e.g. admin">
        </div>

        <div class="form-group" style="margin-bottom: 24px; position: relative;">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password" placeholder="Enter password" style="padding-right: 64px;">
            <button type="button" id="togglePassword" class="btn btn-secondary btn-sm"
                    style="position: absolute; right: 8px; bottom: 8px; padding: 5px 10px; font-size: 11.5px; z-index: 2;"
                    title="Show / Hide password">Show</button>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 14px;">
            Sign In to Dashboard
        </button>
    </form>

    <div style="text-align: center; margin-top: 24px;">
        <a href="../index.php" style="color: var(--text-muted); font-size: 12.5px; text-decoration: none;">
            ← Back to Public Website
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('togglePassword');
    const pwd = document.getElementById('password');
    if (toggle && pwd) {
        toggle.addEventListener('click', () => {
            const show = pwd.type === 'password';
            pwd.type = show ? 'text' : 'password';
            toggle.textContent = show ? 'Hide' : 'Show';
        });
    }
});
</script>

</body>
</html>