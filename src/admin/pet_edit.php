<?php
/**
 * Admin – add or edit a pet profile.
 * URL: /admin/pet_edit.php            (add new pet)
 *      /admin/pet_edit.php?id=<id>    (edit existing pet)
 */

require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../includes/pet_model.php';

$pet = null;
$editId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($editId && $editId > 0) {
    $pet = getPetById($editId);
    if (!$pet) {
        header('Location: /admin/');
        exit;
    }
}

$genders = ['Male', 'Female', 'Unknown'];

$pageTitle = $pet ? 'Edit Pet: ' . $pet['name'] . ' – Admin' : 'Add New Pet – Admin';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="breadcrumb">
    <a href="/admin/">← Admin Dashboard</a>
</div>

<h1 class="admin-page-title">
    <?= $pet ? '✏️ Edit Pet: ' . htmlspecialchars($pet['name']) : '➕ Add New Pet' ?>
</h1>

<div class="admin-section admin-form-section">
    <form method="post" action="/admin/pet_save.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">
        <?php if ($pet): ?>
            <input type="hidden" name="id" value="<?= (int)$pet['id'] ?>">
        <?php endif; ?>

        <!-- Row 1: name, species, breed -->
        <div class="form-row">
            <div class="form-group">
                <label for="name" class="form-label">
                    Name <span class="form-required">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-input"
                    required
                    maxlength="100"
                    value="<?= htmlspecialchars($pet['name'] ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="species" class="form-label">
                    Species <span class="form-required">*</span>
                </label>
                <input
                    type="text"
                    id="species"
                    name="species"
                    class="form-input"
                    required
                    maxlength="50"
                    value="<?= htmlspecialchars($pet['species'] ?? 'Dog') ?>"
                >
            </div>

            <div class="form-group">
                <label for="breed" class="form-label">
                    Breed <span class="form-required">*</span>
                </label>
                <input
                    type="text"
                    id="breed"
                    name="breed"
                    class="form-input"
                    required
                    maxlength="100"
                    value="<?= htmlspecialchars($pet['breed'] ?? '') ?>"
                >
            </div>
        </div>

        <!-- Row 2: gender, birthday, weight -->
        <div class="form-row">
            <div class="form-group">
                <label for="gender" class="form-label">
                    Gender <span class="form-required">*</span>
                </label>
                <select id="gender" name="gender" class="form-input" required>
                    <?php foreach ($genders as $g): ?>
                        <option value="<?= $g ?>" <?= ($pet['gender'] ?? 'Unknown') === $g ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="birthday" class="form-label">
                    Birthday <span class="form-optional">optional</span>
                </label>
                <input
                    type="date"
                    id="birthday"
                    name="birthday"
                    class="form-input"
                    value="<?= htmlspecialchars($pet['birthday'] ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="weight_kg" class="form-label">
                    Weight (kg) <span class="form-optional">optional</span>
                </label>
                <input
                    type="number"
                    id="weight_kg"
                    name="weight_kg"
                    class="form-input"
                    step="0.01"
                    min="0"
                    max="999.99"
                    placeholder="e.g. 9.50"
                    value="<?= htmlspecialchars($pet['weight_kg'] ?? '') ?>"
                >
            </div>
        </div>

        <!-- Row 3: colour, favourite toy, favourite food -->
        <div class="form-row">
            <div class="form-group">
                <label for="color" class="form-label">
                    Colour <span class="form-required">*</span>
                </label>
                <input
                    type="text"
                    id="color"
                    name="color"
                    class="form-input"
                    required
                    maxlength="100"
                    value="<?= htmlspecialchars($pet['color'] ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="favourite_toy" class="form-label">
                    Favourite Toy <span class="form-optional">optional</span>
                </label>
                <input
                    type="text"
                    id="favourite_toy"
                    name="favourite_toy"
                    class="form-input"
                    maxlength="150"
                    value="<?= htmlspecialchars($pet['favourite_toy'] ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="favourite_food" class="form-label">
                    Favourite Food <span class="form-optional">optional</span>
                </label>
                <input
                    type="text"
                    id="favourite_food"
                    name="favourite_food"
                    class="form-input"
                    maxlength="150"
                    value="<?= htmlspecialchars($pet['favourite_food'] ?? '') ?>"
                >
            </div>
        </div>

        <!-- Photo URL (full row) -->
        <div class="form-group">
            <label for="photo_url" class="form-label">
                Photo URL <span class="form-optional">optional</span>
            </label>
            <input
                type="text"
                id="photo_url"
                name="photo_url"
                class="form-input"
                maxlength="500"
                placeholder="https://… or /assets/images/…"
                value="<?= htmlspecialchars($pet['photo_url'] ?? '') ?>"
            >
        </div>

        <!-- Description -->
        <div class="form-group">
            <label for="description" class="form-label">
                About / Description <span class="form-optional">optional</span>
            </label>
            <textarea
                id="description"
                name="description"
                class="form-input form-textarea"
                rows="4"
                placeholder="Tell us about this pet…"
            ><?= htmlspecialchars($pet['description'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary">
                <?= $pet ? 'Save Changes' : 'Add Pet' ?>
            </button>
            <a href="/admin/" class="btn btn--secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
