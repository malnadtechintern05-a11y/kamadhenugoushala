<?php
/**
 * Checkout Page — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$pdo = getDBConnection();
$cart = get_cart();

if (empty($cart)) {
    redirect(BASE_URL . '/products.php');
}

$errors = [];
$old = [];
$totalAmount = 0;

// Calculate total and validate stock
$cartItems = [];
foreach ($cart as $pid => $qty) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id AND is_active = 1");
    $stmt->execute([':id' => $pid]);
    $product = $stmt->fetch();
    
    if (!$product || $product['stock_qty'] < $qty) {
        $errors[] = "Product '{$product['name']}' is out of stock or requested quantity is not available.";
    } else {
        $itemTotal = $product['price'] * $qty;
        $totalAmount += $itemTotal;
        $product['cart_qty'] = $qty;
        $product['item_total'] = $itemTotal;
        $cartItems[] = $product;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();

    $old = [
        'customer_name'    => sanitize($_POST['customer_name']    ?? ''),
        'customer_email'   => sanitize($_POST['customer_email']   ?? ''),
        'customer_phone'   => sanitize($_POST['customer_phone']   ?? ''),
        'customer_address' => sanitize($_POST['customer_address'] ?? ''),
        'payment_method'   => sanitize($_POST['payment_method']   ?? 'UPI'),
        'notes'            => sanitize($_POST['notes']            ?? ''),
    ];

    $validPM = ['UPI','Bank Transfer','Cash on Delivery','Online','Other'];

    if (empty($old['customer_name']))  $errors[] = 'Your name is required.';
    if (empty($old['customer_email']) || !is_valid_email($old['customer_email'])) $errors[] = 'Valid email required.';
    if (empty($old['customer_phone'])) $errors[] = 'Phone number is required.';
    if (empty($old['customer_address'])) $errors[] = 'Delivery address is required.';
    if (!in_array($old['payment_method'], $validPM, true)) $errors[] = 'Invalid payment method.';

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Insert into orders
            $stmtIns = $pdo->prepare("
                INSERT INTO orders (customer_name, customer_email, customer_phone, customer_address, total_amount, payment_method, notes, status)
                VALUES (:name, :email, :phone, :address, :total, :payment_method, :notes, 'Pending')
            ");
            $stmtIns->execute([
                ':name'          => $old['customer_name'],
                ':email'         => $old['customer_email'],
                ':phone'         => $old['customer_phone'],
                ':address'       => $old['customer_address'],
                ':total'         => $totalAmount,
                ':payment_method'=> $old['payment_method'],
                ':notes'         => $old['notes'],
            ]);
            $orderId = $pdo->lastInsertId();
            
            // Insert into order_items and update stock
            $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)");
            $stmtUpdateStock = $pdo->prepare("UPDATE products SET stock_qty = stock_qty - :quantity WHERE id = :id");
            
            foreach ($cartItems as $item) {
                $stmtItem->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['id'],
                    ':quantity' => $item['cart_qty'],
                    ':price' => $item['price'],
                ]);
                
                $stmtUpdateStock->execute([
                    ':quantity' => $item['cart_qty'],
                    ':id' => $item['id']
                ]);
            }
            
            $pdo->commit();
            clear_cart();
            redirect(BASE_URL . '/thank-you.php?type=order');
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "An error occurred while placing your order. Please try again.";
        }
    }
}

$pageTitle  = 'Checkout — Products';
$activePage = 'products';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="kg-page-banner">
  <div class="container">
    <h1>Checkout</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/products.php">Products</a></li>
        <li class="breadcrumb-item active">Checkout</li>
      </ol>
    </nav>
  </div>
</section>

<section class="kg-section">
  <div class="container">
    <div class="row g-5">
      <!-- Order Summary -->
      <div class="col-lg-5 order-lg-2">
        <div class="kg-form-card">
          <h5 class="text-kg-green mb-4"><i class="bi bi-bag-check me-2"></i>Order Summary</h5>
          <div class="d-flex flex-column gap-3 mb-4">
            <?php foreach ($cartItems as $item): ?>
            <div class="d-flex align-items-center pb-3 border-bottom">
              <img src="<?= img_url('products', $item['image']) ?>" alt="<?= e($item['name']) ?>" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
              <div class="ms-3 flex-grow-1">
                <h6 class="mb-0" style="font-size: 0.95rem;"><?= e($item['name']) ?></h6>
                <div class="text-muted" style="font-size: 0.8rem;"><?= format_inr((float)$item['price']) ?> x <?= $item['cart_qty'] ?></div>
              </div>
              <div class="fw-bold text-kg-green"><?= format_inr((float)$item['item_total']) ?></div>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted">Subtotal</span>
            <span class="fw-bold"><?= format_inr((float)$totalAmount) ?></span>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
            <span class="text-muted">Delivery</span>
            <span class="fw-bold text-success">Free</span>
          </div>
          <div class="d-flex justify-content-between align-items-center fs-5">
            <span class="fw-bold">Total</span>
            <span class="fw-bold text-kg-green"><?= format_inr((float)$totalAmount) ?></span>
          </div>
        </div>
      </div>

      <!-- Checkout Form -->
      <div class="col-lg-7 order-lg-1">
        <div class="kg-section-label">Checkout</div>
        <h2 class="kg-section-title">Shipping Details</h2>
        <div class="kg-divider mb-4"></div>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <ul class="mb-0">
            <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="" data-validate novalidate>
          <?= csrf_field() ?>

          <div class="row g-3">
            <div class="col-md-6 mb-3">
              <label for="customer_name" class="form-label">Your Name <span class="text-danger">*</span></label>
              <input type="text" id="customer_name" name="customer_name" class="form-control p-3"
                     value="<?= e($old['customer_name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="customer_email" class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" id="customer_email" name="customer_email" class="form-control p-3"
                     value="<?= e($old['customer_email'] ?? '') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="customer_phone" class="form-label">Phone <span class="text-danger">*</span></label>
              <input type="tel" id="customer_phone" name="customer_phone" class="form-control p-3"
                     value="<?= e($old['customer_phone'] ?? '') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="payment_method" class="form-label">Payment Method</label>
              <select name="payment_method" id="payment_method" class="form-select p-3">
                <?php foreach (['UPI','Bank Transfer','Cash on Delivery','Online','Other'] as $pm): ?>
                <option value="<?= e($pm) ?>" <?= ($old['payment_method'] ?? 'UPI') === $pm ? 'selected':'' ?>><?= e($pm) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label for="customer_address" class="form-label">Delivery Address <span class="text-danger">*</span></label>
            <textarea name="customer_address" id="customer_address" class="form-control p-3" rows="3" required><?= e($old['customer_address'] ?? '') ?></textarea>
          </div>

          <div class="mb-4">
            <label for="notes" class="form-label">Additional Notes</label>
            <textarea name="notes" id="notes" class="form-control p-3" rows="2"
                      placeholder="Special delivery instructions, allergies, etc."><?= e($old['notes'] ?? '') ?></textarea>
          </div>

          <button type="submit" class="btn btn-kg-primary w-100 py-3 fs-5 mt-2">
            <i class="bi bi-cart-check me-2"></i>Place Order — <?= format_inr((float)$totalAmount) ?>
          </button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
