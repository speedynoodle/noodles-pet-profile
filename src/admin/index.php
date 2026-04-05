<?php
/**
 * Admin dashboard – lists all pets with links to their health notes.
 * URL: /admin/
 */

require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../includes/pet_model.php';

$pets    = [];
$dbError = null;

try {
    $pets = getAllPets();
} catch (PDOException $e) {
    $dbError = 'Could not load pets. Please ensure the database service is running.';
}

$pageTitle = "Admin Dashboard – Noodle's Pet Profiles";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="breadcrumb">
    <a href="/">← Back to site</a>
</div>

<h1 class="admin-page-title">Admin Dashboard</h1>

<?php if ($dbError !== null): ?>
    <div class="alert alert-error"><?= htmlspecialchars($dbError) ?></div>
<?php elseif (!empty($pets)): ?>
    <div class="admin-pets-list">
        <?php foreach ($pets as $pet): ?>
            <div class="admin-pet-row">
                <div class="admin-pet-info">
                    <?php if (!empty($pet['photo_url'])): ?>
                        <img
                            src="<?= htmlspecialchars($pet['photo_url']) ?>"
                            alt="<?= htmlspecialchars($pet['name']) ?>"
                            class="admin-pet-thumb"
                        >
                    <?php endif; ?>
                    <div>
                        <span class="admin-pet-name"><?= htmlspecialchars($pet['name']) ?></span>
                        <span class="admin-pet-breed"><?= htmlspecialchars($pet['breed']) ?></span>
                    </div>
                </div>
                <div class="admin-pet-actions">
                    <a
                        href="/admin/health_notes.php?pet_id=<?= (int)$pet['id'] ?>"
                        class="btn btn--primary btn--sm"
                    >Health Notes</a>
                    <a
                        href="/pages/pet.php?id=<?= (int)$pet['id'] ?>"
                        class="btn btn--secondary btn--sm"
                    >View Profile</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="margin-top:1.5rem">
        <a href="/admin/sitter_info.php" class="btn btn--secondary">🏠 Manage Sitter Information</a>
    </div>
<?php else: ?>
    <p class="empty-state">No pets found.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
