<?php
/**
 * Adopt Page — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$pdo = getDBConnection();

// Pre-select cow if coming from cow-details.php
$preSelectedCowId = isset($_GET['cow_id']) ? (int)$_GET['cow_id'] : 0;

// Available cows for adoption
$availableCows = $pdo->query("SELECT id, name, breed, age FROM cows WHERE adoption_status='Available' ORDER BY name")->fetchAll();

$errors  = [];
$success = false;
$old     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();

    $old = [
        'cow_id'          => (int)($_POST['cow_id'] ?? 0),
        'adopter_name'    => sanitize($_POST['adopter_name']    ?? ''),
        'adopter_email'   => sanitize($_POST['adopter_email']   ?? ''),
        'adopter_phone'   => sanitize($_POST['adopter_phone']   ?? ''),
        'adopter_address' => sanitize($_POST['adopter_address'] ?? ''),
        'duration_months' => (int)($_POST['duration_months']    ?? 12),
        'message'         => sanitize($_POST['message']         ?? ''),
    ];

    // Validate
    if ($old['cow_id'] <= 0) $errors[] = 'Please select a cow to adopt.';
    if (empty($old['adopter_name']))  $errors[] = 'Your name is required.';
    if (empty($old['adopter_email']) || !is_valid_email($old['adopter_email'])) $errors[] = 'A valid email address is required.';
    if (empty($old['adopter_phone'])) $errors[] = 'Phone number is required.';
    if (!in_array($old['duration_months'], [3,6,12,24], true)) $errors[] = 'Invalid adoption duration.';

    if (empty($errors)) {
        // Verify cow exists and is available
        $stmtC = $pdo->prepare("SELECT id FROM cows WHERE id = :id AND adoption_status='Available'");
        $stmtC->execute([':id' => $old['cow_id']]);
        if (!$stmtC->fetch()) {
            $errors[] = 'The selected cow is no longer available for adoption.';
        }
    }

    if (empty($errors)) {
        $amountPerMonth = 1500.00;
        $stmtIns = $pdo->prepare("
            INSERT INTO adoptions (cow_id, adopter_name, adopter_email, adopter_phone, adopter_address, duration_months, amount_per_month, message, status)
            VALUES (:cow_id, :name, :email, :phone, :address, :duration, :amount, :message, 'Pending')
        ");
        $stmtIns->execute([
            ':cow_id'   => $old['cow_id'],
            ':name'     => $old['adopter_name'],
            ':email'    => $old['adopter_email'],
            ':phone'    => $old['adopter_phone'],
            ':address'  => $old['adopter_address'],
            ':duration' => $old['duration_months'],
            ':amount'   => $amountPerMonth,
            ':message'  => $old['message'],
        ]);
        redirect(BASE_URL . '/thank-you.php?type=adoption');
    }
}

$pageTitle  = 'Adopt a Cow';
$activePage = 'adopt';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="kg-page-banner">
  <div class="container">
    <h1><i class="bi bi-heart-fill me-2"></i>Adopt a Sacred Cow</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Home</a></li>
        <li class="breadcrumb-item active">Adopt</li>
      </ol>
    </nav>
  </div>
</section>

<section class="kg-section">
  <div class="container">
    <div class="row g-5">
      <!-- Info -->
      <div class="col-lg-5">
        <div class="kg-section-label">Gau Mata Adoption</div>
        <h2 class="kg-section-title">Why Adopt?</h2>
        <div class="kg-divider mb-4"></div>
        <p style="color:var(--kg-text-muted);">
          By adopting a cow at Kamadhenu Goushala, you become her guardian. 
          Your monthly contribution covers her food, medical care, shelter, and daily seva.
        </p>
        <ul class="list-unstyled mt-3" style="color:var(--kg-text-muted);">
          <?php
          $benefits = [
            'Monthly updates with photos of your adopted cow',
            'Certificate of Adoption',
            'Personalised blessings from our Goushala',
            '80G tax exemption receipt',
            'Invite to visit the Goushala anytime',
            'Annual Gau Puja prasad delivered to your home',
          ];
          foreach ($benefits as $b): ?>
          <li class="d-flex align-items-start gap-2 mb-2">
            <i class="bi bi-check-circle-fill mt-1" style="color:var(--kg-green);font-size:.9rem;flex-shrink:0;"></i>
            <span><?= e($b) ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
        <div class="kg-info-box mt-4">
          <p class="mb-0" style="color:var(--kg-green-dark);font-size:.9rem;">
            <strong>Adoption Cost:</strong> ₹1,500 per month per cow<br>
            <strong>Minimum Duration:</strong> 3 months
          </p>
        </div>

        <!-- Available Cows Preview -->
        <?php if (!empty($availableCows)): ?>
        <h5 class="mt-4 mb-3 text-kg-green">Available for Adoption</h5>
        <?php foreach (array_slice($availableCows, 0, 4) as $ac): ?>
        <div class="d-flex align-items-center gap-3 mb-2 p-2 bg-white rounded-3 border" style="border-color:var(--kg-border)!important;">
          <div style="width:40px;height:40px;background:var(--kg-green-pale);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--kg-green);font-size:1.2rem;">
            <i class="bi bi-emoji-heart-eyes"></i>
          </div>
          <div>
            <div class="fw-600" style="font-size:.9rem;"><?= e($ac['name']) ?></div>
            <div style="font-size:.78rem;color:var(--kg-text-muted);"><?= e($ac['breed']) ?> · <?= (int)$ac['age'] ?> yrs</div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Form -->
      <div class="col-lg-7">
        <div class="kg-form-card">
          <h4 class="mb-4 text-kg-green"><i class="bi bi-heart-fill me-2"></i>Adoption Application</h4>

          <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
              <?php foreach ($errors as $err): ?>
              <li><?= e($err) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>

          <form method="POST" action="" data-validate novalidate>
            <?= csrf_field() ?>

            <div class="mb-3">
              <label for="cow_id" class="form-label">Select Cow to Adopt <span class="text-danger">*</span></label>
              <select name="cow_id" id="cow_id" class="form-select" required>
                <option value="">— Choose a cow —</option>
                <?php foreach ($availableCows as $ac): ?>
                <option value="<?= (int)$ac['id'] ?>"
                  <?= ((int)($old['cow_id'] ?? $preSelectedCowId) === (int)$ac['id']) ? 'selected' : '' ?>>
                  <?= e($ac['name']) ?> (<?= e($ac['breed']) ?>, <?= (int)$ac['age'] ?> yrs)
                </option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">Please select a cow.</div>
            </div>

            <div class="row g-3">
              <div class="col-md-6 mb-3">
                <label for="adopter_name" class="form-label">Your Full Name <span class="text-danger">*</span></label>
                <input type="text" id="adopter_name" name="adopter_name" class="form-control"
                       placeholder="e.g. Ramesh Sharma"
                       value="<?= e($old['adopter_name'] ?? '') ?>" required>
                <div class="invalid-feedback">Name is required.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="adopter_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" id="adopter_email" name="adopter_email" class="form-control"
                       placeholder="you@example.com"
                       value="<?= e($old['adopter_email'] ?? '') ?>" required>
                <div class="invalid-feedback">Valid email required.</div>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-md-6 mb-3">
                <label for="adopter_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                <input type="tel" id="adopter_phone" name="adopter_phone" class="form-control"
                       placeholder="+91 98765 43210"
                       value="<?= e($old['adopter_phone'] ?? '') ?>" required>
                <div class="invalid-feedback">Phone number is required.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="duration_months" class="form-label">Duration <span class="text-danger">*</span></label>
                <select name="duration_months" id="duration_months" class="form-select" required>
                  <option value="3"  <?= ($old['duration_months'] ?? 12) == 3  ? 'selected':'' ?>>3 months — ₹4,500</option>
                  <option value="6"  <?= ($old['duration_months'] ?? 12) == 6  ? 'selected':'' ?>>6 months — ₹9,000</option>
                  <option value="12" <?= ($old['duration_months'] ?? 12) == 12 ? 'selected':'' ?>>12 months — ₹18,000</option>
                  <option value="24" <?= ($old['duration_months'] ?? 12) == 24 ? 'selected':'' ?>>24 months — ₹36,000</option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label for="adopter_address" class="form-label">Your Address</label>
              <textarea name="adopter_address" id="adopter_address" class="form-control" rows="2"
                        placeholder="Your full postal address"><?= e($old['adopter_address'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
              <label for="message" class="form-label">Message / Special Wishes</label>
              <textarea name="message" id="message" class="form-control" rows="3"
                        placeholder="Any message or special instructions for us…"><?= e($old['message'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-kg-gold w-100 py-3" id="adoptSubmitBtn">
              <i class="bi bi-heart-fill me-2"></i>Submit Adoption Application
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
