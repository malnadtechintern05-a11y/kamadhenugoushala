<?php
/**
 * Admin Settings — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$pdo = getDBConnection();
$success = false;
$errors  = [];

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    csrf_validate();

    $currentPass  = $_POST['current_password'] ?? '';
    $newPass      = $_POST['new_password']      ?? '';
    $confirmPass  = $_POST['confirm_password']  ?? '';

    $adminId = (int)($_SESSION['admin_id'] ?? 0);
    $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = :id");
    $stmt->execute([':id' => $adminId]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($currentPass, $admin['password'])) {
        $errors[] = 'Current password is incorrect.';
    } elseif (strlen($newPass) < 8) {
        $errors[] = 'New password must be at least 8 characters.';
    } elseif ($newPass !== $confirmPass) {
        $errors[] = 'New passwords do not match.';
    }

    if (empty($errors)) {
        $hash = password_hash($newPass, PASSWORD_BCRYPT, ['cost' => 12]);
        $pdo->prepare("UPDATE admins SET password = :pass WHERE id = :id")->execute([':pass' => $hash, ':id' => $adminId]);
        $success = true;
    }
}

$adminPageTitle  = 'Settings';
$adminActivePage = 'settings';
require_once __DIR__ . '/includes/admin_layout_header.php';
?>

<div class="row g-4">
  <!-- Change Password -->
  <div class="col-lg-6">
    <div class="kg-admin-form-card">
      <h5 class="text-kg-green mb-4"><i class="bi bi-lock me-2"></i>Change Password</h5>

      <?php if ($success): ?>
      <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Password changed successfully!</div>
      <?php endif; ?>
      <?php if (!empty($errors)): ?>
      <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>

      <form method="POST" action="">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="change_password">
        <div class="mb-3">
          <label class="form-label">Current Password</label>
          <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">New Password (min 8 characters)</label>
          <input type="password" name="new_password" class="form-control" required minlength="8">
        </div>
        <div class="mb-4">
          <label class="form-label">Confirm New Password</label>
          <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-kg-primary">
          <i class="bi bi-check-circle me-2"></i>Update Password
        </button>
      </form>
    </div>
  </div>

  <!-- Configuration Info -->
  <div class="col-lg-6">
    <div class="kg-admin-form-card">
      <h5 class="text-kg-green mb-4"><i class="bi bi-info-circle me-2"></i>Configuration</h5>
      <table class="table table-sm table-borderless">
        <tr><td class="fw-600 text-muted" style="width:140px;">Site Name</td><td><?= e(SITE_NAME) ?></td></tr>
        <tr><td class="fw-600 text-muted">Base URL</td><td><code><?= e(BASE_URL) ?></code></td></tr>
        <tr><td class="fw-600 text-muted">Site Email</td><td><?= e(SITE_EMAIL) ?></td></tr>
        <tr><td class="fw-600 text-muted">Phone</td><td><?= e(SITE_PHONE) ?></td></tr>
        <tr><td class="fw-600 text-muted">PHP Version</td><td><?= phpversion() ?></td></tr>
        <tr><td class="fw-600 text-muted">Timezone</td><td><?= date_default_timezone_get() ?></td></tr>
        <tr><td class="fw-600 text-muted">Max Upload</td><td>5 MB</td></tr>
      </table>
      <div class="kg-info-box mt-3">
        <p class="mb-0" style="font-size:.85rem;color:var(--kg-green-dark);">
          To update site settings, edit <code>config/config.php</code> directly on the server.
        </p>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
