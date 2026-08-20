<?php
/**
 * Admin Gallery — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$pdo = getDBConnection();
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$pagData = paginate($pdo,
    "SELECT COUNT(*) FROM gallery",
    "SELECT * FROM gallery ORDER BY sort_order ASC, created_at DESC",
    [], $page, 18
);

$adminPageTitle  = 'Manage Gallery';
$adminActivePage = 'gallery';
require_once __DIR__ . '/includes/admin_layout_header.php';
echo flash_alert();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <p class="text-muted mb-0"><?= $pagData['total'] ?> images</p>
  <a href="<?= BASE_URL ?>/admin/gallery-add.php" class="btn btn-kg-primary">
    <i class="bi bi-image me-2"></i>Upload Photo
  </a>
</div>

<?php if (empty($pagData['items'])): ?>
<div class="text-center py-5">
  <i class="bi bi-images" style="font-size:3rem;color:var(--kg-border);"></i>
  <h5 class="mt-3 text-muted">No gallery images yet.</h5>
  <a href="<?= BASE_URL ?>/admin/gallery-add.php" class="btn btn-kg-primary mt-2">Upload First Photo</a>
</div>
<?php else: ?>
<div class="row g-3">
  <?php foreach ($pagData['items'] as $g): ?>
  <div class="col-6 col-md-4 col-lg-3">
    <div class="position-relative rounded-3 overflow-hidden" style="aspect-ratio:1;border:2px solid var(--kg-border);">
      <img src="<?= img_url('gallery', $g['image']) ?>"
           alt="<?= e($g['title']) ?>"
           style="width:100%;height:100%;object-fit:cover;"
           onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'">
      <div class="position-absolute bottom-0 start-0 end-0 p-2" style="background:linear-gradient(transparent,rgba(0,0,0,.7));">
        <div class="text-white fw-600" style="font-size:.78rem;"><?= e($g['title']) ?></div>
        <div class="text-white-50" style="font-size:.68rem;"><?= e($g['category']) ?></div>
      </div>
      <div class="position-absolute top-0 end-0 p-1">
        <a href="<?= BASE_URL ?>/admin/gallery-delete.php?id=<?= (int)$g['id'] ?>&csrf_token=<?= urlencode(csrf_token()) ?>"
           class="btn btn-sm" style="background:rgba(185,28,28,.8);color:#fff;border-radius:6px;padding:.2rem .5rem;"
           data-confirm="Delete this gallery image?">
          <i class="bi bi-trash" style="font-size:.75rem;"></i>
        </a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<div class="mt-4"><?= pagination_html($pagData, BASE_URL . '/admin/gallery.php') ?></div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
