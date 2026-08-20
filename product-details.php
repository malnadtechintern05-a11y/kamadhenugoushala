<?php
/**
 * Product Detail & Order Page — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$pdo = getDBConnection();
$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) redirect(BASE_URL . '/products.php');

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id AND is_active = 1");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();
if (!$product) redirect(BASE_URL . '/products.php');

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();

    $old = [
        'customer_name'    => sanitize($_POST['customer_name']    ?? ''),
        'customer_email'   => sanitize($_POST['customer_email']   ?? ''),
        'customer_phone'   => sanitize($_POST['customer_phone']   ?? ''),
        'customer_address' => sanitize($_POST['customer_address'] ?? ''),
        'quantity'         => (int)($_POST['quantity']            ?? 1),
        'payment_method'   => sanitize($_POST['payment_method']   ?? 'UPI'),
        'notes'            => sanitize($_POST['notes']            ?? ''),
    ];

    $validPM = ['UPI','Bank Transfer','Cash on Delivery','Online','Other'];

    if (empty($old['customer_name']))  $errors[] = 'Your name is required.';
    if (empty($old['customer_email']) || !is_valid_email($old['customer_email'])) $errors[] = 'Valid email required.';
    if (empty($old['customer_phone'])) $errors[] = 'Phone number is required.';
    if (empty($old['customer_address'])) $errors[] = 'Delivery address is required.';
    if ($old['quantity'] < 1) $errors[] = 'Quantity must be at least 1.';
    if (!in_array($old['payment_method'], $validPM, true)) $errors[] = 'Invalid payment method.';
    if ($old['quantity'] > (int)$product['stock_qty']) $errors[] = 'Quantity exceeds available stock.';

    if (empty($errors)) {
        $totalAmount = $old['quantity'] * (float)$product['price'];
        $stmtIns = $pdo->prepare("
            INSERT INTO orders (product_id, customer_name, customer_email, customer_phone, customer_address, quantity, total_amount, payment_method, notes, status)
            VALUES (:product_id, :name, :email, :phone, :address, :qty, :total, :payment_method, :notes, 'Pending')
        ");
        $stmtIns->execute([
            ':product_id'    => $id,
            ':name'          => $old['customer_name'],
            ':email'         => $old['customer_email'],
            ':phone'         => $old['customer_phone'],
            ':address'       => $old['customer_address'],
            ':qty'           => $old['quantity'],
            ':total'         => $totalAmount,
            ':payment_method'=> $old['payment_method'],
            ':notes'         => $old['notes'],
        ]);
        redirect(BASE_URL . '/thank-you.php?type=order');
    }
}

$pageTitle  = e($product['name']) . ' — Products';
$activePage = 'products';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="kg-page-banner">
  <div class="container">
    <h1><?= e($product['name']) ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/products.php">Products</a></li>
        <li class="breadcrumb-item active"><?= e($product['name']) ?></li>
      </ol>
    </nav>
  </div>
</section>

<section class="kg-section">
  <div class="container">
    <div class="row g-5">
      <!-- Product Info -->
      <div class="col-lg-5">
        <img src="<?= img_url('products', $product['image']) ?>"
             alt="<?= e($product['name']) ?>"
             class="kg-cow-detail-img"
             loading="eager"
             onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'">
        <div class="mt-3 d-flex align-items-center gap-2 flex-wrap">
          <span class="kg-badge-green"><?= e($product['category']) ?></span>
          <?php if ($product['stock_qty'] > 0): ?>
          <span class="kg-badge-green">In Stock (<?= (int)$product['stock_qty'] ?> available)</span>
          <?php else: ?>
          <span class="kg-badge-red">Out of Stock</span>
          <?php endif; ?>
        </div>
      </div>

      <!-- Details & Order -->
      <div class="col-lg-7">
        <div class="kg-section-label"><?= e($product['category']) ?></div>
        <h2 class="kg-section-title"><?= e($product['name']) ?></h2>
        <div class="kg-divider mb-3"></div>
        <div style="font-size:2rem;font-weight:700;color:var(--kg-green);margin-bottom:.5rem;">
          <?= format_inr((float)$product['price']) ?>
          <small style="font-size:.9rem;color:var(--kg-text-muted);font-weight:400;"> / <?= e($product['unit']) ?></small>
        </div>
        <?php if (!empty($product['description'])): ?>
        <p style="color:var(--kg-text-muted);line-height:1.8;margin-bottom:1.5rem;">
          <?= nl2br(e($product['description'])) ?>
        </p>
        <?php endif; ?>

        <!-- Order Form -->
        <div id="order" class="kg-form-card mt-0">
          <h5 class="text-kg-green mb-3"><i class="bi bi-cart-fill me-2"></i>Place Your Order</h5>

          <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>

          <?php if ($product['stock_qty'] <= 0): ?>
          <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>This product is currently out of stock. Please check back later.
          </div>
          <?php else: ?>
          <form method="POST" action="" data-validate novalidate>
            <?= csrf_field() ?>

            <div class="row g-3">
              <div class="col-md-6 mb-3">
                <label for="customer_name" class="form-label">Your Name <span class="text-danger">*</span></label>
                <input type="text" id="customer_name" name="customer_name" class="form-control"
                       value="<?= e($old['customer_name'] ?? '') ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="customer_email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" id="customer_email" name="customer_email" class="form-control"
                       value="<?= e($old['customer_email'] ?? '') ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="customer_phone" class="form-label">Phone <span class="text-danger">*</span></label>
                <input type="tel" id="customer_phone" name="customer_phone" class="form-control"
                       value="<?= e($old['customer_phone'] ?? '') ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                <input type="number" id="quantity" name="quantity" class="form-control"
                       min="1" max="<?= (int)$product['stock_qty'] ?>"
                       value="<?= (int)($old['quantity'] ?? 1) ?>" required
                       oninput="updateTotal(this.value)">
              </div>
            </div>

            <div class="mb-3">
              <label for="customer_address" class="form-label">Delivery Address <span class="text-danger">*</span></label>
              <textarea name="customer_address" id="customer_address" class="form-control" rows="2" required><?= e($old['customer_address'] ?? '') ?></textarea>
            </div>

            <div class="row g-3">
              <div class="col-md-6 mb-3">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select name="payment_method" id="payment_method" class="form-select">
                  <?php foreach (['UPI','Bank Transfer','Cash on Delivery','Online','Other'] as $pm): ?>
                  <option value="<?= e($pm) ?>" <?= ($old['payment_method'] ?? 'UPI') === $pm ? 'selected':'' ?>><?= e($pm) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Total Amount</label>
                <div class="form-control bg-light fw-700 text-kg-green" id="totalDisplay">
                  <?= format_inr((float)$product['price']) ?>
                </div>
              </div>
            </div>

            <div class="mb-4">
              <label for="notes" class="form-label">Additional Notes</label>
              <textarea name="notes" id="notes" class="form-control" rows="2"
                        placeholder="Special delivery instructions, allergies, etc."><?= e($old['notes'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-kg-primary w-100 py-3">
              <i class="bi bi-cart-check me-2"></i>Place Order
            </button>
          </form>

          <script>
          const unitPrice = <?= (float)$product['price'] ?>;
          function updateTotal(qty) {
            const q = parseInt(qty) || 1;
            const total = (unitPrice * q).toFixed(2);
            const formatted = '₹' + parseFloat(total).toLocaleString('en-IN', {minimumFractionDigits:2});
            document.getElementById('totalDisplay').textContent = formatted;
          }
          </script>
          <?php endif; ?>
        </div>

        <div class="mt-3">
          <a href="<?= BASE_URL ?>/products.php" class="btn btn-kg-outline">
            <i class="bi bi-arrow-left me-2"></i>Back to Products
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
