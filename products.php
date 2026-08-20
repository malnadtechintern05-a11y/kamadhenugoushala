<?php
/**
 * Products Listing Page — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$pdo = getDBConnection();

$page     = isset($_GET['page'])     ? max(1, (int)$_GET['page']) : 1;
$category = sanitize($_GET['cat']   ?? '');

$where  = ['p.is_active = 1'];
$params = [];
$validCats = ['Milk Products','Ghee','Gomutra','Panchamrit','Panchagavya','Organic','Other'];
if (in_array($category, $validCats, true)) {
    $where[]          = 'p.category = :cat';
    $params[':cat']   = $category;
}
$whereStr = implode(' AND ', $where);

$countSql = "SELECT COUNT(*) FROM products p WHERE $whereStr";
$dataSql  = "SELECT * FROM products p WHERE $whereStr ORDER BY p.is_featured DESC, p.name ASC";
$pagData  = paginate($pdo, $countSql, $dataSql, $params, $page);

$pageTitle  = 'Gau Products';
$activePage = 'products';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="kg-page-banner">
  <div class="container">
    <h1><i class="bi bi-shop me-2"></i>Gau Products</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Home</a></li>
        <li class="breadcrumb-item active">Products</li>
      </ol>
    </nav>
  </div>
</section>

<section class="kg-section">
  <div class="container">
    <!-- Category Filter -->
    <div class="d-flex flex-wrap gap-2 mb-5">
      <a href="<?= BASE_URL ?>/products.php" class="btn btn-sm <?= $category === '' ? 'btn-kg-primary' : 'btn-kg-outline' ?>">All</a>
      <?php foreach (['Milk Products','Ghee','Gomutra','Panchamrit','Panchagavya','Organic','Other'] as $cat): ?>
      <a href="<?= BASE_URL ?>/products.php?cat=<?= urlencode($cat) ?>"
         class="btn btn-sm <?= $category === $cat ? 'btn-kg-primary' : 'btn-kg-outline' ?>">
        <?= e($cat) ?>
      </a>
      <?php endforeach; ?>
    </div>

    <?php if (empty($pagData['items'])): ?>
    <div class="text-center py-5">
      <i class="bi bi-bag-x" style="font-size:3rem;color:var(--kg-border);"></i>
      <h4 class="mt-3 text-muted">No products found</h4>
      <a href="<?= BASE_URL ?>/products.php" class="btn btn-kg-primary mt-2">View All Products</a>
    </div>
    <?php else: ?>
    <div class="row g-4">
      <?php foreach ($pagData['items'] as $prod): ?>
      <div class="col-md-6 col-lg-3">
        <div class="kg-product-card">
          <div class="kg-product-card-img">
            <img src="<?= img_url('products', $prod['image']) ?>"
                 alt="<?= e($prod['name']) ?>"
                 loading="lazy"
                 onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'">
            <span class="kg-product-tag"><?= e($prod['category']) ?></span>
          </div>
          <div class="kg-product-card-body">
            <div class="kg-product-name"><?= e($prod['name']) ?></div>
            <div class="kg-product-unit"><?= e($prod['unit']) ?></div>
            <?php if (!empty($prod['description'])): ?>
            <p style="font-size:.8rem;color:var(--kg-text-muted);margin-bottom:.5rem;">
              <?= e(mb_strimwidth($prod['description'], 0, 80, '…')) ?>
            </p>
            <?php endif; ?>
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="kg-product-price"><?= format_inr((float)$prod['price']) ?></div>
              <?php if ($prod['stock_qty'] > 0): ?>
              <small class="kg-badge-green">In Stock</small>
              <?php else: ?>
              <small class="kg-badge-red">Out of Stock</small>
              <?php endif; ?>
            </div>
            <div class="d-flex gap-2">
              <a href="<?= BASE_URL ?>/product-details.php?id=<?= (int)$prod['id'] ?>" class="btn btn-sm btn-kg-outline flex-grow-1">Details</a>
              <a href="<?= BASE_URL ?>/product-details.php?id=<?= (int)$prod['id'] ?>#order" class="btn btn-sm btn-kg-primary flex-grow-1">Order</a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="mt-5">
      <?php
        $base = BASE_URL . '/products.php?' . http_build_query(['cat' => $category]);
        echo pagination_html($pagData, $base);
      ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- Why Our Products -->
<section class="kg-section kg-section-alt">
  <div class="container">
    <div class="kg-section-header">
      <div class="kg-section-label">Pure &amp; Natural</div>
      <h2 class="kg-section-title">Why Choose Our Products?</h2>
      <div class="kg-divider"></div>
    </div>
    <div class="row g-4">
      <?php
      $reasons = [
        ['icon'=>'shield-check-fill','title'=>'100% A2 Milk','desc'=>'All our products are made exclusively from the milk of our healthy indigenous A2 cows.'],
        ['icon'=>'leaf-fill','title'=>'No Chemicals','desc'=>'No artificial preservatives, colours, or additives. Everything is natural and pure.'],
        ['icon'=>'flower2','title'=>'Traditional Methods','desc'=>'We use age-old Bilona and Ayurvedic techniques passed down through generations.'],
        ['icon'=>'award-fill','title'=>'Ethically Sourced','desc'=>'Our cows are never harmed. They live happy, healthy lives at our Goushala.'],
      ];
      foreach ($reasons as $r): ?>
      <div class="col-md-6 col-lg-3">
        <div class="kg-seva-card h-100">
          <div class="kg-seva-icon"><i class="bi bi-<?= $r['icon'] ?>"></i></div>
          <h5><?= e($r['title']) ?></h5>
          <p style="color:var(--kg-text-muted);font-size:.88rem;"><?= e($r['desc']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
