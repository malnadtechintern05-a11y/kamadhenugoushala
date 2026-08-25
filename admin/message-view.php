<?php
/**
 * Admin Message View — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    redirect(BASE_URL . '/admin/messages.php');
}

$pdo = getDBConnection();

// Fetch message
$stmt = $pdo->prepare("SELECT * FROM messages WHERE id = :id");
$stmt->execute([':id' => $id]);
$msg = $stmt->fetch();

if (!$msg) {
    redirect(BASE_URL . '/admin/messages.php');
}

// Mark as read if not already
if (!$msg['is_read']) {
    $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = :id")->execute([':id' => $id]);
}

$adminPageTitle  = 'View Message';
$adminActivePage = 'messages';
require_once __DIR__ . '/includes/admin_layout_header.php';
?>

<div class="kg-form-card" style="max-width: 800px;">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Message Details</h4>
    <div>
      <a href="<?= BASE_URL ?>/admin/message-delete.php?id=<?= (int)$msg['id'] ?>" class="btn btn-sm btn-outline-danger me-2" onclick="return confirm('Are you sure you want to delete this message?');">
        <i class="bi bi-trash me-1"></i> Delete
      </a>
      <a href="<?= BASE_URL ?>/admin/messages.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Messages
      </a>
    </div>
  </div>

  <table class="table table-bordered align-middle">
    <tbody>
      <tr>
        <th class="bg-body-tertiary" style="width:150px;">From Name</th>
        <td><?= e($msg['name']) ?></td>
      </tr>
      <tr>
        <th class="bg-body-tertiary">Email Address</th>
        <td><a href="mailto:<?= e($msg['email']) ?>"><?= e($msg['email']) ?></a></td>
      </tr>
      <tr>
        <th class="bg-body-tertiary">Phone Number</th>
        <td><?= $msg['phone'] ? e($msg['phone']) : '<em class="text-muted">Not provided</em>' ?></td>
      </tr>
      <tr>
        <th class="bg-body-tertiary">Subject</th>
        <td><strong><?= e($msg['subject'] ?? 'No Subject') ?></strong></td>
      </tr>
      <tr>
        <th class="bg-body-tertiary">Date Received</th>
        <td><?= format_datetime($msg['created_at']) ?></td>
      </tr>
      <tr>
        <th class="bg-body-tertiary">Status</th>
        <td>
          <?php if ($msg['is_read']): ?>
            <span class="badge bg-success">Read</span>
          <?php else: ?>
            <span class="badge bg-danger">Unread</span>
          <?php endif; ?>
        </td>
      </tr>
    </tbody>
  </table>

  <h5 class="mt-4 mb-3" style="color:var(--kg-green-dark);">Message Content</h5>
  <div class="p-4 rounded bg-body-tertiary border" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;"><?= e($msg['message']) ?></div>
  
  <div class="mt-4 d-flex gap-2">
    <a href="mailto:<?= e($msg['email']) ?>" class="btn btn-primary">
      <i class="bi bi-reply-fill me-1"></i> Reply via Email
    </a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
