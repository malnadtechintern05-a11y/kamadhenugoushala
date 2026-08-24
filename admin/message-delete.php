<?php
/**
 * Admin Message Delete — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

redirect(BASE_URL . '/admin/messages.php');
