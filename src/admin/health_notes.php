<?php
/**
 * Admin – manage health notes for a pet.
 * URL: /admin/health_notes.php?pet_id=<id>[&edit=<note_id>]
 */

require_once __DIR__ . '/../includes/auth_middleware.php';
require_once __DIR__ . '/../includes/pet_model.php';

$petId = filter_input(INPUT_GET, 'pet_id', FILTER_VALIDATE_INT);
if (!$petId || $petId < 1) {
    header('Location: /admin/');
    exit;
}

$pet = getPetById($petId);
if (!$pet) {
    header('Location: /admin/');
    exit;
}

$notes = getHealthNotesByPetId($petId);

// Pre-populate the form when editing an existing note
$editNote = null;
$editId   = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
if ($editId && $editId > 0) {
    $candidate = getHealthNoteById($editId);
    // Only load the note if it belongs to this pet
    if ($candidate && (int)$candidate['pet_id'] === $petId) {
        $editNote = $candidate;
    }
}

$validTypes = ['injection', 'physio', 'fleaing', 'vet_visit', 'medication', 'other'];

$pageTitle = 'Health Notes: ' . $pet['name'] . ' – Admin';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="breadcrumb">
    <a href="/admin/">← Admin Dashboard</a>
</div>

<h1 class="admin-page-title">🏥 Health Notes: <?= htmlspecialchars($pet['name']) ?></h1>

<!-- Existing notes list -->
<?php if (!empty($notes)): ?>
    <div class="admin-section">
        <div class="admin-section-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Weight (kg)</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notes as $n): ?>
                    <tr <?= ($editNote && (int)$editNote['id'] === (int)$n['id']) ? 'class="row-editing"' : '' ?>>
                        <td><?= htmlspecialchars(date('d M Y', strtotime($n['note_date']))) ?></td>
                        <td>
                            <span class="record-badge">
                                <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $n['type']))) ?>
                            </span>
                        </td>
                        <td><?= !empty($n['weight_kg']) ? htmlspecialchars($n['weight_kg']) . ' kg' : '—' ?></td>
                        <td><?= nl2br(htmlspecialchars($n['notes'])) ?></td>
                        <td class="admin-actions">
                            <a
                                href="?pet_id=<?= $petId ?>&edit=<?= (int)$n['id'] ?>#note-form"
                                class="btn btn--sm btn--secondary"
                            >Edit</a>
                            <form
                                method="post"
                                action="/admin/health_note_delete.php"
                                onsubmit="return confirm('Delete this health note?')"
                            >
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">
                                <input type="hidden" name="id"     value="<?= (int)$n['id'] ?>">
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
<?php else: ?>
    <p class="empty-state-small admin-section">
        No health notes yet for <?= htmlspecialchars($pet['name']) ?>.
    </p>
<?php endif; ?>

<!-- Add / Edit form -->
<div class="admin-section admin-form-section" id="note-form">
    <h2 class="section-heading">
        <?= $editNote ? '✏️ Edit Health Note' : '➕ Add Health Note' ?>
    </h2>

    <form method="post" action="/admin/health_note_save.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">
        <input type="hidden" name="pet_id"     value="<?= $petId ?>">
        <?php if ($editNote): ?>
            <input type="hidden" name="id" value="<?= (int)$editNote['id'] ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="note_date" class="form-label">
                    Date <span class="form-required">*</span>
                </label>
                <input
                    type="date"
                    id="note_date"
                    name="note_date"
                    class="form-input"
                    required
                    value="<?= htmlspecialchars($editNote['note_date'] ?? date('Y-m-d')) ?>"
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
                    value="<?= htmlspecialchars($editNote['weight_kg'] ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="type" class="form-label">
                    Type <span class="form-required">*</span>
                </label>
                <select id="type" name="type" class="form-input" required>
                    <?php
                    $currentType = $editNote['type'] ?? 'other';
                    foreach ($validTypes as $t):
                    ?>
                        <option value="<?= $t ?>" <?= $currentType === $t ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $t))) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="notes" class="form-label">
                Notes <span class="form-required">*</span>
            </label>
            <textarea
                id="notes"
                name="notes"
                class="form-input form-textarea"
                required
                rows="4"
                placeholder="Enter health note details…"
            ><?= htmlspecialchars($editNote['notes'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn--primary">
                <?= $editNote ? 'Update Note' : 'Add Note' ?>
            </button>
            <?php if ($editNote): ?>
                <a href="/admin/health_notes.php?pet_id=<?= $petId ?>" class="btn btn--secondary">
                    Cancel
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
