<?php
/**
 * Admin Adoptions View — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$pdo    = getDBConnection();
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$status = sanitize($_GET['status'] ?? '');

$where  = ['1=1'];
$params = [];
if (in_array($status, ['Pending','Active','Completed','Cancelled'], true)) {
    $where[]           = 'a.status = :status';
    $params[':status'] = $status;
}
$w = implode(' AND ', $where);

$pagData = paginate($pdo,
    "SELECT COUNT(*) FROM adoptions a WHERE $w",
    "SELECT a.*, c.name AS cow_name, c.breed AS cow_breed FROM adoptions a LEFT JOIN cows c ON a.cow_id = c.id WHERE $w ORDER BY a.created_at DESC",
    $params, $page, 20
);

$adminPageTitle  = 'Adoptions';
$adminActivePage = 'adoptions';
require_once __DIR__ . '/includes/admin_layout_header.php';
?>

<div class="d-flex flex-wrap gap-2 mb-4">
  <a href="<?= BASE_URL ?>/admin/adoptions.php" class="btn btn-sm <?= $status==='' ? 'btn-kg-primary':'btn-kg-outline' ?>">All</a>
  <?php foreach (['Pending','Active','Completed','Cancelled'] as $st): ?>
  <a href="<?= BASE_URL ?>/admin/adoptions.php?status=<?= urlencode($st) ?>" class="btn btn-sm <?= $status===$st ? 'btn-kg-primary':'btn-kg-outline' ?>"><?= $st ?></a>
  <?php endforeach; ?>
</div>

<div class="kg-admin-table">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr><th>Adopter</th><th>Cow</th><th>Duration</th><th>Monthly ₹</th><th>Status</th><th>Applied On</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php if (empty($pagData['items'])): ?>
        <tr><td colspan="7" class="text-center py-4 text-muted">No adoptions found.</td></tr>
        <?php else: ?>
        <?php foreach ($pagData['items'] as $a): ?>
        <tr>
          <td>
            <div class="fw-600"><?= e($a['adopter_name']) ?></div>
            <div style="font-size:.78rem;color:#888;"><?= e($a['adopter_email']) ?></div>
            <div style="font-size:.78rem;color:#888;"><?= e($a['adopter_phone']) ?></div>
          </td>
          <td>
            <div class="fw-600"><?= e($a['cow_name'] ?? 'N/A') ?></div>
            <div style="font-size:.78rem;color:#888;"><?= e($a['cow_breed'] ?? '') ?></div>
          </td>
          <td><?= (int)$a['duration_months'] ?> months</td>
          <td class="fw-700 text-kg-green"><?= format_inr((float)$a['amount_per_month']) ?></td>
          <td>
            <?php $sc = match($a['status']) { 'Active'=>'kg-badge-green','Pending'=>'kg-badge-gold','Cancelled'=>'kg-badge-red',default=>'kg-badge-gold' }; ?>
            <span class="<?= $sc ?>"><?= e($a['status']) ?></span>
          </td>
          <td style="font-size:.78rem;color:#888;"><?= format_date($a['created_at']) ?></td>
          <td>
            <a href="adoption-view.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-kg-outline py-0 px-2">View</a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<div class="mt-4">
  <?php $base = BASE_URL . '/admin/adoptions.php?status=' . urlencode($status); echo pagination_html($pagData, $base); ?>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
