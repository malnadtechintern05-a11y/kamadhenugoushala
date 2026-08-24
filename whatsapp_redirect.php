<?php
/**
 * WhatsApp Tracking Redirect — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$cow_id = isset($_GET['cow_id']) ? (int)$_GET['cow_id'] : 0;

if ($cow_id > 0) {
    $pdo = getDBConnection();
    
    // Fetch cow details
    $stmt = $pdo->prepare("SELECT name, breed, whatsapp_number, whatsapp_message FROM cows WHERE id = :id");
    $stmt->execute([':id' => $cow_id]);
    $cow = $stmt->fetch();
    
    if ($cow) {
        // Log the click
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $logStmt = $pdo->prepare("INSERT INTO whatsapp_logs (cow_id, ip_address) VALUES (:cow_id, :ip)");
        $logStmt->execute([':cow_id' => $cow_id, ':ip' => $ip]);

        // Phone logic
        $waPhone = '';
        if (!empty($cow['whatsapp_number'])) {
            $waPhone = preg_replace('/[^0-9]/', '', $cow['whatsapp_number']);
        } elseif (defined('WA_PHONE_COWS') && WA_PHONE_COWS !== '') {
            $waPhone = preg_replace('/[^0-9]/', '', WA_PHONE_COWS);
        } else {
            $waPhone = preg_replace('/[^0-9]/', '', SITE_PHONE);
        }

        // Message logic
        $msgText = '';
        if (!empty($cow['whatsapp_message'])) {
            $msgText = $cow['whatsapp_message'];
        } elseif (defined('WA_DEFAULT_MSG_COWS') && WA_DEFAULT_MSG_COWS !== '') {
            $msgText = str_replace('{cow_name}', $cow['name'], WA_DEFAULT_MSG_COWS);
        } else {
            $msgText = "Hello, I am interested in adopting " . $cow['name'] . " (Breed: " . $cow['breed'] . "). Please provide more details.";
        }
            
        $waText = urlencode($msgText);
        $waUrl = "https://wa.me/{$waPhone}?text={$waText}";
        
        // Redirect
        header("Location: $waUrl");
        exit;
    }
}

// Fallback for generic adoption inquiries if cow_id is not provided
if (defined('CHECKOUT_MODE_COWS') && CHECKOUT_MODE_COWS === 'whatsapp') {
    $waPhone = preg_replace('/[^0-9]/', '', SITE_PHONE);
    $waText = urlencode("Hello, I am interested in adopting a cow. Please provide more details about the adoption process.");
    header("Location: https://wa.me/{$waPhone}?text={$waText}");
    exit;
}

// Fallback if something fails
header("Location: " . BASE_URL . "/cows.php");
exit;
