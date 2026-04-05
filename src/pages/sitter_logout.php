<?php
/**
 * Sitter logout – clears the sitter session flag and redirects to the login page.
 * URL: /pages/sitter_logout.php
 */

require_once __DIR__ . '/../config/session.php';

unset($_SESSION['sitter_logged_in']);

header('Location: /pages/sitter_login.php');
exit;
