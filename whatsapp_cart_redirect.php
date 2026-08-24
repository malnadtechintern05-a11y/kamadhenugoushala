<?php
/**
 * WhatsApp Cart Checkout Redirect — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$cart = get_cart();

if (empty($cart)) {
    header("Location: " . BASE_URL . "/products.php");
    exit;
}

$pdo = getDBConnection();
$total = 0;
$itemsText = "";

foreach ($cart as $pid => $qty) {
    $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = :id");
    $stmt->execute([':id' => $pid]);
    $product = $stmt->fetch();
    
    if ($product) {
        $item_total = $product['price'] * $qty;
        $total += $item_total;
        $itemsText .= "- " . $qty . "x " . $product['name'] . " (" . format_inr((float)$item_total) . ")\n";
    }
}

if ($total > 0) {
    $waPhone = '';
    if (defined('WA_PHONE_PRODUCTS') && WA_PHONE_PRODUCTS !== '') {
        $waPhone = preg_replace('/[^0-9]/', '', WA_PHONE_PRODUCTS);
    } else {
        $waPhone = preg_replace('/[^0-9]/', '', SITE_PHONE);
    }
    
    $msgText = "Hello, I would like to place an order for the following items:\n\n";
    $msgText .= $itemsText;
    $msgText .= "\n*Total Amount:* " . format_inr((float)$total) . "\n\n";
    $msgText .= "Please let me know the next steps for payment and delivery.";
    
    $waText = urlencode($msgText);
    $waUrl = "https://wa.me/{$waPhone}?text={$waText}";
    
    // Clear cart after redirecting to WhatsApp checkout
    clear_cart();
    
    header("Location: $waUrl");
    exit;
}

header("Location: " . BASE_URL . "/products.php");
exit;
