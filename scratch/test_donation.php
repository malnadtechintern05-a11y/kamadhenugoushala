<?php
$_POST['amount'] = 100;
$_POST['purpose'] = 'General';
$_POST['donor_name'] = 'Test';

require 'config/database.php';
$pdo = getDBConnection();
$status = 'Pending';
$payment_method = 'Online';
$message = "Donation for General";

try {
    $stmt = $pdo->prepare("INSERT INTO donations (donor_name, donor_email, donor_phone, amount, purpose, message, payment_method, status) VALUES (:name, :email, :phone, :amount, :purpose, :message, :pm, :status)");
    $stmt->execute([
        ':name' => 'Test',
        ':email' => '',
        ':phone' => '',
        ':amount' => 100,
        ':purpose' => 'General',
        ':message' => $message,
        ':pm' => $payment_method,
        ':status' => $status
    ]);
    echo "Donation OK\n";
    
    $stmt_sp = $pdo->prepare("INSERT INTO supporter_payments (supporter_id, amount, payment_method, message, status) VALUES (:sup_id, :amount, :pm, :msg, :status)");
    $stmt_sp->execute([
        ':sup_id' => 1,
        ':amount' => 100,
        ':pm' => $payment_method,
        ':msg' => $message,
        ':status' => $status
    ]);
    echo "Supporter Payment OK\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
