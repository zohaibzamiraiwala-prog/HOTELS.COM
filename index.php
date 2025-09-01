<?php
include 'db.php';
 
// Fetch featured hotels (random 3)
$stmt_featured = $pdo->query("SELECT * FROM hotels ORDER BY RAND() LIMIT 3");
$featured = $stmt_featured->fetchAll(PDO::FETCH_ASSOC);
 
// Fetch top-rated hotels (top 3 by rating)
$stmt_top = $pdo->query("SELECT * FROM hotels ORDER BY rating DESC LIMIT 3");
$top_rated = $stmt_top->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking - Home</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #f5f7fa, #c3cfe2); color: #333; }
        header { background: #007bff; color: white; padding: 20px; text-align: center; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .search-bar { max-width: 800px; margin: 20px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .search-bar form { display: flex; flex-wrap: wrap; justify-content: space-around; }
        .search-bar input, .search-bar button { padding: 10px; margin: 10px; border: 1px solid #ddd; border-radius: 5px; flex: 1; min-width: 200px; }
        .search-bar button { background: #28a745; color: white; border: none; cursor: pointer; transition: background 0.3s; }
        .search-bar button:hover { background: #218838; }
        .section { padding: 40px 20px; }
        .section h2 { text-align: center; margin-bottom: 20px; color: #007bff; }
        .hotels-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; max-width: 1200px; margin: 0 auto; }
        .hotel-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; }
        .hotel-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.15); }
        .hotel-card img { width: 100%; height: 200px; object-fit: cover; }
        .hotel-info { padding: 15px; }
        .hotel-info h3 { margin: 0 0 10px; color: #333; }
        .hotel-info p { margin: 5px 0; color: #666; }
        .rating { color: #ffc107; font-weight: bold; }
        footer { background: #007bff; color: white; text-align: center; padding: 10px; margin-top: 40px; }
        @media (max-width: 768px) { .search-bar form { flex-direction: column; } .hotels-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <h1>Hotel Booking Platform</h1>
        <p>Find and book the best hotels worldwide</p>
    </header>
    <div class="search-bar">
        <form action="search.php" method="GET">
            <input type="text" name="location" placeholder="Destination (e.g., New York)" required>
            <input type="date" name="check_in" required>
            <input type="date" name="check_out" required>
            <button type="submit">Search Hotels</button>
        </form>
    </div>
    <div class="section">
        <h2>Featured Hotels</h2>
        <div class="hotels-grid">
            <?php foreach ($featured as $hotel): ?>
                <div class="hotel-card">
                    <img src="<?= htmlspecialchars($hotel['image']) ?>" alt="<?= htmlspecialchars($hotel['name']) ?>">
                    <div class="hotel-info">
                        <h3><?= htmlspecialchars($hotel['name']) ?></h3>
                        <p><?= htmlspecialchars($hotel['location']) ?></p>
                        <p class="rating">Rating: <?= htmlspecialchars($hotel['rating']) ?> ★</p>
                        <p>$<?= htmlspecialchars($hotel['price']) ?> / night</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="section">
        <h2>Top-Rated Stays</h2>
        <div class="hotels-grid">
            <?php foreach ($top_rated as $hotel): ?>
                <div class="hotel-card">
                    <img src="<?= htmlspecialchars($hotel['image']) ?>" alt="<?= htmlspecialchars($hotel['name']) ?>">
                    <div class="hotel-info">
                        <h3><?= htmlspecialchars($hotel['name']) ?></h3>
                        <p><?= htmlspecialchars($hotel['location']) ?></p>
                        <p class="rating">Rating: <?= htmlspecialchars($hotel['rating']) ?> ★</p>
                        <p>$<?= htmlspecialchars($hotel['price']) ?> / night</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <footer>
        <p>&copy; 2025 Hotel Booking Platform</p>
    </footer>
</body>
</html>
