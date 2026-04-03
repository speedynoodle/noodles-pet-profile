<?php
/**
 * CLI script to create an admin user.
 *
 * LOCAL DEVELOPMENT (run from the project root):
 *   php scripts/create_admin.php
 *
 * IONOS / SHARED HOSTING via SSH:
 *   The src/ directory is uploaded to httpdocs/ on the server, so the
 *   database.php config lives at a different relative path.  Pass the
 *   path explicitly:
 *
 *   php create_admin.php --db-config=/path/to/httpdocs/config/database.php
 *
 *   Example (typical IONOS home directory layout):
 *   php ~/create_admin.php --db-config=~/httpdocs/config/database.php
 *
 * ALTERNATIVE – phpMyAdmin:
 *   If you don't have SSH access you can insert an admin user directly
 *   in phpMyAdmin.  Run the following query (replace the values):
 *
 *   INSERT INTO admin_users (username, password_hash)
 *   VALUES ('admin', '$2y$12$...');   -- generate hash with php -r "echo password_hash('yourpassword', PASSWORD_BCRYPT);"
 */

if (PHP_SAPI !== 'cli') {
    exit('This script must be run from the command line.' . PHP_EOL);
}

// Parse optional --db-config= argument
$dbConfigPath = null;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--db-config=')) {
        $dbConfigPath = substr($arg, strlen('--db-config='));
        break;
    }
}

if ($dbConfigPath === null) {
    // Default: project root structure (local dev)
    $dbConfigPath = __DIR__ . '/../src/config/database.php';
}

if (!file_exists($dbConfigPath)) {
    fwrite(STDERR, "Error: database config not found at '{$dbConfigPath}'.\n");
    fwrite(STDERR, "Use --db-config=/path/to/database.php to specify the location.\n");
    exit(1);
}

require_once $dbConfigPath;

// Prompt for username
fwrite(STDOUT, 'Enter admin username: ');
$username = trim(fgets(STDIN));

if ($username === '') {
    fwrite(STDERR, "Error: Username cannot be empty.\n");
    exit(1);
}

// Prompt for password (hidden on Unix-like systems)
if (PHP_OS_FAMILY !== 'Windows') {
    system('stty -echo');
    fwrite(STDOUT, 'Enter admin password (min 8 characters): ');
    $password = trim(fgets(STDIN));
    system('stty echo');
    fwrite(STDOUT, "\n");
} else {
    fwrite(STDOUT, 'Enter admin password (min 8 characters): ');
    $password = trim(fgets(STDIN));
}

if (strlen($password) < 8) {
    fwrite(STDERR, "Error: Password must be at least 8 characters.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_BCRYPT);

try {
    $pdo  = getDbConnection();
    $stmt = $pdo->prepare(
        'INSERT INTO admin_users (username, password_hash) VALUES (:username, :hash)'
    );
    $stmt->execute([':username' => $username, ':hash' => $hash]);
    fwrite(STDOUT, "Admin user '{$username}' created successfully.\n");
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'Duplicate') || str_contains($e->getMessage(), '1062')) {
        fwrite(STDERR, "Error: Username '{$username}' already exists.\n");
    } else {
        fwrite(STDERR, 'Database error: ' . $e->getMessage() . "\n");
    }
    exit(1);
}
