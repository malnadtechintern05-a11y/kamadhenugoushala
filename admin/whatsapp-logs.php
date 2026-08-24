<?php
/**
 * Admin WhatsApp Logs View — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$pdo = getDBConnection();
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

$pagData = paginate($pdo,
    "SELECT COUNT(*) FROM whatsapp_logs",
    "SELECT w.*, c.name AS cow_name, c.breed AS cow_breed 
     FROM whatsapp_logs w 
     LEFT JOIN cows c ON w.cow_id = c.id 
     ORDER BY w.created_at DESC",
    [], $page, 20
);

// Summary stats
$summary = $pdo->query("
    SELECT c.name, COUNT(w.id) as clicks 
    FROM whatsapp_logs w 
    JOIN cows c ON w.cow_id = c.id 
    GROUP BY w.cow_id 
    ORDER BY clicks DESC 
    LIMIT 4
")->fetchAll();

$totalClicks = $pdo->query("SELECT COUNT(*) FROM whatsapp_logs")->fetchColumn();

$adminPageTitle  = 'WhatsApp Logs';
$adminActivePage = 'whatsapp_logs';
require_once __DIR__ . '/includes/admin_layout_header.php';
?>

<!-- Summary -->
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="kg-dash-stat" style="border-left-color:var(--kg-green);">
      <div class="kg-dash-stat-icon" style="background:var(--kg-green-pale);color:var(--kg-green);">
        <i class="bi bi-whatsapp"></i>
      </div>
      <div>
        <div class="kg-dash-stat-number"><?= (int)$totalClicks ?></div>
        <div class="kg-dash-stat-label">Total WhatsApp Clicks</div>
      </div>
    </div>
  </div>
  
  <div class="col-md-9">
    <div class="card h-100 border-0 shadow-sm">
      <div class="card-body">
        <h6 class="card-title text-muted fw-bold mb-3">Top Clicked Cows</h6>
        <div class="d-flex flex-wrap gap-3">
            <?php if (empty($summary)): ?>
            <span class="text-muted">No clicks yet.</span>
            <?php else: ?>
                <?php foreach($summary as $s): ?>
                <div class="border rounded p-2 text-center" style="min-width: 120px;">
                    <div class="fw-bold text-kg-green fs-5"><?= (int)$s['clicks'] ?> <i class="bi bi-hand-index-thumb"></i></div>
                    <div style="font-size: 0.85rem;" class="text-muted"><?= e($s['name']) ?></div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="kg-admin-table">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr><th>ID</th><th>Cow</th><th>IP Address</th><th>Time of Click</th></tr>
      </thead>
      <tbody>
        <?php if (empty($pagData['items'])): ?>
        <tr><td colspan="4" class="text-center py-4 text-muted">No logs found.</td></tr>
        <?php else: ?>
        <?php foreach ($pagData['items'] as $log): ?>
        <tr>
          <td>#<?= (int)$log['id'] ?></td>
          <td>
            <div class="fw-600"><?= e($log['cow_name'] ?? 'Unknown Cow') ?></div>
            <div style="font-size:.78rem;color:#888;"><?= e($log['cow_breed'] ?? '') ?></div>
          </td>
          <td><span class="font-monospace text-muted" style="font-size: 0.85rem;"><?= e($log['ip_address']) ?></span></td>
          <td style="font-size:.85rem;color:#555;"><?= format_datetime($log['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<div class="mt-4">
  <?php echo pagination_html($pagData, BASE_URL . '/admin/whatsapp-logs.php'); ?>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
