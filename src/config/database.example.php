<?php
/**
 * Database configuration and connection helper – EXAMPLE FILE.
 *
 * Copy this file to database.php and fill in your credentials:
 *
 *   cp src/config/database.example.php src/config/database.php
 *
 * For local development the defaults below match a standard MySQL
 * installation with no root password (e.g. Homebrew, XAMPP, Laragon).
 * Adjust the values to suit your local setup.
 *
 * ⚠️  Never commit database.php – it is listed in .gitignore.
 */

define('DB_HOST',     'localhost');   // MySQL host
define('DB_PORT',     '3306');        // MySQL port
define('DB_NAME',     'pet_profiles');// Database name (created by sql/init.sql)
define('DB_USER',     'root');        // MySQL username
define('DB_PASSWORD', '');            // MySQL password (often empty for local dev)
define('DB_CHARSET',  'utf8mb4');

/**
 * Returns a PDO connection instance (singleton).
 */
function getDbConnection(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
    }

    return $pdo;
}
