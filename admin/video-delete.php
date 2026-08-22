<?php
/**
 * Admin Delete Video — Kamadhenu Goushala
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
    
    // Check if it was featured
    $stmt = $pdo->prepare("SELECT is_featured FROM videos WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $video = $stmt->fetch();
    
    if ($video) {
        $pdo->prepare("DELETE FROM videos WHERE id = :id")->execute([':id' => $id]);
        
        // If we deleted the featured video, randomly pick another one to be featured
        if ($video['is_featured']) {
            $pdo->query("UPDATE videos SET is_featured = 1 ORDER BY created_at DESC LIMIT 1");
        }
        
        flash_set('Video deleted successfully.', 'success');
    } else {
        flash_set('Video not found.', 'danger');
    }
} else {
    flash_set('Invalid request.', 'danger');
}

redirect(BASE_URL . '/admin/videos.php');
