<?php
/**
 * Admin Events List — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$pdo = getDBConnection();
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

$countSql = "SELECT COUNT(*) FROM events";
$dataSql  = "SELECT * FROM events ORDER BY event_date DESC";
$pagData  = paginate($pdo, $countSql, $dataSql, [], $page, 15);

$adminPageTitle  = 'Manage Events';
$adminActivePage = 'events';
require_once __DIR__ . '/includes/admin_layout_header.php';
echo flash_alert();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <p class="text-muted mb-0">Showing <?= count($pagData['items']) ?> of <?= $pagData['total'] ?> events</p>
  <a href="<?= BASE_URL ?>/admin/event-add.php" class="btn btn-kg-primary">
    <i class="bi bi-plus-circle me-2"></i>Add New Event
  </a>
</div>

<div class="kg-admin-table">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Image</th>
          <th>Title</th>
          <th>Date & Time</th>
          <th>Location</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($pagData['items'])): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">No events found. <a href="<?= BASE_URL ?>/admin/event-add.php">Add one now</a>.</td></tr>
        <?php else: ?>
        <?php foreach ($pagData['items'] as $event): ?>
        <tr>
          <td>
            <img src="<?= img_url('events', $event['image']) ?>"
                 alt="<?= e($event['title']) ?>"
                 style="width:52px;height:52px;object-fit:cover;border-radius:8px;border:2px solid var(--kg-border);"
                 onerror="this.src='<?= BASE_URL ?>/assets/images/placeholder.jpg'">
          </td>
          <td class="fw-600"><?= e($event['title']) ?></td>
          <td><?= date('d M Y, h:i A', strtotime($event['event_date'])) ?></td>
          <td><?= e($event['location']) ?></td>
          <td>
            <?php if ($event['is_active']): ?>
              <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
            <?php else: ?>
              <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">Inactive</span>
            <?php endif; ?>
          </td>
          <td class="text-end">
            <div class="btn-group">
              <a href="<?= BASE_URL ?>/admin/event-edit.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                <i class="bi bi-pencil-square"></i>
              </a>
              <form action="<?= BASE_URL ?>/admin/event-delete.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this event?');">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $event['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                  <i class="bi bi-trash3"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php if ($pagData['total_pages'] > 1): ?>
<nav aria-label="Events pagination" class="mt-4">
  <ul class="pagination justify-content-center">
    <?php for ($i = 1; $i <= $pagData['total_pages']; $i++): ?>
      <li class="page-item <?= ($i === $pagData['current_page']) ? 'active' : '' ?>">
        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
