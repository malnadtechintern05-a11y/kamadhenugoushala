<?php
/**
 * Donate Page — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$errors  = [];
$old     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();

    $old = [
        'donor_name'     => sanitize($_POST['donor_name']     ?? ''),
        'donor_email'    => sanitize($_POST['donor_email']    ?? ''),
        'donor_phone'    => sanitize($_POST['donor_phone']    ?? ''),
        'amount'         => sanitize($_POST['amount']         ?? ''),
        'purpose'        => sanitize($_POST['purpose']        ?? 'General'),
        'payment_method' => sanitize($_POST['payment_method'] ?? 'UPI'),
        'transaction_id' => sanitize($_POST['transaction_id'] ?? ''),
        'message'        => sanitize($_POST['message']        ?? ''),
    ];

    $validPurposes = ['General','Cow Feed','Medical','Infrastructure','Gau Seva','Other'];
    $validPayments = ['UPI','Bank Transfer','Cash','Online','Other'];

    if (empty($old['donor_name']))  $errors[] = 'Your name is required.';
    if (empty($old['donor_email']) || !is_valid_email($old['donor_email'])) $errors[] = 'A valid email address is required.';
    if (!is_valid_amount($old['amount'])) $errors[] = 'Please enter a valid donation amount (positive number).';
    if (!in_array($old['purpose'], $validPurposes, true)) $errors[] = 'Invalid donation purpose.';
    if (!in_array($old['payment_method'], $validPayments, true)) $errors[] = 'Invalid payment method.';

    if (empty($errors)) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO donations (donor_name, donor_email, donor_phone, amount, purpose, payment_method, transaction_id, message, status)
            VALUES (:name, :email, :phone, :amount, :purpose, :payment_method, :txn_id, :message, 'Completed')
        ");
        $stmt->execute([
            ':name'           => $old['donor_name'],
            ':email'          => $old['donor_email'],
            ':phone'          => $old['donor_phone'],
            ':amount'         => (float)$old['amount'],
            ':purpose'        => $old['purpose'],
            ':payment_method' => $old['payment_method'],
            ':txn_id'         => $old['transaction_id'],
            ':message'        => $old['message'],
        ]);
        redirect(BASE_URL . '/thank-you.php?type=donation');
    }
}

$pageTitle  = 'Donate';
$activePage = 'donate';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="kg-page-banner">
  <div class="container">
    <h1><i class="bi bi-currency-rupee me-2"></i>Donate to Gau Seva</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Home</a></li>
        <li class="breadcrumb-item active">Donate</li>
      </ol>
    </nav>
  </div>
</section>

<section class="kg-section">
  <div class="container">
    <div class="row g-5">
      <!-- Info Column -->
      <div class="col-lg-5">
        <div class="kg-section-label">Your Contribution Matters</div>
        <h2 class="kg-section-title">Every Rupee Makes a Difference</h2>
        <div class="kg-divider mb-4"></div>
        <p style="color:var(--kg-text-muted);">
          100% of your donation goes directly towards the care of our sacred cows. 
          We maintain full transparency in our use of funds.
        </p>

        <!-- Impact Cards -->
        <div class="row g-3 mt-2">
          <?php
          $impacts = [
            ['amount'=>'₹100',   'desc'=>'1 day\'s feed for a calf',       'icon'=>'bag-heart-fill'],
            ['amount'=>'₹500',   'desc'=>'1 day\'s feed for an adult cow', 'icon'=>'bag-heart-fill'],
            ['amount'=>'₹2,000', 'desc'=>'Routine veterinary care',        'icon'=>'activity'],
            ['amount'=>'₹5,000', 'desc'=>'1 month\'s feed for 10 cows',   'icon'=>'people-fill'],
          ];
          foreach ($impacts as $imp): ?>
          <div class="col-6">
            <div class="p-3 bg-white rounded-3 shadow-sm border text-center h-100" style="border-color:var(--kg-border)!important;">
              <i class="bi bi-<?= $imp['icon'] ?>" style="color:var(--kg-gold-dark);font-size:1.4rem;"></i>
              <div class="fw-700 mt-2" style="color:var(--kg-green);font-size:1.1rem;"><?= $imp['amount'] ?></div>
              <div style="font-size:.78rem;color:var(--kg-text-muted);"><?= $imp['desc'] ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Bank Details -->
        <div class="kg-info-box mt-4">
          <h6 class="text-kg-green mb-3"><i class="bi bi-bank me-2"></i>Bank Transfer Details</h6>
          <table class="table table-sm table-borderless mb-0" style="font-size:.85rem;">
            <tr><td class="fw-600 text-muted">Bank</td><td>State Bank of India</td></tr>
            <tr><td class="fw-600 text-muted">Account</td><td>00000012345678</td></tr>
            <tr><td class="fw-600 text-muted">IFSC</td><td>SBIN0001234</td></tr>
            <tr><td class="fw-600 text-muted">Account Name</td><td>Kamadhenu Goushala Trust</td></tr>
            <tr><td class="fw-600 text-muted">UPI ID</td><td>kamadhenu@sbi</td></tr>
          </table>
        </div>

        <div class="mt-3" style="font-size:.83rem;color:var(--kg-text-muted);">
          <i class="bi bi-shield-check me-1 text-kg-green"></i>
          Donations eligible for 80G tax exemption under Income Tax Act.
        </div>
      </div>

      <!-- Donation Form -->
      <div class="col-lg-7">
        <div class="kg-form-card">
          <h4 class="mb-4 text-kg-green"><i class="bi bi-heart-fill me-2"></i>Make a Donation</h4>

          <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Please fix:</strong>
            <ul class="mb-0 mt-2">
              <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>

          <form method="POST" action="" data-validate novalidate>
            <?= csrf_field() ?>

            <!-- Quick Amounts -->
            <div class="mb-3">
              <label class="form-label">Quick Select Amount</label>
              <div class="kg-amount-grid">
                <?php foreach ([500,1000,2000,5000,10000] as $amt): ?>
                <button type="button" class="kg-amount-btn" data-amount="<?= $amt ?>">₹<?= number_format($amt) ?></button>
                <?php endforeach; ?>
              </div>
            </div>

            <div class="mb-3">
              <label for="donationAmount" class="form-label">Donation Amount (₹) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text" style="background:var(--kg-green-pale);border-color:var(--kg-border);color:var(--kg-green-dark);font-weight:600;">₹</span>
                <input type="number" id="donationAmount" name="amount" class="form-control"
                       placeholder="Enter amount" min="1" step="1"
                       value="<?= e($old['amount'] ?? '') ?>" required>
                <div class="invalid-feedback">Please enter a valid amount.</div>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-md-6 mb-3">
                <label for="donor_name" class="form-label">Your Full Name <span class="text-danger">*</span></label>
                <input type="text" id="donor_name" name="donor_name" class="form-control"
                       placeholder="e.g. Sunita Patel"
                       value="<?= e($old['donor_name'] ?? '') ?>" required>
                <div class="invalid-feedback">Name is required.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="donor_email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" id="donor_email" name="donor_email" class="form-control"
                       placeholder="you@example.com"
                       value="<?= e($old['donor_email'] ?? '') ?>" required>
                <div class="invalid-feedback">Valid email required.</div>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-md-6 mb-3">
                <label for="donor_phone" class="form-label">Phone</label>
                <input type="tel" id="donor_phone" name="donor_phone" class="form-control"
                       placeholder="+91 98765 43210"
                       value="<?= e($old['donor_phone'] ?? '') ?>">
              </div>
              <div class="col-md-6 mb-3">
                <label for="purpose" class="form-label">Donation Purpose</label>
                <select name="purpose" id="purpose" class="form-select">
                  <?php foreach (['General','Cow Feed','Medical','Infrastructure','Gau Seva','Other'] as $p): ?>
                  <option value="<?= e($p) ?>" <?= ($old['purpose'] ?? 'General') === $p ? 'selected':'' ?>><?= e($p) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-md-6 mb-3">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select name="payment_method" id="payment_method" class="form-select">
                  <?php foreach (['UPI','Bank Transfer','Cash','Online','Other'] as $pm): ?>
                  <option value="<?= e($pm) ?>" <?= ($old['payment_method'] ?? 'UPI') === $pm ? 'selected':'' ?>><?= e($pm) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="transaction_id" class="form-label">Transaction ID (optional)</label>
                <input type="text" id="transaction_id" name="transaction_id" class="form-control"
                       placeholder="UPI / bank reference"
                       value="<?= e($old['transaction_id'] ?? '') ?>">
              </div>
            </div>

            <div class="mb-4">
              <label for="message" class="form-label">Message (optional)</label>
              <textarea name="message" id="message" class="form-control" rows="2"
                        placeholder="In memory of, in honour of, or any special message…"><?= e($old['message'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-kg-gold w-100 py-3" id="donateSubmitBtn">
              <i class="bi bi-heart-fill me-2"></i>Donate Now
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
