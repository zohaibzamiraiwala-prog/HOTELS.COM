<?php
include 'db.php';
 
$hotel_id = isset($_GET['hotel_id']) ? (int)$_GET['hotel_id'] : 0;
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';
 
if ($hotel_id <= 0) {
    die("Invalid hotel ID.");
}
 
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = :id");
$stmt->execute([':id' => $hotel_id]);
$hotel = $stmt->fetch(PDO::FETCH_ASSOC);
 
if (!$hotel) {
    die("Hotel not found.");
}
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guest_name = isset($_POST['guest_name']) ? $_POST['guest_name'] : '';
 
    if (!empty($guest_name) && !empty($check_in) && !empty($check_out)) {
        $stmt_book = $pdo->prepare("INSERT INTO bookings (hotel_id, guest_name, check_in, check_out) VALUES (:hotel_id, :guest_name, :check_in, :check_out)");
        $stmt_book->execute([
            ':hotel_id' => $hotel_id,
            ':guest_name' => $guest_name,
            ':check_in' => $check_in,
            ':check_out' => $check_out
        ]);
        echo "<p>Booking confirmed for " . htmlspecialchars($guest_name) . " at " . htmlspecialchars($hotel['name']) . "!</p>";
        echo "<script>setTimeout(() => { location.href = 'index.php'; }, 3000);</script>";
        exit;
    } else {
        echo "<p>Please fill in all details.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Hotel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #f5f7fa, #c3cfe2); color: #333; }
        header { background: #007bff; color: white; padding: 20px; text-align: center; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .booking-form { max-width: 600px; margin: 20px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .booking-form img { width: 100%; height: 300px; object-fit: cover; border-radius: 10px; margin-bottom: 20px; }
        .booking-form h2 { color: #333; }
        .booking-form p { margin: 10px 0; color: #666; }
        .booking-form form { display: flex; flex-direction: column; }
        .booking-form input { padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        .booking-form button { padding: 10px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; transition: background 0.3s; }
        .booking-form button:hover { background: #218838; }
        .rating { color: #ffc107; font-weight: bold; }
        footer { background: #007bff; color: white; text-align: center; padding: 10px; margin-top: 40px; }
        @media (max-width: 768px) { .booking-form { padding: 10px; } }
    </style>
</head>
<body>
    <header>
        <h1>Book <?= htmlspecialchars($hotel['name']) ?></h1>
    </header>
    <div class="booking-form">
        <img src="<?= htmlspecialchars($hotel['image']) ?>" alt="<?= htmlspecialchars($hotel['name']) ?>">
        <h2><?= htmlspecialchars($hotel['name']) ?></h2>
        <p>Location: <?= htmlspecialchars($hotel['location']) ?></p>
        <p>Description: <?= htmlspecialchars($hotel['description']) ?></p>
        <p>Amenities: <?= htmlspecialchars($hotel['amenities']) ?></p>
        <p class="rating">Rating: <?= htmlspecialchars($hotel['rating']) ?> ★</p>
        <p>Price: $<?= htmlspecialchars($hotel['price']) ?> / night</p>
        <p>Dates: <?= htmlspecialchars($check_in) ?> to <?= htmlspecialchars($check_out) ?></p>
        <form method="POST">
            <input type="text" name="guest_name" placeholder="Your Name" required>
            <button type="submit">Confirm Booking</button>
        </form>
    </div>
    <footer>
        <p>&copy; 2025 Hotel Booking Platform</p>
    </footer>
</body>
</html>
