<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tree_id'])) {
    $user_id = $_SESSION['user_id'];
    $price = $_POST['price'];

    // This reduces the account balance in your database
    $query = "UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ? AND wallet_balance >= ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("dii", $price, $user_id, $price);
    
    if ($stmt->execute()) {
        header("Location: buy_seedlings.php?status=success");
    } else {
        header("Location: buy_seedlings.php?status=error");
    }
    exit();
}