<?php
/**
 * Admin Add Video — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();

    $old['title'] = sanitize($_POST['title'] ?? '');
    $old['url']   = trim($_POST['url'] ?? '');

    if (empty($old['title'])) $errors[] = 'Title is required.';
    if (empty($old['url'])) {
        $errors[] = 'YouTube URL is required.';
    } else {
        // Extract YouTube ID
        // Supports youtu.be, youtube.com/watch?v=, youtube.com/embed/
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $old['url'], $match);
        $youtube_id = $match[1] ?? null;
        
        if (!$youtube_id) {
            $errors[] = 'Invalid YouTube URL. Please provide a standard link.';
        }
    }

    if (empty($errors)) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("INSERT INTO videos (title, youtube_id) VALUES (:t, :y)");
        $stmt->execute([
            ':t' => $old['title'],
            ':y' => $youtube_id
        ]);
        
        // If this is the first video, make it featured
        $count = $pdo->query("SELECT COUNT(*) FROM videos")->fetchColumn();
        if ($count == 1) {
            $pdo->query("UPDATE videos SET is_featured = 1");
        }

        flash_set('Video added successfully!', 'success');
        redirect(BASE_URL . '/admin/videos.php');
    }
}

$adminPageTitle  = 'Add Video';
$adminActivePage = 'videos';
require_once __DIR__ . '/includes/admin_layout_header.php';
?>

<div class="mb-3">
  <a href="<?= BASE_URL ?>/admin/videos.php" class="text-muted text-decoration-none">
    <i class="bi bi-arrow-left"></i> Back to Videos
  </a>
</div>

<div class="kg-card" style="max-width: 600px;">
  <?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <form method="POST" action="">
    <?= csrf_field() ?>
    
    <div class="mb-3">
      <label for="title" class="form-label">Video Title</label>
      <input type="text" id="title" name="title" class="form-control" value="<?= e($old['title'] ?? '') ?>" required>
    </div>
    
    <div class="mb-4">
      <label for="url" class="form-label">YouTube URL</label>
      <input type="url" id="url" name="url" class="form-control" placeholder="https://www.youtube.com/watch?v=..." value="<?= e($old['url'] ?? '') ?>" required>
      <div class="form-text">Paste the full YouTube video link.</div>
    </div>
    
    <button type="submit" class="btn btn-kg-primary px-4">Save Video</button>
  </form>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
