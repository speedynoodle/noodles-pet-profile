<?php
/**
 * Pet model – functions for retrieving pet data from the database.
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Fetch all pets ordered by id.
 */
function getAllPets(): array
{
    $pdo = getDbConnection();
    $stmt = $pdo->query('SELECT * FROM pets ORDER BY id ASC');
    return $stmt->fetchAll();
}

/**
 * Fetch a single pet by its id.
 */
function getPetById(int $id): array|false
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare('SELECT * FROM pets WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

/**
 * Fetch vaccinations for a pet.
 */
function getVaccinationsByPetId(int $petId): array
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare(
        'SELECT * FROM vaccinations WHERE pet_id = :pet_id ORDER BY date_given DESC'
    );
    $stmt->execute([':pet_id' => $petId]);
    return $stmt->fetchAll();
}

/**
 * Fetch medical records for a pet.
 */
function getMedicalRecordsByPetId(int $petId): array
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare(
        'SELECT * FROM medical_records WHERE pet_id = :pet_id ORDER BY record_date DESC'
    );
    $stmt->execute([':pet_id' => $petId]);
    return $stmt->fetchAll();
}

/**
 * Calculate age in years and months from a birthday date string.
 */
function calculateAge(string $birthday): string
{
    $birth = new DateTime($birthday);
    $now   = new DateTime();
    $diff  = $now->diff($birth);

    $parts = [];
    if ($diff->y > 0) {
        $parts[] = $diff->y . ' year' . ($diff->y !== 1 ? 's' : '');
    }
    if ($diff->m > 0) {
        $parts[] = $diff->m . ' month' . ($diff->m !== 1 ? 's' : '');
    }
    return $parts ? implode(', ', $parts) : 'Less than a month';
}
