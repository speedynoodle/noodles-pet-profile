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

$pageTitle = "Admin Dashboard – Noodles’ Pet Profiles";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="breadcrumb">
    <a href="/">← Back to site</a>
</div>

<h1 class="admin-page-title">Admin Dashboard</h1>

<?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success">✅ Pet profile saved successfully.</div>
<?php endif; ?>

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
                        href="/admin/pet_edit.php?id=<?= (int)$pet['id'] ?>"
                        class="btn btn--primary btn--sm"
                    >✏️ Edit Pet</a>
                    <a
                        href="/admin/health_notes.php?pet_id=<?= (int)$pet['id'] ?>"
                        class="btn btn--secondary btn--sm"
                    >Health Notes</a>
                    <a
                        href="/pages/pet.php?id=<?= (int)$pet['id'] ?>"
                        class="btn btn--secondary btn--sm"
                    >View Profile</a>
                    <form
                        method="post"
                        action="/admin/pet_delete.php"
                        onsubmit="return confirm('Permanently delete <?= htmlspecialchars($pet['name'], ENT_QUOTES) ?> and all related records? This cannot be undone.')"
                    >
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">
                        <input type="hidden" name="id" value="<?= (int)$pet['id'] ?>">
                        <button type="submit" class="btn btn--danger btn--sm">🗑 Delete</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="margin-top:1.5rem;display:flex;gap:.75rem;flex-wrap:wrap;">
        <a href="/admin/pet_edit.php" class="btn btn--primary">➕ Add New Pet</a>
        <a href="/admin/sitter_info.php" class="btn btn--secondary">🏠 Manage Sitter Information</a>
    </div>
<?php else: ?>
    <p class="empty-state">No pets found.</p>
    <div style="margin-top:1.5rem">
        <a href="/admin/pet_edit.php" class="btn btn--primary">➕ Add New Pet</a>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
