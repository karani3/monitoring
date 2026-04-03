<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    $user_id = $_SESSION['user_id'];
    $amount = floatval($_POST['amount']);

    if ($amount <= 0) {
        header("Location: beneficiary_dashboard.php?error=invalid_amount");
        exit();
    }

    try {
        // Check if a wallet record already exists for this user
        $checkWallet = $conn->prepare("SELECT user_id FROM wallet WHERE user_id = ?");
        $checkWallet->execute([$user_id]);
        
        if ($checkWallet->fetch()) {
            // Update existing wallet balance
            $stmt = $conn->prepare("UPDATE wallet SET balance = balance + ? WHERE user_id = ?");
            $stmt->execute([$amount, $user_id]);
        } else {
            // Create a new wallet record if it doesn't exist
            $stmt = $conn->prepare("INSERT INTO wallet (user_id, balance) VALUES (?, ?)");
            $stmt->execute([$user_id, $amount]);
        }

        header("Location: beneficiary_dashboard.php?status=success");
        exit();

    } catch (PDOException $e) {
        error_log("Recharge Error: " . $e->getMessage());
        die("System error processing your recharge.");
    }
} else {
    header("Location: beneficiary_dashboard.php");
    exit();
}
?>