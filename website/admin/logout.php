<?php
/**
 * Admin Logout Handler
 */

require_once __DIR__ . '/includes/auth.php';
logAdminActivity('logout', 'admin', 0, 'Admin user signed out');
logoutAdmin();
header('Location: login.php');
exit;
