<?php
/**
 * Site Settings & Administration Configuration
 */

require_once __DIR__ . '/includes/auth.php';
requireAuth();

$db = getDB();
$adminUser = getCurrentAdmin();

// Handle POST actions BEFORE header output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlash('danger', 'Security token expired. Please try again.');
        header('Location: settings.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    // 1. Update Homepage Counters & General Settings
    if ($action === 'save_settings') {
        $keys = [
            'counter_courses',
            'counter_pass_rate',
            'counter_students',
            'counter_alumni',
            'college_name',
            'college_slogan',
            'contact_phone_primary',
            'contact_email_primary',
            'contact_address',
            'announcement_banner'
        ];

        foreach ($keys as $k) {
            if (isset($_POST[$k])) {
                setSetting($k, trim($_POST[$k]));
            }
        }

        setFlash('success', 'Site settings and statistics updated.');
        header('Location: settings.php');
        exit;
    }

    // 2. Change Admin Password
    if ($action === 'change_password') {
        $currentPass = $_POST['current_password'] ?? '';
        $newPass     = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        if (empty($currentPass) || empty($newPass) || empty($confirmPass)) {
            setFlash('danger', 'All password fields are required.');
            header('Location: settings.php');
            exit;
        }

        if ($newPass !== $confirmPass) {
            setFlash('danger', 'New password and confirmation do not match.');
            header('Location: settings.php');
            exit;
        }

        if (strlen($newPass) < 8) {
            setFlash('danger', 'New password must be at least 8 characters long.');
            header('Location: settings.php');
            exit;
        }

        // Verify current password
        $stmt = $db->prepare('SELECT password_hash FROM admins WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $adminUser['id']]);
        $hash = $stmt->fetchColumn();

        if (!$hash || !password_verify($currentPass, $hash)) {
            setFlash('danger', 'Current password is incorrect.');
            header('Location: settings.php');
            exit;
        }

        // Update password
        $newHash = password_hash($newPass, PASSWORD_DEFAULT);
        $stmtU = $db->prepare('UPDATE admins SET password_hash = :h, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        $stmtU->execute([':h' => $newHash, ':id' => $adminUser['id']]);

        setFlash('success', 'Admin password changed successfully.');
        header('Location: settings.php');
        exit;
    }
}

$pageTitle = 'Site Settings & Stats';
require_once __DIR__ . '/includes/header.php';
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 24px;">
    <!-- Homepage Counters -->
    <div class="panel" style="grid-column: 1 / -1;">
        <div class="panel-header">
            <div class="panel-title">Homepage Statistics Counters</div>
        </div>
        <div class="panel-body">
            <form method="POST" action="settings.php">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="save_settings">

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="counter_courses">Academic Programs Count</label>
                        <input type="text" id="counter_courses" name="counter_courses" class="form-control" value="<?= htmlspecialchars(getSetting('counter_courses', '8')) ?>">
                        <span class="form-hint">Displayed on the animated numbers strip.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="counter_pass_rate">Academic Pass Rate (%)</label>
                        <input type="text" id="counter_pass_rate" name="counter_pass_rate" class="form-control" value="<?= htmlspecialchars(getSetting('counter_pass_rate', '97.6')) ?>">
                        <span class="form-hint">e.g. 97.6</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="counter_students">Active Enrolled Students</label>
                        <input type="text" id="counter_students" name="counter_students" class="form-control" value="<?= htmlspecialchars(getSetting('counter_students', '1500')) ?>">
                        <span class="form-hint">e.g. 1500</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="counter_alumni">Graduated Alumni</label>
                        <input type="text" id="counter_alumni" name="counter_alumni" class="form-control" value="<?= htmlspecialchars(getSetting('counter_alumni', '7900')) ?>">
                        <span class="form-hint">e.g. 7900</span>
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label" for="announcement_banner">Header Ticker Announcement (Optional)</label>
                        <input type="text" id="announcement_banner" name="announcement_banner" class="form-control" value="<?= htmlspecialchars(getSetting('announcement_banner', '')) ?>" placeholder="e.g. Admissions Open for Academic Year 2025-26">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact_phone_primary">Primary Contact Phone</label>
                        <input type="text" id="contact_phone_primary" name="contact_phone_primary" class="form-control" value="<?= htmlspecialchars(getSetting('contact_phone_primary', '+91 99788 18009')) ?>">
                        <span class="form-hint">Shown on the Contact page &amp; site footer.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact_email_primary">Primary Contact Email</label>
                        <input type="email" id="contact_email_primary" name="contact_email_primary" class="form-control" value="<?= htmlspecialchars(getSetting('contact_email_primary', 'shrivjmodha@gmail.com')) ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contact_whatsapp_secondary">WhatsApp Secondary Number</label>
                        <input type="text" id="contact_whatsapp_secondary" name="contact_whatsapp_secondary" class="form-control" value="<?= htmlspecialchars(getSetting('contact_whatsapp_secondary', '+91 98256 73093')) ?>">
                        <span class="form-hint">Optional alternate WhatsApp helpdesk number.</span>
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label" for="contact_address">College Physical Address</label>
                        <input type="text" id="contact_address" name="contact_address" class="form-control" value="<?= htmlspecialchars(getSetting('contact_address', '"Vidhyadham", Chhaya-Birla Road, Nr. Pakshi Abhiyaran, Porbandar, Gujarat, 360575')) ?>">
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">Save Settings &amp; Stats</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password Panel -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">Change Admin Password</div>
        </div>
        <div class="panel-body">
            <form method="POST" action="settings.php">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="change_password">

                <div class="form-group">
                    <label class="form-label" for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required placeholder="Enter current password">
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required minlength="8" placeholder="Minimum 8 characters">
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="8" placeholder="Repeat new password">
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>

    <!-- System Information Panel -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">System &amp; Database Info</div>
        </div>
        <div class="panel-body">
            <table class="admin-table">
                <tbody>
                    <tr>
                        <td><strong>PHP Version</strong></td>
                        <td><?= phpversion() ?></td>
                    </tr>
                    <tr>
                        <td><strong>Database Engine</strong></td>
                        <td>SQLite 3 (PDO WAL Mode)</td>
                    </tr>
                    <tr>
                        <td><strong>Database Location</strong></td>
                        <td><code>website/data/database.sqlite</code></td>
                    </tr>
                    <tr>
                        <td><strong>Database Size</strong></td>
                        <td><?= file_exists(DB_FILE_PATH) ? round(filesize(DB_FILE_PATH) / 1024, 2) . ' KB' : '0 KB' ?></td>
                    </tr>
                    <tr>
                        <td><strong>Uploads Directory</strong></td>
                        <td><code>website/assets/uploads/</code> (Writable)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
