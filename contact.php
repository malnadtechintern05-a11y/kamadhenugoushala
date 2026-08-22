<?php
/**
 * Contact Page — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();

    $old = [
        'name'    => sanitize($_POST['name']    ?? ''),
        'email'   => sanitize($_POST['email']   ?? ''),
        'phone'   => sanitize($_POST['phone']   ?? ''),
        'subject' => sanitize($_POST['subject'] ?? ''),
        'message' => sanitize($_POST['message'] ?? ''),
    ];

    if (empty($old['name']))    $errors[] = 'Your name is required.';
    if (empty($old['email']) || !is_valid_email($old['email'])) $errors[] = 'A valid email address is required.';
    if (empty($old['message'])) $errors[] = 'A message is required.';

    if (empty($errors)) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO messages (name, email, phone, subject, message)
            VALUES (:name, :email, :phone, :subject, :message)
        ");
        $stmt->execute([
            ':name'    => $old['name'],
            ':email'   => $old['email'],
            ':phone'   => $old['phone'],
            ':subject' => $old['subject'],
            ':message' => $old['message'],
        ]);
        redirect(BASE_URL . '/thank-you.php?type=contact');
    }
}

$pageTitle  = 'Contact Us';
$activePage = 'contact';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="kg-page-banner">
  <div class="container">
    <h1><i class="bi bi-envelope me-2"></i>Contact Us</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Home</a></li>
        <li class="breadcrumb-item active">Contact</li>
      </ol>
    </nav>
  </div>
</section>

<section class="kg-section">
  <div class="container">
    <div class="row g-5">
      <!-- Contact Info -->
      <div class="col-lg-4">
        <div class="kg-section-label">Get in Touch</div>
        <h2 class="kg-section-title" style="font-size:1.8rem;">We'd Love to Hear From You</h2>
        <div class="kg-divider mb-4"></div>
        <p style="color:var(--kg-text-muted);">
          Whether you have questions about adopting a cow, donating, visiting the Goushala, 
          or volunteering — we're here to help.
        </p>

        <ul class="kg-footer-contact mt-4" style="color:var(--kg-text-muted);">
          <li style="color:var(--kg-text-muted);">
            <i class="bi bi-geo-alt-fill" style="color:var(--kg-gold-dark);"></i>
            <span><?= e(SITE_ADDRESS) ?></span>
          </li>
          <li>
            <i class="bi bi-telephone-fill" style="color:var(--kg-gold-dark);"></i>
            <a href="tel:<?= e(SITE_PHONE) ?>" style="color:var(--kg-text-muted);"><?= e(SITE_PHONE) ?></a>
          </li>
          <li>
            <i class="bi bi-envelope-fill" style="color:var(--kg-gold-dark);"></i>
            <a href="mailto:<?= e(SITE_EMAIL) ?>" style="color:var(--kg-text-muted);"><?= e(SITE_EMAIL) ?></a>
          </li>
          <li style="color:var(--kg-text-muted);">
            <i class="bi bi-clock-fill" style="color:var(--kg-gold-dark);"></i>
            <span>Open daily: 5:00 AM – 8:00 PM IST</span>
          </li>
          <li style="color:var(--kg-text-muted);">
            <i class="bi bi-whatsapp" style="color:var(--kg-gold-dark);"></i>
            <span>WhatsApp: <?= e(SITE_PHONE) ?></span>
          </li>
        </ul>

        <!-- Social Media Links -->
        <div class="mt-5 mb-4">
          <h5 class="mb-3" style="color:var(--kg-green-dark); font-weight:600;">Connect With Us</h5>
          <div class="d-flex gap-3">
            <a href="<?= e(defined('SOCIAL_INSTAGRAM') ? SOCIAL_INSTAGRAM : '#') ?>" class="social-icon-box" style="background:#E1306C;" title="Instagram" target="_blank">
              <i class="bi bi-instagram"></i>
            </a>
            <a href="<?= e(defined('SOCIAL_WHATSAPP') ? SOCIAL_WHATSAPP : '#') ?>" class="social-icon-box" style="background:#25D366;" title="WhatsApp" target="_blank">
              <i class="bi bi-whatsapp"></i>
            </a>
            <a href="<?= e(defined('SOCIAL_FACEBOOK') ? SOCIAL_FACEBOOK : '#') ?>" class="social-icon-box" style="background:#1877F2;" title="Facebook" target="_blank">
              <i class="bi bi-facebook"></i>
            </a>
            <a href="<?= e(defined('SOCIAL_TWITTER') ? SOCIAL_TWITTER : '#') ?>" class="social-icon-box" style="background:#000000;" title="Twitter / X" target="_blank">
              <i class="bi bi-twitter-x"></i>
            </a>
          </div>
        </div>
        
        <style>
        .social-icon-box {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          width: 45px;
          height: 45px;
          border-radius: 50%;
          color: white;
          font-size: 1.3rem;
          text-decoration: none;
          transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .social-icon-box:hover {
          transform: translateY(-4px) scale(1.05);
          box-shadow: 0 8px 20px rgba(0,0,0,0.15);
          color: white;
        }
        </style>

      </div>

      <!-- Contact Form -->
      <div class="col-lg-8">
        <div class="kg-form-card">
          <h4 class="mb-4 text-kg-green"><i class="bi bi-send me-2"></i>Send Us a Message</h4>

          <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>

          <form method="POST" action="" data-validate novalidate>
            <?= csrf_field() ?>

            <div class="row g-3">
              <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control"
                       placeholder="Ramesh Kumar"
                       value="<?= e($old['name'] ?? '') ?>" required>
                <div class="invalid-feedback">Name is required.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" id="email" name="email" class="form-control"
                       placeholder="you@example.com"
                       value="<?= e($old['email'] ?? '') ?>" required>
                <div class="invalid-feedback">Valid email required.</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control"
                       placeholder="+91 98765 43210"
                       value="<?= e($old['phone'] ?? '') ?>">
              </div>
              <div class="col-md-6 mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" id="subject" name="subject" class="form-control"
                       placeholder="How can we help you?"
                       value="<?= e($old['subject'] ?? '') ?>">
              </div>
            </div>

            <div class="mb-4">
              <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
              <textarea name="message" id="message" class="form-control" rows="5"
                        placeholder="Write your message here…" required><?= e($old['message'] ?? '') ?></textarea>
              <div class="invalid-feedback">Message is required.</div>
            </div>

            <button type="submit" class="btn btn-kg-primary py-3 px-5" id="contactSubmitBtn">
              <i class="bi bi-send me-2"></i>Send Message
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Google Maps Embed Full Width -->
    <div class="mt-5 rounded-4 shadow-sm overflow-hidden" style="border:1px solid var(--kg-border);">
      <iframe 
        src="https://maps.google.com/maps?q=Dharmapur,%20Mysuru%20District,%20Karnataka%20571201&t=&z=13&ie=UTF8&iwloc=&output=embed" 
        width="100%" 
        height="450" 
        style="border:0; display:block;" 
        allowfullscreen="" 
        loading="lazy" 
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
