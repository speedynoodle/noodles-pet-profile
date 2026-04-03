<?php
/**
 * Local development router for PHP built-in server.
 *
 * Simulates the Apache rewrite rules defined in src/.htaccess so that
 * clean URLs work correctly without needing Apache or nginx.
 *
 * Usage (run from the project root):
 *   php -S localhost:8080 -t src router.php
 *
 * Then open http://localhost:8080 in your browser.
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve existing static files (CSS, JS, images, etc.) directly
if ($uri !== '/' && is_file($_SERVER['DOCUMENT_ROOT'] . $uri)) {
    return false;
}

// All other requests fall through to index.php (homepage / app entry point)
require $_SERVER['DOCUMENT_ROOT'] . '/index.php';
