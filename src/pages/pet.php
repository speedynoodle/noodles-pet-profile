<?php
/**
 * Individual pet profile page.
 * URL: /pages/pet.php?id=<pet_id>
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/pet_model.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: /');
    exit;
}

try {
    $pet            = getPetById($id);
    $vaccinations   = getVaccinationsByPetId($id);
    $medicalRecords = getMedicalRecordsByPetId($id);
    $healthNotes    = isAdminLoggedIn() ? getHealthNotesByPetId($id) : [];
} catch (PDOException $e) {
    $dbError = 'Could not load pet data. Please ensure the database service is running.';
    $pet = false;
}

if (!isset($dbError) && $pet === false) {
    header('Location: /');
    exit;
}

$pageTitle = $pet ? htmlspecialchars($pet['name']) . "'s Profile – Noodle's Pet Profiles" : "Pet Profile";

require_once __DIR__ . '/../includes/header.php';
?>

<?php if (isset($dbError)): ?>
    <div class="alert alert-error">
        <strong>Database error:</strong> <?= htmlspecialchars($dbError) ?>
    </div>
<?php elseif ($pet): ?>

    <div class="breadcrumb">
        <a href="/">← Back to all pets</a>
    </div>

    <div class="profile-layout">

        <!-- Left column: photo + quick facts -->
        <aside class="profile-sidebar">
            <div class="profile-photo-wrapper">
                <?php if (!empty($pet['photo_url'])): ?>
                    <img
                        src="<?= htmlspecialchars($pet['photo_url']) ?>"
                        alt="Photo of <?= htmlspecialchars($pet['name']) ?>"
                        class="profile-photo"
                    >
                <?php else: ?>
                    <div class="profile-photo profile-photo--placeholder">🐶</div>
                <?php endif; ?>
            </div>

            <div class="profile-quick-facts">
                <h1 class="profile-name"><?= htmlspecialchars($pet['name']) ?></h1>
                <p class="profile-breed"><?= htmlspecialchars($pet['species']) ?> · <?= htmlspecialchars($pet['breed']) ?></p>

                <table class="facts-table">
                    <tbody>
                        <tr>
                            <th>Gender</th>
                            <td><?= htmlspecialchars($pet['gender']) ?></td>
                        </tr>
                        <?php if (!empty($pet['birthday'])): ?>
                        <tr>
                            <th>Birthday</th>
                            <td>
                                <?= htmlspecialchars(date('d M Y', strtotime($pet['birthday']))) ?>
                                <small>(<?= htmlspecialchars(calculateAge($pet['birthday'])) ?>)</small>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($pet['weight_kg'])): ?>
                        <tr>
                            <th>Weight</th>
                            <td><?= htmlspecialchars($pet['weight_kg']) ?> kg</td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($pet['color'])): ?>
                        <tr>
                            <th>Colour</th>
                            <td><?= htmlspecialchars($pet['color']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($pet['favourite_toy'])): ?>
                        <tr>
                            <th>Fav Toy</th>
                            <td><?= htmlspecialchars($pet['favourite_toy']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($pet['favourite_food'])): ?>
                        <tr>
                            <th>Fav Food</th>
                            <td><?= htmlspecialchars($pet['favourite_food']) ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </aside>

        <!-- Right column: description, personality, records -->
        <div class="profile-main">

            <?php if (!empty($pet['description'])): ?>
            <section class="profile-section">
                <h2 class="section-heading">About <?= htmlspecialchars($pet['name']) ?></h2>
                <p><?= nl2br(htmlspecialchars($pet['description'])) ?></p>
            </section>
            <?php endif; ?>

            <?php if (!empty($pet['personality'])): ?>
            <section class="profile-section">
                <h2 class="section-heading">Personality</h2>
                <p><?= nl2br(htmlspecialchars($pet['personality'])) ?></p>
            </section>
            <?php endif; ?>

            <!-- Vaccinations -->
            <section class="profile-section">
                <h2 class="section-heading">💉 Vaccination History</h2>
                <?php if (!empty($vaccinations)): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Vaccine</th>
                                <th>Date Given</th>
                                <th>Next Due</th>
                                <th>Vet</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vaccinations as $v): ?>
                            <tr>
                                <td><?= htmlspecialchars($v['vaccine_name']) ?></td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($v['date_given']))) ?></td>
                                <td>
                                    <?= !empty($v['next_due_date'])
                                        ? htmlspecialchars(date('d M Y', strtotime($v['next_due_date'])))
                                        : '—' ?>
                                </td>
                                <td><?= !empty($v['vet_name']) ? htmlspecialchars($v['vet_name']) : '—' ?></td>
                                <td><?= !empty($v['notes']) ? htmlspecialchars($v['notes']) : '—' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="empty-state-small">No vaccination records on file.</p>
                <?php endif; ?>
            </section>

            <!-- Medical Records -->
            <section class="profile-section">
                <h2 class="section-heading">🏥 Medical Records</h2>
                <?php if (!empty($medicalRecords)): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Vet</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medicalRecords as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($r['record_date']))) ?></td>
                                <td><span class="record-badge"><?= htmlspecialchars($r['record_type']) ?></span></td>
                                <td><?= !empty($r['description']) ? htmlspecialchars($r['description']) : '—' ?></td>
                                <td><?= !empty($r['vet_name']) ? htmlspecialchars($r['vet_name']) : '—' ?></td>
                                <td><?= !empty($r['notes']) ? htmlspecialchars($r['notes']) : '—' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="empty-state-small">No medical records on file.</p>
                <?php endif; ?>
            </section>

            <!-- Health Notes (admin only) -->
            <?php if (isAdminLoggedIn()): ?>
            <section class="profile-section profile-section--admin">
                <div class="section-heading-row">
                    <h2 class="section-heading">🩺 Health Notes</h2>
                    <a
                        href="/admin/health_notes.php?pet_id=<?= (int)$pet['id'] ?>"
                        class="btn btn--sm btn--secondary"
                    >Manage</a>
                </div>
                <?php if (!empty($healthNotes)): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Weight (kg)</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($healthNotes as $hn): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($hn['note_date']))) ?></td>
                                <td>
                                    <span class="record-badge">
                                        <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $hn['type']))) ?>
                                    </span>
                                </td>
                                <td><?= !empty($hn['weight_kg']) ? htmlspecialchars($hn['weight_kg']) . ' kg' : '—' ?></td>
                                <td><?= nl2br(htmlspecialchars($hn['notes'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="empty-state-small">No health notes on file.
                        <a href="/admin/health_notes.php?pet_id=<?= (int)$pet['id'] ?>">Add one →</a>
                    </p>
                <?php endif; ?>
            </section>
            <?php endif; ?>

        </div><!-- /.profile-main -->
    </div><!-- /.profile-layout -->

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
