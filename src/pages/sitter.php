<?php
/**
 * Public Sitter Information page.
 * URL: /pages/sitter.php
 *
 * Displays household walk schedule, emergency contacts, vet info,
 * per-pet feeding schedules and sitter notes in an easily readable format.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/pet_model.php';

try {
    $householdInfo  = getHouseholdSitterInfo();
    $walkSchedules  = getAllWalkSchedules();
    $pets           = getAllPets();

    $feedingByPet = [];
    foreach ($pets as $pet) {
        $feedingByPet[(int)$pet['id']] = getFeedingSchedulesByPetId((int)$pet['id']);
    }
} catch (PDOException $e) {
    $dbError = 'Could not load sitter information. Please ensure the database service is running.';
}

$pageTitle = "Sitter Information – Noodle's Pet Profiles";
require_once __DIR__ . '/../includes/header.php';
?>

<?php if (isset($dbError)): ?>
    <div class="alert alert-error">
        <strong>Database error:</strong> <?= htmlspecialchars($dbError) ?>
    </div>
<?php else: ?>

<div class="sitter-page">

    <!-- Page heading -->
    <div class="sitter-hero">
        <h1 class="sitter-hero__title">🏠 Sitter Information</h1>
        <p class="sitter-hero__subtitle">Everything you need to know to look after our pets</p>
        <?php if (isAdminLoggedIn()): ?>
            <a href="/admin/sitter_info.php" class="btn btn--secondary btn--sm sitter-hero__edit">
                ✏️ Edit Sitter Info
            </a>
        <?php endif; ?>
    </div>

    <!-- Emergency contacts & vet info -->
    <?php if ($householdInfo): ?>
    <div class="sitter-grid sitter-grid--2">

        <?php if (!empty($householdInfo['emergency_contact_name']) || !empty($householdInfo['emergency_contact_phone'])): ?>
        <div class="sitter-card sitter-card--highlight">
            <h2 class="sitter-card__heading">🆘 Emergency Contact</h2>
            <?php if (!empty($householdInfo['emergency_contact_name'])): ?>
                <p class="sitter-contact__name"><?= htmlspecialchars($householdInfo['emergency_contact_name']) ?></p>
            <?php endif; ?>
            <?php if (!empty($householdInfo['emergency_contact_phone'])): ?>
                <a href="tel:<?= htmlspecialchars(preg_replace('/\s+/', '', $householdInfo['emergency_contact_phone'])) ?>"
                   class="sitter-contact__phone">
                    📞 <?= htmlspecialchars($householdInfo['emergency_contact_phone']) ?>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($householdInfo['vet_name'])): ?>
        <div class="sitter-card">
            <h2 class="sitter-card__heading">🏥 Vet Clinic</h2>
            <p class="sitter-contact__name"><?= htmlspecialchars($householdInfo['vet_name']) ?></p>
            <?php if (!empty($householdInfo['vet_phone'])): ?>
                <a href="tel:<?= htmlspecialchars(preg_replace('/\s+/', '', $householdInfo['vet_phone'])) ?>"
                   class="sitter-contact__phone">
                    📞 <?= htmlspecialchars($householdInfo['vet_phone']) ?>
                </a>
            <?php endif; ?>
            <?php if (!empty($householdInfo['vet_address'])): ?>
                <p class="sitter-contact__address">
                    📍 <?= nl2br(htmlspecialchars($householdInfo['vet_address'])) ?>
                </p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
    <?php endif; ?>

    <!-- Walk Schedule (household-wide) -->
    <section class="sitter-section">
        <h2 class="sitter-section__heading">🦮 Walk Schedule</h2>
        <p class="sitter-section__intro">The following walk schedule applies to <strong>all pets</strong> in the household.</p>

        <?php if (!empty($walkSchedules)): ?>
            <div class="sitter-grid sitter-grid--walks">
                <?php foreach ($walkSchedules as $walk): ?>
                <div class="sitter-walk-card">
                    <div class="sitter-walk-card__label"><?= htmlspecialchars($walk['label']) ?></div>
                    <div class="sitter-walk-card__time">
                        <?= htmlspecialchars(formatTime($walk['walk_time'])) ?>
                    </div>
                    <div class="sitter-walk-card__duration">
                        ⏱ <?= (int)$walk['duration_minutes'] ?> minutes
                    </div>
                    <?php if (!empty($walk['notes'])): ?>
                        <p class="sitter-walk-card__notes">
                            <?= nl2br(htmlspecialchars($walk['notes'])) ?>
                        </p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="empty-state-small">No walk schedule has been set up yet.</p>
        <?php endif; ?>
    </section>

    <!-- Per-pet sections -->
    <?php foreach ($pets as $pet):
        $petId       = (int)$pet['id'];
        $petFeeding  = $feedingByPet[$petId] ?? [];
    ?>
    <section class="sitter-section">
        <div class="sitter-pet-header">
            <?php if (!empty($pet['photo_url'])): ?>
                <img
                    src="<?= htmlspecialchars($pet['photo_url']) ?>"
                    alt="Photo of <?= htmlspecialchars($pet['name']) ?>"
                    class="sitter-pet-header__photo"
                    loading="lazy"
                >
            <?php endif; ?>
            <div class="sitter-pet-header__info">
                <h2 class="sitter-pet-header__name"><?= htmlspecialchars($pet['name']) ?></h2>
                <p class="sitter-pet-header__breed">
                    <?= htmlspecialchars($pet['species']) ?> · <?= htmlspecialchars($pet['breed']) ?>
                    <?php if (!empty($pet['gender'])): ?>
                        · <?= htmlspecialchars($pet['gender']) ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Feeding Schedule -->
        <h3 class="sitter-subsection__heading">🍽 Feeding Schedule</h3>
        <?php if (!empty($petFeeding)): ?>
            <div class="sitter-grid sitter-grid--walks">
                <?php foreach ($petFeeding as $meal): ?>
                <div class="sitter-walk-card">
                    <div class="sitter-walk-card__label"><?= htmlspecialchars($meal['meal_label']) ?></div>
                    <div class="sitter-walk-card__time">
                        <?= htmlspecialchars(formatTime($meal['feed_time'])) ?>
                    </div>
                    <div class="sitter-walk-card__food">
                        <?= htmlspecialchars($meal['food_description']) ?>
                    </div>
                    <?php if (!empty($meal['notes'])): ?>
                        <p class="sitter-walk-card__notes">
                            <?= nl2br(htmlspecialchars($meal['notes'])) ?>
                        </p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="empty-state-small">No feeding schedule set up for <?= htmlspecialchars($pet['name']) ?>.</p>
        <?php endif; ?>

        <!-- Personality / sitter notes -->
        <?php if (!empty($pet['personality'])): ?>
        <h3 class="sitter-subsection__heading">💬 Personality &amp; Tips</h3>
        <p class="sitter-pet-notes"><?= nl2br(htmlspecialchars($pet['personality'])) ?></p>
        <?php endif; ?>

    </section>
    <?php endforeach; ?>

    <!-- General Notes -->
    <?php if ($householdInfo && !empty($householdInfo['general_notes'])): ?>
    <section class="sitter-section">
        <h2 class="sitter-section__heading">📋 General Notes</h2>
        <p class="sitter-general-notes"><?= nl2br(htmlspecialchars($householdInfo['general_notes'])) ?></p>
    </section>
    <?php endif; ?>

</div><!-- /.sitter-page -->

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
