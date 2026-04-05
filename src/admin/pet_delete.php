<?php
/**
 * Admin – delete a pet and all related records (POST handler).
 * URL: /admin/pet_delete.php
 */

require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../includes/pet_model.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/');
    exit;
}

if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    header('Location: /admin/');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id && $id > 0) {
    try {
        deletePet($id);
    } catch (PDOException $e) {
        // Redirect back on error; the record is still intact
    }
}

header('Location: /admin/');
exit;
