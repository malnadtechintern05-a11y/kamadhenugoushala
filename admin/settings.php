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

// Handle Checkout Settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_checkout_settings') {
    csrf_validate();

    $settingsToUpdate = [
        'CHECKOUT_MODE_COWS'     => $_POST['checkout_mode_cows'] ?? 'website',
        'CHECKOUT_MODE_PRODUCTS' => $_POST['checkout_mode_products'] ?? 'website',
        'WA_DEFAULT_MSG_COWS'    => $_POST['wa_default_msg_cows'] ?? '',
        'WA_DEFAULT_MSG_PRODUCTS'=> $_POST['wa_default_msg_products'] ?? '',
        'WA_PHONE_COWS'          => preg_replace('/[^0-9]/', '', $_POST['wa_phone_cows'] ?? ''),
        'WA_PHONE_PRODUCTS'      => preg_replace('/[^0-9]/', '', $_POST['wa_phone_products'] ?? ''),
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
            header("Location: settings.php?success=checkout_updated");
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
  <!-- Left Column -->
  <div class="col-lg-6 d-flex flex-column gap-4">
    
    <!-- General Settings Info -->
    <div class="kg-admin-form-card">
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

    <!-- Social Media Links -->
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

  </div> <!-- End Left Column -->

  <!-- Right Column -->
  <div class="col-lg-6 d-flex flex-column gap-4">
    
    <!-- Checkout Settings -->
    <div class="kg-admin-form-card">
      <h5 class="text-kg-green mb-4"><i class="bi bi-cart me-2"></i>Checkout Settings</h5>
      
      <?php if (isset($_GET['success']) && $_GET['success'] === 'checkout_updated'): ?>
      <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Checkout settings updated!</div>
      <?php endif; ?>

      <form method="POST" action="">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update_checkout_settings">
        
        <div class="mb-4">
          <label class="form-label">Cow Adoptions Mode</label>
          <select name="checkout_mode_cows" class="form-select">
            <option value="website" <?= (defined('CHECKOUT_MODE_COWS') && CHECKOUT_MODE_COWS === 'website') ? 'selected' : '' ?>>Website Checkout System</option>
            <option value="whatsapp" <?= (defined('CHECKOUT_MODE_COWS') && CHECKOUT_MODE_COWS === 'whatsapp') ? 'selected' : '' ?>>WhatsApp Redirect</option>
          </select>
          <div class="form-text">Choose how users adopt cows.</div>
        </div>
        
        <div class="mb-4">
          <label class="form-label">Global Cow WhatsApp Message (Optional)</label>
          <textarea name="wa_default_msg_cows" class="form-control" rows="2" placeholder="Hello, I want to adopt the cow: {cow_name}..."><?= e(defined('WA_DEFAULT_MSG_COWS') ? WA_DEFAULT_MSG_COWS : '') ?></textarea>
          <div class="form-text">Use <code>{cow_name}</code> as a placeholder. This will be used if the specific cow doesn't have a custom message.</div>
        </div>

        <div class="mb-4 border-bottom pb-3">
          <label class="form-label">Cow WhatsApp Number (Optional)</label>
          <input type="text" name="wa_phone_cows" class="form-control" value="<?= e(defined('WA_PHONE_COWS') ? WA_PHONE_COWS : '') ?>" placeholder="e.g. 919876543210">
          <div class="form-text">If left blank, the general site phone number will be used. Include country code.</div>
        </div>
        
        <div class="mb-4">
          <label class="form-label">Product Purchases Mode</label>
          <select name="checkout_mode_products" class="form-select">
            <option value="website" <?= (defined('CHECKOUT_MODE_PRODUCTS') && CHECKOUT_MODE_PRODUCTS === 'website') ? 'selected' : '' ?>>Website Checkout System</option>
            <option value="whatsapp" <?= (defined('CHECKOUT_MODE_PRODUCTS') && CHECKOUT_MODE_PRODUCTS === 'whatsapp') ? 'selected' : '' ?>>WhatsApp Redirect</option>
            <option value="both" <?= (defined('CHECKOUT_MODE_PRODUCTS') && CHECKOUT_MODE_PRODUCTS === 'both') ? 'selected' : '' ?>>Both (Website & WhatsApp)</option>
          </select>
          <div class="form-text">Choose how users buy products.</div>
        </div>

        <div class="mb-4">
          <label class="form-label">Global Product WhatsApp Message (Optional)</label>
          <textarea name="wa_default_msg_products" class="form-control" rows="2" placeholder="Hello, I want to buy {qty}x {product_name}..."><?= e(defined('WA_DEFAULT_MSG_PRODUCTS') ? WA_DEFAULT_MSG_PRODUCTS : '') ?></textarea>
          <div class="form-text">Use <code>{product_name}</code>, <code>{qty}</code>, and <code>{price}</code> as placeholders.</div>
        </div>

        <div class="mb-4">
          <label class="form-label">Product WhatsApp Number (Optional)</label>
          <input type="text" name="wa_phone_products" class="form-control" value="<?= e(defined('WA_PHONE_PRODUCTS') ? WA_PHONE_PRODUCTS : '') ?>" placeholder="e.g. 919876543210">
          <div class="form-text">If left blank, the general site phone number will be used. Include country code.</div>
        </div>

        <button type="submit" class="btn btn-kg-primary w-100 mt-auto">
          <i class="bi bi-save me-2"></i>Save Checkout Settings
        </button>
      </form>
    </div>

    <!-- Change Password -->
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
        <button type="submit" class="btn btn-kg-primary w-100">
          <i class="bi bi-check-circle me-2"></i>Update Password
        </button>
      </form>
    </div>

  </div> <!-- End Right Column -->
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
