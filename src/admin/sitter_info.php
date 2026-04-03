<?php
/**
 * Admin – manage sitter information (household info, walk schedules, feeding schedules).
 * URL: /admin/sitter_info.php[?edit_walk=<id>][?edit_feeding=<id>&pet_id=<pet_id>]
 */

require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../includes/pet_model.php';

$householdInfo = getHouseholdSitterInfo();
$walkSchedules = getAllWalkSchedules();
$pets          = getAllPets();

$feedingByPet = [];
foreach ($pets as $pet) {
    $feedingByPet[(int)$pet['id']] = getFeedingSchedulesByPetId((int)$pet['id']);
}

// Pre-populate walk schedule form when editing
$editWalk   = null;
$editWalkId = filter_input(INPUT_GET, 'edit_walk', FILTER_VALIDATE_INT);
if ($editWalkId && $editWalkId > 0) {
    $editWalk = getWalkScheduleById($editWalkId) ?: null;
}

// Pre-populate feeding schedule form when editing
$editFeeding   = null;
$editFeedingId = filter_input(INPUT_GET, 'edit_feeding', FILTER_VALIDATE_INT);
$feedingPetId  = filter_input(INPUT_GET, 'pet_id', FILTER_VALIDATE_INT);
if ($editFeedingId && $editFeedingId > 0) {
    $candidate = getFeedingScheduleById($editFeedingId);
    if ($candidate) {
        $editFeeding  = $candidate;
        $feedingPetId = (int)$candidate['pet_id'];
    }
}

$pageTitle = 'Sitter Information – Admin';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="breadcrumb">
    <a href="/admin/">← Admin Dashboard</a>
</div>

<div class="section-heading-row" style="margin-bottom:1.5rem">
    <h1 class="admin-page-title" style="margin-bottom:0">🏠 Sitter Information</h1>
    <a href="/pages/sitter.php" class="btn btn--secondary btn--sm">👁 View Sitter Page</a>
</div>

<!-- ======================================================
     1. Household Info
     ====================================================== -->
<div class="admin-section admin-form-section">
    <h2 class="section-heading">🆘 Emergency Contact &amp; Vet Info</h2>
    <form method="post" action="/admin/sitter_info_save.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">

        <div class="form-row">
            <div class="form-group">
                <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                <input
                    type="text"
                    id="emergency_contact_name"
                    name="emergency_contact_name"
                    class="form-input"
                    maxlength="120"
                    placeholder="e.g. Alex &amp; Jordan"
                    value="<?= htmlspecialchars($householdInfo['emergency_contact_name'] ?? '') ?>"
                >
            </div>
            <div class="form-group">
                <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                <input
                    type="text"
                    id="emergency_contact_phone"
                    name="emergency_contact_phone"
                    class="form-input"
                    maxlength="40"
                    placeholder="e.g. +61 400 000 000"
                    value="<?= htmlspecialchars($householdInfo['emergency_contact_phone'] ?? '') ?>"
                >
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="vet_name" class="form-label">Vet Clinic Name</label>
                <input
                    type="text"
                    id="vet_name"
                    name="vet_name"
                    class="form-input"
                    maxlength="120"
                    placeholder="e.g. Happy Paws Vet Clinic"
                    value="<?= htmlspecialchars($householdInfo['vet_name'] ?? '') ?>"
                >
            </div>
            <div class="form-group">
                <label for="vet_phone" class="form-label">Vet Phone</label>
                <input
                    type="text"
                    id="vet_phone"
                    name="vet_phone"
                    class="form-input"
                    maxlength="40"
                    placeholder="e.g. +61 2 9000 0000"
                    value="<?= htmlspecialchars($householdInfo['vet_phone'] ?? '') ?>"
                >
            </div>
        </div>

        <div class="form-group">
            <label for="vet_address" class="form-label">Vet Address</label>
            <input
                type="text"
                id="vet_address"
                name="vet_address"
                class="form-input"
                maxlength="255"
                placeholder="e.g. 123 Woof Street, Sydney NSW 2000"
                value="<?= htmlspecialchars($householdInfo['vet_address'] ?? '') ?>"
            >
        </div>

        <div class="form-group">
            <label for="general_notes" class="form-label">
                General Notes <span class="form-optional">optional</span>
            </label>
            <textarea
                id="general_notes"
                name="general_notes"
                class="form-input form-textarea"
                rows="4"
                placeholder="House rules, gate latching reminders, etc."
            ><?= htmlspecialchars($householdInfo['general_notes'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary">Save Household Info</button>
        </div>
    </form>
</div>

<!-- ======================================================
     2. Walk Schedules
     ====================================================== -->
<?php if (!empty($walkSchedules)): ?>
<div class="admin-section">
    <h2 class="section-heading">🦮 Walk Schedules</h2>
    <div class="admin-section-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Label</th>
                    <th>Time</th>
                    <th>Duration</th>
                    <th>Notes</th>
                    <th>Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($walkSchedules as $w): ?>
                <tr <?= ($editWalk && (int)$editWalk['id'] === (int)$w['id']) ? 'class="row-editing"' : '' ?>>
                    <td><?= htmlspecialchars($w['label']) ?></td>
                    <td><?= htmlspecialchars(formatTime($w['walk_time'])) ?></td>
                    <td><?= (int)$w['duration_minutes'] ?> min</td>
                    <td><?= !empty($w['notes']) ? nl2br(htmlspecialchars($w['notes'])) : '—' ?></td>
                    <td><?= (int)$w['sort_order'] ?></td>
                    <td class="admin-actions">
                        <a
                            href="?edit_walk=<?= (int)$w['id'] ?>#walk-form"
                            class="btn btn--sm btn--secondary"
                        >Edit</a>
                        <form
                            method="post"
                            action="/admin/walk_schedule_delete.php"
                            onsubmit="return confirm('Delete this walk schedule entry?')"
                        >
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">
                            <input type="hidden" name="id" value="<?= (int)$w['id'] ?>">
                            <button type="submit" class="btn btn--sm btn--danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<div class="admin-section admin-form-section" id="walk-form">
    <h2 class="section-heading">
        <?= $editWalk ? '✏️ Edit Walk Schedule Entry' : '➕ Add Walk Schedule Entry' ?>
    </h2>
    <form method="post" action="/admin/walk_schedule_save.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">
        <?php if ($editWalk): ?>
            <input type="hidden" name="id" value="<?= (int)$editWalk['id'] ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="walk_label" class="form-label">
                    Label <span class="form-required">*</span>
                </label>
                <input
                    type="text"
                    id="walk_label"
                    name="label"
                    class="form-input"
                    required
                    maxlength="80"
                    placeholder="e.g. Morning Walk"
                    value="<?= htmlspecialchars($editWalk['label'] ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="walk_time" class="form-label">
                    Time <span class="form-required">*</span>
                </label>
                <input
                    type="time"
                    id="walk_time"
                    name="walk_time"
                    class="form-input"
                    required
                    value="<?= htmlspecialchars(isset($editWalk['walk_time']) ? substr($editWalk['walk_time'], 0, 5) : '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="walk_duration" class="form-label">
                    Duration (minutes) <span class="form-required">*</span>
                </label>
                <input
                    type="number"
                    id="walk_duration"
                    name="duration_minutes"
                    class="form-input"
                    required
                    min="1"
                    max="300"
                    placeholder="30"
                    value="<?= htmlspecialchars((string)($editWalk['duration_minutes'] ?? '30')) ?>"
                >
            </div>

            <div class="form-group">
                <label for="walk_sort_order" class="form-label">Sort Order</label>
                <input
                    type="number"
                    id="walk_sort_order"
                    name="sort_order"
                    class="form-input"
                    min="0"
                    max="255"
                    placeholder="0"
                    value="<?= htmlspecialchars((string)($editWalk['sort_order'] ?? '0')) ?>"
                >
            </div>
        </div>

        <div class="form-group">
            <label for="walk_notes" class="form-label">
                Notes <span class="form-optional">optional</span>
            </label>
            <textarea
                id="walk_notes"
                name="notes"
                class="form-input form-textarea"
                rows="3"
                placeholder="e.g. Allow off-leash time in the park if available."
            ><?= htmlspecialchars($editWalk['notes'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary">
                <?= $editWalk ? 'Update Walk Entry' : 'Add Walk Entry' ?>
            </button>
            <?php if ($editWalk): ?>
                <a href="/admin/sitter_info.php#walk-form" class="btn btn--secondary">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- ======================================================
     3. Feeding Schedules (per pet)
     ====================================================== -->
<?php foreach ($pets as $pet):
    $petId      = (int)$pet['id'];
    $petFeeding = $feedingByPet[$petId] ?? [];
    $isEditingThisPet = ($feedingPetId === $petId);
?>

<?php if (!empty($petFeeding)): ?>
<div class="admin-section">
    <h2 class="section-heading">
        🍽 <?= htmlspecialchars($pet['name']) ?>'s Feeding Schedule
    </h2>
    <div class="admin-section-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Meal</th>
                    <th>Time</th>
                    <th>Food</th>
                    <th>Notes</th>
                    <th>Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($petFeeding as $meal): ?>
                <tr <?= ($editFeeding && (int)$editFeeding['id'] === (int)$meal['id']) ? 'class="row-editing"' : '' ?>>
                    <td><?= htmlspecialchars($meal['meal_label']) ?></td>
                    <td><?= htmlspecialchars(formatTime($meal['feed_time'])) ?></td>
                    <td><?= htmlspecialchars($meal['food_description']) ?></td>
                    <td><?= !empty($meal['notes']) ? nl2br(htmlspecialchars($meal['notes'])) : '—' ?></td>
                    <td><?= (int)$meal['sort_order'] ?></td>
                    <td class="admin-actions">
                        <a
                            href="?edit_feeding=<?= (int)$meal['id'] ?>&pet_id=<?= $petId ?>#feeding-form-<?= $petId ?>"
                            class="btn btn--sm btn--secondary"
                        >Edit</a>
                        <form
                            method="post"
                            action="/admin/feeding_schedule_delete.php"
                            onsubmit="return confirm('Delete this feeding entry?')"
                        >
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">
                            <input type="hidden" name="id"     value="<?= (int)$meal['id'] ?>">
                            <input type="hidden" name="pet_id" value="<?= $petId ?>">
                            <button type="submit" class="btn btn--sm btn--danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<div class="admin-section admin-form-section" id="feeding-form-<?= $petId ?>">
    <h2 class="section-heading">
        <?= ($isEditingThisPet && $editFeeding)
            ? '✏️ Edit Feeding Entry: ' . htmlspecialchars($pet['name'])
            : '➕ Add Feeding Entry: ' . htmlspecialchars($pet['name']) ?>
    </h2>
    <form method="post" action="/admin/feeding_schedule_save.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">
        <input type="hidden" name="pet_id"     value="<?= $petId ?>">
        <?php if ($isEditingThisPet && $editFeeding): ?>
            <input type="hidden" name="id" value="<?= (int)$editFeeding['id'] ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="meal_label_<?= $petId ?>" class="form-label">
                    Meal Label <span class="form-required">*</span>
                </label>
                <input
                    type="text"
                    id="meal_label_<?= $petId ?>"
                    name="meal_label"
                    class="form-input"
                    required
                    maxlength="80"
                    placeholder="e.g. Breakfast"
                    value="<?= htmlspecialchars(($isEditingThisPet && $editFeeding) ? $editFeeding['meal_label'] : '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="feed_time_<?= $petId ?>" class="form-label">
                    Time <span class="form-required">*</span>
                </label>
                <input
                    type="time"
                    id="feed_time_<?= $petId ?>"
                    name="feed_time"
                    class="form-input"
                    required
                    value="<?= htmlspecialchars(($isEditingThisPet && $editFeeding) ? substr($editFeeding['feed_time'], 0, 5) : '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="sort_order_<?= $petId ?>" class="form-label">Sort Order</label>
                <input
                    type="number"
                    id="sort_order_<?= $petId ?>"
                    name="sort_order"
                    class="form-input"
                    min="0"
                    max="255"
                    placeholder="0"
                    value="<?= htmlspecialchars((string)(($isEditingThisPet && $editFeeding) ? $editFeeding['sort_order'] : '0')) ?>"
                >
            </div>
        </div>

        <div class="form-group">
            <label for="food_desc_<?= $petId ?>" class="form-label">
                Food Description <span class="form-required">*</span>
            </label>
            <input
                type="text"
                id="food_desc_<?= $petId ?>"
                name="food_description"
                class="form-input"
                required
                maxlength="255"
                placeholder="e.g. 1 cup Acana Regionals dry food"
                value="<?= htmlspecialchars(($isEditingThisPet && $editFeeding) ? $editFeeding['food_description'] : '') ?>"
            >
        </div>

        <div class="form-group">
            <label for="feeding_notes_<?= $petId ?>" class="form-label">
                Notes <span class="form-optional">optional</span>
            </label>
            <textarea
                id="feeding_notes_<?= $petId ?>"
                name="notes"
                class="form-input form-textarea"
                rows="3"
                placeholder="e.g. Mix with warm water to soften."
            ><?= htmlspecialchars(($isEditingThisPet && $editFeeding) ? $editFeeding['notes'] : '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary">
                <?= ($isEditingThisPet && $editFeeding) ? 'Update Feeding Entry' : 'Add Feeding Entry' ?>
            </button>
            <?php if ($isEditingThisPet && $editFeeding): ?>
                <a href="/admin/sitter_info.php#feeding-form-<?= $petId ?>" class="btn btn--secondary">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php endforeach; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
