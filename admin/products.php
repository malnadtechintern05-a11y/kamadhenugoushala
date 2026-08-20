<?php
/**
 * Admin Products List — Kamadhenu Goushala
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
    "SELECT COUNT(*) FROM products",
    "SELECT * FROM products ORDER BY is_featured DESC, created_at DESC",
    [], $page, 15
);

$adminPageTitle  = 'Manage Products';
$adminActivePage = 'products';
require_once __DIR__ . '/includes/admin_layout_header.php';
echo flash_alert();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <p class="text-muted mb-0">Showing <?= count($pagData['items']) ?> of <?= $pagData['total'] ?> products</p>
  <a href="<?= BASE_URL ?>/admin/product-add.php" class="btn btn-kg-primary">
    <i class="bi bi-plus-circle me-2"></i>Add New Product
  </a>
</div>

<div class="kg-admin-table">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Featured</th><th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($pagData['items'])): ?>
        <tr><td colspan="8" class="text-center py-4 text-muted">No products. <a href="<?= BASE_URL ?>/admin/product-add.php">Add one</a>.</td></tr>
        <?php else: ?>
        <?php foreach ($pagData['items'] as $p): ?>
        <tr>
          <td><img src="<?= img_url('products', $p['image']) ?>" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:6px;border:2px solid var(--kg-border);" onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'"></td>
          <td class="fw-600"><?= e($p['name']) ?></td>
          <td><span class="kg-badge-green"><?= e($p['category']) ?></span></td>
          <td class="fw-600 text-kg-green"><?= format_inr((float)$p['price']) ?></td>
          <td><?= (int)$p['stock_qty'] ?></td>
          <td><?= $p['is_active'] ? '<span class="kg-badge-green">Active</span>' : '<span class="kg-badge-red">Inactive</span>' ?></td>
          <td><?= $p['is_featured'] ? '<i class="bi bi-star-fill" style="color:var(--kg-gold-dark);"></i>' : '<i class="bi bi-star" style="color:#ccc;"></i>' ?></td>
          <td class="text-end">
            <a href="<?= BASE_URL ?>/admin/product-edit.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm" style="background:#dbeafe;color:#1d4ed8;border-radius:6px;" title="Edit"><i class="bi bi-pencil"></i></a>
            <a href="<?= BASE_URL ?>/admin/product-delete.php?id=<?= (int)$p['id'] ?>&csrf_token=<?= urlencode(csrf_token()) ?>"
               class="btn btn-sm ms-1" style="background:#fee2e2;color:#b91c1c;border-radius:6px;"
               data-confirm="Delete product '<?= e($p['name']) ?>'?" title="Delete"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<div class="mt-4"><?= pagination_html($pagData, BASE_URL . '/admin/products.php') ?></div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
