<?php
/**
 * Cows Listing Page — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$pdo = getDBConnection();

$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$filter = sanitize($_GET['status'] ?? '');
$breed  = sanitize($_GET['breed']  ?? '');

// Build filters
$where  = ['1=1'];
$params = [];
if (in_array($filter, ['Available','Adopted','Not Available'])) {
    $where[]            = 'c.adoption_status = :status';
    $params[':status']  = $filter;
}
if ($breed !== '') {
    $where[]           = 'c.breed LIKE :breed';
    $params[':breed']  = '%' . $breed . '%';
}
$whereStr = implode(' AND ', $where);

$countSql = "SELECT COUNT(*) FROM cows c WHERE $whereStr";
$dataSql  = "SELECT * FROM cows c WHERE $whereStr ORDER BY c.is_featured DESC, c.name ASC";
$pagData  = paginate($pdo, $countSql, $dataSql, $params, $page);

// Distinct breeds for filter
$breeds = $pdo->query("SELECT DISTINCT breed FROM cows WHERE breed != '' ORDER BY breed")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle  = 'Our Cows';
$pageDesc   = 'Meet the indigenous Indian cows at Kamadhenu Goushala. Gir, Sahiwal, Tharparkar and more — each with a name, a story and a home.';
$activePage = 'cows';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="kg-page-banner">
  <div class="container">
    <h1><i class="bi bi-emoji-heart-eyes me-2"></i>Our Cows</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Home</a></li>
        <li class="breadcrumb-item active">Our Cows</li>
      </ol>
    </nav>
  </div>
</section>

<section class="kg-section">
  <div class="container">

    <!-- Filters -->
    <form method="get" action="" class="row g-3 mb-5 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Filter by Status</label>
        <select name="status" class="form-select" onchange="this.form.submit()">
          <option value="">All Statuses</option>
          <option value="Available"     <?= $filter === 'Available'     ? 'selected' : '' ?>>Available for Adoption</option>
          <option value="Adopted"       <?= $filter === 'Adopted'       ? 'selected' : '' ?>>Adopted</option>
          <option value="Not Available" <?= $filter === 'Not Available' ? 'selected' : '' ?>>Not Available</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Filter by Breed</label>
        <select name="breed" class="form-select" onchange="this.form.submit()">
          <option value="">All Breeds</option>
          <?php foreach ($breeds as $b): ?>
          <option value="<?= e($b) ?>" <?= $breed === $b ? 'selected' : '' ?>><?= e($b) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <a href="<?= BASE_URL ?>/cows.php" class="btn btn-kg-outline w-100">
          <i class="bi bi-x-circle me-2"></i>Clear Filters
        </a>
      </div>
    </form>

    <!-- Results count -->
    <p class="mb-4 text-muted">
      Showing <strong><?= count($pagData['items']) ?></strong> of <strong><?= $pagData['total'] ?></strong> cows
    </p>

    <?php if (empty($pagData['items'])): ?>
    <div class="text-center py-5">
      <i class="bi bi-search" style="font-size:3rem;color:var(--kg-border);"></i>
      <h4 class="mt-3 text-muted">No cows found</h4>
      <p class="text-muted">Try clearing your filters.</p>
      <a href="<?= BASE_URL ?>/cows.php" class="btn btn-kg-primary mt-2">View All Cows</a>
    </div>
    <?php else: ?>
    <div class="row g-4">
      <?php foreach ($pagData['items'] as $cow): ?>
      <div class="col-md-6 col-lg-4">
        <div class="kg-cow-card">
          <div class="kg-cow-card-img">
            <img src="<?= img_url('cows', $cow['image']) ?>"
                 alt="<?= e($cow['name']) ?>"
                 loading="lazy"
                 onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'">
            <?php
              $badgeClass = match($cow['adoption_status']) {
                'Available' => 'available',
                'Adopted'   => 'adopted',
                default     => 'unavailable',
              };
            ?>
            <span class="kg-cow-card-badge <?= $badgeClass ?>"><?= e($cow['adoption_status']) ?></span>
            <?php if ($cow['is_featured']): ?>
            <span class="position-absolute top-0 start-0 m-2 kg-badge-gold">Featured</span>
            <?php endif; ?>
          </div>
          <div class="kg-cow-card-body">
            <h3 class="kg-cow-card-name"><?= e($cow['name']) ?></h3>
            <p class="kg-cow-card-breed"><?= e($cow['breed']) ?></p>
            <div class="kg-cow-card-meta">
              <span><i class="bi bi-calendar3"></i> <?= (int)$cow['age'] ?> yrs</span>
              <span><i class="bi bi-gender-ambiguous"></i> <?= e($cow['gender']) ?></span>
              <span><i class="bi bi-palette"></i> <?= e($cow['color']) ?></span>
            </div>
            <?php if (!empty($cow['description'])): ?>
            <p style="font-size:.83rem;color:var(--kg-text-muted);margin-bottom:.8rem;">
              <?= e(mb_strimwidth($cow['description'], 0, 90, '…')) ?>
            </p>
            <?php endif; ?>
            <div class="d-flex gap-2">
              <a href="<?= BASE_URL ?>/cow-details.php?id=<?= (int)$cow['id'] ?>" class="btn btn-sm btn-kg-primary flex-grow-1">
                View Details
              </a>
              <?php if ($cow['adoption_status'] === 'Available'): ?>
              <a href="<?= BASE_URL ?>/adopt.php?cow_id=<?= (int)$cow['id'] ?>" class="btn btn-sm btn-kg-gold">
                Adopt
              </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <div class="mt-5">
      <?php
        $base = BASE_URL . '/cows.php?' . http_build_query(['status' => $filter, 'breed' => $breed]);
        echo pagination_html($pagData, $base);
      ?>
    </div>
    <?php endif; ?>

  </div>
</section>

<!-- Adopt CTA -->
<section class="kg-section kg-section-green">
  <div class="container text-center">
    <h2 style="color:#fff;" class="mb-3">Ready to Adopt a Sacred Cow?</h2>
    <p style="color:rgba(255,255,255,.8);max-width:500px;margin:0 auto 2rem;">
      Give a cow a loving home from afar. Receive monthly updates and the blessings of Gau Mata.
    </p>
    <a href="<?= BASE_URL ?>/adopt.php" class="btn-kg-gold btn btn-lg">
      <i class="bi bi-heart-fill me-2"></i>Adopt Now
    </a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
