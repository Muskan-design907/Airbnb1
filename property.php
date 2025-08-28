<?php
require 'db.php';
 
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid property ID.");
}
 
$property_id = (int)$_GET['id'];
 
// Fetch property details
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = :id");
$stmt->execute([':id' => $property_id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);
 
if (!$property) {
    die("Property not found.");
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?= htmlspecialchars($property['title']) ?> - Airbnb Clone</title>
<style>
  /* Internal CSS */
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
    max-width: 900px;
    margin: 20px auto;
    background:#fff;
    padding: 20px;
    border-radius: 8px;
  }
  h1 {
    margin-top: 0;
    color: #222;
  }
  .property-image {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 8px;
  }
  .details {
    margin-top: 15px;
    color: #555;
  }
  .amenities {
    margin-top: 10px;
  }
  .amenities span {
    background: #FF385C;
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 13px;
    margin-right: 8px;
    display: inline-block;
    margin-bottom: 5px;
  }
  form {
    margin-top: 25px;
    background: #fafafa;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 1px 5px rgba(0,0,0,0.1);
  }
  label {
    display: block;
    margin-top: 10px;
    font-weight: bold;
  }
  input[type="date"], input[type="number"] {
    padding: 8px;
    width: 100%;
    max-width: 200px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-top: 5px;
  }
  button {
    margin-top: 15px;
    background:#FF385C;
    color:#fff;
    border:none;
    padding: 10px 20px;
    border-radius:4px;
    cursor:pointer;
    font-weight:bold;
  }
  button:hover {
    background:#e82d51;
  }
  a.back-link {
    display: inline-block;
    margin-bottom: 20px;
    color: #FF385C;
    text-decoration: none;
    font-weight: bold;
  }
  a.back-link:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>
 
<header><a href="listings.php" class="back-link" style="color:#fff; text-decoration:none;">&larr; Back to Listings</a> Airbnb Clone</header>
 
<div class="container">
 
  <h1><?= htmlspecialchars($property['title']) ?></h1>
  <img src="<?= htmlspecialchars($property['image_url']) ?>" alt="<?= htmlspecialchars($property['title']) ?>" class="property-image" />
 
  <p class="details"><strong>Location:</strong> <?= htmlspecialchars($property['location']) ?></p>
  <p class="details"><strong>Price per night:</strong> $<?= number_format($property['price_per_night'], 2) ?></p>
  <p class="details"><strong>Rating:</strong> <?= htmlspecialchars($property['rating']) ?>/5</p>
 
  <div class="amenities">
    <strong>Amenities:</strong><br />
    <?php
      // Assuming amenities stored as CSV string
      $amenities = explode(',', $property['amenities']);
      foreach ($amenities as $amenity) {
          echo '<span>' . htmlspecialchars(trim($amenity)) . '</span>';
      }
    ?>
  </div>
 
  <form action="book.php" method="POST">
    <input type="hidden" name="property_id" value="<?= $property_id ?>" />
    <label for="checkin">Check-in Date</label>
    <input type="date" name="checkin" id="checkin" required min="<?= date('Y-m-d') ?>" />
 
    <label for="checkout">Check-out Date</label>
    <input type="date" name="checkout" id="checkout" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>" />
 
    <label for="guests">Number of Guests</label>
    <input type="number" name="guests" id="guests" required min="1" max="20" value="1" />
 
    <button type="submit">Book Now</button>
  </form>
 
</div>
 
</body>
</html>
 
