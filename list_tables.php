<?php
require 'config/database.php';
$pdo = getDBConnection();
$stmt = $pdo->query('SHOW TABLES');
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    echo $row[0] . "\n";
}
