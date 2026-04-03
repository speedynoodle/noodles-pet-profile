<?php
/**
 * Admin login page.
 * URL: /admin/login.php
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/database.php';

// Already logged in – go straight to the dashboard
if (isAdminLoggedIn()) {
    header('Location: /admin/');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Please enter your username and password.';
        } else {
            try {
                $pdo  = getDbConnection();
                $stmt = $pdo->prepare(
                    'SELECT id, username, password_hash
                     FROM admin_users
                     WHERE username = :username
                     LIMIT 1'
                );
                $stmt->execute([':username' => $username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password_hash'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username']  = $user['username'];
                    header('Location: /admin/');
                    exit;
                } else {
                    $error = 'Invalid username or password.';
                }
            } catch (PDOException $e) {
                $error = 'Database error. Please try again later.';
            }
        }
    }
}

$pageTitle = "Admin Login – Noodle's Pet Profiles";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-login-wrapper">
    <div class="admin-login-card">
        <h1 class="admin-login-title">🔒 Admin Login</h1>

        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="/admin/login.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken()) ?>">

            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-input"
                    required
                    autocomplete="username"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" class="btn btn--primary btn--full">Log In</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
