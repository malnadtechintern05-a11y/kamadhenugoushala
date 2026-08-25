<?php
/**
 * Admin Adoption View — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    redirect(BASE_URL . '/admin/adoptions.php');
}

$pdo = getDBConnection();

// Fetch adoption
$stmt = $pdo->prepare("SELECT a.*, c.name AS cow_name, c.breed AS cow_breed FROM adoptions a LEFT JOIN cows c ON a.cow_id = c.id WHERE a.id = :id");
$stmt->execute([':id' => $id]);
$adoption = $stmt->fetch();

if (!$adoption) {
    redirect(BASE_URL . '/admin/adoptions.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    csrf_validate();
    $newStatus = sanitize($_POST['status']);
    if (in_array($newStatus, ['Pending', 'Active', 'Completed', 'Cancelled'])) {
        $pdo->prepare("UPDATE adoptions SET status = :status WHERE id = :id")->execute([
            ':status' => $newStatus,
            ':id' => $id
        ]);
        redirect(BASE_URL . '/admin/adoption-view.php?id=' . $id . '&msg=updated');
    }
}

$adminPageTitle  = 'View Adoption';
$adminActivePage = 'adoptions';
require_once __DIR__ . '/includes/admin_layout_header.php';
?>

<div class="kg-form-card" style="max-width: 800px;">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Adoption Details</h4>
    <div>
      <a href="<?= BASE_URL ?>/admin/adoptions.php" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Adoptions
      </a>
    </div>
  </div>

  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
  <div class="alert alert-success py-2">Status updated successfully.</div>
  <?php endif; ?>

  <table class="table table-bordered align-middle">
    <tbody>
      <tr>
        <th class="bg-body-tertiary" style="width:180px;">Adopter Name</th>
        <td><?= e($adoption['adopter_name']) ?></td>
      </tr>
      <tr>
        <th class="bg-body-tertiary">Email Address</th>
        <td><a href="mailto:<?= e($adoption['adopter_email']) ?>"><?= e($adoption['adopter_email']) ?></a></td>
      </tr>
      <tr>
        <th class="bg-body-tertiary">Phone Number</th>
        <td><?= $adoption['adopter_phone'] ? e($adoption['adopter_phone']) : '<em class="text-muted">Not provided</em>' ?></td>
      </tr>
      <tr>
        <th class="bg-body-tertiary">Address</th>
        <td><?= $adoption['adopter_address'] ? nl2br(e($adoption['adopter_address'])) : '<em class="text-muted">Not provided</em>' ?></td>
      </tr>
      <tr>
        <th class="bg-body-tertiary">Cow Details</th>
        <td>
           <strong><?= e($adoption['cow_name'] ?? 'N/A') ?></strong><br>
           <span style="font-size:0.85rem; color:#666;"><?= e($adoption['cow_breed'] ?? '') ?></span>
        </td>
      </tr>
      <tr>
        <th class="bg-body-tertiary">Duration</th>
        <td><?= (int)$adoption['duration_months'] ?> months</td>
      </tr>
      <tr>
        <th class="bg-body-tertiary">Monthly Amount</th>
        <td class="fw-bold text-kg-green"><?= format_inr((float)$adoption['amount_per_month']) ?></td>
      </tr>
      <tr>
        <th class="bg-body-tertiary">Date Applied</th>
        <td><?= format_datetime($adoption['created_at']) ?></td>
      </tr>
      <tr>
        <th class="bg-body-tertiary">Status</th>
        <td>
          <form method="POST" action="" class="d-flex align-items-center gap-2">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="update_status">
            <select name="status" class="form-select form-select-sm" style="width: auto;">
              <?php foreach(['Pending', 'Active', 'Completed', 'Cancelled'] as $s): ?>
              <option value="<?= $s ?>" <?= $adoption['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-kg-primary">Update</button>
          </form>
        </td>
      </tr>
    </tbody>
  </table>

  <?php if (!empty($adoption['message'])): ?>
  <h5 class="mt-4 mb-3" style="color:var(--kg-green-dark);">Adopter Message / Notes</h5>
  <div class="p-4 rounded bg-body-tertiary border" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;"><?= e($adoption['message']) ?></div>
  <?php endif; ?>
  
  <div class="mt-4 d-flex gap-2">
    <a href="mailto:<?= e($adoption['adopter_email']) ?>" class="btn btn-primary">
      <i class="bi bi-envelope me-1"></i> Email Adopter
    </a>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_layout_footer.php'; ?>
