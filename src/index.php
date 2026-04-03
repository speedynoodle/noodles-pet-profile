<?php
/**
 * Homepage – displays all pet profiles.
 */

require_once __DIR__ . '/includes/pet_model.php';

$pageTitle = "Noodle's Pet Profiles – Jack-Jack & Nagi";

try {
    $pets = getAllPets();
} catch (PDOException $e) {
    $dbError = 'Could not connect to the database. Please ensure the database service is running.';
    $pets = [];
}

require_once __DIR__ . '/includes/header.php';
?>

<?php if (isset($dbError)): ?>
    <div class="alert alert-error">
        <strong>Database connection error:</strong> <?= htmlspecialchars($dbError) ?>
    </div>
<?php endif; ?>

<section class="hero">
    <h1 class="hero-title">Welcome to Noodle's Pet Profiles 🐾</h1>
    <p class="hero-subtitle">Meet our beloved Shiba Inu family – Jack-Jack &amp; Nagi</p>
</section>

<?php if (!empty($pets)): ?>
    <section class="pets-grid">
        <?php foreach ($pets as $pet): ?>
            <article class="pet-card">
                <div class="pet-card__image-wrapper">
                    <?php if (!empty($pet['photo_url'])): ?>
                        <img
                            src="<?= htmlspecialchars($pet['photo_url']) ?>"
                            alt="Photo of <?= htmlspecialchars($pet['name']) ?>"
                            class="pet-card__image"
                            loading="lazy"
                        >
                    <?php else: ?>
                        <div class="pet-card__image pet-card__image--placeholder">🐶</div>
                    <?php endif; ?>
                    <span class="pet-badge"><?= htmlspecialchars($pet['gender']) ?></span>
                </div>
                <div class="pet-card__body">
                    <h2 class="pet-card__name"><?= htmlspecialchars($pet['name']) ?></h2>
                    <p class="pet-card__breed"><?= htmlspecialchars($pet['breed']) ?></p>
                    <ul class="pet-card__meta">
                        <?php if (!empty($pet['birthday'])): ?>
                            <li>
                                <span class="meta-icon">🎂</span>
                                <span>Age: <?= htmlspecialchars(calculateAge($pet['birthday'])) ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($pet['weight_kg'])): ?>
                            <li>
                                <span class="meta-icon">⚖️</span>
                                <span>Weight: <?= htmlspecialchars($pet['weight_kg']) ?> kg</span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($pet['color'])): ?>
                            <li>
                                <span class="meta-icon">🎨</span>
                                <span>Colour: <?= htmlspecialchars($pet['color']) ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($pet['favourite_toy'])): ?>
                            <li>
                                <span class="meta-icon">🧸</span>
                                <span>Fav toy: <?= htmlspecialchars($pet['favourite_toy']) ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($pet['favourite_food'])): ?>
                            <li>
                                <span class="meta-icon">🍖</span>
                                <span>Fav food: <?= htmlspecialchars($pet['favourite_food']) ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <?php if (!empty($pet['description'])): ?>
                        <p class="pet-card__description">
                            <?= htmlspecialchars(mb_strimwidth($pet['description'], 0, 130, '…')) ?>
                        </p>
                    <?php endif; ?>
                    <a href="/pages/pet.php?id=<?= (int)$pet['id'] ?>" class="btn btn--primary">
                        View Full Profile
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php elseif (!isset($dbError)): ?>
    <p class="empty-state">No pet profiles found. Please check the database seed data.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
