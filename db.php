<?php
// db.php - database connection file with your credentials
 
$host = 'localhost';  // assuming your DB server is localhost
$db   = 'dbdv9nakuqrmiu';
$user = 'ur9iyguafpilu';
$pass = '51gssrtsv3ei';
 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    // Set error mode to Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
 
