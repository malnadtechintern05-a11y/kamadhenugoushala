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
    (SELECT COUNT(*) + 120 FROM adoptions WHERE status='Active') AS active_adoptions,
    (SELECT COALESCE(SUM(amount),0) + 1500000 FROM donations WHERE status='Completed') AS total_donations,
    (SELECT COUNT(*) + 45 FROM volunteers WHERE status='Approved') AS total_volunteers
");
$stats = $stmtStats->fetch();

// Gallery preview
$stmtGallery = $pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC, created_at DESC LIMIT 6");
$galleryItems = $stmtGallery->fetchAll();

// Fetch up to 2 featured videos (or latest)
try {
    $stmtVideo = $pdo->query("SELECT youtube_id FROM videos ORDER BY is_featured DESC, created_at DESC LIMIT 2");
    $featuredVideos = $stmtVideo->fetchAll(PDO::FETCH_COLUMN);
    if (empty($featuredVideos)) {
        $featuredVideos = ['GHCNIJZw3UM'];
    }
} catch (Exception $e) {
    $featuredVideos = ['GHCNIJZw3UM'];
}

$pageTitle = 'Home — Sacred Cow Sanctuary';
$activePage = 'home';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<!-- ═══════════════════════ HERO ═══════════════════════════ -->
<section class="kg-hero" id="vanta-hero-bg">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6 kg-hero-content">
        <div class="kg-hero-badge">
          <?= get_cow_logo_svg() ?> Est. 1998 · Karnataka, India
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

          <a href="<?= BASE_URL ?>/donate.php" target="_blank" class="btn-kg-outline btn" style="border-color:rgba(255,255,255,.6);color:#fff;">
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
  <a href="#impact-section" class="kg-scroll-indicator" aria-label="Scroll down">
    <i class="bi bi-chevron-double-down"></i>
  </a>
</section>

<!-- Page Sub-Navigation -->
<div class="sticky-top bg-white border-bottom py-3 shadow-sm" style="z-index: 1010; top: 70px;">
  <div class="container d-flex justify-content-center gap-2 gap-md-3 flex-wrap">
    <a href="#impact-section" class="btn btn-outline-success rounded-pill px-4 fw-medium">Our Impact</a>
    <a href="#about-section" class="btn btn-outline-success rounded-pill px-4 fw-medium">About</a>
    <a href="#video-section" class="btn btn-outline-success rounded-pill px-4 fw-medium">Video Tour</a>
    <a href="#cows-section" class="btn btn-outline-success rounded-pill px-4 fw-medium">Our Cows</a>
  </div>
</div>

<style>
/* Smooth Scroll & Highlight Animation */
html { scroll-behavior: smooth; }
.kg-anim-section { scroll-margin-top: 140px; transition: all 0.6s ease-out; }
.kg-anim-section:target {
  box-shadow: inset 0 0 50px rgba(45, 82, 54, 0.15);
  background-color: rgba(45, 82, 54, 0.02);
  transform: scale(1.005);
  border-radius: 12px;
}
</style>

<!-- ═══════════════════════ IMPACT DASHBOARD ══════════════════════════ -->
<section class="kg-impact-dashboard py-5 kg-anim-section" id="impact-section" style="background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%); overflow: hidden; position: relative;">
  <div class="container position-relative" style="z-index: 1;">
    <div class="kg-section-header text-center mb-5">
      <div class="kg-section-label" style="color: #e67e22; font-weight: 800; text-transform: uppercase; letter-spacing: 3px;">Our Impact</div>
      <h2 class="kg-section-title" style="font-size: 2.8rem; color: #2d5236; font-weight: 800;">Impact Dashboard</h2>
      <div class="kg-divider mx-auto" style="background: #e67e22; width: 80px; height: 4px; margin-top: 15px; border-radius: 2px;"></div>
      <p class="kg-section-desc mt-3 text-muted" style="font-size: 1.1rem;">See how your support is transforming the lives of our sacred Gau Matas.</p>
    </div>
    
    <div class="row g-4 text-center">
      <!-- Card 1 -->
      <div class="col-6 col-lg-3">
        <div class="impact-card shadow-lg h-100 p-4 rounded-4" style="background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);">
          <div class="impact-icon mb-3"><i class="bi bi-emoji-heart-eyes-fill"></i></div>
          <div class="impact-number" data-counter="<?= (int)$stats['total_cows'] ?>" data-suffix="+"><?= (int)$stats['total_cows'] ?>+</div>
          <div class="impact-label">Cows Rescued</div>
        </div>
      </div>
      <!-- Card 2 -->
      <div class="col-6 col-lg-3">
        <div class="impact-card shadow-lg h-100 p-4 rounded-4" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
          <div class="impact-icon mb-3"><i class="bi bi-house-heart-fill"></i></div>
          <div class="impact-number" data-counter="<?= (int)$stats['active_adoptions'] ?>" data-suffix="+"><?= (int)$stats['active_adoptions'] ?>+</div>
          <div class="impact-label">Families Adopting</div>
        </div>
      </div>
      <!-- Card 3 -->
      <div class="col-6 col-lg-3">
        <div class="impact-card shadow-lg h-100 p-4 rounded-4" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
          <div class="impact-icon mb-3"><i class="bi bi-droplet-fill"></i></div>
          <div class="impact-number">15K+</div>
          <div class="impact-label">Liters A2 Milk/Yr</div>
        </div>
      </div>
      <!-- Card 4 -->
      <div class="col-6 col-lg-3">
        <div class="impact-card shadow-lg h-100 p-4 rounded-4" style="background: linear-gradient(135deg, #b06ab3 0%, #4568dc 100%);">
          <div class="impact-icon mb-3"><i class="bi bi-people-fill"></i></div>
          <div class="impact-number" data-counter="<?= (int)$stats['total_volunteers'] ?>" data-suffix="+"><?= (int)$stats['total_volunteers'] ?>+</div>
          <div class="impact-label">Active Volunteers</div>
        </div>
      </div>
    </div>
  </div>
</section>
<style>
/* Dashboard Animations and Colors */
.impact-card {
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  position: relative;
  overflow: hidden;
  border: none;
  z-index: 1;
}
.impact-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(255,255,255,0.15);
  transform: translateY(100%);
  transition: transform 0.4s ease;
  z-index: -1;
}
.impact-card:hover { 
  transform: translateY(-15px) scale(1.03); 
  box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important; 
}
.impact-card:hover::before {
  transform: translateY(0);
}
.impact-icon {
  font-size: 3.5rem; 
  color: #fff;
  transition: transform 0.3s ease;
  text-shadow: 2px 4px 10px rgba(0,0,0,0.2);
}
.impact-card:hover .impact-icon {
  transform: scale(1.2) rotate(5deg);
  animation: pulse 1s infinite alternate;
}
.impact-number {
  font-size: 3rem; 
  font-weight: 900; 
  color: #fff;
  text-shadow: 2px 2px 5px rgba(0,0,0,0.2);
}
.impact-label {
  font-size: 1.15rem; 
  font-weight: 700; 
  color: rgba(255,255,255,0.95);
  letter-spacing: 0.5px;
}
@keyframes pulse {
  0% { transform: scale(1.1); }
  100% { transform: scale(1.25); }
}
</style>

<!-- ═══════════════════════ ABOUT SNIPPET ══════════════════ -->
<section class="kg-section kg-anim-section" id="about-section">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-5 position-relative">
        <div class="kg-realistic-frame shadow-lg" style="border-radius: 20px; overflow: hidden; border: 8px solid #fff; box-shadow: 0 15px 35px rgba(0,0,0,0.15) !important;">
          <img src="https://images.unsplash.com/photo-1596781255401-447a1ec6338b?q=80&w=1470&auto=format&fit=crop"
               onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'"
               alt="Realistic Kamadhenu Goushala Home"
               class="w-100" style="object-fit: cover; min-height: 400px; transform: scale(1.05); transition: transform 0.8s cubic-bezier(0.25, 0.8, 0.25, 1);"
               onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1.05)'" loading="lazy">
        </div>
      </div>
      <div class="col-lg-7">
        <div class="kg-section-label" style="color: var(--kg-gold-dark); font-weight: 700; letter-spacing: 2px; text-transform: uppercase;">Our Goushala Home</div>
        <h2 class="kg-section-title">A True Sanctuary for Gau Mata</h2>
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

<!-- ═══════════════════════ OUR COWS ══════════════════ -->
<?php if (!empty($featuredCows)): ?>
<section class="kg-section kg-anim-section" id="cows-section" style="background-color: var(--kg-white);">
  <div class="container">
    <div class="kg-section-header">
      <div class="kg-section-label">Meet Our Family</div>
      <h2 class="kg-section-title">Our Sacred Cows</h2>
      <div class="kg-divider"></div>
      <p class="kg-section-desc mt-3">Get to know the divine residents of our sanctuary. You can symbolically adopt a cow to support her daily needs.</p>
    </div>
    
    <div class="row g-4">
      <?php foreach ($featuredCows as $cow): ?>
      <div class="col-md-6 col-lg-4">
        <div class="kg-cow-card h-100 shadow-sm" style="border-radius: var(--kg-radius); overflow: hidden; background-color: var(--kg-white); border: 1px solid var(--kg-border);">
          <div class="position-relative">
            <img src="<?= img_url('cows', $cow['image']) ?>"
                 alt="<?= e($cow['name']) ?>"
                 loading="lazy"
                 class="w-100"
                 style="height: 250px; object-fit: cover;"
                 onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'">
            <?php if ($cow['adoption_status'] === 'Available'): ?>
            <span class="badge bg-success position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill">Available for Adoption</span>
            <?php else: ?>
            <span class="badge bg-secondary position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill">Adopted</span>
            <?php endif; ?>
          </div>
          <div class="p-4 text-center">
            <h4 class="mb-1" style="color: var(--kg-green-dark); font-family: 'Noto Serif', serif; font-weight: 700;"><?= e($cow['name']) ?></h4>
            <div class="mb-3 text-muted" style="font-size: 0.9rem;">
              Breed: <?= e($cow['breed']) ?> &bull; Age: <?= e($cow['age']) ?> yrs
            </div>
            <a href="<?= BASE_URL ?>/cow-details.php?id=<?= (int)$cow['id'] ?>" class="btn btn-outline-success rounded-pill px-4">
              Meet <?= e($cow['name']) ?>
            </a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-5">
      <a href="<?= BASE_URL ?>/cows.php" class="btn-kg-outline btn">
        <i class="bi bi-suit-heart me-2"></i>View All Cows
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════ VIDEO TOUR ══════════════════════ -->
<section class="kg-section kg-section-alt kg-anim-section" id="video-section">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-12 text-center mb-2">
        <div class="kg-section-label">Video Tour</div>
        <h2 class="kg-section-title">Experience Our Goushala</h2>
        <div class="kg-divider mx-auto mb-4"></div>
        <p style="color:var(--kg-text-muted); max-width: 800px; margin: 0 auto;">
          Take a virtual walk through our sanctuary and see the peace and happiness of our beloved Gau Matas. 
          Witness our traditional milking process, our lush green pastures, and the deep bond between our caretakers and the cows.
        </p>
      </div>

      <?php foreach($featuredVideos as $vid): ?>
      <div class="col-md-6 mb-4">
        <div class="kg-video-frame position-relative shadow-lg" style="border-radius: 20px; overflow: hidden; border: 8px solid #fff; box-shadow: 0 15px 35px rgba(0,0,0,0.15) !important;">
          <!-- Aspect ratio wrapper -->
          <div class="ratio ratio-16x9 bg-dark" id="vid-wrapper-<?= e($vid) ?>">
            <!-- iframe will be injected here by JS -->
          </div>
          
          <!-- Thumbnail Overlay -->
          <div class="kg-video-overlay" id="vid-overlay-<?= e($vid) ?>" onclick="playVideo('<?= e($vid) ?>')" style="cursor:pointer; position:absolute; top:0; left:0; width:100%; height:100%; z-index:2; overflow:hidden;">
            <img src="https://img.youtube.com/vi/<?= e($vid) ?>/maxresdefault.jpg" alt="Video Thumbnail" class="w-100 h-100" style="object-fit:cover; transition:transform 0.6s cubic-bezier(0.25, 0.8, 0.25, 1);" onmouseover="this.style.transform='scale(1.08)'; this.nextElementSibling.nextElementSibling.firstElementChild.style.transform='scale(1.15)'; this.nextElementSibling.nextElementSibling.firstElementChild.style.background='#fff'" onmouseout="this.style.transform='scale(1)'; this.nextElementSibling.nextElementSibling.firstElementChild.style.transform='scale(1)'; this.nextElementSibling.nextElementSibling.firstElementChild.style.background='rgba(255,255,255,0.95)'">
            
            <!-- Subtle dark overlay -->
            <div style="position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.25); pointer-events:none;"></div>
            
            <!-- Play Button -->
            <div class="kg-play-btn-wrapper" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); pointer-events:none;">
              <div class="kg-play-btn" style="width: 85px; height: 85px; background: rgba(255,255,255,0.95); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 15px 35px rgba(0,0,0,0.3); transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);">
                <i class="bi bi-play-fill" style="font-size: 3.5rem; color: var(--kg-green-dark); margin-left: 8px;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

      <script>
      function playVideo(vid) {
        document.getElementById('vid-overlay-' + vid).style.display = 'none';
        document.getElementById('vid-wrapper-' + vid).innerHTML = '<iframe src="https://www.youtube.com/embed/' + vid + '?autoplay=1&rel=0" title="Kamadhenu Goushala Video Tour" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width:100%; height:100%; border:0;"></iframe>';
      }
      </script>

      <div class="col-12 text-center mt-4">
        <a href="<?= BASE_URL ?>/gallery.php" class="btn-kg-outline btn">
          <i class="bi bi-images me-2"></i>View Photo Gallery
        </a>
      </div>
    </div>
  </div>
</section>



<!-- ═══════════════════════ SEVA CARDS ═════════════════════ -->
<section class="kg-section">
  <div class="container">
    <div class="kg-section-header">
      <div class="kg-section-label">Ways to Help</div>
      <h2 class="kg-section-title">Participate in Gau Seva</h2>
      <div class="kg-divider"></div>
      <p class="kg-section-desc mt-3">Every act of kindness — big or small — makes a difference for our sacred cows.</p>
    </div>
    <div class="row g-4 justify-content-center">
      <div class="col-md-4">
        <div class="kg-seva-card h-100">
          <div class="kg-seva-icon"><i class="bi bi-bag-heart-fill"></i></div>
          <h4>Feed a Cow</h4>
          <div class="kg-seva-amount">₹500/day</div>
          <p style="color:var(--kg-text-muted);font-size:.88rem;">Donate a day's feed for our cows. Includes green fodder, dry fodder, and mineral supplements.</p>
          <a href="<?= BASE_URL ?>/donate.php" target="_blank" class="btn btn-sm btn-kg-primary mt-auto">Donate Feed</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="kg-seva-card h-100">
          <div class="kg-seva-icon"><i class="bi bi-activity"></i></div>
          <h4>Medical Seva</h4>
          <div class="kg-seva-amount">₹2,000+</div>
          <p style="color:var(--kg-text-muted);font-size:.88rem;">Help pay for veterinary care, medicines, and surgeries for injured or sick cows in our care.</p>
          <a href="<?= BASE_URL ?>/donate.php" target="_blank" class="btn btn-sm btn-kg-primary mt-auto">Support Health</a>
        </div>
      </div>
      <div class="col-md-4">
        <div class="kg-seva-card h-100">
          <div class="kg-seva-icon"><i class="bi bi-people-fill"></i></div>
          <h4>Volunteer</h4>
          <div class="kg-seva-amount">Your Time</div>
          <p style="color:var(--kg-text-muted);font-size:.88rem;">Give your time and energy to directly serve the cows. Weekends, holidays or full-time — all welcome.</p>
          <a href="<?= BASE_URL ?>/volunteer.php" class="btn btn-sm btn-kg-primary mt-auto">Join Us</a>
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
      <div class="col-md-6 col-lg-4">
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
            <?php if (defined('CHECKOUT_MODE_PRODUCTS') && CHECKOUT_MODE_PRODUCTS === 'both'): ?>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-sm btn-kg-primary flex-grow-1" onclick="addToCart(<?= (int)$prod['id'] ?>)">
                <i class="bi bi-cart-plus me-1"></i> Cart
              </button>
              <a href="<?= BASE_URL ?>/whatsapp_product_redirect.php?product_id=<?= (int)$prod['id'] ?>" target="_blank" class="btn btn-sm btn-whatsapp flex-grow-1">
                <i class="bi bi-whatsapp me-1"></i> Buy
              </a>
            </div>
            <?php elseif (defined('CHECKOUT_MODE_PRODUCTS') && CHECKOUT_MODE_PRODUCTS === 'whatsapp'): ?>
            <a href="<?= BASE_URL ?>/whatsapp_product_redirect.php?product_id=<?= (int)$prod['id'] ?>" target="_blank" class="btn btn-sm btn-whatsapp w-100">
              <i class="bi bi-whatsapp me-1"></i> Buy via WhatsApp
            </a>
            <?php else: ?>
            <button type="button" class="btn btn-sm btn-kg-primary w-100" onclick="addToCart(<?= (int)$prod['id'] ?>)">
              <i class="bi bi-cart-plus me-1"></i> Add to Cart
            </button>
            <?php endif; ?>
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
    <a href="<?= BASE_URL ?>/donate.php" target="_blank" class="btn-kg-gold btn btn-lg">
      <i class="bi bi-heart-fill me-2"></i>Donate Now
    </a>
    &nbsp;&nbsp;
    <a href="<?= BASE_URL ?>/contact.php" class="btn btn-lg" style="background:rgba(255,255,255,.15);color:#fff;border:2px solid rgba(255,255,255,.4);border-radius:50px;padding:.7rem 2rem;">
      <i class="bi bi-telephone me-2"></i>Contact Us
    </a>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
