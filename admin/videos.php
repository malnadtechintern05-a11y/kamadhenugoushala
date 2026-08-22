<?php
/**
 * Admin Videos — Kamadhenu Goushala
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
    "SELECT COUNT(*) FROM videos",
    "SELECT * FROM videos ORDER BY created_at DESC",
    [], $page, 12
);

$adminPageTitle  = 'Manage Videos';
$adminActivePage = 'videos';
require_once __DIR__ . '/includes/admin_layout_header.php';
echo flash_alert();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <p class="text-muted mb-0"><?= $pagData['total'] ?> videos</p>
  <a href="<?= BASE_URL ?>/admin/video-add.php" class="btn btn-kg-primary">
    <i class="bi bi-play-circle me-2"></i>Add Video
  </a>
</div>

<?php if (empty($pagData['items'])): ?>
<div class="text-center py-5">
  <i class="bi bi-play-btn" style="font-size:3rem;color:var(--kg-border);"></i>
  <h5 class="mt-3 text-muted">No videos yet.</h5>
  <a href="<?= BASE_URL ?>/admin/video-add.php" class="btn btn-kg-primary mt-2">Add First Video</a>
</div>
<?php else: ?>
<div class="row g-3">
  <?php foreach ($pagData['items'] as $v): ?>
  <div class="col-6 col-md-4 col-lg-3">
    <div class="position-relative rounded-3 overflow-hidden" style="aspect-ratio:16/9;border:2px solid <?= $v['is_featured'] ? 'var(--kg-gold)' : 'var(--kg-border)' ?>;">
      <img src="https://img.youtube.com/vi/<?= e($v['youtube_id']) ?>/mqdefault.jpg"
           alt="<?= e($v['title']) ?>"
           style="width:100%;height:100%;object-fit:cover;">
      
      <div class="position-absolute bottom-0 start-0 end-0 p-2" style="background:linear-gradient(transparent,rgba(0,0,0,.8));">
        <div class="text-white fw-600 text-truncate" style="font-size:.78rem;" title="<?= e($v['title']) ?>"><?= e($v['title']) ?></div>
      </div>
      
      <div class="position-absolute top-0 end-0 p-1 d-flex gap-1">
        <?php if (!$v['is_featured']): ?>
        <a href="<?= BASE_URL ?>/admin/video-feature.php?id=<?= (int)$v['id'] ?>&csrf_token=<?= urlencode(csrf_token()) ?>"
           class="btn btn-sm btn-light" style="border-radius:6px;padding:.2rem .5rem;" title="Make Featured (Show on Homepage)">
           <i class="bi bi-star" style="font-size:.75rem;"></i>
        </a>
        <a href="<?= BASE_URL ?>/admin/video-delete.php?id=<?= (int)$v['id'] ?>&csrf_token=<?= urlencode(csrf_token()) ?>"
           class="btn btn-sm" style="background:rgba(185,28,28,.9);color:#fff;border-radius:6px;padding:.2rem .5rem;"
           data-confirm="Delete this video?">
          <i class="bi bi-trash" style="font-size:.75rem;"></i>
        </a>
        <?php else: ?>
        <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Featured</span>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<div class="mt-4"><?= pagination_html($pagData, BASE_URL . '/admin/videos.php') ?></div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
