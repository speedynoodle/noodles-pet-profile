<?php
/**
 * Auth middleware – redirects to the login page if no admin session is active.
 *
 * Include this at the very top of every admin page (before any output).
 */

require_once __DIR__ . '/../config/session.php';

if (!isAdminLoggedIn()) {
    header('Location: /admin/login.php');
    exit;
}
