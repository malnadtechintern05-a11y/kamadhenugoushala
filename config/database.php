<?php
/**
 * Database Configuration — Kamadhenu Goushala
 * Edit DB_HOST, DB_NAME, DB_USER, DB_PASS to match your environment.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'kamadhenu_goushala');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function getDBConnection(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Do not expose credentials or full error in production
            error_log('Database connection failed: ' . $e->getMessage());
            die('<p style="color:red;font-family:sans-serif;padding:2rem;">Database connection error. Please try again later.</p>');
        }
    }
    return $pdo;
}
