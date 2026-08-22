<?php
/**
 * Cart AJAX Action Handler — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$action = $_REQUEST['action'] ?? '';
$pdo = getDBConnection();

if ($action === 'add' || $action === 'update') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product.']);
        exit;
    }

    // Check stock
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id AND is_active = 1");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found.']);
        exit;
    }

    if ($action === 'add') {
        $current_qty = get_cart()[$product_id] ?? 0;
        $new_qty = $current_qty + $quantity;
        if ($new_qty > $product['stock_qty']) {
            echo json_encode(['success' => false, 'message' => 'Cannot add more than available stock.']);
            exit;
        }
        add_to_cart($product_id, $quantity);
    } else {
        if ($quantity > $product['stock_qty']) {
             echo json_encode(['success' => false, 'message' => 'Cannot update to more than available stock.']);
             exit;
        }
        update_cart($product_id, $quantity);
    }

    echo json_encode(['success' => true, 'count' => get_cart_count()]);
    exit;
}

if ($action === 'view') {
    $cart = get_cart();
    if (empty($cart)) {
        echo '<div class="text-center text-muted my-5"><i class="bi bi-cart-x fs-1 mb-3 d-block"></i>Your cart is empty.</div>';
        echo '<div class="mt-auto p-3 border-top"><a href="'.BASE_URL.'/products.php" class="btn btn-kg-outline w-100">Shop Now</a></div>';
        exit;
    }

    $total = 0;
    $html = '<div class="flex-grow-1 overflow-auto p-3">';
    
    foreach ($cart as $pid => $qty) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $pid]);
        $product = $stmt->fetch();
        
        if ($product) {
            $item_total = $product['price'] * $qty;
            $total += $item_total;
            $img = img_url('products', $product['image']);
            $price_fmt = format_inr((float)$product['price']);
            $item_total_fmt = format_inr((float)$item_total);
            $name = e($product['name']);
            
            $html .= <<<HTML
            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                <img src="{$img}" alt="{$name}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                <div class="ms-3 flex-grow-1">
                    <h6 class="mb-1" style="font-size: 0.95rem;">{$name}</h6>
                    <div class="text-muted" style="font-size: 0.8rem;">{$price_fmt} x {$qty}</div>
                    <div class="fw-bold mt-1 text-kg-green" style="font-size: 0.9rem;">{$item_total_fmt}</div>
                </div>
                <div class="d-flex flex-column align-items-center ms-2 gap-1">
                    <div class="input-group input-group-sm" style="width: 80px;">
                        <button class="btn btn-outline-secondary px-2" type="button" onclick="updateCartItem({$pid}, {$qty} - 1)">-</button>
                        <input type="text" class="form-control text-center px-1" value="{$qty}" readonly>
                        <button class="btn btn-outline-secondary px-2" type="button" onclick="updateCartItem({$pid}, {$qty} + 1)">+</button>
                    </div>
                    <button class="btn btn-link text-danger p-0 mt-1" style="font-size: 0.75rem; text-decoration: none;" onclick="removeFromCart({$pid})">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
            </div>
HTML;
        }
    }
    
    $html .= '</div>';
    
    $total_fmt = format_inr((float)$total);
    $html .= <<<HTML
    <div class="mt-auto p-3 border-top bg-light">
        <div class="d-flex justify-content-between mb-3 fw-bold fs-5">
            <span>Total:</span>
            <span class="text-kg-green">{$total_fmt}</span>
        </div>
        <a href="checkout.php" class="btn btn-kg-primary w-100 py-2">
            <i class="bi bi-shield-lock me-2"></i>Secure Checkout
        </a>
    </div>
HTML;

    echo $html;
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action.']);
