<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $role     = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("INSERT INTO users (username, email, role, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $role, $password]);
        
        header("Location: user_management.php?msg=User added successfully");
    } catch(PDOException $e) {
        header("Location: user_management.php?msg=Error: " . $e->getMessage());
    }
}
?>