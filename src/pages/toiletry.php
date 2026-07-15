<?php
/**
 * Toiletry Logs Page
 * Accessible via unique token: /pages/toiletry.php?token=<uuid>
 * Allows viewing and logging toiletry entries for a pet
 */

require_once __DIR__ . '/../includes/pet_model.php';

// Get and validate token
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);

if (!$token || empty($token)) {
    http_response_code(404);
    header('Location: /');
    exit;
}

try {
    // Fetch pet by token
    $pet = getPetByToiletryToken($token);
    if (!$pet) {
        http_response_code(404);
        header('Location: /');
        exit;
    }

    // Fetch toiletry logs
    $toiletryLogs = getToiletryLogsByPetId($pet['id'], 50);
} catch (PDOException $e) {
    $dbError = 'Could not load pet data. Please ensure the database service is running.';
    $pet = false;
}

$petId = $pet['id'] ?? null;
$pageTitle = $pet ? htmlspecialchars($pet['name']) . "'s Toiletry Log – Noodles' Pet Profiles" : "Toiletry Log";

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

    <div class="toiletry-page-container">
        <!-- Pet Info Header -->
        <div class="toiletry-page-header">
            <div class="toiletry-pet-info">
                <?php if (!empty($pet['photo_url'])): ?>
                    <img src="<?= htmlspecialchars($pet['photo_url']) ?>" alt="<?= htmlspecialchars($pet['name']) ?>" class="toiletry-pet-photo">
                <?php else: ?>
                    <div class="toiletry-pet-photo toiletry-pet-photo--placeholder">🐶</div>
                <?php endif; ?>
                <div class="toiletry-pet-details">
                    <h1 class="toiletry-pet-name"><?= htmlspecialchars($pet['name']) ?>'s Toiletry Log</h1>
                    <p class="toiletry-pet-breed"><?= htmlspecialchars($pet['breed']) ?></p>
                </div>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div class="toiletry-quick-actions">
            <button class="btn-toiletry btn-toiletry--pee" onclick="showToiletryModal('<?= htmlspecialchars($token) ?>', 'pee', '<?= addslashes($pet['name']) ?>')">
                💧 Log Pee
            </button>
            <button class="btn-toiletry btn-toiletry--poo" onclick="showToiletryModal('<?= htmlspecialchars($token) ?>', 'poo', '<?= addslashes($pet['name']) ?>')">
                💩 Log Poo
            </button>
        </div>

        <!-- Toiletry Logs Table -->
        <?php if (!empty($toiletryLogs)): ?>
        <section class="toiletry-logs-section">
            <h2 class="section-heading">📋 Activity History</h2>
            <div class="toiletry-logs-table-wrapper">
                <table class="toiletry-logs-table">
                    <thead>
                        <tr>
                            <th>Date &amp; Time (NZT)</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($toiletryLogs as $log): ?>
                        <tr>
                            <td class="toiletry-time">
                                <?= htmlspecialchars(formatDateTimeNZ($log['logged_at'], 'd M Y, g:i A')) ?>
                            </td>
                            <td class="toiletry-type">
                                <span class="toiletry-log-type toiletry-log-type--<?= htmlspecialchars($log['log_type']) ?>">
                                    <?= $log['log_type'] === 'pee' ? '💧' : '💩' ?> 
                                    <?= htmlspecialchars(ucfirst($log['log_type'])) ?>
                                </span>
                            </td>
                            <td class="toiletry-status">
                                <?php if ($log['is_accident']): ?>
                                    <span class="toiletry-accident-badge">⚠️ Accident</span>
                                <?php else: ?>
                                    <span class="toiletry-normal-badge">✓ Normal</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php else: ?>
        <div class="empty-state">
            <p>No activity logged yet. Click "Log Pee" or "Log Poo" above to get started!</p>
        </div>
        <?php endif; ?>

    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

