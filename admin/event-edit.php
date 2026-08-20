<?php
/**
 * Admin Edit Event — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    set_flash('danger', 'Invalid event ID.');
    redirect(BASE_URL . '/admin/events.php');
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
$stmt->execute([':id' => $id]);
$event = $stmt->fetch();

if (!$event) {
    set_flash('danger', 'Event not found.');
    redirect(BASE_URL . '/admin/events.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();

    $event['title']       = sanitize($_POST['title']       ?? '');
    $event['description'] = sanitize($_POST['description'] ?? '');
    $event['event_date']  = sanitize($_POST['event_date']  ?? '');
    $event['location']    = sanitize($_POST['location']    ?? '');
    $event['is_active']   = isset($_POST['is_active']) ? 1 : 0;

    if (empty($event['title']))       $errors[] = 'Title is required.';
    if (empty($event['event_date']))  $errors[] = 'Event Date & Time is required.';
    if (empty($event['location']))    $errors[] = 'Location is required.';

    $imageFilename = $event['image'];
    if (!empty($_FILES['image']['name'])) {
        try {
            $newImage = upload_image($_FILES['image'], UPLOAD_EVENTS_DIR);
            if ($imageFilename && file_exists(UPLOAD_EVENTS_DIR . $imageFilename)) {
                unlink(UPLOAD_EVENTS_DIR . $imageFilename);
            }
            $imageFilename = $newImage;
        } catch (RuntimeException $e) {
            $errors[] = 'Image upload failed: ' . $e->getMessage();
        }
    }

    if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
        if ($imageFilename && file_exists(UPLOAD_EVENTS_DIR . $imageFilename)) {
            unlink(UPLOAD_EVENTS_DIR . $imageFilename);
        }
        $imageFilename = null;
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE events 
            SET title = :title, description = :description, event_date = :event_date, 
                location = :location, image = :image, is_active = :is_active
            WHERE id = :id
        ");
        $stmt->execute([
            ':title'       => $event['title'],
            ':description' => $event['description'],
            ':event_date'  => $event['event_date'],
            ':location'    => $event['location'],
            ':image'       => $imageFilename,
            ':is_active'   => $event['is_active'],
            ':id'          => $id
        ]);
        
        set_flash('success', 'Event updated successfully.');
        redirect(BASE_URL . '/admin/events.php');
    }
}

$adminPageTitle  = 'Edit Event';
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
        <input type="text" name="title" class="form-control" value="<?= e($event['title']) ?>" required>
      </div>

      <div class="col-md-4">
        <label class="form-label fw-600">Date & Time <span class="text-danger">*</span></label>
        <?php 
          // Format for datetime-local input
          $dt = new DateTime($event['event_date']);
          $formattedDate = $dt->format('Y-m-d\TH:i');
        ?>
        <input type="datetime-local" name="event_date" class="form-control" value="<?= $formattedDate ?>" required>
      </div>

      <div class="col-12">
        <label class="form-label fw-600">Location <span class="text-danger">*</span></label>
        <input type="text" name="location" class="form-control" value="<?= e($event['location']) ?>" required>
      </div>

      <div class="col-12">
        <label class="form-label fw-600">Description</label>
        <textarea name="description" class="form-control" rows="5"><?= e($event['description']) ?></textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label fw-600">Event Image</label>
        <?php if (!empty($event['image'])): ?>
          <div class="mb-2 position-relative d-inline-block">
            <img src="<?= img_url('events', $event['image']) ?>" alt="Event Image" class="img-thumbnail" style="max-height:150px;">
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_img">
            <label class="form-check-label text-danger" for="remove_img">Remove Current Image</label>
          </div>
        <?php endif; ?>
        <input type="file" name="image" class="form-control" accept="<?= implode(',', ALLOWED_MIME_TYPES) ?>">
        <div class="form-text">Upload to replace image. Max size: 5MB. Formats: JPG, PNG, WEBP.</div>
      </div>

      <div class="col-md-6 d-flex align-items-end">
        <div class="form-check form-switch fs-5 mb-2">
          <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= $event['is_active'] ? 'checked':'' ?>>
          <label class="form-check-label fw-600" for="is_active">Active Event</label>
        </div>
      </div>
    </div>
    
    <hr class="my-4">
    <div class="text-end">
      <a href="<?= BASE_URL ?>/admin/events.php" class="btn btn-light me-2">Cancel</a>
      <button type="submit" class="btn btn-kg-primary px-4">Update Event</button>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
