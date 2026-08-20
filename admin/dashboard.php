<?php
/**
 * Admin Dashboard — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$pdo = getDBConnection();

// Stats
$stmt = $pdo->query("
  SELECT
    (SELECT COUNT(*) FROM cows)                                    AS total_cows,
    (SELECT COUNT(*) FROM cows WHERE adoption_status='Available')  AS available_cows,
    (SELECT COUNT(*) FROM donations WHERE status='Completed')      AS completed_donations,
    (SELECT COALESCE(SUM(amount),0) FROM donations WHERE status='Completed') AS total_donated,
    (SELECT COUNT(*) FROM adoptions WHERE status='Active')         AS active_adoptions,
    (SELECT COUNT(*) FROM adoptions WHERE status='Pending')        AS pending_adoptions,
    (SELECT COUNT(*) FROM products WHERE is_active=1)              AS total_products,
    (SELECT COUNT(*) FROM orders WHERE status='Pending')           AS pending_orders,
    (SELECT COUNT(*) FROM messages WHERE is_read=0)                AS unread_messages,
    (SELECT COUNT(*) FROM volunteers WHERE status='Pending')       AS pending_volunteers,
    (SELECT COUNT(*) FROM gallery)                                 AS gallery_count
");
$stats = $stmt->fetch();

// Recent donations
$recentDonations = $pdo->query("SELECT * FROM donations ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Recent messages
$recentMessages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5")->fetchAll();

$adminPageTitle  = 'Dashboard';
$adminActivePage = 'dashboard';
require_once __DIR__ . '/includes/admin_layout_header.php';
?>

<!-- Stat Cards Row 1 -->
<div class="row g-4 mb-4">
  <div class="col-6 col-md-3">
    <div class="kg-dash-stat" style="border-left-color:var(--kg-green);">
      <div class="kg-dash-stat-icon" style="background:var(--kg-green-pale);color:var(--kg-green);">
        <i class="bi bi-emoji-heart-eyes"></i>
      </div>
      <div>
        <div class="kg-dash-stat-number"><?= (int)$stats['total_cows'] ?></div>
        <div class="kg-dash-stat-label">Total Cows</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kg-dash-stat" style="border-left-color:var(--kg-gold);">
      <div class="kg-dash-stat-icon" style="background:#fef9c3;color:#a16207;">
        <i class="bi bi-currency-rupee"></i>
      </div>
      <div>
        <div class="kg-dash-stat-number"><?= format_inr((float)$stats['total_donated']) ?></div>
        <div class="kg-dash-stat-label">Total Donated</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kg-dash-stat" style="border-left-color:#3b82f6;">
      <div class="kg-dash-stat-icon" style="background:#dbeafe;color:#1d4ed8;">
        <i class="bi bi-heart-fill"></i>
      </div>
      <div>
        <div class="kg-dash-stat-number"><?= (int)$stats['active_adoptions'] ?></div>
        <div class="kg-dash-stat-label">Active Adoptions</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kg-dash-stat" style="border-left-color:#ef4444;">
      <div class="kg-dash-stat-icon" style="background:#fee2e2;color:#b91c1c;">
        <i class="bi bi-envelope-fill"></i>
      </div>
      <div>
        <div class="kg-dash-stat-number"><?= (int)$stats['unread_messages'] ?></div>
        <div class="kg-dash-stat-label">Unread Messages</div>
      </div>
    </div>
  </div>
</div>

<!-- Stat Cards Row 2 -->
<div class="row g-4 mb-5">
  <div class="col-6 col-md-3">
    <div class="kg-dash-stat" style="border-left-color:#8b5cf6;">
      <div class="kg-dash-stat-icon" style="background:#ede9fe;color:#7c3aed;">
        <i class="bi bi-shop"></i>
      </div>
      <div>
        <div class="kg-dash-stat-number"><?= (int)$stats['total_products'] ?></div>
        <div class="kg-dash-stat-label">Active Products</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kg-dash-stat" style="border-left-color:#f59e0b;">
      <div class="kg-dash-stat-icon" style="background:#fef3c7;color:#b45309;">
        <i class="bi bi-bag-fill"></i>
      </div>
      <div>
        <div class="kg-dash-stat-number"><?= (int)$stats['pending_orders'] ?></div>
        <div class="kg-dash-stat-label">Pending Orders</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kg-dash-stat" style="border-left-color:#14b8a6;">
      <div class="kg-dash-stat-icon" style="background:#ccfbf1;color:#0f766e;">
        <i class="bi bi-people-fill"></i>
      </div>
      <div>
        <div class="kg-dash-stat-number"><?= (int)$stats['pending_volunteers'] ?></div>
        <div class="kg-dash-stat-label">Pending Volunteers</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kg-dash-stat" style="border-left-color:#10b981;">
      <div class="kg-dash-stat-icon" style="background:#d1fae5;color:#059669;">
        <i class="bi bi-images"></i>
      </div>
      <div>
        <div class="kg-dash-stat-number"><?= (int)$stats['gallery_count'] ?></div>
        <div class="kg-dash-stat-label">Gallery Images</div>
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-5">
  <div class="col-12">
    <h6 class="text-muted fw-600 mb-3">Quick Actions</h6>
  </div>
  <?php
  $actions = [
    ['href'=>BASE_URL.'/admin/cow-add.php',     'icon'=>'plus-circle',     'label'=>'Add Cow',      'color'=>'var(--kg-green)'],
    ['href'=>BASE_URL.'/admin/product-add.php',  'icon'=>'plus-circle',     'label'=>'Add Product',  'color'=>'#7c3aed'],
    ['href'=>BASE_URL.'/admin/gallery-add.php',  'icon'=>'image',           'label'=>'Add Photo',    'color'=>'#0f766e'],
    ['href'=>BASE_URL.'/admin/messages.php',     'icon'=>'envelope-open',   'label'=>'View Messages','color'=>'#b91c1c'],
    ['href'=>BASE_URL.'/admin/donations.php',    'icon'=>'currency-rupee',  'label'=>'Donations',    'color'=>'#a16207'],
    ['href'=>BASE_URL.'/admin/adoptions.php',    'icon'=>'heart',           'label'=>'Adoptions',    'color'=>'#1d4ed8'],
  ];
  foreach ($actions as $a): ?>
  <div class="col-6 col-md-2">
    <a href="<?= $a['href'] ?>" class="d-flex flex-column align-items-center justify-content-center p-3 bg-white rounded-3 shadow-sm text-decoration-none h-100" style="border:1px solid #e5e7eb;transition:all .2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform=''">
      <i class="bi bi-<?= $a['icon'] ?>" style="font-size:1.6rem;color:<?= $a['color'] ?>;"></i>
      <span style="font-size:.78rem;font-weight:600;color:#374151;margin-top:.4rem;"><?= e($a['label']) ?></span>
    </a>
  </div>
  <?php endforeach; ?>
</div>

<!-- Recent Activity -->
<div class="row g-4">
  <!-- Recent Donations -->
  <div class="col-lg-6">
    <div class="kg-admin-table">
      <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-700" style="color:var(--kg-green-dark);">Recent Donations</h6>
        <a href="<?= BASE_URL ?>/admin/donations.php" class="btn btn-sm btn-kg-outline" style="font-size:.75rem;">View All</a>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Donor</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($recentDonations)): ?>
            <tr><td colspan="4" class="text-center text-muted py-3">No donations yet.</td></tr>
            <?php else: ?>
            <?php foreach ($recentDonations as $d): ?>
            <tr>
              <td><?= e($d['donor_name']) ?></td>
              <td class="fw-600" style="color:var(--kg-green);"><?= format_inr((float)$d['amount']) ?></td>
              <td>
                <?php
                  $sc = match($d['status']) { 'Completed'=>'kg-badge-green','Pending'=>'kg-badge-gold',default=>'kg-badge-red' };
                ?>
                <span class="<?= $sc ?>"><?= e($d['status']) ?></span>
              </td>
              <td style="font-size:.78rem;color:#888;"><?= format_date($d['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Recent Messages -->
  <div class="col-lg-6">
    <div class="kg-admin-table">
      <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-700" style="color:var(--kg-green-dark);">Recent Messages</h6>
        <a href="<?= BASE_URL ?>/admin/messages.php" class="btn btn-sm btn-kg-outline" style="font-size:.75rem;">View All</a>
      </div>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>From</th>
              <th>Subject</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($recentMessages)): ?>
            <tr><td colspan="3" class="text-center text-muted py-3">No messages yet.</td></tr>
            <?php else: ?>
            <?php foreach ($recentMessages as $m): ?>
            <tr <?= $m['is_read'] ? '' : 'style="font-weight:600;"' ?>>
              <td>
                <?= e($m['name']) ?>
                <?php if (!$m['is_read']): ?><span class="kg-badge-red ms-1">New</span><?php endif; ?>
              </td>
              <td style="font-size:.83rem;"><?= e(mb_strimwidth($m['subject'] ?: 'General enquiry', 0, 30, '…')) ?></td>
              <td style="font-size:.78rem;color:#888;"><?= format_date($m['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
