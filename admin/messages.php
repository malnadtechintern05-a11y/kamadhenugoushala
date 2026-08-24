<?php
/**
 * Admin Messages — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$pdo  = getDBConnection();
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Mark as read if viewing a single message
if (!empty($_GET['read']) && (int)$_GET['read'] > 0) {
    $pdo->prepare("UPDATE messages SET is_read=1 WHERE id=:id")->execute([':id' => (int)$_GET['read']]);
    redirect(BASE_URL . '/admin/messages.php');
}

$pagData = paginate($pdo,
    "SELECT COUNT(*) FROM messages",
    "SELECT * FROM messages ORDER BY is_read ASC, created_at DESC",
    [], $page, 20
);

$adminPageTitle  = 'Contact Messages';
$adminActivePage = 'messages';
require_once __DIR__ . '/includes/admin_layout_header.php';
?>

<div class="kg-admin-table">
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr><th>From</th><th>Email</th><th>Subject</th><th>Message</th><th>Date</th><th>Actions</th></tr>
      </thead>
      <tbody>
        <?php if (empty($pagData['items'])): ?>
        <tr><td colspan="6" class="text-center py-4 text-muted">No messages.</td></tr>
        <?php else: ?>
        <?php foreach ($pagData['items'] as $m): ?>
        <tr <?= !$m['is_read'] ? 'style="font-weight:600;background:#fafff8;"' : '' ?>>
          <td>
            <?= e($m['name']) ?>
            <?php if (!$m['is_read']): ?><span class="kg-badge-red ms-1">New</span><?php endif; ?>
          </td>
          <td style="font-size:.82rem;"><a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a></td>
          <td style="font-size:.82rem;"><?= e(mb_strimwidth($m['subject'] ?: '(no subject)', 0, 40, '…')) ?></td>
          <td style="font-size:.82rem;max-width:250px;word-break:break-word;"><?= e(mb_strimwidth($m['message'], 0, 80, '…')) ?></td>
          <td style="font-size:.75rem;color:#888;white-space:nowrap;"><?= format_datetime($m['created_at']) ?></td>
          <td>
            <a href="<?= BASE_URL ?>/admin/message-view.php?id=<?= (int)$m['id'] ?>" class="btn btn-sm btn-outline-secondary" style="border-radius:6px;" title="View Message">
              <i class="bi bi-eye"></i>
            </a>
            <?php if (!$m['is_read']): ?>
            <a href="<?= BASE_URL ?>/admin/messages.php?read=<?= (int)$m['id'] ?>" class="btn btn-sm" style="background:#d1fae5;color:#065f46;border-radius:6px;" title="Mark as Read">
              <i class="bi bi-check2"></i>
            </a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/admin/message-delete.php?id=<?= (int)$m['id'] ?>" class="btn btn-sm btn-outline-danger" style="border-radius:6px;" title="Delete Message" onclick="return confirm('Are you sure you want to delete this message?');">
              <i class="bi bi-trash"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<div class="mt-4"><?= pagination_html($pagData, BASE_URL . '/admin/messages.php') ?></div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
