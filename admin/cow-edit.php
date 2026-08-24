<?php
/**
 * Admin Edit Cow — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$pdo = getDBConnection();
$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) redirect(BASE_URL . '/admin/cows.php');

$stmt = $pdo->prepare("SELECT * FROM cows WHERE id = :id");
$stmt->execute([':id' => $id]);
$cow = $stmt->fetch();
if (!$cow) redirect(BASE_URL . '/admin/cows.php');

$errors = [];
$old    = $cow; // pre-fill with existing

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();

    $old = [
        'name'            => sanitize($_POST['name']             ?? ''),
        'breed'           => sanitize($_POST['breed']            ?? ''),
        'age'             => (int)($_POST['age']                 ?? 0),
        'gender'          => sanitize($_POST['gender']           ?? 'Female'),
        'color'           => sanitize($_POST['color']            ?? ''),
        'weight_kg'       => sanitize($_POST['weight_kg']        ?? ''),
        'health_status'   => sanitize($_POST['health_status']    ?? 'Healthy'),
        'adoption_status' => sanitize($_POST['adoption_status']  ?? 'Available'),
        'description'     => sanitize($_POST['description']      ?? ''),
        'whatsapp_number' => sanitize($_POST['whatsapp_number']  ?? ''),
        'whatsapp_message'=> sanitize($_POST['whatsapp_message'] ?? ''),
        'is_featured'     => isset($_POST['is_featured']) ? 1 : 0,
        'image'           => $cow['image'], // keep existing
    ];

    if (empty($old['name'])) $errors[] = 'Name is required.';
    if (!in_array($old['gender'], ['Female','Male','Calf'], true)) $errors[] = 'Invalid gender.';
    if (!in_array($old['health_status'], ['Healthy','Under Treatment','Recovered'], true)) $errors[] = 'Invalid health status.';
    if (!in_array($old['adoption_status'], ['Available','Adopted','Not Available'], true)) $errors[] = 'Invalid adoption status.';

    if (!empty($_FILES['image']['name'])) {
        try {
            $newImage = upload_image($_FILES['image'], UPLOAD_COWS_DIR);
            // Delete old image
            if (!empty($cow['image'])) delete_image(UPLOAD_COWS_DIR, $cow['image']);
            $old['image'] = $newImage;
        } catch (RuntimeException $e) {
            $errors[] = 'Image upload failed: ' . $e->getMessage();
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE cows SET name=:name, breed=:breed, age=:age, gender=:gender, color=:color,
            weight_kg=:weight_kg, health_status=:health, adoption_status=:adoption, description=:desc,
            whatsapp_number=:whatsapp_number, whatsapp_message=:whatsapp_message, image=:image, is_featured=:featured
            WHERE id=:id
        ");
        $stmt->execute([
            ':name'     => $old['name'],
            ':breed'    => $old['breed'],
            ':age'      => $old['age'],
            ':gender'   => $old['gender'],
            ':color'    => $old['color'],
            ':weight_kg'=> $old['weight_kg'] !== '' ? (float)$old['weight_kg'] : null,
            ':health'   => $old['health_status'],
            ':adoption' => $old['adoption_status'],
            ':desc'     => $old['description'],
            ':whatsapp_number'  => $old['whatsapp_number'] !== '' ? $old['whatsapp_number'] : null,
            ':whatsapp_message' => $old['whatsapp_message'] !== '' ? $old['whatsapp_message'] : null,
            ':image'    => $old['image'],
            ':featured' => $old['is_featured'],
            ':id'       => $id
        ]);
        set_flash('success', 'Cow "' . $old['name'] . '" updated successfully.');
        redirect(BASE_URL . '/admin/cows.php');
    }
}

$adminPageTitle  = 'Edit Cow: ' . e($cow['name']);
$adminActivePage = 'cows';
require_once __DIR__ . '/includes/admin_layout_header.php';
?>

<div class="mb-3">
  <a href="<?= BASE_URL ?>/admin/cows.php" class="btn btn-sm btn-kg-outline">
    <i class="bi bi-arrow-left me-1"></i>Back to Cows
  </a>
</div>

<div class="kg-admin-form-card">
  <?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
  </div>
  <?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data" data-validate novalidate>
    <?= csrf_field() ?>
    <div class="row g-4">
      <div class="col-md-6">
        <label class="form-label">Cow Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="<?= e($old['name']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Breed</label>
        <input type="text" name="breed" class="form-control" value="<?= e($old['breed']) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Age (years)</label>
        <input type="number" name="age" class="form-control" min="0" value="<?= (int)$old['age'] ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select">
          <?php foreach (['Female','Male','Calf'] as $g): ?>
          <option value="<?= $g ?>" <?= $old['gender'] === $g ? 'selected':'' ?>><?= $g ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Color</label>
        <input type="text" name="color" class="form-control" value="<?= e($old['color']) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Weight (kg)</label>
        <input type="number" name="weight_kg" class="form-control" step="0.01" min="0" value="<?= e($old['weight_kg'] ?? '') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Health Status</label>
        <select name="health_status" class="form-select">
          <?php foreach (['Healthy','Under Treatment','Recovered'] as $hs): ?>
          <option value="<?= $hs ?>" <?= $old['health_status'] === $hs ? 'selected':'' ?>><?= $hs ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Adoption Status</label>
        <select name="adoption_status" class="form-select">
          <?php foreach (['Available','Adopted','Not Available'] as $as): ?>
          <option value="<?= $as ?>" <?= $old['adoption_status'] === $as ? 'selected':'' ?>><?= $as ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch fs-5">
          <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" <?= $old['is_featured'] ? 'checked':'' ?>>
          <label class="form-check-label fw-600" for="is_featured" style="color:var(--kg-green-dark);">Featured</label>
        </div>
      </div>
          <div class="col-md-6">
            <label for="whatsapp_number" class="form-label">Custom WhatsApp Number</label>
            <input type="text" id="whatsapp_number" name="whatsapp_number" class="form-control" value="<?= e($old['whatsapp_number'] ?? '') ?>" placeholder="e.g. 919876543210">
            <div class="form-text">Leave blank to use the global site default.</div>
          </div>
          <div class="col-md-6">
            <label for="whatsapp_message" class="form-label">Custom WhatsApp Message</label>
            <input type="text" id="whatsapp_message" name="whatsapp_message" class="form-control" value="<?= e($old['whatsapp_message'] ?? '') ?>" placeholder="Hello, I want to adopt...">
            <div class="form-text">Leave blank to auto-generate based on cow name and breed.</div>
          </div>

          <div class="col-12 mt-4">
            <label for="description" class="form-label">Description / History</label>
            <textarea id="description" name="description" class="form-control" rows="4"><?= e($old['description'] ?? '') ?></textarea>
          </div>
      <div class="col-md-6">
        <label class="form-label">Current Image</label>
        <div>
          <img src="<?= img_url('cows', $old['image']) ?>"
               alt="Current" id="cowPreview"
               style="max-height:100px;border-radius:8px;object-fit:cover;border:2px solid var(--kg-border);"
               onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'">
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Replace Image (optional)</label>
        <input type="file" name="image" class="form-control" accept="image/*" data-preview="cowPreview">
      </div>
    </div>
    <hr class="my-4">
    <div class="d-flex gap-3">
      <button type="submit" class="btn btn-kg-primary px-4"><i class="bi bi-check-circle me-2"></i>Update Cow</button>
      <a href="<?= BASE_URL ?>/admin/cows.php" class="btn btn-kg-outline">Cancel</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
