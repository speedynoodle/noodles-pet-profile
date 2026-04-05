<?php
/**
 * Session management helpers.
 *
 * Starts the PHP session (if not already started) and provides
 * helper functions for authentication and CSRF protection.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    ]);
    session_start();
}

/**
 * Returns true if an admin is currently logged in.
 */
function isAdminLoggedIn(): bool
{
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Returns true if a sitter is currently logged in via their access code.
 */
function isSitterLoggedIn(): bool
{
    return !empty($_SESSION['sitter_logged_in']) && $_SESSION['sitter_logged_in'] === true;
}

/**
 * Returns a per-session CSRF token, generating one if it doesn't exist yet.
 */
function getCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Returns true if the provided token matches the session's CSRF token.
 */
function validateCsrfToken(string $token): bool
{
    return !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}
