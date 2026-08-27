<?php
/**
 * Seva Page — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$pageTitle  = 'Seva — Ways to Serve';
$pageDesc   = 'Participate in Gau Seva at Kamadhenu Goushala. Feed a cow, sponsor medical care, or volunteer your time.';
$activePage = 'seva';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$sevaOptions = [
  ['icon'=>'bag-heart-fill','title'=>'Feed a Cow','amount'=>'₹500/day','desc'=>'Sponsor a full day\'s nutritious feed — green fodder, dry fodder, and mineral mix — for one cow.','bg'=>'var(--kg-green-pale)','color'=>'var(--kg-green)'],
  ['icon'=>'activity','title'=>'Medical Seva','amount'=>'₹2,000+','desc'=>'Cover the cost of medicines, vet visits, or surgery for one of our sick or injured cows.','bg'=>'#fee2e2','color'=>'#b91c1c'],
  ['icon'=>'house-heart-fill','title'=>'Shelter Seva','amount'=>'₹5,000+','desc'=>'Help maintain and improve the sheds, flooring, and infrastructure that keeps our cows comfortable.','bg'=>'#fef9c3','color'=>'#a16207'],
  ['icon'=>'droplet-fill','title'=>'Clean Water','amount'=>'₹1,000','desc'=>'Fund the cleaning and maintenance of water troughs so our cows always have fresh, clean water.','bg'=>'#dbeafe','color'=>'#1d4ed8'],
  ['icon'=>'sun-fill','title'=>'Annual Gau Puja','amount'=>'₹10,000','desc'=>'Sponsor the annual Gau Puja ceremony — a sacred ritual honouring the divine cow mother.','bg'=>'#ede9fe','color'=>'#7c3aed'],
  ['icon'=>'heart-fill','title'=>'Cow Adoption','amount'=>'₹1,500/month','desc'=>'Fully sponsor a single cow\'s care every month — food, medical, and shelter.','bg'=>'#fce7f3','color'=>'#be185d'],
];
?>

<section class="kg-page-banner">
  <div class="container">
    <h1><i class="bi bi-hand-index-thumb me-2"></i>Gau Seva</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Home</a></li>
        <li class="breadcrumb-item active">Seva</li>
      </ol>
    </nav>
  </div>
</section>

<!-- Intro -->
<section class="kg-section">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="kg-section-label">Sacred Service</div>
        <h2 class="kg-section-title">What is Gau Seva?</h2>
        <div class="kg-divider mb-4"></div>
        <p style="color:var(--kg-text-muted);">
          In the Indian tradition, Gau Seva — the service of the cow — is considered one of the highest forms of devotion. 
          The cow is revered as Gau Mata, the mother who nourishes all life. She gives us milk, ghee, gomutra and 
          gobar — all of which have immense nutritional, medicinal, and spiritual value.
        </p>
        <p style="color:var(--kg-text-muted);">
          At Kamadhenu Goushala, we offer many ways for you to participate in this sacred service, 
          regardless of where you live or how much you can contribute. Every act of Seva matters.
        </p>
        <div class="kg-info-box mt-4">
          <p class="mb-0" style="color:var(--kg-green-dark);font-weight:500;">
            <i class="bi bi-shield-check me-2" style="color:var(--kg-gold-dark);"></i>
            All donations are eligible for 80G tax exemption under the Income Tax Act, India.
          </p>
        </div>
      </div>
      <div class="col-lg-6">
        <img src="<?= BASE_URL ?>/uploads/cows/sahiwal_lakshmi.jpg"
             onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'"
             alt="Beautiful Sahiwal Cow at Kamadhenu Goushala"
             class="kg-about-img" loading="lazy" style="border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
      </div>
    </div>
  </div>
</section>

<!-- Seva Cards -->
<section class="kg-section kg-section-alt">
  <div class="container">
    <div class="kg-section-header">
      <div class="kg-section-label">Ways to Serve</div>
      <h2 class="kg-section-title">Choose Your Seva</h2>
      <div class="kg-divider"></div>
      <p class="kg-section-desc mt-3">Each form of seva directly impacts the wellbeing of our cows.</p>
    </div>
    <div class="row g-4">
      <?php foreach ($sevaOptions as $seva): ?>
      <div class="col-12 col-md-6 col-lg-4">
        <div class="kg-seva-card h-100">
          <div class="kg-seva-icon" style="background:<?= $seva['bg'] ?>;color:<?= $seva['color'] ?>;">
            <i class="bi bi-<?= $seva['icon'] ?>"></i>
          </div>
          <h4><?= $seva['title'] ?></h4>
          <div class="kg-seva-amount"><?= $seva['amount'] ?></div>
          <p style="color:var(--kg-text-muted);font-size:.88rem;"><?= $seva['desc'] ?></p>
          <a href="<?= BASE_URL ?>/donate.php" target="_blank" class="btn btn-sm btn-kg-primary mt-auto">Donate Now</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Daily Schedule -->
<section class="kg-section">
  <div class="container">
    <div class="kg-section-header">
      <div class="kg-section-label">Our Routine</div>
      <h2 class="kg-section-title">A Day at the Goushala</h2>
      <div class="kg-divider"></div>
    </div>
    <div class="row g-4 justify-content-center">
      <?php
      $schedule = [
        ['time'=>'5:00 AM','icon'=>'sunrise','label'=>'Morning Prayer','desc'=>'Day begins with Gau Puja and prayers for the wellbeing of all our cows.'],
        ['time'=>'6:00 AM','icon'=>'droplet-fill','label'=>'Milking','desc'=>'A2 milk is collected gently and with respect. Calves always feed first.'],
        ['time'=>'7:30 AM','icon'=>'bag-heart-fill','label'=>'Morning Feed','desc'=>'Fresh green fodder, dry fodder, and mineral mix are given to each cow.'],
        ['time'=>'10:00 AM','icon'=>'activity','label'=>'Vet Check','desc'=>'Daily health inspection and treatment of any sick or recovering cows.'],
        ['time'=>'4:00 PM','icon'=>'bag-heart-fill','label'=>'Evening Feed','desc'=>'Second feeding of the day with supplemental jaggery and oil cake.'],
        ['time'=>'7:00 PM','icon'=>'moon-fill','label'=>'Evening Prayer','desc'=>'Day closes with a lamp-lighting ceremony and a prayer for all cows.'],
      ];
      foreach ($schedule as $sch): ?>
      <div class="col-12 col-md-6 col-lg-4">
        <div class="d-flex gap-3 p-3 bg-white rounded-3 shadow-sm border h-100" style="border-color:var(--kg-border)!important;">
          <div style="width:44px;height:44px;background:var(--kg-green-pale);border-radius:var(--kg-radius-sm);display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:var(--kg-green);flex-shrink:0;">
            <i class="bi bi-<?= $sch['icon'] ?>"></i>
          </div>
          <div>
            <div style="font-size:.72rem;font-weight:700;color:var(--kg-gold-dark);letter-spacing:.05em;"><?= $sch['time'] ?></div>
            <div class="fw-600" style="color:var(--kg-green-dark);font-size:.92rem;"><?= $sch['label'] ?></div>
            <p class="mb-0 mt-1" style="font-size:.82rem;color:var(--kg-text-muted);"><?= $sch['desc'] ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="kg-section kg-section-green">
  <div class="container text-center">
    <h2 style="color:#fff;" class="mb-3">Start Your Seva Today</h2>
    <p style="color:rgba(255,255,255,.8);max-width:500px;margin:0 auto 2rem;">
      Every rupee you contribute goes 100% towards the care of our sacred cows. No overhead deductions.
    </p>
    <a href="<?= BASE_URL ?>/donate.php" target="_blank" class="btn-kg-gold btn btn-lg me-2">
      <i class="bi bi-heart-fill me-2"></i>Donate Now
    </a>
    <a href="<?= BASE_URL ?>/volunteer.php" class="btn btn-lg" style="background:rgba(255,255,255,.15);color:#fff;border:2px solid rgba(255,255,255,.4);border-radius:50px;padding:.7rem 2rem;">
      <i class="bi bi-people me-2"></i>Volunteer
    </a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
