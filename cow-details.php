<?php
/**
 * Cow Details Page — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$pdo = getDBConnection();
$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    redirect(BASE_URL . '/cows.php');
}

$stmt = $pdo->prepare("SELECT * FROM cows WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$cow  = $stmt->fetch();

if (!$cow) {
    redirect(BASE_URL . '/cows.php');
}

// Other available cows for suggestions
$stmtOthers = $pdo->prepare("SELECT * FROM cows WHERE id != :id ORDER BY RAND() LIMIT 3");
$stmtOthers->execute([':id' => $id]);
$otherCows = $stmtOthers->fetchAll();

$pageTitle  = e($cow['name']) . ' — Cow Details';
$activePage = 'cows';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="kg-page-banner">
  <div class="container">
    <h1><i class="bi bi-emoji-heart-eyes me-2"></i><?= e($cow['name']) ?></h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/cows.php">Our Cows</a></li>
        <li class="breadcrumb-item active"><?= e($cow['name']) ?></li>
      </ol>
    </nav>
  </div>
</section>

<section class="kg-section">
  <div class="container">
    <div class="row g-5">
      <!-- Image -->
      <div class="col-lg-5">
        <img src="<?= img_url('cows', $cow['image']) ?>"
             alt="<?= e($cow['name']) ?>"
             class="kg-cow-detail-img"
             loading="eager"
             onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'">

        <!-- Adoption Status Badge -->
        <?php
          $badgeMap = [
            'Available'     => ['class' => 'kg-badge-green', 'icon' => 'check-circle'],
            'Adopted'       => ['class' => 'kg-badge-gold',  'icon' => 'heart-fill'],
            'Not Available' => ['class' => 'kg-badge-red',   'icon' => 'x-circle'],
          ];
          $bInfo = $badgeMap[$cow['adoption_status']] ?? $badgeMap['Not Available'];
        ?>
        <div class="mt-3 d-flex align-items-center gap-2">
          <span class="<?= $bInfo['class'] ?> px-3 py-2 fs-6">
            <i class="bi bi-<?= $bInfo['icon'] ?> me-1"></i>
            <?= e($cow['adoption_status']) ?>
          </span>
          <span class="kg-badge-green">
            <i class="bi bi-heart-pulse me-1"></i><?= e($cow['health_status']) ?>
          </span>
        </div>
      </div>

      <!-- Details -->
      <div class="col-lg-7">
        <div class="kg-section-label">Cow Profile</div>
        <h2 class="kg-section-title"><?= e($cow['name']) ?></h2>
        <div class="kg-divider mb-4"></div>

        <?php if (!empty($cow['description'])): ?>
        <p style="color:var(--kg-text-muted);font-size:1rem;line-height:1.8;margin-bottom:1.5rem;">
          <?= nl2br(e($cow['description'])) ?>
        </p>
        <?php endif; ?>

        <!-- Meta List -->
        <ul class="kg-cow-meta-list mb-4">
          <li>
            <i class="bi bi-bookmark-star-fill"></i>
            <span class="kg-cow-meta-key">Breed</span>
            <span class="kg-cow-meta-val"><?= e($cow['breed']) ?></span>
          </li>
          <li>
            <i class="bi bi-calendar3"></i>
            <span class="kg-cow-meta-key">Age</span>
            <span class="kg-cow-meta-val"><?= (int)$cow['age'] ?> years</span>
          </li>
          <li>
            <i class="bi bi-gender-ambiguous"></i>
            <span class="kg-cow-meta-key">Gender</span>
            <span class="kg-cow-meta-val"><?= e($cow['gender']) ?></span>
          </li>
          <li>
            <i class="bi bi-palette-fill"></i>
            <span class="kg-cow-meta-key">Color</span>
            <span class="kg-cow-meta-val"><?= e($cow['color']) ?></span>
          </li>
          <?php if ($cow['weight_kg']): ?>
          <li>
            <i class="bi bi-speedometer2"></i>
            <span class="kg-cow-meta-key">Weight</span>
            <span class="kg-cow-meta-val"><?= e($cow['weight_kg']) ?> kg</span>
          </li>
          <?php endif; ?>
          <li>
            <i class="bi bi-heart-pulse-fill"></i>
            <span class="kg-cow-meta-key">Health</span>
            <span class="kg-cow-meta-val"><?= e($cow['health_status']) ?></span>
          </li>
          <li>
            <i class="bi bi-calendar-check"></i>
            <span class="kg-cow-meta-key">Resident Since</span>
            <span class="kg-cow-meta-val"><?= format_date($cow['created_at']) ?></span>
          </li>
        </ul>

        <!-- CTA Buttons -->
        <div class="d-flex flex-wrap gap-3">
          <?php if ($cow['adoption_status'] === 'Available'): ?>
          <?php if (defined('CHECKOUT_MODE_COWS') && CHECKOUT_MODE_COWS === 'whatsapp'): ?>
          <a href="<?= BASE_URL ?>/whatsapp_redirect.php?cow_id=<?= (int)$cow['id'] ?>" target="_blank" class="btn btn-kg-gold">
            Adopt <?= e($cow['name']) ?>
          </a>
          <?php else: ?>
          <a href="<?= BASE_URL ?>/adopt.php?cow_id=<?= (int)$cow['id'] ?>" class="btn btn-kg-gold">
            Adopt <?= e($cow['name']) ?>
          </a>
          <?php endif; ?>
          <?php endif; ?>
          <a href="<?= BASE_URL ?>/donate.php" class="btn btn-kg-primary">
            <i class="bi bi-currency-rupee me-2"></i>Donate for Her Care
          </a>
          <a href="<?= BASE_URL ?>/cows.php" class="btn btn-kg-outline">
            <i class="bi bi-arrow-left me-2"></i>Back to All Cows
          </a>
        </div>
      </div>
    </div>

    <!-- Other Cows -->
    <?php if (!empty($otherCows)): ?>
    <div class="mt-5 pt-4">
      <h3 class="kg-section-title mb-4">Other Cows You May Like</h3>
      <div class="row g-4">
        <?php foreach ($otherCows as $oc): ?>
        <div class="col-md-4">
          <div class="kg-cow-card">
            <div class="kg-cow-card-img">
              <img src="<?= img_url('cows', $oc['image']) ?>"
                   alt="<?= e($oc['name']) ?>"
                   loading="lazy"
                   onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'">
            </div>
            <div class="kg-cow-card-body">
              <h4 class="kg-cow-card-name"><?= e($oc['name']) ?></h4>
              <p class="kg-cow-card-breed"><?= e($oc['breed']) ?></p>
              <a href="<?= BASE_URL ?>/cow-details.php?id=<?= (int)$oc['id'] ?>" class="btn btn-sm btn-kg-primary w-100">View Details</a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
