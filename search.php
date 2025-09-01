<?php
include 'db.php';
 
$location = isset($_GET['location']) ? $_GET['location'] : '';
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';
$price_min = isset($_GET['price_min']) ? (float)$_GET['price_min'] : 0;
$price_max = isset($_GET['price_max']) ? (float)$_GET['price_max'] : PHP_FLOAT_MAX;
$rating_min = isset($_GET['rating_min']) ? (float)$_GET['rating_min'] : 0;
$hotel_type = isset($_GET['hotel_type']) ? $_GET['hotel_type'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'rating_desc';
 
$where = "WHERE location LIKE :location";
$params = [':location' => "%$location%"];
 
if ($price_min > 0) {
    $where .= " AND price >= :price_min";
    $params[':price_min'] = $price_min;
}
if ($price_max < PHP_FLOAT_MAX) {
    $where .= " AND price <= :price_max";
    $params[':price_max'] = $price_max;
}
if ($rating_min > 0) {
    $where .= " AND rating >= :rating_min";
    $params[':rating_min'] = $rating_min;
}
if ($hotel_type) {
    $where .= " AND hotel_type = :hotel_type";
    $params[':hotel_type'] = $hotel_type;
}
 
$order = '';
switch ($sort) {
    case 'price_asc': $order = 'ORDER BY price ASC'; break;
    case 'price_desc': $order = 'ORDER BY price DESC'; break;
    case 'rating_desc': $order = 'ORDER BY rating DESC'; break;
}
 
$stmt = $pdo->prepare("SELECT * FROM hotels $where $order");
$stmt->execute($params);
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Listings</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #f5f7fa, #c3cfe2); color: #333; }
        header { background: #007bff; color: white; padding: 20px; text-align: center; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .filters { max-width: 1200px; margin: 20px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; flex-wrap: wrap; justify-content: space-around; }
        .filters input, .filters select, .filters button { padding: 10px; margin: 10px; border: 1px solid #ddd; border-radius: 5px; flex: 1; min-width: 150px; }
        .filters button { background: #28a745; color: white; border: none; cursor: pointer; transition: background 0.3s; }
        .filters button:hover { background: #218838; }
        .hotels-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; max-width: 1200px; margin: 20px auto; }
        .hotel-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; }
        .hotel-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
        .hotel-card img { width: 100%; height: 200px; object-fit: cover; }
        .hotel-info { padding: 15px; }
        .hotel-info h3 { margin: 0 0 10px; color: #333; }
        .hotel-info p { margin: 5px 0; color: #666; }
        .amenities { font-style: italic; }
        .rating { color: #ffc107; font-weight: bold; }
        .book-btn { display: block; margin: 10px auto; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; text-align: center; transition: background 0.3s; }
        .book-btn:hover { background: #0056b3; }
        footer { background: #007bff; color: white; text-align: center; padding: 10px; margin-top: 40px; }
        @media (max-width: 768px) { .filters { flex-direction: column; } .hotels-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <h1>Available Hotels in <?= htmlspecialchars($location) ?></h1>
        <p>Dates: <?= htmlspecialchars($check_in) ?> to <?= htmlspecialchars($check_out) ?></p>
    </header>
    <div class="filters">
        <form action="search.php" method="GET">
            <input type="hidden" name="location" value="<?= htmlspecialchars($location) ?>">
            <input type="hidden" name="check_in" value="<?= htmlspecialchars($check_in) ?>">
            <input type="hidden" name="check_out" value="<?= htmlspecialchars($check_out) ?>">
            <input type="number" name="price_min" placeholder="Min Price" value="<?= htmlspecialchars($price_min ?: '') ?>">
            <input type="number" name="price_max" placeholder="Max Price" value="<?= htmlspecialchars($price_max ?: '') ?>">
            <input type="number" step="0.1" name="rating_min" placeholder="Min Rating" value="<?= htmlspecialchars($rating_min ?: '') ?>">
            <select name="hotel_type">
                <option value="">Any Type</option>
                <option value="Luxury" <?= $hotel_type == 'Luxury' ? 'selected' : '' ?>>Luxury</option>
                <option value="Mid-range" <?= $hotel_type == 'Mid-range' ? 'selected' : '' ?>>Mid-range</option>
                <option value="Budget" <?= $hotel_type == 'Budget' ? 'selected' : '' ?>>Budget</option>
            </select>
            <select name="sort">
                <option value="rating_desc" <?= $sort == 'rating_desc' ? 'selected' : '' ?>>Best Rated</option>
                <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Price Low to High</option>
                <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Price High to Low</option>
            </select>
            <button type="submit">Apply Filters</button>
        </form>
    </div>
    <div class="hotels-grid">
        <?php if (empty($hotels)): ?>
            <p>No hotels found for this search.</p>
        <?php else: ?>
            <?php foreach ($hotels as $hotel): ?>
                <div class="hotel-card">
                    <img src="<?= htmlspecialchars($hotel['image']) ?>" alt="<?= htmlspecialchars($hotel['name']) ?>">
                    <div class="hotel-info">
                        <h3><?= htmlspecialchars($hotel['name']) ?></h3>
                        <p><?= htmlspecialchars($hotel['description']) ?></p>
                        <p class="amenities">Amenities: <?= htmlspecialchars($hotel['amenities']) ?></p>
                        <p class="rating">Rating: <?= htmlspecialchars($hotel['rating']) ?> ★</p>
                        <p>$<?= htmlspecialchars($hotel['price']) ?> / night</p>
                        <a href="book.php?hotel_id=<?= $hotel['id'] ?>&check_in=<?= urlencode($check_in) ?>&check_out=<?= urlencode($check_out) ?>" class="book-btn">Book Now</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <footer>
        <p>&copy; 2025 Hotel Booking Platform</p>
    </footer>
</body>
</html>
