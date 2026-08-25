<?php
/**
 * Donate via WhatsApp — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Donate via WhatsApp - Kamadhenu Goushala';
$activePage = 'donate';

// Pre-fill from URL
$defaultPurpose = isset($_GET['purpose']) ? $_GET['purpose'] : 'General';
if ($defaultPurpose === 'feed') $defaultPurpose = 'Cow Feed';
$defaultCow = isset($_GET['cow']) ? strip_tags($_GET['cow']) : '';
if ($defaultCow && $defaultPurpose === 'General') {
    $defaultPurpose = 'Medical Care';
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="py-5" style="background: var(--kg-bg-color); min-height: calc(100vh - 200px);">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8">
        <div class="card shadow-sm border-0" style="border-radius: 1rem; overflow: hidden;">
          <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);">
            <h3 class="mb-1 fw-bold"><i class="bi bi-whatsapp me-2"></i>Donate via WhatsApp</h3>
            <p class="mb-0 opacity-75 small">Choose your amount to send us a message</p>
          </div>
          
          <div class="card-body p-4 p-md-5">
            <?php if ($defaultCow): ?>
            <div class="alert alert-success d-flex align-items-center mb-4">
              <i class="bi bi-heart-fill me-3 fs-3 text-success"></i>
              <div>
                <strong>Supporting <?= e($defaultCow) ?></strong><br>
                Thank you for choosing to help <?= e($defaultCow) ?>!
              </div>
            </div>
            <?php endif; ?>

            <form id="waDonateForm" novalidate>
              
              <!-- Quick Amounts -->
              <div class="mb-4">
                <label class="form-label fw-bold">Select Amount (₹)</label>
                <div class="d-flex flex-wrap gap-2 mb-3" id="quickAmounts">
                  <?php foreach ([500, 1000, 2000, 5000, 10000] as $amt): ?>
                  <button type="button" class="btn btn-outline-success flex-grow-1 quick-amt-btn" data-amount="<?= $amt ?>">₹<?= number_format($amt) ?></button>
                  <?php endforeach; ?>
                </div>
                <div class="input-group">
                  <span class="input-group-text bg-light text-success fw-bold">₹</span>
                  <input type="number" id="donationAmount" class="form-control form-control-lg" placeholder="Or enter custom amount" min="1" step="1" required>
                </div>
              </div>

              <!-- Purpose -->
              <div class="mb-4">
                <label for="purpose" class="form-label fw-bold">Donation Purpose</label>
                <select id="purpose" class="form-select form-select-lg">
                  <?php foreach (['General', 'Cow Feed', 'Medical Care', 'Infrastructure', 'Gau Seva'] as $p): ?>
                  <option value="<?= e($p) ?>" <?= ($defaultPurpose === $p) ? 'selected' : '' ?>><?= e($p) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Name (Optional) -->
              <div class="mb-4">
                <label for="donor_name" class="form-label fw-bold">Your Name (Optional)</label>
                <input type="text" id="donor_name" class="form-control form-control-lg" placeholder="e.g. Sunita Patel">
              </div>

              <button type="button" id="waSubmitBtn" class="btn btn-whatsapp w-100 py-3 fs-5 fw-bold" style="border-radius: 50px;">
                <i class="bi bi-whatsapp me-2"></i> Proceed to WhatsApp
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quickBtns = document.querySelectorAll('.quick-amt-btn');
    const amtInput = document.getElementById('donationAmount');
    const waSubmitBtn = document.getElementById('waSubmitBtn');
    
    // Quick Amount Buttons Logic
    quickBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all
            quickBtns.forEach(b => {
                b.classList.remove('btn-success', 'text-white');
                b.classList.add('btn-outline-success');
            });
            // Add active class to clicked
            this.classList.remove('btn-outline-success');
            this.classList.add('btn-success', 'text-white');
            
            // Set input value
            amtInput.value = this.getAttribute('data-amount');
        });
    });

    // Clear active buttons if user types custom amount
    amtInput.addEventListener('input', function() {
        quickBtns.forEach(b => {
            b.classList.remove('btn-success', 'text-white');
            b.classList.add('btn-outline-success');
        });
    });

    // Submit Logic
    waSubmitBtn.addEventListener('click', function() {
        const amount = amtInput.value.trim();
        const purpose = document.getElementById('purpose').value;
        const name = document.getElementById('donor_name').value.trim() || 'A well-wisher';
        const cowName = '<?= addslashes($defaultCow) ?>';
        
        if (!amount || isNaN(amount) || amount <= 0) {
            alert('Please select or enter a valid donation amount.');
            amtInput.focus();
            return;
        }

        const phone = '<?= preg_replace('/[^0-9]/', '', SITE_PHONE) ?>';
        
        let text = "Hello Kamadhenu Goushala! 🙏\n\n";
        text += "My name is " + name + ".\n";
        
        if (cowName && purpose !== 'General') {
            text += "I would like to make a donation of ₹" + amount + " towards " + purpose + " for " + cowName + ".\n\n";
        } else {
            text += "I would like to make a donation of ₹" + amount + " towards " + purpose + ".\n\n";
        }
        
        text += "Please share the payment details.";

        const url = "https://wa.me/" + phone + "?text=" + encodeURIComponent(text);
        
        // Since the previous links already have target="_blank", opening in current window is fine, 
        // but we'll use window.location.href to redirect the popup tab directly to WA.
        window.location.href = url;
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
