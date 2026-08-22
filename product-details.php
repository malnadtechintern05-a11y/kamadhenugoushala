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
          <?php if ($product['stock_qty'] <= 0): ?>
          <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>This product is currently out of stock. Please check back later.
          </div>
          <?php else: ?>
          <div class="d-flex flex-column gap-3">
            <div>
              <label for="quantity" class="form-label text-muted fw-bold mb-1">Quantity</label>
              <input type="number" id="detail-quantity" class="form-control" style="max-width: 150px;"
                     min="1" max="<?= (int)$product['stock_qty'] ?>"
                     value="1">
            </div>
            <button type="button" class="btn btn-kg-primary py-3 px-4" style="max-width: 250px;" onclick="addDetailToCart(<?= (int)$product['id'] ?>)">
              <i class="bi bi-cart-plus me-2"></i>Add to Cart
            </button>
          </div>

          <script>
          function addDetailToCart(productId) {
              const qty = document.getElementById('detail-quantity').value;
              addToCart(productId, parseInt(qty));
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
