<?php
require 'config/database.php';
$pdo = getDBConnection();
try {
    $pdo->query('ALTER TABLE products ADD COLUMN whatsapp_message TEXT DEFAULT NULL AFTER is_featured');
    echo 'Column added successfully.';
} catch(PDOException $e) {
    echo $e->getMessage();
}
