<?php
/**
 * Gallery Page — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$pdo = getDBConnection();

$page     = isset($_GET['page'])  ? max(1, (int)$_GET['page']) : 1;
$category = sanitize($_GET['cat'] ?? '');

$where  = ['1=1'];
$params = [];
if ($category !== '') {
    $where[]         = 'g.category = :cat';
    $params[':cat']  = $category;
}
$whereStr = implode(' AND ', $where);

$countSql = "SELECT COUNT(*) FROM gallery g WHERE $whereStr";
$dataSql  = "SELECT * FROM gallery g WHERE $whereStr ORDER BY g.sort_order ASC, g.created_at DESC";
$pagData  = paginate($pdo, $countSql, $dataSql, $params, $page, 18);

$categories = $pdo->query("SELECT DISTINCT category FROM gallery WHERE category != '' ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle  = 'Gallery';
$activePage = 'gallery';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="kg-page-banner">
  <div class="container">
    <h1><i class="bi bi-images me-2"></i>Gallery</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Home</a></li>
        <li class="breadcrumb-item active">Gallery</li>
      </ol>
    </nav>
  </div>
</section>

<section class="kg-section">
  <div class="container">
    <!-- Category Filters -->
    <?php if (!empty($categories)): ?>
    <div class="d-flex flex-wrap gap-2 mb-5">
      <a href="<?= BASE_URL ?>/gallery.php" class="btn btn-sm <?= $category === '' ? 'btn-kg-primary' : 'btn-kg-outline' ?>">All</a>
      <?php foreach ($categories as $cat): ?>
      <a href="<?= BASE_URL ?>/gallery.php?cat=<?= urlencode($cat) ?>"
         class="btn btn-sm <?= $category === $cat ? 'btn-kg-primary' : 'btn-kg-outline' ?>">
        <?= e($cat) ?>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (empty($pagData['items'])): ?>
    <div class="text-center py-5">
      <i class="bi bi-images" style="font-size:3rem;color:var(--kg-border);"></i>
      <h4 class="mt-3 text-muted">Gallery coming soon</h4>
      <p class="text-muted">Photos will be uploaded here shortly.</p>
    </div>
    <?php else: ?>
    <div class="row g-3">
      <?php foreach ($pagData['items'] as $item): ?>
      <div class="col-6 col-md-4 col-lg-3">
        <div class="kg-gallery-item"
             data-lightbox="<?= img_url('gallery', $item['image']) ?>"
             data-title="<?= e($item['title']) ?>">
          <img src="<?= img_url('gallery', $item['image']) ?>"
               alt="<?= e($item['title']) ?>"
               loading="lazy"
               onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'"
               style="width:100%;height:220px;object-fit:cover;">
          <div class="kg-gallery-overlay">
            <div>
              <div class="kg-gallery-title"><?= e($item['title']) ?></div>
              <?php if (!empty($item['category'])): ?>
              <small style="color:var(--kg-gold-light);font-size:.72rem;"><?= e($item['category']) ?></small>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="mt-5">
      <?php
        $base = BASE_URL . '/gallery.php?' . http_build_query(['cat' => $category]);
        echo pagination_html($pagData, $base);
      ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
