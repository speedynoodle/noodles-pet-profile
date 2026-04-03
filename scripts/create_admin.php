<?php
/**
 * CLI script to create an admin user.
 *
 * Usage (run from the project root):
 *   php scripts/create_admin.php
 */

if (PHP_SAPI !== 'cli') {
    exit('This script must be run from the command line.' . PHP_EOL);
}

require_once __DIR__ . '/../src/config/database.php';

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
