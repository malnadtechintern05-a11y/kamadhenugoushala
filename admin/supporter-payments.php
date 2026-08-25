<?php
/**
 * Admin Supporter Payments — Kamadhenu Goushala
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
        $payment_id = (int)$_POST['payment_id'];
        $new_status = in_array($_POST['status'], ['Pending', 'Completed', 'Failed']) ? $_POST['status'] : 'Pending';
        
        $stmt = $pdo->prepare("UPDATE supporter_payments SET status = :status WHERE id = :id");
        if ($stmt->execute([':status' => $new_status, ':id' => $payment_id])) {
            set_flash('success', "Payment status updated successfully.");
        } else {
            set_flash('danger', "Failed to update status.");
        }
    }
    redirect(BASE_URL . '/admin/supporter-payments.php');
}

// Fetch all payments with supporter details
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$countSql = "SELECT COUNT(*) FROM supporter_payments";
$dataSql = "SELECT sp.*, s.name as supporter_name, s.email, s.phone 
            FROM supporter_payments sp 
            JOIN supporters s ON sp.supporter_id = s.id 
            ORDER BY sp.created_at DESC";

$pagData = paginate($pdo, $countSql, $dataSql, [], $page, 20);

$adminPageTitle = 'Supporter Payments & Messages';
$adminActivePage = 'supporter_payments';
require_once __DIR__ . '/includes/admin_layout_header.php';

echo flash_alert();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Showing <?= count($pagData['items']) ?> of <?= $pagData['total'] ?> payments</p>
</div>

<div class="kg-admin-table">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Supporter</th>
                    <th>Amount</th>
                    <th>Method & Txn ID</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pagData['items'])): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No supporter payments found.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($pagData['items'] as $pay): ?>
                    <tr>
                        <td class="text-nowrap text-muted small"><?= date('M d, Y h:i A', strtotime($pay['created_at'])) ?></td>
                        <td>
                            <strong><?= e($pay['supporter_name']) ?></strong><br>
                            <a href="mailto:<?= e($pay['email']) ?>" class="text-decoration-none small text-muted"><i class="bi bi-envelope me-1"></i><?= e($pay['email']) ?></a>
                            <?php if ($pay['phone']): ?>
                            <br><span class="small text-muted"><i class="bi bi-telephone me-1"></i><?= e($pay['phone']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold text-success">₹<?= number_format($pay['amount'], 2) ?></td>
                        <td>
                            <?= e($pay['payment_method']) ?>
                            <?php if ($pay['transaction_id']): ?>
                                <br><small class="text-muted">Txn: <?= e($pay['transaction_id']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td style="max-width:250px;">
                            <?php if ($pay['message']): ?>
                                <div class="p-2 rounded bg-light border fst-italic small text-wrap">
                                    "<?= nl2br(e($pay['message'])) ?>"
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">No message</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $badgeClass = 'bg-warning text-dark';
                                if ($pay['status'] === 'Completed') $badgeClass = 'bg-success';
                                if ($pay['status'] === 'Failed') $badgeClass = 'bg-danger';
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= $pay['status'] ?></span>
                        </td>
                        <td class="text-end">
                            <form method="POST" action="" class="d-inline-flex gap-2">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="payment_id" value="<?= $pay['id'] ?>">
                                <select name="status" class="form-select form-select-sm" style="width:110px;" onchange="this.form.submit()">
                                    <option value="Pending" <?= $pay['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Completed" <?= $pay['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="Failed" <?= $pay['status'] === 'Failed' ? 'selected' : '' ?>>Failed</option>
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
    <?= pagination_html($pagData, BASE_URL . '/admin/supporter-payments.php') ?>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
