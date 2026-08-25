<?php
/**
 * Supporter Login — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

if (isset($_SESSION['supporter_id'])) {
    redirect(BASE_URL . '/supporter-dashboard.php');
}

$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals(csrf_token(), $token)) {
        $errors[] = "Invalid session. Please try again.";
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $old['email'] = $email;

        if (!$email || !$password) {
            $errors[] = "Email and password are required.";
        } else {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM supporters WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $supporter = $stmt->fetch();
            
            if ($supporter && password_verify($password, $supporter['password_hash'])) {
                $_SESSION['supporter_id'] = $supporter['id'];
                $_SESSION['supporter_name'] = $supporter['name'];
                redirect(BASE_URL . '/supporter-dashboard.php');
            } else {
                $errors[] = "Invalid email or password.";
            }
        }
    }
}

$pageTitle = 'Supporter Login - Kamadhenu Goushala';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5" style="min-height: calc(100vh - 300px);">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0" style="border-radius:1rem;">
                <div class="card-header bg-success text-white text-center py-4" style="border-top-left-radius:1rem; border-top-right-radius:1rem;">
                    <h4 class="mb-0 fw-bold">Supporter Login</h4>
                </div>
                <div class="card-body p-4 p-md-5">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $e): ?>
                                    <li><?= e($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg" value="<?= e($old['email'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg" required>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 mb-3 rounded-pill fw-bold">Login</button>
                        <div class="text-center">
                            <p class="mb-0 text-muted">Don't have an account? <a href="<?= BASE_URL ?>/supporter-register.php" class="fw-bold text-success text-decoration-none">Register here</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
