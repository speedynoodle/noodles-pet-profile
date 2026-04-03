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
    $pet          = getPetById($id);
    $galleryPhotos = getGalleryPhotosByPetId($id);
    $healthNotes  = isAdminLoggedIn() ? getHealthNotesByPetId($id) : [];
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

        <!-- Right column: description, personality, important note, gallery, admin notes -->
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

            <!-- Important Note -->
            <?php if (!empty($pet['important_note'])): ?>
            <section class="profile-section profile-section--important">
                <h2 class="section-heading section-heading--important">⚠️ Important Note</h2>
                <p class="important-note-text"><?= nl2br(htmlspecialchars($pet['important_note'])) ?></p>
            </section>
            <?php endif; ?>

            <!-- Photo Gallery -->
            <?php if (!empty($galleryPhotos)): ?>
            <section class="profile-section">
                <h2 class="section-heading">📸 Photo Gallery</h2>
                <div class="photo-gallery">
                    <?php foreach ($galleryPhotos as $photo): ?>
                    <figure class="gallery-item">
                        <img
                            src="<?= htmlspecialchars($photo['photo_url']) ?>"
                            alt="<?= !empty($photo['caption']) ? htmlspecialchars($photo['caption']) : 'Photo of ' . htmlspecialchars($pet['name']) ?>"
                            class="gallery-photo"
                            loading="lazy"
                        >
                        <?php if (!empty($photo['caption'])): ?>
                        <figcaption class="gallery-caption"><?= htmlspecialchars($photo['caption']) ?></figcaption>
                        <?php endif; ?>
                    </figure>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

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
