<?php
/**
 * Admin – delete a health note (POST handler).
 * URL: /admin/health_note_delete.php
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

$id    = filter_input(INPUT_POST, 'id',     FILTER_VALIDATE_INT);
$petId = filter_input(INPUT_POST, 'pet_id', FILTER_VALIDATE_INT);

if ($id && $id > 0) {
    try {
        deleteHealthNote($id);
    } catch (PDOException $e) {
        // Redirect back on error; the record is still intact
    }
}

header('Location: /admin/health_notes.php?pet_id=' . (int)$petId);
exit;
