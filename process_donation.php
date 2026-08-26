<?php
/**
 * Process Donation Pledges — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$amount = (float)($_POST['amount'] ?? 0);
$purpose = $_POST['purpose'] ?? 'General';
$donor_name = $_POST['donor_name'] ?? 'A well-wisher';

if ($amount <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid amount.']);
    exit;
}

$pdo = getDBConnection();
$status = 'Pending';
$payment_method = 'Online'; // Or WhatsApp based
$message = "Donation for " . $purpose;

try {
    $pdo->beginTransaction();
    
    // Insert into donations (for admins)
    // We might not have email/phone if they are a guest, so we use session or empty
    $donor_email = '';
    $donor_phone = '';
    
    if (isset($_SESSION['supporter_id'])) {
        $stmt_sup = $pdo->prepare("SELECT email, phone FROM supporters WHERE id = :id");
        $stmt_sup->execute([':id' => $_SESSION['supporter_id']]);
        $sup = $stmt_sup->fetch();
        if ($sup) {
            $donor_email = $sup['email'];
            $donor_phone = $sup['phone'];
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO donations (donor_name, donor_email, donor_phone, amount, purpose, message, payment_method, status) VALUES (:name, :email, :phone, :amount, :purpose, :message, :pm, :status)");
    $stmt->execute([
        ':name' => $donor_name,
        ':email' => $donor_email,
        ':phone' => $donor_phone,
        ':amount' => $amount,
        ':purpose' => $purpose,
        ':message' => $message,
        ':pm' => $payment_method,
        ':status' => $status
    ]);
    
    // If logged in, also record in supporter_payments for the dashboard
    if (isset($_SESSION['supporter_id'])) {
        $stmt_sp = $pdo->prepare("INSERT INTO supporter_payments (supporter_id, amount, payment_method, message, status) VALUES (:sup_id, :amount, :pm, :msg, :status)");
        $stmt_sp->execute([
            ':sup_id' => $_SESSION['supporter_id'],
            ':amount' => $amount,
            ':pm' => $payment_method,
            ':msg' => $message,
            ':status' => $status
        ]);
    }
    
    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => 'Database error.']);
}
