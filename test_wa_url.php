<?php
$_GET['cow_id'] = 1;
require 'config/config.php';
require 'config/database.php';
require 'includes/functions.php';

$cow_id = isset($_GET['cow_id']) ? (int)$_GET['cow_id'] : 0;

if ($cow_id > 0) {
    $pdo = getDBConnection();
    
    // Fetch cow details
    $stmt = $pdo->prepare("SELECT name, breed, whatsapp_number, whatsapp_message FROM cows WHERE id = :id");
    $stmt->execute([':id' => $cow_id]);
    $cow = $stmt->fetch();
    
    if ($cow) {
        $rawPhone = !empty($cow['whatsapp_number']) ? $cow['whatsapp_number'] : SITE_PHONE;
        $waPhone = preg_replace('/[^0-9]/', '', $rawPhone);
        
        $msgText = !empty($cow['whatsapp_message']) 
            ? $cow['whatsapp_message'] 
            : "Hello, I am interested in adopting " . $cow['name'] . " (Breed: " . $cow['breed'] . "). Please provide more details.";
            
        $waText = urlencode($msgText);
        $waUrl = "https://wa.me/{$waPhone}?text={$waText}";
        
        echo "URL: $waUrl\n";
    }
}
