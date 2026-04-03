<?php
/**
 * Database configuration and connection helper.
 * Reads credentials from environment variables (set via Docker Compose).
 */

define('DB_HOST',     getenv('DB_HOST')     ?: 'localhost');
define('DB_PORT',     getenv('DB_PORT')     ?: '3306');
define('DB_NAME',     getenv('DB_NAME')     ?: 'pet_profiles');
define('DB_USER',     getenv('DB_USER')     ?: 'pet_user');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'pet_password');
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
