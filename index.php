<?php
/**
 * Home Page — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$pdo = getDBConnection();

// Fetch featured cows
$stmtCows = $pdo->query("SELECT * FROM cows WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 6");
$featuredCows = $stmtCows->fetchAll();

// Fetch featured products
$stmtProds = $pdo->query("SELECT * FROM products WHERE is_featured = 1 AND is_active = 1 ORDER BY created_at DESC LIMIT 4");
$featuredProducts = $stmtProds->fetchAll();

// Stats
$stmtStats = $pdo->query("
  SELECT
    (SELECT COUNT(*) FROM cows)         AS total_cows,
    (SELECT COUNT(*) FROM adoptions WHERE status='Active') AS active_adoptions,
    (SELECT COALESCE(SUM(amount),0) FROM donations WHERE status='Completed') AS total_donations,
    (SELECT COUNT(*) FROM volunteers WHERE status='Approved') AS total_volunteers
");
$stats = $stmtStats->fetch();

// Gallery preview
$stmtGallery = $pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC, created_at DESC LIMIT 6");
$galleryItems = $stmtGallery->fetchAll();

$pageTitle = 'Home — Sacred Cow Sanctuary';
$activePage = 'home';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<!-- ═══════════════════════ HERO ═══════════════════════════ -->
<section class="kg-hero">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6 kg-hero-content">
        <div class="kg-hero-badge">
          <i class="bi bi-flower2"></i> Est. 1998 · Karnataka, India
        </div>
        <h1>
          Dedicated to<br>
          <span class="highlight">Gau Seva</span> &amp;<br>
          Sacred Protection
        </h1>
        <p class="kg-hero-subtitle">
          Kamadhenu Goushala is a sanctuary devoted to the loving care of indigenous Indian cows. 
          Every cow here is family. Join us in this divine service.
        </p>
        <div class="kg-hero-actions">
          <a href="<?= BASE_URL ?>/adopt.php" class="btn-kg-gold btn">
            <i class="bi bi-heart-fill me-2"></i>Adopt a Cow
          </a>
          <a href="<?= BASE_URL ?>/donate.php" class="btn-kg-outline btn" style="border-color:rgba(255,255,255,.6);color:#fff;">
            <i class="bi bi-gift me-2"></i>Donate Now
          </a>
        </div>
        <div class="d-flex gap-4 mt-4">
          <div>
            <div class="fw-700" style="color:var(--kg-gold);font-size:1.5rem;font-family:'Noto Serif',serif;"><?= (int)$stats['total_cows'] ?>+</div>
            <div style="color:rgba(255,255,255,.7);font-size:.82rem;">Cows Protected</div>
          </div>
          <div style="width:1px;background:rgba(255,255,255,.2);"></div>
          <div>
            <div class="fw-700" style="color:var(--kg-gold);font-size:1.5rem;font-family:'Noto Serif',serif;"><?= (int)$stats['active_adoptions'] ?>+</div>
            <div style="color:rgba(255,255,255,.7);font-size:.82rem;">Active Adoptions</div>
          </div>
          <div style="width:1px;background:rgba(255,255,255,.2);"></div>
          <div>
            <div class="fw-700" style="color:var(--kg-gold);font-size:1.5rem;font-family:'Noto Serif',serif;">25+</div>
            <div style="color:rgba(255,255,255,.7);font-size:.82rem;">Years of Seva</div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 d-none d-lg-block">
        <div class="kg-hero-img-wrap">
          <img src="<?= BASE_URL ?>/assets/images/hero-cow.jpg"
               onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'"
               alt="Sacred cow at Kamadhenu Goushala" loading="eager">
          <div class="kg-hero-img-badge">
            <i class="bi bi-shield-check me-1"></i> 100% Cage-Free &amp; Cruelty-Free
          </div>
        </div>
      </div>
    </div>
  </div>
  <a href="#about-section" class="kg-scroll-indicator" aria-label="Scroll down">
    <i class="bi bi-chevron-double-down"></i>
  </a>
</section>

<!-- ═══════════════════════ STATS ══════════════════════════ -->
<section class="kg-stats py-5" id="about-section">
  <div class="container">
    <div class="row g-0 text-center">
      <div class="col-6 col-md-3">
        <div class="kg-stat-card">
          <div class="kg-stat-icon"><i class="bi bi-emoji-heart-eyes"></i></div>
          <div class="kg-stat-number" data-counter="<?= (int)$stats['total_cows'] ?>" data-suffix="+">0</div>
          <div class="kg-stat-label">Cows Protected</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="kg-stat-card">
          <div class="kg-stat-icon"><i class="bi bi-people-fill"></i></div>
          <div class="kg-stat-number" data-counter="<?= (int)$stats['active_adoptions'] ?>" data-suffix="+">0</div>
          <div class="kg-stat-label">Active Adoptions</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="kg-stat-card">
          <div class="kg-stat-icon"><i class="bi bi-currency-rupee"></i></div>
          <div class="kg-stat-number" data-counter="<?= (int)($stats['total_donations'] / 1000) ?>" data-suffix="K+">0</div>
          <div class="kg-stat-label">Rupees Donated</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="kg-stat-card">
          <div class="kg-stat-icon"><i class="bi bi-calendar3"></i></div>
          <div class="kg-stat-number" data-counter="25" data-suffix="+">0</div>
          <div class="kg-stat-label">Years of Seva</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════ ABOUT SNIPPET ══════════════════ -->
<section class="kg-section">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-5">
        <img src="<?= BASE_URL ?>/assets/images/about-goushala.jpg"
             onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'"
             alt="Kamadhenu Goushala premises"
             class="kg-about-img" loading="lazy">
      </div>
      <div class="col-lg-7">
        <div class="kg-section-label">About Us</div>
        <h2 class="kg-section-title">A Home for Gau Mata</h2>
        <div class="kg-divider mb-4"></div>
        <p style="color:var(--kg-text-muted);">
          Founded in 1998, Kamadhenu Goushala has been a refuge of compassion and devotion for indigenous Indian cows. 
          Nestled in the serene landscape of Karnataka, our Goushala shelters over <?= (int)$stats['total_cows'] ?> cows, 
          providing them with nutritious food, expert veterinary care, and a loving environment.
        </p>
        <p style="color:var(--kg-text-muted);">
          We believe that caring for the cow is a sacred duty — an act of service not only to the animal but to the 
          entire ecosystem. Our products, made purely from A2 milk, bring the blessings of Gau Mata directly to your home.
        </p>
        <div class="d-flex flex-wrap gap-3 mt-4">
          <a href="<?= BASE_URL ?>/about.php" class="btn-kg-primary btn">
            <i class="bi bi-info-circle me-2"></i>Learn More
          </a>
          <a href="<?= BASE_URL ?>/seva.php" class="btn-kg-outline btn">
            <i class="bi bi-hand-index-thumb me-2"></i>Our Seva
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════ FEATURED COWS ══════════════════ -->
<?php if (!empty($featuredCows)): ?>
<section class="kg-section kg-section-alt">
  <div class="container">
    <div class="kg-section-header">
      <div class="kg-section-label">Our Residents</div>
      <h2 class="kg-section-title">Meet Our Sacred Cows</h2>
      <div class="kg-divider"></div>
      <p class="kg-section-desc mt-3">Each cow at our Goushala has a name, a story, and a home. Come meet them.</p>
    </div>
    <div class="row g-4">
      <?php foreach ($featuredCows as $cow): ?>
      <div class="col-md-6 col-lg-4">
        <div class="kg-cow-card">
          <div class="kg-cow-card-img">
            <img src="<?= img_url('cows', $cow['image']) ?>"
                 alt="<?= e($cow['name']) ?>"
                 loading="lazy"
                 onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'">
            <?php
              $badgeClass = match($cow['adoption_status']) {
                'Available'     => 'available',
                'Adopted'       => 'adopted',
                default         => 'unavailable',
              };
            ?>
            <span class="kg-cow-card-badge <?= $badgeClass ?>"><?= e($cow['adoption_status']) ?></span>
          </div>
          <div class="kg-cow-card-body">
            <h3 class="kg-cow-card-name"><?= e($cow['name']) ?></h3>
            <p class="kg-cow-card-breed"><?= e($cow['breed']) ?></p>
            <div class="kg-cow-card-meta">
              <span><i class="bi bi-calendar3"></i> <?= (int)$cow['age'] ?> yrs</span>
              <span><i class="bi bi-gender-ambiguous"></i> <?= e($cow['gender']) ?></span>
              <span><i class="bi bi-palette"></i> <?= e($cow['color']) ?></span>
            </div>
            <a href="<?= BASE_URL ?>/cow-details.php?id=<?= (int)$cow['id'] ?>" class="btn btn-sm btn-kg-primary w-100">
              View Details
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-5">
      <a href="<?= BASE_URL ?>/cows.php" class="btn-kg-outline btn">
        <i class="bi bi-grid me-2"></i>View All Cows
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════ SEVA CARDS ═════════════════════ -->
<section class="kg-section">
  <div class="container">
    <div class="kg-section-header">
      <div class="kg-section-label">Ways to Help</div>
      <h2 class="kg-section-title">Participate in Gau Seva</h2>
      <div class="kg-divider"></div>
      <p class="kg-section-desc mt-3">Every act of kindness — big or small — makes a difference for our sacred cows.</p>
    </div>
    <div class="row g-4">
      <div class="col-md-6 col-lg-3">
        <div class="kg-seva-card">
          <div class="kg-seva-icon"><i class="bi bi-heart-fill"></i></div>
          <h4>Adopt a Cow</h4>
          <div class="kg-seva-amount">₹1,500/month</div>
          <p style="color:var(--kg-text-muted);font-size:.88rem;">Sponsor the full care of a cow — food, shelter, medical. Receive blessings and monthly updates.</p>
          <a href="<?= BASE_URL ?>/adopt.php" class="btn btn-sm btn-kg-primary mt-2">Adopt Now</a>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="kg-seva-card">
          <div class="kg-seva-icon"><i class="bi bi-bag-heart-fill"></i></div>
          <h4>Feed a Cow</h4>
          <div class="kg-seva-amount">₹500/day</div>
          <p style="color:var(--kg-text-muted);font-size:.88rem;">Donate a day's feed for our cows. Includes green fodder, dry fodder, and mineral supplements.</p>
          <a href="<?= BASE_URL ?>/donate.php" class="btn btn-sm btn-kg-primary mt-2">Donate Feed</a>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="kg-seva-card">
          <div class="kg-seva-icon"><i class="bi bi-activity"></i></div>
          <h4>Medical Seva</h4>
          <div class="kg-seva-amount">₹2,000+</div>
          <p style="color:var(--kg-text-muted);font-size:.88rem;">Help pay for veterinary care, medicines, and surgeries for injured or sick cows in our care.</p>
          <a href="<?= BASE_URL ?>/donate.php" class="btn btn-sm btn-kg-primary mt-2">Support Health</a>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="kg-seva-card">
          <div class="kg-seva-icon"><i class="bi bi-people-fill"></i></div>
          <h4>Volunteer</h4>
          <div class="kg-seva-amount">Your Time</div>
          <p style="color:var(--kg-text-muted);font-size:.88rem;">Give your time and energy to directly serve the cows. Weekends, holidays or full-time — all welcome.</p>
          <a href="<?= BASE_URL ?>/volunteer.php" class="btn btn-sm btn-kg-primary mt-2">Join Us</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════ PRODUCTS ══════════════════════ -->
<?php if (!empty($featuredProducts)): ?>
<section class="kg-section kg-section-alt">
  <div class="container">
    <div class="kg-section-header">
      <div class="kg-section-label">Gau Products</div>
      <h2 class="kg-section-title">Pure A2 Products</h2>
      <div class="kg-divider"></div>
      <p class="kg-section-desc mt-3">Authentic products prepared with love from the milk of our healthy A2 cows.</p>
    </div>
    <div class="row g-4">
      <?php foreach ($featuredProducts as $prod): ?>
      <div class="col-md-6 col-lg-3">
        <div class="kg-product-card">
          <div class="kg-product-card-img">
            <img src="<?= img_url('products', $prod['image']) ?>"
                 alt="<?= e($prod['name']) ?>"
                 loading="lazy"
                 onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'">
            <span class="kg-product-tag"><?= e($prod['category']) ?></span>
          </div>
          <div class="kg-product-card-body">
            <div class="kg-product-name"><?= e($prod['name']) ?></div>
            <div class="kg-product-unit"><?= e($prod['unit']) ?></div>
            <div class="kg-product-price"><?= format_inr((float)$prod['price']) ?></div>
            <a href="<?= BASE_URL ?>/product-details.php?id=<?= (int)$prod['id'] ?>" class="btn btn-sm btn-kg-primary w-100">
              Order Now
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-5">
      <a href="<?= BASE_URL ?>/products.php" class="btn-kg-outline btn">
        <i class="bi bi-shop me-2"></i>View All Products
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════ GALLERY PREVIEW ════════════════ -->
<?php if (!empty($galleryItems)): ?>
<section class="kg-section">
  <div class="container">
    <div class="kg-section-header">
      <div class="kg-section-label">Gallery</div>
      <h2 class="kg-section-title">Life at the Goushala</h2>
      <div class="kg-divider"></div>
    </div>
    <div class="row g-3">
      <?php foreach (array_slice($galleryItems, 0, 6) as $gItem): ?>
      <div class="col-6 col-md-4 col-lg-2">
        <div class="kg-gallery-item"
             data-lightbox="<?= img_url('gallery', $gItem['image']) ?>"
             data-title="<?= e($gItem['title']) ?>">
          <img src="<?= img_url('gallery', $gItem['image']) ?>"
               alt="<?= e($gItem['title']) ?>"
               loading="lazy"
               onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'">
          <div class="kg-gallery-overlay">
            <span class="kg-gallery-title"><?= e($gItem['title']) ?></span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-5">
      <a href="<?= BASE_URL ?>/gallery.php" class="btn-kg-outline btn">
        <i class="bi bi-images me-2"></i>View Full Gallery
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════ DONATION CTA ══════════════════ -->
<section class="kg-section kg-section-green">
  <div class="container text-center">
    <div class="kg-section-label" style="color:var(--kg-gold-light);">Make a Difference Today</div>
    <h2 class="kg-section-title" style="color:#fff;">Your Donation Feeds, Heals &amp; Protects</h2>
    <div class="kg-divider mb-4"></div>
    <p style="color:rgba(255,255,255,.8);max-width:550px;margin:0 auto 2rem;">
      Every rupee you donate goes directly towards feeding our cows, their medical care, and maintaining this sacred space.
    </p>
    <a href="<?= BASE_URL ?>/donate.php" class="btn-kg-gold btn btn-lg">
      <i class="bi bi-heart-fill me-2"></i>Donate Now
    </a>
    &nbsp;&nbsp;
    <a href="<?= BASE_URL ?>/contact.php" class="btn btn-lg" style="background:rgba(255,255,255,.15);color:#fff;border:2px solid rgba(255,255,255,.4);border-radius:50px;padding:.7rem 2rem;">
      <i class="bi bi-telephone me-2"></i>Contact Us
    </a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
