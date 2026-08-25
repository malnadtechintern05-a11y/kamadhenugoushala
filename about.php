<?php
/**
 * About Page — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$pageTitle  = 'About Us';
$pageDesc   = 'Learn about the history, mission and vision of Kamadhenu Goushala — Karnataka\'s premier indigenous cow sanctuary.';
$activePage = 'about';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<!-- Page Banner -->
<section class="kg-page-banner">
  <div class="container">
    <h1><i class="bi bi-info-circle me-2"></i>About Us</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Home</a></li>
        <li class="breadcrumb-item active">About</li>
      </ol>
    </nav>
  </div>
</section>

<!-- Mission & Vision -->
<section class="kg-section">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-5">
        <img src="<?= BASE_URL ?>/assets/images/about-goushala.jpg"
             onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'"
             alt="Kamadhenu Goushala" class="kg-about-img" loading="lazy">
      </div>
      <div class="col-lg-7">
        <div class="kg-section-label">Our Story</div>
        <h2 class="kg-section-title">25+ Years of Gau Seva</h2>
        <div class="kg-divider mb-4"></div>
        <p style="color:var(--kg-text-muted);">
          Kamadhenu Goushala was founded in 1998 by a group of devoted individuals who believed that the indigenous Indian cow 
          is not merely an animal — she is Gau Mata, the divine mother who nurtures all life. What began as a small shelter 
          for abandoned and injured cows has grown into one of Karnataka's most respected Goushalas.
        </p>
        <p style="color:var(--kg-text-muted);">
          Today, our Goushala is home to over 100 cows of various indigenous breeds including Gir, Sahiwal, Tharparkar, 
          Hariana, and Red Sindhi. Each cow receives individualized care, nutritious food, clean water, and regular veterinary attention.
        </p>
        <div class="row g-3 mt-3">
          <div class="col-6">
            <div class="kg-info-box">
              <div class="fw-700 text-kg-green" style="font-size:1.4rem;">100+</div>
              <div style="color:var(--kg-text-muted);font-size:.85rem;">Cows in our care</div>
            </div>
          </div>
          <div class="col-6">
            <div class="kg-info-box">
              <div class="fw-700 text-kg-green" style="font-size:1.4rem;">6+</div>
              <div style="color:var(--kg-text-muted);font-size:.85rem;">Indigenous breeds</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Mission / Vision / Values -->
<section class="kg-section kg-section-alt">
  <div class="container">
    <div class="kg-section-header">
      <div class="kg-section-label">What Drives Us</div>
      <h2 class="kg-section-title">Our Mission &amp; Vision</h2>
      <div class="kg-divider"></div>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="kg-seva-card h-100">
          <div class="kg-seva-icon"><i class="bi bi-bullseye"></i></div>
          <h4>Our Mission</h4>
          <p style="color:var(--kg-text-muted);font-size:.9rem;">
            To provide a permanent, loving sanctuary for indigenous Indian cows; to promote Gau Seva as a path of dharma; 
            and to share the natural goodness of A2 cow products with the world.
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="kg-seva-card h-100">
          <div class="kg-seva-icon"><i class="bi bi-eye-fill"></i></div>
          <h4>Our Vision</h4>
          <p style="color:var(--kg-text-muted);font-size:.9rem;">
            A world where every indigenous cow is protected, honoured, and free from suffering. We envision 
            a network of model Goushalas across India, inspired by our example.
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="kg-seva-card h-100">
          <div class="kg-seva-icon"><i class="bi bi-gem"></i></div>
          <h4>Our Values</h4>
          <p style="color:var(--kg-text-muted);font-size:.9rem;">
            Ahimsa (non-violence), Seva (selfless service), Satya (truth), transparency in all our operations, 
            and deep reverence for all living beings.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Timeline -->
<section class="kg-section">
  <div class="container">
    <div class="row g-5 align-items-start">
      <div class="col-lg-6">
        <div class="kg-section-label">Our Journey</div>
        <h2 class="kg-section-title">Milestones of Seva</h2>
        <div class="kg-divider mb-4"></div>
        <div class="kg-timeline">
          <div class="kg-timeline-item">
            <div class="kg-timeline-year">1998</div>
            <h5 class="kg-timeline-title">Founded</h5>
            <p class="kg-timeline-desc">Kamadhenu Goushala established with 12 rescued cows on 2 acres of land in Dharmapur.</p>
          </div>
          <div class="kg-timeline-item">
            <div class="kg-timeline-year">2005</div>
            <h5 class="kg-timeline-title">Expansion</h5>
            <p class="kg-timeline-desc">Extended to 10 acres. Started producing A2 ghee and distributing to local families.</p>
          </div>
          <div class="kg-timeline-item">
            <div class="kg-timeline-year">2012</div>
            <h5 class="kg-timeline-title">Veterinary Centre</h5>
            <p class="kg-timeline-desc">Established an in-house veterinary care centre with a full-time resident vet.</p>
          </div>
          <div class="kg-timeline-item">
            <div class="kg-timeline-year">2018</div>
            <h5 class="kg-timeline-title">Adoption Programme</h5>
            <p class="kg-timeline-desc">Launched the Gau Mata Adoption programme, connecting donors with specific cows.</p>
          </div>
          <div class="kg-timeline-item">
            <div class="kg-timeline-year">2023</div>
            <h5 class="kg-timeline-title">Online Presence</h5>
            <p class="kg-timeline-desc">Launched this website to bring the Goushala's mission to a global audience.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="kg-section-label">Our Team</div>
        <h2 class="kg-section-title">The People Behind the Seva</h2>
        <div class="kg-divider mb-4"></div>
        <div class="row g-3">
          <?php
          $team = [
            ['name' => 'Shri Raghavendra Swami', 'role' => 'Founder &amp; Trustee', 'icon' => 'person-circle'],
            ['name' => 'Dr. Anand Kumar', 'role' => 'Chief Veterinarian', 'icon' => 'heart-pulse-fill'],
            ['name' => 'Smt. Shanthi Devi', 'role' => 'Goushala Manager', 'icon' => 'person-badge-fill'],
            ['name' => 'Shri Venkatesh M', 'role' => 'Operations Head', 'icon' => 'gear-fill'],
          ];
          foreach ($team as $member): ?>
          <div class="col-sm-6">
            <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3 shadow-sm border" style="border-color:var(--kg-border)!important;">
              <div style="width:48px;height:48px;background:var(--kg-green-pale);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--kg-green);font-size:1.5rem;flex-shrink:0;">
                <i class="bi bi-<?= $member['icon'] ?>"></i>
              </div>
              <div>
                <div class="fw-600" style="font-size:.9rem;color:var(--kg-green-dark);"><?= $member['name'] ?></div>
                <div style="font-size:.78rem;color:var(--kg-text-muted);"><?= $member['role'] ?></div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="kg-section kg-section-green">
  <div class="container text-center">
    <h2 style="color:#fff;" class="mb-3">Be Part of This Sacred Journey</h2>
    <p style="color:rgba(255,255,255,.8);max-width:500px;margin:0 auto 2rem;">
      Whether you adopt, donate, volunteer or simply spread the word — your support keeps this sanctuary alive.
    </p>
    <a href="<?= BASE_URL ?>/donate.php" target="_blank" class="btn-kg-gold btn btn-lg me-3">
      <i class="bi bi-heart-fill me-2"></i>Donate Now
    </a>
    <a href="<?= BASE_URL ?>/contact.php" class="btn btn-lg" style="background:rgba(255,255,255,.15);color:#fff;border:2px solid rgba(255,255,255,.4);border-radius:50px;padding:.7rem 2rem;">
      <i class="bi bi-envelope me-2"></i>Get in Touch
    </a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
