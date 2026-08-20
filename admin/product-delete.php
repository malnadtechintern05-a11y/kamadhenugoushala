<?php
/**
 * Admin Delete Product — Kamadhenu Goushala
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
    redirect(BASE_URL . '/admin/products.php');
}

$pdo  = getDBConnection();
$stmt = $pdo->prepare("SELECT id, name, image FROM products WHERE id = :id");
$stmt->execute([':id' => $id]);
$prod = $stmt->fetch();

if (!$prod) {
    set_flash('danger', 'Product not found.');
    redirect(BASE_URL . '/admin/products.php');
}

if (!empty($prod['image'])) delete_image(UPLOAD_PRODUCTS_DIR, $prod['image']);
$pdo->prepare("DELETE FROM products WHERE id = :id")->execute([':id' => $id]);

set_flash('success', 'Product "' . $prod['name'] . '" deleted.');
redirect(BASE_URL . '/admin/products.php');
