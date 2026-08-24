<?php
require_once __DIR__ . '/config/database.php';
$pdo = getDBConnection();
$stmt = $pdo->query('SHOW COLUMNS FROM settings');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
