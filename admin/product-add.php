<?php
/**
 * Admin Add Product — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();

    $old = [
        'name'        => sanitize($_POST['name']        ?? ''),
        'category'    => sanitize($_POST['category']    ?? 'Other'),
        'price'       => sanitize($_POST['price']       ?? ''),
        'unit'        => sanitize($_POST['unit']        ?? ''),
        'stock_qty'   => (int)($_POST['stock_qty']      ?? 0),
        'description' => sanitize($_POST['description'] ?? ''),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'is_active'   => isset($_POST['is_active'])   ? 1 : 0,
    ];

    $validCats = ['Milk Products','Ghee','Gomutra','Panchamrit','Panchagavya','Organic','Other'];

    if (empty($old['name']))    $errors[] = 'Product name is required.';
    if (!is_valid_amount($old['price'])) $errors[] = 'Valid price is required.';
    if (!in_array($old['category'], $validCats, true)) $errors[] = 'Invalid category.';

    $imgFilename = null;
    if (!empty($_FILES['image']['name'])) {
        try {
            $imgFilename = upload_image($_FILES['image'], UPLOAD_PRODUCTS_DIR);
        } catch (RuntimeException $e) {
            $errors[] = 'Image upload: ' . $e->getMessage();
        }
    }

    if (empty($errors)) {
        $pdo = getDBConnection();
        $pdo->prepare("
            INSERT INTO products (name, category, price, unit, stock_qty, description, image, is_featured, is_active)
            VALUES (:name, :cat, :price, :unit, :stock, :desc, :image, :featured, :active)
        ")->execute([
            ':name'    => $old['name'],
            ':cat'     => $old['category'],
            ':price'   => (float)$old['price'],
            ':unit'    => $old['unit'],
            ':stock'   => $old['stock_qty'],
            ':desc'    => $old['description'],
            ':image'   => $imgFilename,
            ':featured'=> $old['is_featured'],
            ':active'  => $old['is_active'],
        ]);
        set_flash('success', 'Product "' . $old['name'] . '" added.');
        redirect(BASE_URL . '/admin/products.php');
    }
}

$adminPageTitle  = 'Add New Product';
$adminActivePage = 'products';
require_once __DIR__ . '/includes/admin_layout_header.php';
?>

<div class="mb-3"><a href="<?= BASE_URL ?>/admin/products.php" class="btn btn-sm btn-kg-outline"><i class="bi bi-arrow-left me-1"></i>Back</a></div>

<div class="kg-admin-form-card">
  <?php if (!empty($errors)): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>
  <form method="POST" action="" enctype="multipart/form-data" data-validate novalidate>
    <?= csrf_field() ?>
    <div class="row g-4">
      <div class="col-md-8">
        <label class="form-label">Product Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="<?= e($old['name'] ?? '') ?>" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Category</label>
        <select name="category" class="form-select">
          <?php foreach (['Milk Products','Ghee','Gomutra','Panchamrit','Panchagavya','Organic','Other'] as $cat): ?>
          <option value="<?= $cat ?>" <?= ($old['category'] ?? 'Other') === $cat ? 'selected':'' ?>><?= $cat ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Price (₹) <span class="text-danger">*</span></label>
        <input type="number" name="price" class="form-control" step="0.01" min="0" value="<?= e($old['price'] ?? '') ?>" required>
      </div>
      <div class="col-md-4">
        <label class="form-label">Unit</label>
        <input type="text" name="unit" class="form-control" placeholder="e.g. 500 ml, 1 kg" value="<?= e($old['unit'] ?? '') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Stock Quantity</label>
        <input type="number" name="stock_qty" class="form-control" min="0" value="<?= (int)($old['stock_qty'] ?? 0) ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4"><?= e($old['description'] ?? '') ?></textarea>
      </div>
      <div class="col-md-6">
        <label class="form-label">Product Image</label>
        <input type="file" name="image" class="form-control" accept="image/*" data-preview="prodPreview">
      </div>
      <div class="col-md-6 d-flex align-items-end">
        <img id="prodPreview" src="" alt="" class="d-none" style="max-height:120px;border-radius:8px;">
      </div>
      <div class="col-md-6">
        <div class="form-check form-switch fs-5">
          <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" <?= ($old['is_featured'] ?? 0) ? 'checked':'' ?>>
          <label class="form-check-label fw-600" for="is_featured">Featured on Homepage</label>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-check form-switch fs-5">
          <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= ($old['is_active'] ?? 1) ? 'checked':'' ?>>
          <label class="form-check-label fw-600" for="is_active">Active (visible on site)</label>
        </div>
      </div>
    </div>
    <hr class="my-4">
    <div class="d-flex gap-3">
      <button type="submit" class="btn btn-kg-primary px-4"><i class="bi bi-check-circle me-2"></i>Save Product</button>
      <a href="<?= BASE_URL ?>/admin/products.php" class="btn btn-kg-outline">Cancel</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
