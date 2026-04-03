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
 * Fetch health notes for a pet, most recent first.
 */
function getHealthNotesByPetId(int $petId): array
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare(
        'SELECT * FROM health_notes WHERE pet_id = :pet_id ORDER BY note_date DESC, id DESC'
    );
    $stmt->execute([':pet_id' => $petId]);
    return $stmt->fetchAll();
}

/**
 * Fetch a single health note by its id.
 */
function getHealthNoteById(int $id): array|false
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare('SELECT * FROM health_notes WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

/**
 * Insert or update a health note.
 *
 * @param array{id: ?int, pet_id: int, note_date: string, weight_kg: ?float, type: string, notes: string} $data
 */
function saveHealthNote(array $data): void
{
    $pdo = getDbConnection();

    if (!empty($data['id'])) {
        $stmt = $pdo->prepare(
            'UPDATE health_notes
             SET note_date = :note_date, weight_kg = :weight_kg, type = :type, notes = :notes
             WHERE id = :id'
        );
        $stmt->execute([
            ':note_date' => $data['note_date'],
            ':weight_kg' => $data['weight_kg'],
            ':type'      => $data['type'],
            ':notes'     => $data['notes'],
            ':id'        => $data['id'],
        ]);
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO health_notes (pet_id, note_date, weight_kg, type, notes)
             VALUES (:pet_id, :note_date, :weight_kg, :type, :notes)'
        );
        $stmt->execute([
            ':pet_id'    => $data['pet_id'],
            ':note_date' => $data['note_date'],
            ':weight_kg' => $data['weight_kg'],
            ':type'      => $data['type'],
            ':notes'     => $data['notes'],
        ]);
    }
}

/**
 * Delete a health note by its id.
 */
function deleteHealthNote(int $id): void
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare('DELETE FROM health_notes WHERE id = :id');
    $stmt->execute([':id' => $id]);
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
