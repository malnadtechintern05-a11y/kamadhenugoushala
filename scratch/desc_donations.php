<?php
require 'config/database.php';
$pdo = getDBConnection();
$stmt = $pdo->query('DESCRIBE donations');
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $r['Field'] . ' - ' . $r['Type'] . "\n";
}
