<?php
require 'db.php';
 
// Fetch featured properties (e.g., top 4 properties ordered by rating or any logic)
$stmt = $pdo->prepare("SELECT * FROM properties ORDER BY rating DESC LIMIT 4");
$stmt->execute();
$featured = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Airbnb Clone - Home</title>
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
  .search-bar {
    background:#fff;
    padding:20px;
    border-radius:8px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    align-items: center;
  }
  .search-bar input, .search-bar select {
    padding:10px;
    border:1px solid #ccc;
    border-radius:4px;
    flex-grow: 1;
    min-width: 150px;
  }
  .search-bar button {
    background:#FF385C;
    color:#fff;
    border:none;
    padding: 12px 20px;
    border-radius:4px;
    cursor:pointer;
    font-weight:bold;
  }
  .search-bar button:hover {
    background:#e82d51;
  }
  h2 {
    margin-top:40px;
    margin-bottom:20px;
    color:#333;
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
    .search-bar {
      flex-direction: column;
    }
  }
</style>
</head>
<body>
 
<header>Airbnb Clone</header>
 
<div class="container">
 
  <form class="search-bar" action="listings.php" method="GET">
    <input type="text" name="destination" placeholder="Where are you going?" required />
    <input type="date" name="checkin" placeholder="Check-in" required />
    <input type="date" name="checkout" placeholder="Check-out" required />
    <select name="property_type">
      <option value="">Property Type (Any)</option>
      <option value="Apartment">Apartment</option>
      <option value="House">House</option>
      <option value="Villa">Villa</option>
      <option value="Cottage">Cottage</option>
    </select>
    <select name="amenities[]" multiple>
      <option value="WiFi">WiFi</option>
      <option value="Kitchen">Kitchen</option>
      <option value="Air Conditioning">Air Conditioning</option>
      <option value="Pool">Pool</option>
    </select>
    <button type="submit">Search</button>
  </form>
 
  <h2>Featured Listings</h2>
  <div class="listings">
    <?php foreach($featured as $prop): ?>
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
 
</div>
 
</body>
</html>
 
