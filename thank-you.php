<?php
/**
 * Thank You Page — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$type = sanitize($_GET['type'] ?? 'contact');

$messages = [
    'donation' => [
        'icon'     => 'heart-fill',
        'color'    => 'var(--kg-gold)',
        'heading'  => 'Thank You for Your Generous Donation!',
        'subtext'  => 'Your donation has been received and will go directly towards the care of our sacred cows. You will receive a confirmation and 80G receipt via email shortly.',
        'next'     => 'Our team will verify your transaction and send you a receipt within 24 hours.',
    ],
    'adoption' => [
        'icon'     => 'emoji-heart-eyes-fill',
        'color'    => 'var(--kg-green)',
        'heading'  => 'Your Adoption Application is Submitted!',
        'subtext'  => 'You have taken the first step to become a proud Gau Mata guardian! Our team will review your application and contact you within 48 hours.',
        'next'     => 'You\'ll receive an email with your adoption certificate and payment details.',
    ],
    'contact'  => [
        'icon'     => 'envelope-check-fill',
        'color'    => 'var(--kg-green)',
        'heading'  => 'Message Received!',
        'subtext'  => 'Thank you for reaching out to Kamadhenu Goushala. We have received your message and will get back to you within 24–48 hours.',
        'next'     => 'For urgent matters, please call us directly at ' . SITE_PHONE . '.',
    ],
    'volunteer'=> [
        'icon'     => 'people-fill',
        'color'    => 'var(--kg-green)',
        'heading'  => 'Volunteer Application Submitted!',
        'subtext'  => 'Thank you for your desire to serve Gau Mata! Your application has been received. Our coordinator will be in touch soon to discuss next steps.',
        'next'     => 'Please keep your phone nearby — we may call within 72 hours.',
    ],
    'order'    => [
        'icon'     => 'bag-check-fill',
        'color'    => 'var(--kg-green)',
        'heading'  => 'Order Placed Successfully!',
        'subtext'  => 'Your order has been received. Our team will confirm availability and contact you with payment and delivery details shortly.',
        'next'     => 'Expected response within 24 hours on business days.',
    ],
];

$info = $messages[$type] ?? $messages['contact'];
$pageTitle  = 'Thank You';
$activePage = '';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="kg-section" style="min-height:70vh;display:flex;align-items:center;">
  <div class="container">
    <div class="kg-thankyou-card">
      <div class="kg-thankyou-icon" style="background:rgba(45,106,79,.1);color:<?= $info['color'] ?>;">
        <i class="bi bi-<?= $info['icon'] ?>"></i>
      </div>
      <!-- Om symbol -->
      <div style="font-size:2rem;color:var(--kg-gold);margin-bottom:.5rem;">ॐ</div>
      <h1 class="kg-section-title" style="font-size:1.8rem;margin-bottom:1rem;"><?= e($info['heading']) ?></h1>
      <p style="color:var(--kg-text-muted);margin-bottom:1rem;"><?= e($info['subtext']) ?></p>
      <div class="kg-info-box mb-4">
        <p class="mb-0" style="font-size:.88rem;color:var(--kg-green-dark);">
          <i class="bi bi-info-circle me-2"></i><?= e($info['next']) ?>
        </p>
      </div>

      <!-- Sharing text -->
      <p style="font-size:.85rem;color:var(--kg-text-muted);font-style:italic;margin-bottom:1.5rem;">
        "गाव के पीछे गऊशाला, गऊशाला के पीछे भगवान"<br>
        <small>— Behind the cow is the Goushala, behind the Goushala is God.</small>
      </p>

      <div class="d-flex gap-3 justify-content-center flex-wrap">
        <a href="<?= BASE_URL ?>/" class="btn btn-kg-primary">
          <i class="bi bi-house me-2"></i>Back to Home
        </a>
        <a href="<?= BASE_URL ?>/donate.php" class="btn btn-kg-outline">
          <i class="bi bi-heart me-2"></i>Donate Again
        </a>
        <a href="<?= BASE_URL ?>/cows.php" class="btn btn-kg-outline">
          <i class="bi bi-emoji-heart-eyes me-2"></i>Meet Our Cows
        </a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
