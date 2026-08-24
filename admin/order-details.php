<?php
/**
 * Admin Order Details View — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$pdo = getDBConnection();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    redirect(BASE_URL . '/admin/orders.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    csrf_validate();
    $newStatus = sanitize($_POST['status']);
    if (in_array($newStatus, ['Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled'])) {
        $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id")->execute([
            ':status' => $newStatus,
            ':id' => $id
        ]);
        redirect(BASE_URL . '/admin/order-details.php?id=' . $id . '&msg=updated');
    }
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
$stmt->execute([':id' => $id]);
$order = $stmt->fetch();

if (!$order) {
    redirect(BASE_URL . '/admin/orders.php');
}

$stmtItems = $pdo->prepare("
    SELECT oi.*, p.name AS product_name, p.image AS product_image, p.unit AS product_unit 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = :order_id
");
$stmtItems->execute([':order_id' => $id]);
$items = $stmtItems->fetchAll();

$adminPageTitle  = 'Order Details #' . $id;
$adminActivePage = 'orders';
require_once __DIR__ . '/includes/admin_layout_header.php';
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <a href="<?= BASE_URL ?>/admin/orders.php" class="btn btn-sm btn-kg-outline">
        <i class="bi bi-arrow-left"></i> Back to Orders
    </a>
    <form method="POST" action="" class="d-flex align-items-center gap-2">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="update_status">
      <select name="status" class="form-select form-select-sm" style="width: auto;">
        <?php foreach(['Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled'] as $s): ?>
        <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-sm btn-kg-primary">Update Status</button>
    </form>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
<div class="alert alert-success py-2">Order status updated successfully.</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Order Summary -->
    <div class="col-lg-8">
        <div class="kg-admin-table mb-4">
            <div class="p-3 border-bottom d-flex align-items-center gap-2">
                <i class="bi bi-cart3 fs-5 text-kg-green"></i>
                <h6 class="mb-0 fw-bold">Ordered Items</h6>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= img_url('products', $item['product_image']) ?>" alt="<?= e($item['product_name']) ?>" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                    <div>
                                        <div class="fw-bold"><?= e($item['product_name']) ?></div>
                                        <div class="text-muted" style="font-size: 0.8rem;">Unit: <?= e($item['product_unit']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center"><?= format_inr((float)$item['price']) ?></td>
                            <td class="text-center"><?= (int)$item['quantity'] ?></td>
                            <td class="text-end fw-bold"><?= format_inr((float)($item['price'] * $item['quantity'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr class="table-light fw-bold text-kg-green">
                            <td colspan="3" class="text-end">Grand Total:</td>
                            <td class="text-end"><?= format_inr((float)$order['total_amount']) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Customer Details -->
    <div class="col-lg-4">
        <div class="kg-form-card mb-4">
            <h6 class="border-bottom pb-2 mb-3 fw-bold"><i class="bi bi-person-fill me-2 text-kg-green"></i>Customer Info</h6>
            <div class="mb-2"><strong>Name:</strong> <?= e($order['customer_name']) ?></div>
            <div class="mb-2">
                <strong>Email:</strong> 
                <a href="mailto:<?= e($order['customer_email']) ?>" class="text-decoration-none"><?= e($order['customer_email']) ?></a>
            </div>
            <div class="mb-2">
                <strong>Phone:</strong> 
                <a href="tel:<?= e($order['customer_phone']) ?>" class="text-decoration-none"><?= e($order['customer_phone']) ?></a>
            </div>
            <div class="mb-0">
                <strong>Address:</strong><br>
                <span class="text-muted"><?= nl2br(e($order['customer_address'])) ?></span>
            </div>
        </div>

        <div class="kg-form-card">
            <h6 class="border-bottom pb-2 mb-3 fw-bold"><i class="bi bi-info-circle-fill me-2 text-kg-green"></i>Order Info</h6>
            <div class="mb-2"><strong>Order Date:</strong> <?= format_datetime($order['created_at']) ?></div>
            <div class="mb-2"><strong>Payment Method:</strong> <?= e($order['payment_method']) ?></div>
            <?php if (!empty($order['notes'])): ?>
            <div class="mb-0">
                <strong>Notes:</strong><br>
                <div class="p-2 bg-light rounded mt-1 border text-muted" style="font-size: 0.9rem;">
                    <?= nl2br(e($order['notes'])) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
