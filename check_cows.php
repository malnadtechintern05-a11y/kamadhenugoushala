<?php
require 'config/database.php';
$pdo = getDBConnection();
$stmt = $pdo->query('SHOW COLUMNS FROM cows');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
