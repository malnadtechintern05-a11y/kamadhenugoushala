<?php
/**
 * Admin Gallery Add — Kamadhenu Goushala
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
        'title'       => sanitize($_POST['title']       ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
        'category'    => sanitize($_POST['category']    ?? 'General'),
        'sort_order'  => (int)($_POST['sort_order']     ?? 0),
    ];

    if (empty($_FILES['image']['name'])) {
        $errors[] = 'Please select an image to upload.';
    }

    $imageFilename = null;
    if (empty($errors) && !empty($_FILES['image']['name'])) {
        try {
            $imageFilename = upload_image($_FILES['image'], UPLOAD_GALLERY_DIR);
        } catch (RuntimeException $e) {
            $errors[] = 'Upload failed: ' . $e->getMessage();
        }
    }

    if (empty($errors)) {
        $pdo = getDBConnection();
        $pdo->prepare("
            INSERT INTO gallery (title, description, category, image, sort_order)
            VALUES (:title, :desc, :cat, :image, :sort)
        ")->execute([
            ':title' => $old['title'],
            ':desc'  => $old['description'],
            ':cat'   => $old['category'],
            ':image' => $imageFilename,
            ':sort'  => $old['sort_order'],
        ]);
        set_flash('success', 'Image uploaded to gallery.');
        redirect(BASE_URL . '/admin/gallery.php');
    }
}

$adminPageTitle  = 'Upload Gallery Photo';
$adminActivePage = 'gallery';
require_once __DIR__ . '/includes/admin_layout_header.php';
?>

<div class="mb-3"><a href="<?= BASE_URL ?>/admin/gallery.php" class="btn btn-sm btn-kg-outline"><i class="bi bi-arrow-left me-1"></i>Back</a></div>

<div class="kg-admin-form-card" style="max-width:600px;">
  <?php if (!empty($errors)): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data" data-validate novalidate>
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label">Image <span class="text-danger">*</span></label>
      <input type="file" name="image" class="form-control" accept="image/*" data-preview="galleryPreview" required>
      <img id="galleryPreview" src="" class="d-none mt-2" style="max-height:200px;border-radius:8px;">
    </div>
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input type="text" name="title" class="form-control" placeholder="e.g. Morning Seva" value="<?= e($old['title'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label">Category</label>
      <select name="category" class="form-select">
        <?php foreach (['General','Cows','Seva','Events','Volunteers','Premises'] as $cat): ?>
        <option value="<?= $cat ?>" <?= ($old['category'] ?? 'General') === $cat ? 'selected':'' ?>><?= $cat ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="2"><?= e($old['description'] ?? '') ?></textarea>
    </div>
    <div class="mb-4">
      <label class="form-label">Sort Order (lower = appears first)</label>
      <input type="number" name="sort_order" class="form-control" min="0" value="<?= (int)($old['sort_order'] ?? 0) ?>">
    </div>
    <div class="d-flex gap-3">
      <button type="submit" class="btn btn-kg-primary px-4"><i class="bi bi-upload me-2"></i>Upload</button>
      <a href="<?= BASE_URL ?>/admin/gallery.php" class="btn btn-kg-outline">Cancel</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
