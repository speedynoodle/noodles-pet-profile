<?php
/**
 * Sitter access code login page.
 * URL: /pages/sitter_login.php
 *
 * Sitters enter the access code set by the owner to view sitter information.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../includes/pet_model.php';

// Admins and already-authenticated sitters go straight to the sitter page
if (isAdminLoggedIn() || isSitterLoggedIn()) {
    header('Location: /pages/sitter.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $code = $_POST['access_code'] ?? '';

        if ($code === '') {
            $error = 'Please enter the access code.';
        } else {
            try {
                $householdInfo = getHouseholdSitterInfo();

                if (!$householdInfo || empty($householdInfo['sitter_access_code_hash'])) {
                    $error = 'No access code has been set. Please contact the owner.';
                } elseif (password_verify($code, $householdInfo['sitter_access_code_hash'])) {
                    if (password_needs_rehash($householdInfo['sitter_access_code_hash'], PASSWORD_BCRYPT, ['cost' => 12])) {
                        try {
                            saveSitterAccessCode(password_hash($code, PASSWORD_BCRYPT, ['cost' => 12]));
                        } catch (PDOException $rehashEx) {
                            // Rehash failed; log silently – login still proceeds
                            error_log('Sitter code rehash failed: ' . $rehashEx->getMessage());
                        }
                    }
                    session_regenerate_id(true);
                    $_SESSION['sitter_logged_in'] = true;
                    header('Location: /pages/sitter.php');
                    exit;
                } else {
                    $error = 'Incorrect access code. Please try again.';
                }
            } catch (PDOException $e) {
                $error = 'Database error. Please try again later.';
            }
        }
    }
}

$pageTitle = "Sitter Access – Noodles’ Pet Profiles";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-login-wrapper">
    <div class="admin-login-card">
        <h1 class="admin-login-title">🔑 Sitter Access</h1>
        <p style="text-align:center;color:var(--color-text-muted);font-size:.9rem;margin-bottom:1.25rem">
            Enter the access code provided by the owner.
        </p>

        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="/pages/sitter_login.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">

            <div class="form-group">
                <label for="access_code" class="form-label">Access Code</label>
                <input
                    type="password"
                    id="access_code"
                    name="access_code"
                    class="form-input"
                    required
                    autocomplete="one-time-code"
                    placeholder="Enter access code"
                >
            </div>

            <button type="submit" class="btn btn--primary btn--full">View Sitter Info</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
