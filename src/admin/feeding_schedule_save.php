<?php
/**
 * Admin POST handler – save (insert or update) a feeding schedule entry.
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

$id              = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: null;
$petId           = filter_input(INPUT_POST, 'pet_id', FILTER_VALIDATE_INT);
$mealLabel       = trim($_POST['meal_label']       ?? '');
$feedTimeRaw     = trim($_POST['feed_time']        ?? '');
$foodDescription = trim($_POST['food_description'] ?? '');
$notes           = trim($_POST['notes']            ?? '');
$sortOrder       = filter_input(INPUT_POST, 'sort_order', FILTER_VALIDATE_INT);

$errors = [];

if (!$petId || $petId < 1) {
    $errors[] = 'Invalid pet.';
} else {
    $pet = getPetById($petId);
    if (!$pet) {
        $errors[] = 'Pet not found.';
    }
}

if ($mealLabel === '') {
    $errors[] = 'Meal label is required.';
}

if ($foodDescription === '') {
    $errors[] = 'Food description is required.';
}

$feedTime = null;
if ($feedTimeRaw !== '') {
    $parsed = DateTime::createFromFormat('H:i', $feedTimeRaw)
           ?: DateTime::createFromFormat('H:i:s', $feedTimeRaw);
    if ($parsed) {
        $feedTime = $parsed->format('H:i:s');
    } else {
        $errors[] = 'Feed time must be a valid time (HH:MM).';
    }
} else {
    $errors[] = 'Feed time is required.';
}

if (!empty($errors)) {
    $anchor = '#feeding-form-' . (int)$petId;
    if ($id) {
        $anchor = '?edit_feeding=' . (int)$id . '&pet_id=' . (int)$petId . $anchor;
        header('Location: /admin/sitter_info.php' . $anchor);
    } else {
        header('Location: /admin/sitter_info.php' . $anchor);
    }
    exit;
}

saveFeedingSchedule([
    'id'               => $id,
    'pet_id'           => (int)$petId,
    'meal_label'       => $mealLabel,
    'feed_time'        => $feedTime,
    'food_description' => $foodDescription,
    'notes'            => $notes,
    'sort_order'       => max(0, (int)($sortOrder ?? 0)),
]);

header('Location: /admin/sitter_info.php#feeding-form-' . (int)$petId);
exit;
