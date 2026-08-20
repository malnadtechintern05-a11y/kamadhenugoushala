<?php
/**
 * Admin Volunteers View — Kamadhenu Goushala
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
if (in_array($status, ['Pending','Approved','Rejected'], true)) {
    $where[]           = 'v.status = :status';
    $params[':status'] = $status;
}
$w = implode(' AND ', $where);

$pagData = paginate($pdo,
    "SELECT COUNT(*) FROM volunteers v WHERE $w",
    "SELECT * FROM volunteers v WHERE $w ORDER BY v.created_at DESC",
    $params, $page, 20
);

$adminPageTitle  = 'Volunteers';
$adminActivePage = 'volunteers';
require_once __DIR__ . '/includes/admin_layout_header.php';
?>

<div class="d-flex flex-wrap gap-2 mb-4">
  <a href="<?= BASE_URL ?>/admin/volunteers.php" class="btn btn-sm <?= $status==='' ? 'btn-kg-primary':'btn-kg-outline' ?>">All</a>
  <?php foreach (['Pending','Approved','Rejected'] as $st): ?>
  <a href="<?= BASE_URL ?>/admin/volunteers.php?status=<?= urlencode($st) ?>" class="btn btn-sm <?= $status===$st ? 'btn-kg-primary':'btn-kg-outline' ?>"><?= $st ?></a>
  <?php endforeach; ?>
</div>

<div class="kg-admin-table">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr><th>Name</th><th>Contact</th><th>Availability</th><th>Skills</th><th>Motivation</th><th>Status</th><th>Date</th></tr>
      </thead>
      <tbody>
        <?php if (empty($pagData['items'])): ?>
        <tr><td colspan="7" class="text-center py-4 text-muted">No volunteers found.</td></tr>
        <?php else: ?>
        <?php foreach ($pagData['items'] as $v): ?>
        <tr>
          <td>
            <div class="fw-600"><?= e($v['name']) ?></div>
            <?php if ($v['age']): ?><div style="font-size:.76rem;color:#888;">Age: <?= (int)$v['age'] ?></div><?php endif; ?>
            <div style="font-size:.76rem;color:#888;"><?= e($v['occupation']) ?></div>
          </td>
          <td style="font-size:.82rem;">
            <a href="mailto:<?= e($v['email']) ?>"><?= e($v['email']) ?></a><br>
            <span><?= e($v['phone']) ?></span>
          </td>
          <td><span class="kg-badge-green"><?= e($v['availability']) ?></span></td>
          <td style="font-size:.8rem;max-width:140px;"><?= e(mb_strimwidth($v['skills'] ?? '', 0, 60, '…')) ?></td>
          <td style="font-size:.8rem;max-width:180px;"><?= e(mb_strimwidth($v['motivation'] ?? '', 0, 70, '…')) ?></td>
          <td>
            <?php $sc = match($v['status']) { 'Approved'=>'kg-badge-green','Pending'=>'kg-badge-gold',default=>'kg-badge-red' }; ?>
            <span class="<?= $sc ?>"><?= e($v['status']) ?></span>
          </td>
          <td style="font-size:.76rem;color:#888;white-space:nowrap;"><?= format_date($v['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<div class="mt-4">
  <?php $base = BASE_URL . '/admin/volunteers.php?status=' . urlencode($status); echo pagination_html($pagData, $base); ?>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
