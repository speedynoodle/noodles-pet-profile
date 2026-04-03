<?php
/**
 * Database configuration and connection helper.
 *
 * Fill in your IONOS MySQL credentials below.
 * These values can be found in your IONOS Control Panel under
 * Hosting → Databases → your database details.
 */

define('DB_HOST',     'localhost');          // Usually 'localhost' on IONOS shared hosting
define('DB_PORT',     '3306');
define('DB_NAME',     'your_database_name'); // IONOS database name (e.g. dbs12345678)
define('DB_USER',     'your_database_user'); // IONOS database username
define('DB_PASSWORD', 'your_db_password');   // IONOS database password
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
