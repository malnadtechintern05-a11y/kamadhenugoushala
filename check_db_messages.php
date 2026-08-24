<?php
require 'config/database.php';
$pdo = getDBConnection();
$stmt = $pdo->query('SELECT id, name, whatsapp_message FROM cows');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
