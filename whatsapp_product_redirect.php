<?php
/**
 * WhatsApp Product Checkout Redirect — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
if ($qty < 1) $qty = 1;

if ($product_id > 0) {
    $pdo = getDBConnection();
    
    // Fetch product details
    $stmt = $pdo->prepare("SELECT name, price, whatsapp_message FROM products WHERE id = :id");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch();
    
    if ($product) {
        // Generate WhatsApp URL
        $waPhone = '';
        if (defined('WA_PHONE_PRODUCTS') && WA_PHONE_PRODUCTS !== '') {
            $waPhone = preg_replace('/[^0-9]/', '', WA_PHONE_PRODUCTS);
        } else {
            $waPhone = preg_replace('/[^0-9]/', '', SITE_PHONE);
        }
        
        $msgText = '';
        if (!empty($product['whatsapp_message'])) {
            $msgText = $product['whatsapp_message'];
            // Still allow placeholders in the individual message
            $msgText = str_replace('{product_name}', $product['name'], $msgText);
            $msgText = str_replace('{qty}', (string)$qty, $msgText);
            $msgText = str_replace('{price}', format_inr((float)$product['price']), $msgText);
        } elseif (defined('WA_DEFAULT_MSG_PRODUCTS') && WA_DEFAULT_MSG_PRODUCTS !== '') {
            $msgText = WA_DEFAULT_MSG_PRODUCTS;
            $msgText = str_replace('{product_name}', $product['name'], $msgText);
            $msgText = str_replace('{qty}', (string)$qty, $msgText);
            $msgText = str_replace('{price}', format_inr((float)$product['price']), $msgText);
        } else {
            $msgText = "Hello, I am interested in purchasing " . $qty . "x '" . $product['name'] . "' for " . format_inr((float)$product['price']) . " each. Please provide more details on how to order.";
        }
            
        $waText = urlencode($msgText);
        $waUrl = "https://wa.me/{$waPhone}?text={$waText}";
        
        // Redirect
        header("Location: $waUrl");
        exit;
    }
}

// Fallback if no product found
header("Location: " . BASE_URL . "/products.php");
exit;
