<?php
/**
 * Admin – create or update a pet record (POST handler).
 * URL: /admin/pet_save.php
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

$id    = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT); // null on create
$name  = trim($_POST['name']    ?? '');
$species = trim($_POST['species'] ?? 'Dog');
$breed = trim($_POST['breed']   ?? '');
$color = trim($_POST['color']   ?? '');

$gender  = trim($_POST['gender'] ?? 'Unknown');
$validGenders = ['Male', 'Female', 'Unknown'];
if (!in_array($gender, $validGenders, true)) {
    $gender = 'Unknown';
}

$birthday = trim($_POST['birthday'] ?? '');
if ($birthday !== '') {
    $dateObj = DateTime::createFromFormat('Y-m-d', $birthday);
    if (!$dateObj || $dateObj->format('Y-m-d') !== $birthday) {
        $birthday = '';
    }
}

$rawWeight = trim($_POST['weight_kg'] ?? '');
$weightKg  = ($rawWeight !== '')
    ? filter_var($rawWeight, FILTER_VALIDATE_FLOAT)
    : null;

$description   = trim($_POST['description']   ?? '');
$favouriteToy  = trim($_POST['favourite_toy'] ?? '');
$favouriteFood = trim($_POST['favourite_food'] ?? '');
$photoUrl      = trim($_POST['photo_url']     ?? '');

// Required fields validation
if ($name === '' || $species === '' || $breed === '' || $color === ''
    || ($weightKg === false && $rawWeight !== '')) {
    $redirect = $id ? '/admin/pet_edit.php?id=' . (int)$id : '/admin/pet_edit.php';
    header('Location: ' . $redirect);
    exit;
}

// Photo URL must be a valid absolute URL or a relative path starting with / (no traversal)
if ($photoUrl !== ''
    && !filter_var($photoUrl, FILTER_VALIDATE_URL)
    && (!preg_match('#^/#', $photoUrl) || strpos($photoUrl, '..') !== false)) {
    $redirect = $id ? '/admin/pet_edit.php?id=' . (int)$id : '/admin/pet_edit.php';
    header('Location: ' . $redirect);
    exit;
}

try {
    savePet([
        'id'            => $id ?: null,
        'name'          => $name,
        'species'       => $species,
        'breed'         => $breed,
        'gender'        => $gender,
        'birthday'      => $birthday ?: null,
        'weight_kg'     => $weightKg,
        'color'         => $color,
        'description'   => $description,
        'favourite_toy' => $favouriteToy,
        'favourite_food'=> $favouriteFood,
        'photo_url'     => $photoUrl,
    ]);
} catch (PDOException $e) {
    $redirect = $id ? '/admin/pet_edit.php?id=' . (int)$id : '/admin/pet_edit.php';
    header('Location: ' . $redirect);
    exit;
}

header('Location: /admin/?saved=1');
exit;
