<?php
/**
 * Admin Orders View — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$pdo = getDBConnection();
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$status = sanitize($_GET['status'] ?? '');

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    csrf_validate();
    $oId = (int)$_POST['order_id'];
    $nStatus = sanitize($_POST['new_status']);
    if (in_array($nStatus, ['Pending','Confirmed','Shipped','Delivered','Cancelled'], true)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $nStatus, ':id' => $oId]);
        set_flash('success', "Order #$oId status updated to $nStatus.");
    }
    redirect(BASE_URL . '/admin/orders.php' . ($status ? "?status=$status" : ""));
}

$where  = ['1=1'];
$params = [];
if (in_array($status, ['Pending','Confirmed','Shipped','Delivered','Cancelled'], true)) {
    $where[]          = 'o.status = :status';
    $params[':status']= $status;
}
$w = implode(' AND ', $where);

$pagData = paginate($pdo,
    "SELECT COUNT(*) FROM orders o WHERE $w",
    "SELECT * FROM orders o WHERE $w ORDER BY o.created_at DESC",
    $params, $page, 20
);

// Summary stats
$summary = $pdo->query("SELECT status, COUNT(*) AS cnt, COALESCE(SUM(total_amount),0) AS total FROM orders GROUP BY status")->fetchAll();

$adminPageTitle  = 'Orders';
$adminActivePage = 'orders';
require_once __DIR__ . '/includes/admin_layout_header.php';
?>

<!-- Summary -->
<div class="row g-3 mb-4">
  <?php foreach ($summary as $s): ?>
  <div class="col-md-3">
    <div class="kg-dash-stat" style="border-left-color:<?= $s['status']==='Delivered'?'var(--kg-green)':($s['status']==='Pending'?'var(--kg-gold)':'#ef4444') ?>;">
      <div>
        <div class="kg-dash-stat-number"><?= format_inr((float)$s['total']) ?></div>
        <div class="kg-dash-stat-label"><?= e($s['status']) ?> (<?= (int)$s['cnt'] ?> orders)</div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Filter -->
<div class="d-flex flex-wrap gap-2 mb-4">
  <a href="<?= BASE_URL ?>/admin/orders.php" class="btn btn-sm <?= $status==='' ? 'btn-kg-primary':'btn-kg-outline' ?>">All</a>
  <?php foreach (['Pending','Confirmed','Shipped','Delivered','Cancelled'] as $st): ?>
  <a href="<?= BASE_URL ?>/admin/orders.php?status=<?= urlencode($st) ?>" class="btn btn-sm <?= $status===$st ? 'btn-kg-primary':'btn-kg-outline' ?>"><?= $st ?></a>
  <?php endforeach; ?>
</div>

<?= flash_alert() ?>

<div class="kg-admin-table">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr><th>ID</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php if (empty($pagData['items'])): ?>
        <tr><td colspan="6" class="text-center py-4 text-muted">No orders found.</td></tr>
        <?php else: ?>
        <?php foreach ($pagData['items'] as $o): ?>
        <tr>
          <td>#<?= (int)$o['id'] ?></td>
          <td>
            <div class="fw-600"><?= e($o['customer_name']) ?></div>
            <div style="font-size:.83rem;color:#666;"><?= e($o['customer_phone']) ?></div>
          </td>
          <td class="fw-700 text-kg-green"><?= format_inr((float)$o['total_amount']) ?></td>
          <td>
            <form method="POST" action="" class="d-flex align-items-center gap-2">
              <?= csrf_field() ?>
              <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
              <select name="new_status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                <?php foreach (['Pending','Confirmed','Shipped','Delivered','Cancelled'] as $st): ?>
                <option value="<?= $st ?>" <?= $o['status']===$st ? 'selected':'' ?>><?= $st ?></option>
                <?php endforeach; ?>
              </select>
            </form>
          </td>
          <td style="font-size:.78rem;color:#888;"><?= format_datetime($o['created_at']) ?></td>
          <td>
            <a href="<?= BASE_URL ?>/admin/order-details.php?id=<?= (int)$o['id'] ?>" class="btn btn-sm btn-kg-outline">
              <i class="bi bi-eye"></i> View
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
  <?php $base = BASE_URL . '/admin/orders.php?status=' . urlencode($status); echo pagination_html($pagData, $base); ?>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
