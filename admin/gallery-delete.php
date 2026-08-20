<?php
/**
 * Admin Gallery Delete — Kamadhenu Goushala
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
    redirect(BASE_URL . '/admin/gallery.php');
}

$pdo  = getDBConnection();
$stmt = $pdo->prepare("SELECT id, image FROM gallery WHERE id = :id");
$stmt->execute([':id' => $id]);
$item = $stmt->fetch();

if (!$item) {
    set_flash('danger', 'Gallery item not found.');
    redirect(BASE_URL . '/admin/gallery.php');
}

if (!empty($item['image'])) delete_image(UPLOAD_GALLERY_DIR, $item['image']);
$pdo->prepare("DELETE FROM gallery WHERE id = :id")->execute([':id' => $id]);

set_flash('success', 'Gallery image deleted.');
redirect(BASE_URL . '/admin/gallery.php');
