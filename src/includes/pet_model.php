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
 * Fetch gallery photos for a pet, ordered by display_order.
 */
function getGalleryPhotosByPetId(int $petId): array
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare(
        'SELECT * FROM pet_photos WHERE pet_id = :pet_id ORDER BY display_order ASC, id ASC'
    );
    $stmt->execute([':pet_id' => $petId]);
    return $stmt->fetchAll();
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
 * Update only the weight_kg field on a pet record.
 */
function updatePetWeight(int $petId, float $weightKg): void
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare('UPDATE pets SET weight_kg = :weight_kg WHERE id = :id');
    $stmt->execute([':weight_kg' => $weightKg, ':id' => $petId]);
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

// -------------------------------------------------------
// Sitter Information
// -------------------------------------------------------

/**
 * Fetch the single household sitter-info row, or null if not yet set.
 */
function getHouseholdSitterInfo(): array|null
{
    $pdo  = getDbConnection();
    $stmt = $pdo->query('SELECT * FROM sitter_household_info WHERE id = 1 LIMIT 1');
    $row  = $stmt->fetch();
    return $row ?: null;
}

/**
 * Insert or update the household sitter-info row (always id = 1).
 *
 * @param array{emergency_contact_name: string, emergency_contact_phone: string,
 *              vet_name: string, vet_phone: string, vet_address: string,
 *              general_notes: string} $data
 */
function saveHouseholdSitterInfo(array $data): void
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare(
        'INSERT INTO sitter_household_info
             (id, emergency_contact_name, emergency_contact_phone,
              vet_name, vet_phone, vet_address, general_notes)
         VALUES (1, :ecn, :ecp, :vn, :vp, :va, :gn)
         ON DUPLICATE KEY UPDATE
             emergency_contact_name  = VALUES(emergency_contact_name),
             emergency_contact_phone = VALUES(emergency_contact_phone),
             vet_name                = VALUES(vet_name),
             vet_phone               = VALUES(vet_phone),
             vet_address             = VALUES(vet_address),
             general_notes           = VALUES(general_notes)'
    );
    $stmt->execute([
        ':ecn' => $data['emergency_contact_name'],
        ':ecp' => $data['emergency_contact_phone'],
        ':vn'  => $data['vet_name'],
        ':vp'  => $data['vet_phone'],
        ':va'  => $data['vet_address'],
        ':gn'  => $data['general_notes'],
    ]);
}

/**
 * Set (or update) the sitter access code hash for the household row.
 * Pass null to remove the access code (making the sitter page publicly accessible again).
 */
function saveSitterAccessCode(?string $hash): void
{
    $pdo  = getDbConnection();
    // Ensure the row exists first
    $pdo->exec(
        'INSERT IGNORE INTO sitter_household_info (id) VALUES (1)'
    );
    $stmt = $pdo->prepare(
        'UPDATE sitter_household_info SET sitter_access_code_hash = :hash WHERE id = 1'
    );
    $stmt->execute([':hash' => $hash]);
}

/**
 * Fetch all walk schedule entries ordered by sort_order then id.
 */
function getAllWalkSchedules(): array
{
    $pdo  = getDbConnection();
    $stmt = $pdo->query('SELECT * FROM walk_schedules ORDER BY sort_order ASC, id ASC');
    return $stmt->fetchAll();
}

/**
 * Fetch a single walk schedule entry by id.
 */
function getWalkScheduleById(int $id): array|false
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare('SELECT * FROM walk_schedules WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

/**
 * Insert or update a walk schedule entry.
 *
 * @param array{id: ?int, label: string, walk_time: string,
 *              duration_minutes: int, notes: string, sort_order: int} $data
 */
function saveWalkSchedule(array $data): void
{
    $pdo = getDbConnection();
    if (!empty($data['id'])) {
        $stmt = $pdo->prepare(
            'UPDATE walk_schedules
             SET label = :label, walk_time = :walk_time,
                 duration_minutes = :duration_minutes, notes = :notes,
                 sort_order = :sort_order
             WHERE id = :id'
        );
        $stmt->execute([
            ':label'            => $data['label'],
            ':walk_time'        => $data['walk_time'],
            ':duration_minutes' => $data['duration_minutes'],
            ':notes'            => $data['notes'],
            ':sort_order'       => $data['sort_order'],
            ':id'               => $data['id'],
        ]);
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO walk_schedules (label, walk_time, duration_minutes, notes, sort_order)
             VALUES (:label, :walk_time, :duration_minutes, :notes, :sort_order)'
        );
        $stmt->execute([
            ':label'            => $data['label'],
            ':walk_time'        => $data['walk_time'],
            ':duration_minutes' => $data['duration_minutes'],
            ':notes'            => $data['notes'],
            ':sort_order'       => $data['sort_order'],
        ]);
    }
}

/**
 * Delete a walk schedule entry by id.
 */
function deleteWalkSchedule(int $id): void
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare('DELETE FROM walk_schedules WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

/**
 * Fetch all feeding schedule entries for a pet, ordered by sort_order.
 */
function getFeedingSchedulesByPetId(int $petId): array
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare(
        'SELECT * FROM feeding_schedules WHERE pet_id = :pet_id ORDER BY sort_order ASC, id ASC'
    );
    $stmt->execute([':pet_id' => $petId]);
    return $stmt->fetchAll();
}

/**
 * Fetch a single feeding schedule entry by id.
 */
function getFeedingScheduleById(int $id): array|false
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare('SELECT * FROM feeding_schedules WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

/**
 * Insert or update a feeding schedule entry.
 *
 * @param array{id: ?int, pet_id: int, meal_label: string, feed_time: string,
 *              food_description: string, notes: string, sort_order: int} $data
 */
function saveFeedingSchedule(array $data): void
{
    $pdo = getDbConnection();
    if (!empty($data['id'])) {
        $stmt = $pdo->prepare(
            'UPDATE feeding_schedules
             SET meal_label = :meal_label, feed_time = :feed_time,
                 food_description = :food_description, notes = :notes,
                 sort_order = :sort_order
             WHERE id = :id'
        );
        $stmt->execute([
            ':meal_label'      => $data['meal_label'],
            ':feed_time'       => $data['feed_time'],
            ':food_description'=> $data['food_description'],
            ':notes'           => $data['notes'],
            ':sort_order'      => $data['sort_order'],
            ':id'              => $data['id'],
        ]);
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO feeding_schedules (pet_id, meal_label, feed_time, food_description, notes, sort_order)
             VALUES (:pet_id, :meal_label, :feed_time, :food_description, :notes, :sort_order)'
        );
        $stmt->execute([
            ':pet_id'          => $data['pet_id'],
            ':meal_label'      => $data['meal_label'],
            ':feed_time'       => $data['feed_time'],
            ':food_description'=> $data['food_description'],
            ':notes'           => $data['notes'],
            ':sort_order'      => $data['sort_order'],
        ]);
    }
}

/**
 * Delete a feeding schedule entry by id.
 */
function deleteFeedingSchedule(int $id): void
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare('DELETE FROM feeding_schedules WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

/**
 * Format a MySQL TIME string (HH:MM:SS) as a human-readable time (e.g. "7:00 AM").
 * Returns a fallback string if parsing fails.
 */
function formatTime(string $timeStr, string $format = 'g:i A', string $fallback = '—'): string
{
    $ts = strtotime($timeStr);
    return $ts !== false ? date($format, $ts) : $fallback;
}

/**
 * Insert or update a pet record.
 *
 * @param array{id: ?int, name: string, species: string, breed: string,
 *              gender: string, birthday: ?string, weight_kg: ?float,
 *              color: string, description: ?string,
 *              favourite_toy: ?string, favourite_food: ?string,
 *              photo_url: ?string} $data
 */
function savePet(array $data): void
{
    $pdo = getDbConnection();

    if (!empty($data['id'])) {
        $stmt = $pdo->prepare(
            'UPDATE pets
             SET name = :name, species = :species, breed = :breed,
                 gender = :gender, birthday = :birthday, weight_kg = :weight_kg,
                 color = :color, description = :description,
                 favourite_toy = :favourite_toy,
                 favourite_food = :favourite_food, photo_url = :photo_url
             WHERE id = :id'
        );
        $stmt->execute([
            ':name'          => $data['name'],
            ':species'       => $data['species'],
            ':breed'         => $data['breed'],
            ':gender'        => $data['gender'],
            ':birthday'      => $data['birthday'] ?: null,
            ':weight_kg'     => $data['weight_kg'],
            ':color'         => $data['color'],
            ':description'   => $data['description'] ?: null,
            ':favourite_toy' => $data['favourite_toy'] ?: null,
            ':favourite_food'=> $data['favourite_food'] ?: null,
            ':photo_url'     => $data['photo_url'] ?: null,
            ':id'            => $data['id'],
        ]);
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO pets
                 (name, species, breed, gender, birthday, weight_kg,
                  color, description, favourite_toy,
                  favourite_food, photo_url)
             VALUES
                 (:name, :species, :breed, :gender, :birthday, :weight_kg,
                  :color, :description, :favourite_toy,
                  :favourite_food, :photo_url)'
        );
        $stmt->execute([
            ':name'          => $data['name'],
            ':species'       => $data['species'],
            ':breed'         => $data['breed'],
            ':gender'        => $data['gender'],
            ':birthday'      => $data['birthday'] ?: null,
            ':weight_kg'     => $data['weight_kg'],
            ':color'         => $data['color'],
            ':description'   => $data['description'] ?: null,
            ':favourite_toy' => $data['favourite_toy'] ?: null,
            ':favourite_food'=> $data['favourite_food'] ?: null,
            ':photo_url'     => $data['photo_url'] ?: null,
        ]);
    }
}

/**
 * Delete a pet and all related records (cascaded via FK constraints).
 */
function deletePet(int $id): void
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare('DELETE FROM pets WHERE id = :id');
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

// -------------------------------------------------------
// Toiletry Logging
// -------------------------------------------------------

/**
 * Fetch all toiletry logs for a pet, ordered by logged_at descending (most recent first).
 */
function getToiletryLogsByPetId(int $petId, int $limit = 50): array
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare(
        'SELECT * FROM toiletry_logs WHERE pet_id = :pet_id ORDER BY logged_at DESC LIMIT :limit'
    );
    $stmt->execute([':pet_id' => $petId, ':limit' => $limit]);
    return $stmt->fetchAll();
}

/**
 * Create a new toiletry log entry.
 * 
 * @param array{pet_id: int, log_type: string, is_accident: bool, logged_at: string} $data
 */
function saveToiletryLog(array $data): void
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare(
        'INSERT INTO toiletry_logs (pet_id, log_type, is_accident, logged_at)
         VALUES (:pet_id, :log_type, :is_accident, :logged_at)'
    );
    $stmt->execute([
        ':pet_id'     => $data['pet_id'],
        ':log_type'   => $data['log_type'],
        ':is_accident'=> (int)$data['is_accident'],
        ':logged_at'  => $data['logged_at'],
    ]);
}

/**
 * Delete a toiletry log entry by id.
 */
function deleteToiletryLog(int $id): void
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare('DELETE FROM toiletry_logs WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

/**
 * Format a datetime to NZ timezone (NZDT or NZST).
 * Returns a human-readable format for display.
 */
function formatDateTimeNZ(string $datetime, string $format = 'd M Y, g:i A'): string
{
    try {
        $dt = new DateTime($datetime, new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone('Pacific/Auckland'));
        return $dt->format($format);
    } catch (Exception $e) {
        return '—';
    }
}

/**
 * Get current datetime in NZ timezone in MySQL format (YYYY-MM-DD HH:MM:SS).
 */
function getCurrentTimeNZ(): string
{
    $dt = new DateTime('now', new DateTimeZone('Pacific/Auckland'));
    return $dt->format('Y-m-d H:i:s');
}

/**
 * Fetch a single pet by its toiletry access token.
 */
function getPetByToiletryToken(string $token): array|false
{
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare('SELECT * FROM pets WHERE toiletry_access_token = :token LIMIT 1');
    $stmt->execute([':token' => $token]);
    return $stmt->fetch();
}

/**
 * Generate a new UUID v4 for toiletry access token.
 */
function generateToiletryToken(): string
{
    // Generate a UUID v4
    $data = openssl_random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/**
 * Regenerate the toiletry access token for a pet.
 */
function regenerateToiletryToken(int $petId): string
{
    $pdo = getDbConnection();
    $newToken = generateToiletryToken();
    $stmt = $pdo->prepare('UPDATE pets SET toiletry_access_token = :token WHERE id = :id');
    $stmt->execute([':token' => $newToken, ':id' => $petId]);
    return $newToken;
}
