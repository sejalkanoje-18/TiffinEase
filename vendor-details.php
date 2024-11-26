<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tiffin_service";

// Create connection
$db = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Check if user_id is provided in the URL
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']); // Get user_id and convert to integer

    // Query to get the details of the specific vendor based on user_id
    $query = "SELECT users.firstName AS vendor_name, menus.monthly_price, menus.specialtys, menus.delivery_time
              FROM menus
              JOIN users ON menus.user_id = users.id
              WHERE users.id = ?";

    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $user_id); // Bind the user_id parameter
    $stmt->execute();
    $result = $stmt->get_result();
    $vendor = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "No vendor selected.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Details</title>
    <link rel="stylesheet" href="css/vendors.css"> <!-- Link to your CSS file -->
</head>

<body>
    <header>
        <div class="logo">
            <h1>TiffinEase</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="customer-profile.php">Profile</a></li>
                <li><a href="customer.html">My Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="vendor-details">
        <?php if ($vendor): ?>
            <h2><?php echo htmlspecialchars($vendor['vendor_name']); ?> - Tiffin Vendor</h2>
            <p><strong>Specialty:</strong> <?php echo htmlspecialchars($vendor['specialtys']); ?></p>
            <p><strong>Price:</strong> Rs. <?php echo htmlspecialchars($vendor['monthly_price']); ?>/month</p>
            <p><strong>Delivery Time:</strong> <?php echo htmlspecialchars($vendor['delivery_time']); ?></p>
            <p><strong>Rating:</strong> ⭐⭐⭐⭐ (4.5/5)</p>
            <a href="order.php?user_id=<?php echo urlencode($user_id); ?>"><button>Order Now</button></a>
        <?php else: ?>
            <p>Vendor details not available.</p>
        <?php endif; ?>
    </section>

</body>

</html>