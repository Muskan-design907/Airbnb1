<?php
require 'db.php';
 
// Receive and sanitize GET parameters
$destination = isset($_GET['destination']) ? trim($_GET['destination']) : '';
$checkin = isset($_GET['checkin']) ? $_GET['checkin'] : '';
$checkout = isset($_GET['checkout']) ? $_GET['checkout'] : '';
$property_type = isset($_GET['property_type']) ? $_GET['property_type'] : '';
$amenities = isset($_GET['amenities']) && is_array($_GET['amenities']) ? $_GET['amenities'] : [];
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
 
// Build SQL query dynamically with filters
$sql = "SELECT * FROM properties WHERE 1=1 ";
$params = [];
 
// Destination filter (search location contains input)
if ($destination !== '') {
    $sql .= " AND location LIKE :destination ";
    $params[':destination'] = "%$destination%";
}
 
// Property type filter
if ($property_type !== '') {
    $sql .= " AND property_type = :ptype ";
    $params[':ptype'] = $property_type;
}
 
// Amenities filter (assuming amenities stored as CSV string like "WiFi,Pool,Kitchen")
// We'll check if all selected amenities exist in amenities string using LIKE and AND
foreach ($amenities as $idx => $amenity) {
    $sql .= " AND amenities LIKE :amenity$idx ";
    $params[":amenity$idx"] = "%$amenity%";
}
 
// Sorting
$orderBy = " ORDER BY rating DESC"; // default best-rated
 
if ($sort === 'price_asc') {
    $orderBy = " ORDER BY price_per_night ASC";
} elseif ($sort === 'price_desc') {
    $orderBy = " ORDER BY price_per_night DESC";
} elseif ($sort === 'rating_desc') {
    $orderBy = " ORDER BY rating DESC";
}
 
$sql .= $orderBy;
 
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Airbnb Clone - Listings</title>
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
    max-width:1200px;
    margin: 20px auto;
    padding: 0 15px;
  }
  h2 {
    margin-top: 0;
    color:#333;
  }
  .filters {
    margin-bottom: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: center;
  }
  .filters label {
    font-weight: bold;
    margin-right: 5px;
  }
  select, input[type="text"] {
    padding: 8px;
    border:1px solid #ccc;
    border-radius:4px;
  }
  button {
    background:#FF385C;
    color:#fff;
    border:none;
    padding: 8px 16px;
    border-radius:4px;
    cursor:pointer;
    font-weight:bold;
  }
  button:hover {
    background:#e82d51;
  }
  .listings {
    display: grid;
    grid-template-columns: repeat(auto-fill,minmax(250px,1fr));
    gap:20px;
  }
  .listing {
    background:#fff;
    border-radius:8px;
    overflow: hidden;
    box-shadow: 0 1px 5px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
  }
  .listing img {
    width: 100%;
    height: 160px;
    object-fit: cover;
  }
  .listing-info {
    padding: 15px;
    flex-grow: 1;
  }
  .listing-info h3 {
    margin: 0 0 10px;
    font-size: 18px;
    color: #222;
  }
  .listing-info p {
    margin: 5px 0;
    font-size: 14px;
    color: #555;
  }
  .listing-price {
    padding: 15px;
    border-top: 1px solid #eee;
    font-weight: bold;
    color: #FF385C;
  }
 
  /* Responsive */
  @media(max-width:600px) {
    .filters {
      flex-direction: column;
      align-items: stretch;
    }
  }
</style>
</head>
<body>
 
<header><a href="index.php" style="color:#fff; text-decoration:none;">&larr; Home</a> | Airbnb Clone - Listings</header>
 
<div class="container">
 
  <h2>Search Results</h2>
 
  <form method="GET" class="filters" action="listings.php">
    <input type="text" name="destination" placeholder="Destination" value="<?= htmlspecialchars($destination) ?>" required />
    <select name="property_type">
      <option value="">Property Type (Any)</option>
      <option value="Apartment" <?= $property_type == 'Apartment' ? 'selected' : '' ?>>Apartment</option>
      <option value="House" <?= $property_type == 'House' ? 'selected' : '' ?>>House</option>
      <option value="Villa" <?= $property_type == 'Villa' ? 'selected' : '' ?>>Villa</option>
      <option value="Cottage" <?= $property_type == 'Cottage' ? 'selected' : '' ?>>Cottage</option>
    </select>
    <select name="amenities[]" multiple size="4">
      <?php
      $allAmenities = ['WiFi', 'Kitchen', 'Air Conditioning', 'Pool'];
      foreach ($allAmenities as $a) {
          $selected = in_array($a, $amenities) ? 'selected' : '';
          echo "<option value=\"$a\" $selected>$a</option>";
      }
      ?>
    </select>
    <select name="sort">
      <option value="" <?= $sort == '' ? 'selected' : '' ?>>Sort by Best Rated</option>
      <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
      <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
      <option value="rating_desc" <?= $sort == 'rating_desc' ? 'selected' : '' ?>>Rating: High to Low</option>
    </select>
    <button type="submit">Filter</button>
  </form>
 
  <?php if (count($results) === 0): ?>
    <p>No properties found matching your criteria.</p>
  <?php else: ?>
  <div class="listings">
    <?php foreach ($results as $prop): ?>
      <a href="property.php?id=<?= htmlspecialchars($prop['id']) ?>" class="listing" style="text-decoration:none;">
        <img src="<?= htmlspecialchars($prop['image_url']) ?>" alt="<?= htmlspecialchars($prop['title']) ?>" />
        <div class="listing-info">
          <h3><?= htmlspecialchars($prop['title']) ?></h3>
          <p><?= htmlspecialchars($prop['location']) ?></p>
          <p>Rating: <?= htmlspecialchars($prop['rating']) ?>/5</p>
        </div>
        <div class="listing-price">$<?= htmlspecialchars(number_format($prop['price_per_night'], 2)) ?> / night</div>
      </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
 
</div>
 
</body>
</html>
 
