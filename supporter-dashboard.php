<?php
/**
 * Supporter Dashboard — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

if (!isset($_SESSION['supporter_id'])) {
    redirect(BASE_URL . '/supporter-login.php');
}

$supporter_id = $_SESSION['supporter_id'];
$pdo = getDBConnection();


// Fetch past payments
$stmt = $pdo->prepare("SELECT * FROM supporter_payments WHERE supporter_id = :sup_id ORDER BY created_at DESC");
$stmt->execute([':sup_id' => $supporter_id]);
$payments = $stmt->fetchAll();

$pageTitle = 'Supporter Dashboard - Kamadhenu Goushala';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Welcome, <?= e($_SESSION['supporter_name']) ?>!</h2>
        <a href="<?= BASE_URL ?>/supporter-logout.php" class="btn btn-outline-danger">Logout</a>
    </div>


    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius:1rem;">
                <div class="card-header bg-light py-3" style="border-top-left-radius:1rem; border-top-right-radius:1rem;">
                    <h5 class="mb-0 fw-bold text-success"><i class="bi bi-clock-history me-2"></i>Payment History</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($payments)): ?>
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                            <p>You have not made any payments yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $pay): ?>
                                    <tr>
                                        <td class="ps-4 text-muted small"><?= date('M d, Y h:i A', strtotime($pay['created_at'])) ?></td>
                                        <td class="fw-bold text-success">₹<?= number_format($pay['amount'], 2) ?></td>
                                        <td><?= e($pay['payment_method']) ?></td>
                                        <td>
                                            <?php if ($pay['status'] === 'Completed'): ?>
                                                <span class="badge bg-success">Completed</span>
                                            <?php elseif ($pay['status'] === 'Failed'): ?>
                                                <span class="badge bg-danger">Failed</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
