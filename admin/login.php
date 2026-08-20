<?php
/**
 * Admin Login — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

// Already logged in → redirect to dashboard
if (is_admin_logged_in()) {
    redirect(BASE_URL . '/admin/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();

    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $pdo  = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            admin_login((int)$admin['id'], $admin['username']);
            redirect(BASE_URL . '/admin/dashboard.php');
        } else {
            $error = 'Invalid username or password.';
            // Small delay to deter brute force
            sleep(1);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | <?= SITE_NAME ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Noto+Serif:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <style>
    body { background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .kg-login-card { background: #fff; border-radius: 20px; padding: 2.5rem; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,.3); }
    .kg-login-logo { width: 64px; height: 64px; background: linear-gradient(135deg, #2d6a4f, #40916c); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #fff; margin: 0 auto 1rem; }
  </style>
</head>
<body>
  <div class="kg-login-card">
    <div class="text-center mb-4">
      <div class="kg-login-logo"><i class="bi bi-flower2"></i></div>
      <h4 style="font-family:'Noto Serif',serif;color:#1b4332;">Kamadhenu Goushala</h4>
      <p style="color:#888;font-size:.85rem;margin:0;">Admin Panel</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger py-2">
      <i class="bi bi-exclamation-triangle me-1"></i><?= e($error) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="">
      <?= csrf_field() ?>

      <div class="mb-3">
        <label for="username" class="form-label fw-600" style="color:#2d6a4f;">Username</label>
        <div class="input-group">
          <span class="input-group-text" style="background:#f0faf4;border-color:#c8e6d1;"><i class="bi bi-person-fill" style="color:#2d6a4f;"></i></span>
          <input type="text" id="username" name="username" class="form-control"
                 placeholder="admin" required autocomplete="username"
                 value="<?= e($_POST['username'] ?? '') ?>">
        </div>
      </div>

      <div class="mb-4">
        <label for="password" class="form-label fw-600" style="color:#2d6a4f;">Password</label>
        <div class="input-group">
          <span class="input-group-text" style="background:#f0faf4;border-color:#c8e6d1;"><i class="bi bi-lock-fill" style="color:#2d6a4f;"></i></span>
          <input type="password" id="password" name="password" class="form-control"
                 placeholder="••••••••" required autocomplete="current-password">
        </div>
      </div>

      <button type="submit" class="btn w-100 py-2" style="background:linear-gradient(135deg,#2d6a4f,#40916c);color:#fff;font-weight:600;border-radius:10px;">
        <i class="bi bi-box-arrow-in-right me-2"></i>Login
      </button>
    </form>

    <div class="text-center mt-3">
      <a href="<?= BASE_URL ?>/" style="color:#888;font-size:.82rem;">← Back to Website</a>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
