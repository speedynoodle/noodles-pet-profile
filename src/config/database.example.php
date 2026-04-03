<?php
/**
 * Database configuration and connection helper – EXAMPLE FILE.
 *
 * Copy this file to database.php and fill in your credentials:
 *
 *   cp src/config/database.example.php src/config/database.php
 *
 * LOCAL DEVELOPMENT
 * -----------------
 * The defaults below match a standard MySQL installation with no root
 * password (e.g. Homebrew, XAMPP, Laragon). Adjust to suit your setup.
 *
 * IONOS WEB HOSTING
 * -----------------
 * 1. In the IONOS Control Panel go to Hosting → Databases → your DB.
 * 2. Copy the connection details shown there (host, name, user, password).
 *    - DB_HOST  : usually 'localhost' on current IONOS accounts; older
 *                 accounts may have a hostname like db12345678.hosting-data.io
 *    - DB_NAME  : the auto-generated name assigned by IONOS, e.g. dbs12345678
 *    - DB_USER  : the DB username shown in the control panel
 *    - DB_PASSWORD : the password you set when creating the database
 *
 * ⚠️  Never commit database.php – it is listed in .gitignore.
 */

define('DB_HOST',     'localhost');    // IONOS: check control panel – often 'localhost'
define('DB_PORT',     '3306');         // MySQL port – almost always 3306
define('DB_NAME',     'pet_profiles'); // IONOS: use the name shown in control panel (e.g. dbs12345678)
define('DB_USER',     'root');         // IONOS: use the username shown in control panel
define('DB_PASSWORD', '');             // IONOS: use the password you set for this DB
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
