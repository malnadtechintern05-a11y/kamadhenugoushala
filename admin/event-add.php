<?php
/**
 * Admin Add Event — Kamadhenu Goushala
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
        'event_date'  => sanitize($_POST['event_date']  ?? ''),
        'location'    => sanitize($_POST['location']    ?? ''),
        'is_active'   => isset($_POST['is_active']) ? 1 : 0,
    ];

    if (empty($old['title']))       $errors[] = 'Title is required.';
    if (empty($old['event_date']))  $errors[] = 'Event Date & Time is required.';
    if (empty($old['location']))    $errors[] = 'Location is required.';

    $imageFilename = null;
    if (!empty($_FILES['image']['name'])) {
        try {
            $imageFilename = upload_image($_FILES['image'], UPLOAD_EVENTS_DIR);
        } catch (RuntimeException $e) {
            $errors[] = 'Image upload failed: ' . $e->getMessage();
        }
    }

    if (empty($errors)) {
        $pdo  = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO events (title, description, event_date, location, image, is_active)
            VALUES (:title, :description, :event_date, :location, :image, :is_active)
        ");
        $stmt->execute([
            ':title'       => $old['title'],
            ':description' => $old['description'],
            ':event_date'  => $old['event_date'],
            ':location'    => $old['location'],
            ':image'       => $imageFilename,
            ':is_active'   => $old['is_active'],
        ]);
        
        set_flash('success', 'Event added successfully.');
        redirect(BASE_URL . '/admin/events.php');
    }
}

$adminPageTitle  = 'Add New Event';
$adminActivePage = 'events';
require_once __DIR__ . '/includes/admin_layout_header.php';
?>

<div class="mb-3">
  <a href="<?= BASE_URL ?>/admin/events.php" class="text-decoration-none text-muted">
    <i class="bi bi-arrow-left me-1"></i> Back to Events
  </a>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger shadow-sm border-0">
    <ul class="mb-0">
      <?php foreach ($errors as $e) echo "<li>" . e($e) . "</li>"; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="kg-card">
  <form action="" method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>
    
    <div class="row g-4">
      <div class="col-md-8">
        <label class="form-label fw-600">Event Title <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" value="<?= e($old['title'] ?? '') ?>" required>
      </div>

      <div class="col-md-4">
        <label class="form-label fw-600">Date & Time <span class="text-danger">*</span></label>
        <input type="datetime-local" name="event_date" class="form-control" value="<?= e($old['event_date'] ?? '') ?>" required>
      </div>

      <div class="col-12">
        <label class="form-label fw-600">Location <span class="text-danger">*</span></label>
        <input type="text" name="location" class="form-control" value="<?= e($old['location'] ?? '') ?>" required>
      </div>

      <div class="col-12">
        <label class="form-label fw-600">Description</label>
        <textarea name="description" class="form-control" rows="5"><?= e($old['description'] ?? '') ?></textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label fw-600">Event Image</label>
        <input type="file" name="image" class="form-control" accept="<?= implode(',', ALLOWED_MIME_TYPES) ?>">
        <div class="form-text">Max size: 5MB. Formats: JPG, PNG, WEBP.</div>
      </div>

      <div class="col-md-6 d-flex align-items-end">
        <div class="form-check form-switch fs-5 mb-2">
          <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= (!isset($old['is_active']) || $old['is_active']) ? 'checked':'' ?>>
          <label class="form-check-label fw-600" for="is_active">Active Event</label>
        </div>
      </div>
    </div>
    
    <hr class="my-4">
    <div class="text-end">
      <a href="<?= BASE_URL ?>/admin/events.php" class="btn btn-light me-2">Cancel</a>
      <button type="submit" class="btn btn-kg-primary px-4">Save Event</button>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
