<?php
/**
 * Admin POST handler – save household sitter info.
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

$emergencyContactName  = trim($_POST['emergency_contact_name']  ?? '');
$emergencyContactPhone = trim($_POST['emergency_contact_phone'] ?? '');
$vetName               = trim($_POST['vet_name']               ?? '');
$vetPhone              = trim($_POST['vet_phone']               ?? '');
$vetAddress            = trim($_POST['vet_address']             ?? '');
$generalNotes          = trim($_POST['general_notes']           ?? '');

saveHouseholdSitterInfo([
    'emergency_contact_name'  => $emergencyContactName,
    'emergency_contact_phone' => $emergencyContactPhone,
    'vet_name'                => $vetName,
    'vet_phone'               => $vetPhone,
    'vet_address'             => $vetAddress,
    'general_notes'           => $generalNotes,
]);

header('Location: /admin/sitter_info.php');
exit;
