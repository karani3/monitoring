<?php
// config.php
$host = 'sql100.infinityfree.com';
$db   = 'if0_41483887_db_tawicity';
$user = 'if0_41483887';
$pass = 'Karani2007'; // Visible in your screenshot
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $conn = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // InfinityFree sometimes has connection delays; this keeps it clean
     die("Database connection failed. Please check back in a few minutes.");
}
?>