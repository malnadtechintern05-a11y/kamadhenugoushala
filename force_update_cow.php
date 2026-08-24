<?php
require 'config/database.php';
$pdo = getDBConnection();
$pdo->query("UPDATE cows SET whatsapp_message = NULL WHERE id = 1");
