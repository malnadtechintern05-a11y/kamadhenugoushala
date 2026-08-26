<?php
/**
 * Admin Donations View — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$pdo = getDBConnection();

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $token = $_POST['csrf_token'] ?? '';
    if (hash_equals(csrf_token(), $token)) {
        $donation_id = (int)$_POST['donation_id'];
        $new_status = in_array($_POST['status'], ['Pending', 'Completed', 'Failed']) ? $_POST['status'] : 'Pending';
        
        $stmt = $pdo->prepare("UPDATE donations SET status = :status WHERE id = :id");
        if ($stmt->execute([':status' => $new_status, ':id' => $donation_id])) {
            set_flash('success', "Donation status updated successfully.");
        } else {
            set_flash('danger', "Failed to update status.");
        }
    }
    redirect(BASE_URL . '/admin/donations.php' . (isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : ''));
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$status = sanitize($_GET['status'] ?? '');

$where  = ['1=1'];
$params = [];
if (in_array($status, ['Pending','Completed','Failed'], true)) {
    $where[]          = 'd.status = :status';
    $params[':status']= $status;
}
$w = implode(' AND ', $where);

$pagData = paginate($pdo,
    "SELECT COUNT(*) FROM donations d WHERE $w",
    "SELECT * FROM donations d WHERE $w ORDER BY d.created_at DESC",
    $params, $page, 20
);

// Summary stats
$summary = $pdo->query("SELECT status, COUNT(*) AS cnt, COALESCE(SUM(amount),0) AS total FROM donations GROUP BY status")->fetchAll();

$adminPageTitle  = 'Donations';
$adminActivePage = 'donations';
require_once __DIR__ . '/includes/admin_layout_header.php';

echo flash_alert();
?>

<!-- Summary -->
<div class="row g-3 mb-4">
  <?php foreach ($summary as $s): ?>
  <div class="col-md-4">
    <div class="kg-dash-stat" style="border-left-color:<?= $s['status']==='Completed'?'var(--kg-green)':($s['status']==='Pending'?'var(--kg-gold)':'#ef4444') ?>;">
      <div>
        <div class="kg-dash-stat-number"><?= format_inr((float)$s['total']) ?></div>
        <div class="kg-dash-stat-label"><?= e($s['status']) ?> (<?= (int)$s['cnt'] ?> donations)</div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Filter -->
<div class="d-flex flex-wrap gap-2 mb-4">
  <a href="<?= BASE_URL ?>/admin/donations.php" class="btn btn-sm <?= $status==='' ? 'btn-kg-primary':'btn-kg-outline' ?>">All</a>
  <?php foreach (['Pending','Completed','Failed'] as $st): ?>
  <a href="<?= BASE_URL ?>/admin/donations.php?status=<?= urlencode($st) ?>" class="btn btn-sm <?= $status===$st ? 'btn-kg-primary':'btn-kg-outline' ?>"><?= $st ?></a>
  <?php endforeach; ?>
</div>

<div class="kg-admin-table">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr><th>Donor</th><th>Email</th><th>Amount</th><th>Purpose</th><th>Payment</th><th>Status</th><th>Date</th><th class="text-end">Action</th></tr>
      </thead>
      <tbody>
        <?php if (empty($pagData['items'])): ?>
        <tr><td colspan="7" class="text-center py-4 text-muted">No donations found.</td></tr>
        <?php else: ?>
        <?php foreach ($pagData['items'] as $d): ?>
        <tr>
          <td class="fw-600"><?= e($d['donor_name']) ?></td>
          <td style="font-size:.83rem;"><?= e($d['donor_email']) ?></td>
          <td class="fw-700 text-kg-green"><?= format_inr((float)$d['amount']) ?></td>
          <td><?= e($d['purpose']) ?></td>
          <td><?= e($d['payment_method']) ?></td>
          <td>
            <?php $sc = match($d['status']) { 'Completed'=>'kg-badge-green','Pending'=>'kg-badge-gold',default=>'kg-badge-red' }; ?>
            <span class="<?= $sc ?>"><?= e($d['status']) ?></span>
          </td>
          <td style="font-size:.78rem;color:#888;"><?= format_datetime($d['created_at']) ?></td>
          <td class="text-end">
            <form method="POST" action="" class="d-inline-flex gap-2">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="donation_id" value="<?= $d['id'] ?>">
                <select name="status" class="form-select form-select-sm" style="width:110px;" onchange="this.form.submit()">
                    <option value="Pending" <?= $d['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Completed" <?= $d['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="Failed" <?= $d['status'] === 'Failed' ? 'selected' : '' ?>>Failed</option>
                </select>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<div class="mt-4">
  <?php $base = BASE_URL . '/admin/donations.php?status=' . urlencode($status); echo pagination_html($pagData, $base); ?>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
