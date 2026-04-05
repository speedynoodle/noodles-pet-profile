<?php
/**
 * Admin POST handler – set, change, or remove the sitter access code.
 */

require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../includes/pet_model.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/sitter_info.php');
    exit;
}

if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    exit('Invalid CSRF token.');
}

// Remove-code action
if (!empty($_POST['remove_code'])) {
    saveSitterAccessCode(null);
    header('Location: /admin/sitter_info.php');
    exit;
}

// Set / change action
$newCode     = $_POST['new_access_code']     ?? '';
$confirmCode = $_POST['confirm_access_code'] ?? '';

$errors = [];

if ($newCode === '') {
    $errors[] = 'Access code cannot be empty.';
} elseif (strlen($newCode) < 8) {
    $errors[] = 'Access code must be at least 8 characters.';
} elseif ($newCode !== $confirmCode) {
    $errors[] = 'The codes do not match. Please try again.';
}

if (!empty($errors)) {
    // A full error UI is beyond scope for this handler; redirect back with a flag
    header('Location: /admin/sitter_info.php?code_error=1');
    exit;
}

$hash = password_hash($newCode, PASSWORD_BCRYPT, ['cost' => 12]);
saveSitterAccessCode($hash);

header('Location: /admin/sitter_info.php?code_saved=1');
exit;
