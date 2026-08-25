<?php
/**
 * Supporter Registration — Kamadhenu Goushala
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
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        
        $old['name'] = $name;
        $old['email'] = $email;
        $old['phone'] = $phone;

        if (!$name || !$email || !$password) {
            $errors[] = "Name, email, and password are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        } elseif ($password !== $confirm) {
            $errors[] = "Passwords do not match.";
        } else {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT id FROM supporters WHERE email = :email");
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $errors[] = "This email is already registered. Please log in.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $insert = $pdo->prepare("INSERT INTO supporters (name, email, password_hash, phone) VALUES (:name, :email, :hash, :phone)");
                if ($insert->execute([':name' => $name, ':email' => $email, ':hash' => $hash, ':phone' => $phone])) {
                    $_SESSION['supporter_id'] = $pdo->lastInsertId();
                    $_SESSION['supporter_name'] = $name;
                    redirect(BASE_URL . '/supporter-dashboard.php');
                } else {
                    $errors[] = "Failed to create account. Please try again.";
                }
            }
        }
    }
}

$pageTitle = 'Supporter Registration - Kamadhenu Goushala';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0" style="border-radius:1rem;">
                <div class="card-header bg-success text-white text-center py-4" style="border-top-left-radius:1rem; border-top-right-radius:1rem;">
                    <h4 class="mb-0 fw-bold">Become a Supporter</h4>
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
                            <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-lg" value="<?= e($old['name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control form-control-lg" value="<?= e($old['email'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="tel" name="phone" class="form-control form-control-lg" value="<?= e($old['phone'] ?? '') ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control form-control-lg" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="confirm_password" class="form-control form-control-lg" required>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 mb-3 rounded-pill fw-bold">Register</button>
                        <div class="text-center">
                            <p class="mb-0 text-muted">Already have an account? <a href="<?= BASE_URL ?>/supporter-login.php" class="fw-bold text-success text-decoration-none">Login here</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
