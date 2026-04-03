<?php
/**
 * Admin POST handler – delete a walk schedule entry.
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

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    header('Location: /admin/sitter_info.php');
    exit;
}

deleteWalkSchedule($id);

header('Location: /admin/sitter_info.php');
exit;
