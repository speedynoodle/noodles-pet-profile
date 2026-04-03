<?php
/**
 * Admin – create or update a health note (POST handler).
 * URL: /admin/health_note_save.php
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

$petId    = filter_input(INPUT_POST, 'pet_id',    FILTER_VALIDATE_INT);
$id       = filter_input(INPUT_POST, 'id',        FILTER_VALIDATE_INT); // null on create
$noteDate = trim($_POST['note_date'] ?? '');
$type     = trim($_POST['type']      ?? '');
$notes    = trim($_POST['notes']     ?? '');

// weight_kg is optional – treat empty string as null
$rawWeight = trim($_POST['weight_kg'] ?? '');
$weightKg  = ($rawWeight !== '')
    ? filter_var($rawWeight, FILTER_VALIDATE_FLOAT)
    : null;

$validTypes = ['injection', 'physio', 'fleaing', 'vet_visit', 'medication', 'other'];

// Basic validation
if (
    !$petId
    || $noteDate === ''
    || $notes   === ''
    || !in_array($type, $validTypes, true)
    || ($weightKg === false && $rawWeight !== '')  // non-empty but not a valid float
) {
    header('Location: /admin/health_notes.php?pet_id=' . (int)$petId);
    exit;
}

// Validate date format (YYYY-MM-DD)
$dateObj = DateTime::createFromFormat('Y-m-d', $noteDate);
if (!$dateObj || $dateObj->format('Y-m-d') !== $noteDate) {
    header('Location: /admin/health_notes.php?pet_id=' . (int)$petId);
    exit;
}

try {
    saveHealthNote([
        'id'        => $id ?: null,
        'pet_id'    => $petId,
        'note_date' => $noteDate,
        'weight_kg' => $weightKg,
        'type'      => $type,
        'notes'     => $notes,
    ]);
} catch (PDOException $e) {
    // Redirect back on error; the existing data is still intact
}

header('Location: /admin/health_notes.php?pet_id=' . (int)$petId);
exit;
