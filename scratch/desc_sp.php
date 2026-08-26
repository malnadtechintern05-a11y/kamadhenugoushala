<?php
require 'config/database.php';
$pdo = getDBConnection();
$stmt = $pdo->query('DESCRIBE supporter_payments');
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $r['Field'] . ' - ' . $r['Type'] . "\n";
}
