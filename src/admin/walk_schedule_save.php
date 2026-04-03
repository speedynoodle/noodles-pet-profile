<?php
/**
 * Admin POST handler – save (insert or update) a walk schedule entry.
 */

require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../includes/pet_model.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/sitter_info.php#walk-form');
    exit;
}

if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    exit('Invalid CSRF token.');
}

$id            = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
$label         = trim($_POST['label'] ?? '');
$walkTimeRaw   = trim($_POST['walk_time'] ?? '');
$durationMins  = filter_input(INPUT_POST, 'duration_minutes', FILTER_VALIDATE_INT);
$notes         = trim($_POST['notes'] ?? '');
$sortOrder     = filter_input(INPUT_POST, 'sort_order', FILTER_VALIDATE_INT);

$errors = [];

if ($label === '') {
    $errors[] = 'Label is required.';
}

$walkTime = null;
if ($walkTimeRaw !== '') {
    $parsed = DateTime::createFromFormat('H:i', $walkTimeRaw)
           ?: DateTime::createFromFormat('H:i:s', $walkTimeRaw);
    if ($parsed) {
        $walkTime = $parsed->format('H:i:s');
    } else {
        $errors[] = 'Walk time must be a valid time (HH:MM).';
    }
} else {
    $errors[] = 'Walk time is required.';
}

if (!$durationMins || $durationMins < 1 || $durationMins > 300) {
    $errors[] = 'Duration must be between 1 and 300 minutes.';
}

if (!empty($errors)) {
    // Redirect back with a simple error; a full error UI is outside scope for these handlers
    $anchor = $id ? '?edit_walk=' . (int)$id . '#walk-form' : '#walk-form';
    header('Location: /admin/sitter_info.php' . $anchor);
    exit;
}

saveWalkSchedule([
    'id'               => $id,
    'label'            => $label,
    'walk_time'        => $walkTime,
    'duration_minutes' => (int)$durationMins,
    'notes'            => $notes,
    'sort_order'       => max(0, (int)($sortOrder ?? 0)),
]);

header('Location: /admin/sitter_info.php#walk-form');
exit;
