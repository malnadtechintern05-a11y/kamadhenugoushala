<?php
/**
 * Admin Delete Cow — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$id    = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$token = $_GET['csrf_token'] ?? '';

if ($id <= 0 || !hash_equals(csrf_token(), $token)) {
    set_flash('danger', 'Invalid request.');
    redirect(BASE_URL . '/admin/cows.php');
}

$pdo  = getDBConnection();
$stmt = $pdo->prepare("SELECT id, name, image FROM cows WHERE id = :id");
$stmt->execute([':id' => $id]);
$cow  = $stmt->fetch();

if (!$cow) {
    set_flash('danger', 'Cow not found.');
    redirect(BASE_URL . '/admin/cows.php');
}

// Delete image file
if (!empty($cow['image'])) {
    delete_image(UPLOAD_COWS_DIR, $cow['image']);
}

// Delete row (adoptions cascade due to FK)
$pdo->prepare("DELETE FROM cows WHERE id = :id")->execute([':id' => $id]);

set_flash('success', 'Cow "' . $cow['name'] . '" deleted successfully.');
redirect(BASE_URL . '/admin/cows.php');
