<?php
/**
 * Admin Delete Event — Kamadhenu Goushala
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();
    
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if ($id) {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("SELECT image FROM events WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $image = $stmt->fetchColumn();
        
        if ($image && file_exists(UPLOAD_EVENTS_DIR . $image)) {
            unlink(UPLOAD_EVENTS_DIR . $image);
        }
        
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        set_flash('success', 'Event deleted successfully.');
    } else {
        set_flash('danger', 'Invalid event ID.');
    }
}

redirect(BASE_URL . '/admin/events.php');
