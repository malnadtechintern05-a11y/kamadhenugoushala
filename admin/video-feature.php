<?php
/**
 * Admin Feature Video — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$token = $_GET['csrf_token'] ?? '';

if ($id > 0 && hash_equals(csrf_token(), $token)) {
    $pdo = getDBConnection();
    
    // Un-feature all videos
    $pdo->query("UPDATE videos SET is_featured = 0");
    
    // Feature this one
    $stmt = $pdo->prepare("UPDATE videos SET is_featured = 1 WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    set_flash('success', 'Homepage video updated successfully.');
} else {
    set_flash('danger', 'Invalid request.');
}

redirect(BASE_URL . '/admin/videos.php');
