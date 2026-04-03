<?php
// db_conn.php
$host = "sql100.infinityfree.com"; 
$db_user = "if0_41483887";         
$db_pass = "Karani2007"; 
$db_name = "if0_41483887_db_tawicity";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) { 
    // This will help us see if the connection is still the problem
    die("Connection failed: " . $e->getMessage()); 
}
?>