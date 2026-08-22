<?php
/**
 * Public Events Page — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$activePage = 'events';
$pageTitle  = 'Upcoming Events';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$pdo = getDBConnection();

// Fetch active upcoming events
$stmtUpcoming = $pdo->query("SELECT * FROM events WHERE is_active = 1 AND event_date >= NOW() ORDER BY event_date ASC");
$upcomingEvents = $stmtUpcoming->fetchAll();

// Fetch active past events
$stmtPast = $pdo->query("SELECT * FROM events WHERE is_active = 1 AND event_date < NOW() ORDER BY event_date DESC LIMIT 6");
$pastEvents = $stmtPast->fetchAll();
?>

<!-- Page Header -->
<header class="kg-page-header text-center">
  <div class="container">
    <h1 class="display-4 fw-bold mb-3"><i class="bi bi-calendar-event me-3"></i>Goushala Events</h1>
    <p class="lead mb-0">Join us in celebrating our traditions, festivals, and educational workshops.</p>
  </div>
</header>

<div class="container py-5">
  <div class="row mb-5">
    <div class="col-12 text-center">
      <h2 class="kg-section-title">Upcoming Events</h2>
      <div class="kg-divider mx-auto mb-4"></div>
    </div>
  </div>

  <?php if (empty($upcomingEvents)): ?>
    <div class="text-center py-5 mb-5 bg-white rounded shadow-sm border border-success-subtle">
      <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
      <h4 class="mt-3 text-muted">No upcoming events scheduled at the moment.</h4>
      <p class="text-muted">Please check back later or subscribe to our newsletter.</p>
    </div>
  <?php else: ?>
    <div class="row g-4 mb-5">
      <?php foreach ($upcomingEvents as $event): ?>
        <div class="col-lg-6">
          <div class="card h-100 shadow-sm border-0 bg-white" style="border-radius: 12px; overflow: hidden;">
            <div class="row g-0 h-100">
              <div class="col-md-5">
                <img src="<?= img_url('events', $event['image']) ?>" class="img-fluid rounded-start h-100" alt="<?= e($event['title']) ?>" style="object-fit: cover; width: 100%; min-height: 250px; background-color: #f8f9fa;" onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'">
              </div>
              <div class="col-md-7 d-flex flex-column">
                <div class="card-body d-flex flex-column h-100">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="card-title fw-bold" style="color: var(--kg-green);"><?= e($event['title']) ?></h5>
                  </div>
                  <div class="mb-3 text-muted small">
                    <div class="mb-1"><i class="bi bi-calendar-date me-2"></i><?= date('l, d F Y', strtotime($event['event_date'])) ?></div>
                    <div class="mb-1"><i class="bi bi-clock me-2"></i><?= date('h:i A', strtotime($event['event_date'])) ?></div>
                    <div><i class="bi bi-geo-alt me-2"></i><?= e($event['location']) ?></div>
                  </div>
                  <p class="card-text text-muted mb-4 flex-grow-1" style="font-size: 0.95rem; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                    <?= e(strip_tags($event['description'])) ?>
                  </p>
                  <div class="mt-auto">
                    <a href="<?= BASE_URL ?>/contact.php" class="btn btn-outline-success w-100" style="border-radius: 8px;">Enquire Now</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($pastEvents)): ?>
    <div class="row mt-5 mb-4">
      <div class="col-12">
        <h3 class="fw-bold" style="color: var(--kg-green); border-bottom: 2px solid var(--kg-gold); padding-bottom: 10px; display: inline-block;">Past Events</h3>
      </div>
    </div>
    <div class="row g-4">
      <?php foreach ($pastEvents as $event): ?>
        <div class="col-md-4">
          <div class="card h-100 shadow-sm border-0">
            <img src="<?= img_url('events', $event['image']) ?>" class="card-img-top" alt="<?= e($event['title']) ?>" style="height: 200px; width: 100%; object-fit: cover; background-color: #f8f9fa;" onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'">
            <div class="card-body bg-light">
              <h6 class="card-title fw-bold mb-1 text-dark"><?= e($event['title']) ?></h6>
              <p class="text-muted small mb-0"><i class="bi bi-calendar-check me-1"></i> <?= date('d M Y', strtotime($event['event_date'])) ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
