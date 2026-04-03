<?php
session_start();
include 'db_connection.php'; 

// Check if the user is actually an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $region_name = trim($_POST['region_name']);
    $description = trim($_POST['description']);

    try {
        // We use 'region_name' to match your table structure
        $stmt = $conn->prepare("INSERT INTO regions (region_name, description) VALUES (?, ?)");
        
        if ($stmt->execute([$region_name, $description])) {
            header("Location: field_regions.php?msg=Region Added Successfully");
        }
    } catch(PDOException $e) {
        // This catches errors like 'Table regions not found'
        header("Location: field_regions.php?msg=Error: " . urlencode($e->getMessage()));
    }
    exit();
}
?>