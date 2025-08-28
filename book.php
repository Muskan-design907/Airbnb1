<?php
require 'db.php';
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}
 
// Sanitize and validate input
$property_id = isset($_POST['property_id']) ? (int)$_POST['property_id'] : 0;
$checkin = isset($_POST['checkin']) ? $_POST['checkin'] : '';
$checkout = isset($_POST['checkout']) ? $_POST['checkout'] : '';
$guests = isset($_POST['guests']) ? (int)$_POST['guests'] : 1;
 
if ($property_id <= 0 || !$checkin || !$checkout || $guests < 1) {
    die("Invalid booking data.");
}
 
if (strtotime($checkin) >= strtotime($checkout)) {
    die("Check-out date must be after check-in date.");
}
 
// Check if property exists
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = :id");
$stmt->execute([':id' => $property_id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);
 
if (!$property) {
    die("Property not found.");
}
 
// Calculate total nights and price
$start = new DateTime($checkin);
$end = new DateTime($checkout);
$nights = $start->diff($end)->days;
$total_price = $nights * $property['price_per_night'];
 
// Insert booking into bookings table
try {
    $stmt = $pdo->prepare("INSERT INTO bookings (property_id, checkin_date, checkout_date, guests, total_price, booking_date) VALUES (:pid, :cin, :cout, :guests, :price, NOW())");
    $stmt->execute([
        ':pid' => $property_id,
        ':cin' => $checkin,
        ':cout' => $checkout,
        ':guests' => $guests,
        ':price' => $total_price
    ]);
    $booking_id = $pdo->lastInsertId();
} catch (PDOException $e) {
    die("Booking failed: " . $e->getMessage());
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Booking Confirmation - Airbnb Clone</title>
<style>
  body {
    font-family: Arial, sans-serif;
    margin:0; padding:0; background:#f7f7f7;
  }
  header {
    background:#FF385C;
    color:#fff;
    padding:20px;
    text-align:center;
    font-size:24px;
    font-weight:bold;
  }
  .container {
    max-width: 600px;
    margin: 40px auto;
    background:#fff;
    padding: 30px;
    border-radius: 8px;
    text-align: center;
  }
  h1 {
    color: #22a622;
  }
  p {
    font-size: 16px;
    color: #333;
  }
  a.button {
    display: inline-block;
    margin-top: 20px;
    background:#FF385C;
    color:#fff;
    padding: 12px 25px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
  }
  a.button:hover {
    background:#e82d51;
  }
</style>
</head>
<body>
 
<header>Booking Confirmation</header>
 
<div class="container">
  <h1>Thank you for your booking!</h1>
  <p><strong>Property:</strong> <?= htmlspecialchars($property['title']) ?></p>
  <p><strong>Location:</strong> <?= htmlspecialchars($property['location']) ?></p>
  <p><strong>Check-in:</strong> <?= htmlspecialchars($checkin) ?></p>
  <p><strong>Check-out:</strong> <?= htmlspecialchars($checkout) ?></p>
  <p><strong>Guests:</strong> <?= htmlspecialchars($guests) ?></p>
  <p><strong>Total Nights:</strong> <?= $nights ?></p>
  <p><strong>Total Price:</strong> $<?= number_format($total_price, 2) ?></p>
  <a href="index.php" class="button">Back to Home</a>
</div>
 
</body>
</html>
 
