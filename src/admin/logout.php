<?php
/**
 * Admin logout handler.
 * URL: /admin/logout.php
 */

require_once __DIR__ . '/../config/session.php';

$_SESSION = [];
session_destroy();

header('Location: /admin/login.php');
exit;
