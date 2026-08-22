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

// Handle General Settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_general_settings') {
    csrf_validate();

    $settingsToUpdate = [
        'SITE_NAME'        => $_POST['site_name'] ?? '',
        'SITE_TAGLINE'     => $_POST['site_tagline'] ?? '',
        'SITE_EMAIL'       => $_POST['site_email'] ?? '',
        'SITE_PHONE'       => $_POST['site_phone'] ?? '',
        'SITE_ADDRESS'     => $_POST['site_address'] ?? '',
    ];

    $configFile = __DIR__ . '/../config/config.php';
    if (is_writable($configFile)) {
        $configContent = file_get_contents($configFile);
        foreach ($settingsToUpdate as $key => $value) {
            $valueEscaped = str_replace("'", "\'", $value);
            $pattern = "/define\(\s*['\"]" . preg_quote($key, '/') . "['\"]\s*,\s*['\"].*?['\"]\s*\);/is";
            $replacement = "define('$key', '$valueEscaped');";
            $configContent = preg_replace($pattern, $replacement, $configContent);
        }
        if (file_put_contents($configFile, $configContent)) {
            header("Location: settings.php?success=general_updated");
            exit;
        } else {
            $errors[] = 'Failed to write to config.php. Please try again.';
        }
    } else {
        $errors[] = 'config.php is not writable. Check file permissions.';
    }
}

// Handle Social Media Settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_social_settings') {
    csrf_validate();

    $settingsToUpdate = [
        'SOCIAL_FACEBOOK'  => $_POST['social_facebook'] ?? '',
        'SOCIAL_INSTAGRAM' => $_POST['social_instagram'] ?? '',
        'SOCIAL_TWITTER'   => $_POST['social_twitter'] ?? '',
        'SOCIAL_WHATSAPP'  => $_POST['social_whatsapp'] ?? '',
    ];

    $configFile = __DIR__ . '/../config/config.php';
    if (is_writable($configFile)) {
        $configContent = file_get_contents($configFile);
        foreach ($settingsToUpdate as $key => $value) {
            $valueEscaped = str_replace("'", "\'", $value);
            $pattern = "/define\(\s*['\"]" . preg_quote($key, '/') . "['\"]\s*,\s*['\"].*?['\"]\s*\);/is";
            $replacement = "define('$key', '$valueEscaped');";
            $configContent = preg_replace($pattern, $replacement, $configContent);
        }
        if (file_put_contents($configFile, $configContent)) {
            header("Location: settings.php?success=social_updated");
            exit;
        } else {
            $errors[] = 'Failed to write to config.php. Please try again.';
        }
    } else {
        $errors[] = 'config.php is not writable. Check file permissions.';
    }
}

if (isset($_GET['success']) && $_GET['success'] === 'settings_updated') {
    $settings_success = true;
} else {
    $settings_success = false;
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

  <!-- General Settings Info -->
  <div class="col-lg-6">
    <div class="kg-admin-form-card h-100">
      <h5 class="text-kg-green mb-4"><i class="bi bi-gear me-2"></i>General Settings</h5>
      
      <?php if (isset($_GET['success']) && $_GET['success'] === 'general_updated'): ?>
      <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>General settings updated!</div>
      <?php endif; ?>

      <form method="POST" action="">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update_general_settings">
        
        <div class="mb-3">
          <label class="form-label">Site Name</label>
          <input type="text" name="site_name" class="form-control" value="<?= e(SITE_NAME) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Tagline</label>
          <input type="text" name="site_tagline" class="form-control" value="<?= e(SITE_TAGLINE) ?>">
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="site_email" class="form-control" value="<?= e(SITE_EMAIL) ?>">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="site_phone" class="form-control" value="<?= e(SITE_PHONE) ?>">
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label">Physical Address</label>
          <input type="text" name="site_address" class="form-control" value="<?= e(SITE_ADDRESS) ?>">
        </div>

        <button type="submit" class="btn btn-kg-primary w-100 mt-auto">
          <i class="bi bi-save me-2"></i>Save General Settings
        </button>
      </form>
    </div>
  </div>

  <!-- Social Media Links -->
  <div class="col-lg-12">
    <div class="kg-admin-form-card">
      <h5 class="text-kg-green mb-4"><i class="bi bi-share me-2"></i>Social Media Links</h5>
      
      <?php if (isset($_GET['success']) && $_GET['success'] === 'social_updated'): ?>
      <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Social links updated!</div>
      <?php endif; ?>

      <form method="POST" action="">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update_social_settings">
        
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label"><i class="bi bi-facebook me-2 text-primary"></i>Facebook URL</label>
            <input type="text" name="social_facebook" class="form-control" value="<?= e(defined('SOCIAL_FACEBOOK') ? SOCIAL_FACEBOOK : '') ?>" placeholder="https://facebook.com/yourpage">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label"><i class="bi bi-instagram me-2 text-danger"></i>Instagram URL</label>
            <input type="text" name="social_instagram" class="form-control" value="<?= e(defined('SOCIAL_INSTAGRAM') ? SOCIAL_INSTAGRAM : '') ?>" placeholder="https://instagram.com/yourpage">
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label"><i class="bi bi-twitter-x me-2"></i>Twitter URL</label>
            <input type="text" name="social_twitter" class="form-control" value="<?= e(defined('SOCIAL_TWITTER') ? SOCIAL_TWITTER : '') ?>" placeholder="https://twitter.com/yourpage">
          </div>
          <div class="col-md-6 mb-4">
            <label class="form-label"><i class="bi bi-whatsapp me-2 text-success"></i>WhatsApp Number</label>
            <input type="text" name="social_whatsapp" class="form-control" value="<?= e(defined('SOCIAL_WHATSAPP') ? SOCIAL_WHATSAPP : '') ?>" placeholder="+919876543210">
          </div>
        </div>

        <button type="submit" class="btn btn-kg-primary w-100">
          <i class="bi bi-save me-2"></i>Save Social Media Links
        </button>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
