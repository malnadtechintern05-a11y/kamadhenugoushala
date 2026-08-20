<?php
/**
 * Admin Cows List — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$pdo = getDBConnection();
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

$countSql = "SELECT COUNT(*) FROM cows";
$dataSql  = "SELECT * FROM cows ORDER BY is_featured DESC, created_at DESC";
$pagData  = paginate($pdo, $countSql, $dataSql, [], $page, 15);

$adminPageTitle  = 'Manage Cows';
$adminActivePage = 'cows';
require_once __DIR__ . '/includes/admin_layout_header.php';
echo flash_alert();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <p class="text-muted mb-0">Showing <?= count($pagData['items']) ?> of <?= $pagData['total'] ?> cows</p>
  <a href="<?= BASE_URL ?>/admin/cow-add.php" class="btn btn-kg-primary">
    <i class="bi bi-plus-circle me-2"></i>Add New Cow
  </a>
</div>

<div class="kg-admin-table">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Image</th>
          <th>Name</th>
          <th>Breed</th>
          <th>Age / Gender</th>
          <th>Health</th>
          <th>Adoption</th>
          <th>Featured</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($pagData['items'])): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">No cows found. <a href="<?= BASE_URL ?>/admin/cow-add.php">Add one now</a>.</td></tr>
        <?php else: ?>
        <?php foreach ($pagData['items'] as $cow): ?>
        <tr>
          <td>
            <img src="<?= img_url('cows', $cow['image']) ?>"
                 alt="<?= e($cow['name']) ?>"
                 style="width:52px;height:52px;object-fit:cover;border-radius:8px;border:2px solid var(--kg-border);"
                 onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'">
          </td>
          <td class="fw-600"><?= e($cow['name']) ?></td>
          <td style="color:var(--kg-text-muted);"><?= e($cow['breed']) ?></td>
          <td><?= (int)$cow['age'] ?> yrs / <?= e($cow['gender']) ?></td>
          <td>
            <?php
              $hc = match($cow['health_status']) { 'Healthy'=>'kg-badge-green','Under Treatment'=>'kg-badge-red',default=>'kg-badge-gold' };
            ?>
            <span class="<?= $hc ?>"><?= e($cow['health_status']) ?></span>
          </td>
          <td>
            <?php
              $ac = match($cow['adoption_status']) { 'Available'=>'kg-badge-green','Adopted'=>'kg-badge-gold',default=>'kg-badge-red' };
            ?>
            <span class="<?= $ac ?>"><?= e($cow['adoption_status']) ?></span>
          </td>
          <td><?= $cow['is_featured'] ? '<i class="bi bi-star-fill" style="color:var(--kg-gold-dark);"></i>' : '<i class="bi bi-star" style="color:#ccc;"></i>' ?></td>
          <td class="text-end">
            <a href="<?= BASE_URL ?>/admin/cow-edit.php?id=<?= (int)$cow['id'] ?>" class="btn btn-sm" style="background:#dbeafe;color:#1d4ed8;border-radius:6px;" title="Edit">
              <i class="bi bi-pencil"></i>
            </a>
            <a href="<?= BASE_URL ?>/admin/cow-delete.php?id=<?= (int)$cow['id'] ?>&csrf_token=<?= urlencode(csrf_token()) ?>"
               class="btn btn-sm ms-1" style="background:#fee2e2;color:#b91c1c;border-radius:6px;"
               data-confirm="Delete cow '<?= e($cow['name']) ?>'? This cannot be undone." title="Delete">
              <i class="bi bi-trash"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="mt-4">
  <?= pagination_html($pagData, BASE_URL . '/admin/cows.php') ?>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
