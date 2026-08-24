<?php
/**
 * Admin Add Cow — Kamadhenu Goushala
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
        'name'             => sanitize($_POST['name']             ?? ''),
        'breed'            => sanitize($_POST['breed']            ?? ''),
        'age'              => (int)($_POST['age']                 ?? 0),
        'gender'           => sanitize($_POST['gender']           ?? 'Female'),
        'color'            => sanitize($_POST['color']            ?? ''),
        'weight_kg'        => sanitize($_POST['weight_kg']        ?? ''),
        'health_status'    => sanitize($_POST['health_status']    ?? 'Healthy'),
        'adoption_status'  => sanitize($_POST['adoption_status']  ?? 'Available'),
        'description'      => sanitize($_POST['description']      ?? ''),
        'whatsapp_number'  => sanitize($_POST['whatsapp_number']  ?? ''),
        'whatsapp_message' => sanitize($_POST['whatsapp_message'] ?? ''),
        'is_featured'      => isset($_POST['is_featured']) ? 1 : 0,
    ];

    $validGenders  = ['Female','Male','Calf'];
    $validHealth   = ['Healthy','Under Treatment','Recovered'];
    $validAdoption = ['Available','Adopted','Not Available'];

    if (empty($old['name']))    $errors[] = 'Name is required.';
    if (!in_array($old['gender'], $validGenders, true))   $errors[] = 'Invalid gender.';
    if (!in_array($old['health_status'], $validHealth, true)) $errors[] = 'Invalid health status.';
    if (!in_array($old['adoption_status'], $validAdoption, true)) $errors[] = 'Invalid adoption status.';

    $imageFilename = null;
    if (!empty($_FILES['image']['name'])) {
        try {
            $imageFilename = upload_image($_FILES['image'], UPLOAD_COWS_DIR);
        } catch (RuntimeException $e) {
            $errors[] = 'Image upload failed: ' . $e->getMessage();
        }
    }

    if (empty($errors)) {
        $pdo  = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO cows (name, breed, age, gender, color, weight_kg, health_status, adoption_status, description, whatsapp_number, whatsapp_message, image, is_featured)
            VALUES (:name, :breed, :age, :gender, :color, :weight_kg, :health, :adoption, :desc, :whatsapp_number, :whatsapp_message, :image, :featured)
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
            ':whatsapp_number'  => $old['whatsapp_number'] ?: null,
            ':whatsapp_message' => $old['whatsapp_message'] ?: null,
            ':image'    => $imageFilename,
            ':featured' => $old['is_featured']
        ]);
        set_flash('success', 'Cow "' . $old['name'] . '" added successfully.');
        redirect(BASE_URL . '/admin/cows.php');
    }
}

$adminPageTitle  = 'Add New Cow';
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
    <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
  </div>
  <?php endif; ?>

  <form method="POST" action="" enctype="multipart/form-data" data-validate novalidate>
    <?= csrf_field() ?>
    <div class="row g-4">
      <div class="col-md-6">
        <label class="form-label">Cow Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" value="<?= e($old['name'] ?? '') ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Breed</label>
        <input type="text" name="breed" class="form-control" placeholder="e.g. Gir, Sahiwal" value="<?= e($old['breed'] ?? '') ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Age (years)</label>
        <input type="number" name="age" class="form-control" min="0" value="<?= (int)($old['age'] ?? 0) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select">
          <?php foreach (['Female','Male','Calf'] as $g): ?>
          <option value="<?= $g ?>" <?= ($old['gender'] ?? 'Female') === $g ? 'selected':'' ?>><?= $g ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Color</label>
        <input type="text" name="color" class="form-control" placeholder="e.g. Golden Brown" value="<?= e($old['color'] ?? '') ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Weight (kg)</label>
        <input type="number" name="weight_kg" class="form-control" step="0.01" min="0" value="<?= e($old['weight_kg'] ?? '') ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Health Status</label>
        <select name="health_status" class="form-select">
          <?php foreach (['Healthy','Under Treatment','Recovered'] as $hs): ?>
          <option value="<?= $hs ?>" <?= ($old['health_status'] ?? 'Healthy') === $hs ? 'selected':'' ?>><?= $hs ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Adoption Status</label>
        <select name="adoption_status" class="form-select">
          <?php foreach (['Available','Adopted','Not Available'] as $as): ?>
          <option value="<?= $as ?>" <?= ($old['adoption_status'] ?? 'Available') === $as ? 'selected':'' ?>><?= $as ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch fs-5">
          <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1"
                 <?= ($old['is_featured'] ?? 0) ? 'checked':'' ?>>
          <label class="form-check-label fw-600" for="is_featured" style="color:var(--kg-green-dark);">Featured on Homepage</label>
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
        <label class="form-label">Cow Image (JPG/PNG/WebP, max 5MB)</label>
        <input type="file" name="image" id="cowImage" class="form-control" accept="image/*" data-preview="cowPreview">
      </div>
      <div class="col-md-6 d-flex align-items-end">
        <img id="cowPreview" src="" alt="Preview" class="d-none" style="max-height:120px;border-radius:8px;object-fit:cover;">
      </div>
    </div>
    <hr class="my-4">
    <div class="d-flex gap-3">
      <button type="submit" class="btn btn-kg-primary px-4">
        <i class="bi bi-check-circle me-2"></i>Save Cow
      </button>
      <a href="<?= BASE_URL ?>/admin/cows.php" class="btn btn-kg-outline">Cancel</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
