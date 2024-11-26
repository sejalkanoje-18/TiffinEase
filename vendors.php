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


// Retrieve vendor details from the database by joining users and menus tables
$query = "SELECT users.id AS user_id, users.firstName AS vendor_name, users.address, users.city, menus.monthly_price, menus.specialtys, menus.delivery_time
          FROM menus
          JOIN users ON menus.user_id = users.id";
$result = $db->query($query);

// Group vendors by user_id
$vendors = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vendors[$row['user_id']] = $row; // Store each vendor by user_id
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>See Vendors</title>
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
                <li><a href="customer.php">My Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

    </header>

    <section class="vendor-list">
        <h2>Choose Your Tiffin Vendor</h2>
        <div class="vendor-container">
            <?php
            if (!empty($vendors)) {
                foreach ($vendors as $vendor) {
                    echo '<div class="vendor-card">';
                    echo '<img src="Frontend/images/vendor1.jpg" alt="Vendor Photo" class="vendor-photo">';
                    echo '<h3>' . htmlspecialchars($vendor['vendor_name']) . ' Tiffin Vendor</h3>';
                    echo '<p><strong>Specialty:</strong> ' . htmlspecialchars($vendor['specialtys']) . '</p>';
                    echo '<p><strong>Price:</strong> Rs. ' . htmlspecialchars($vendor['monthly_price']) . '/month</p>';
                    echo '<p><strong>Rating:</strong> ⭐⭐⭐⭐ (4.5/5)</p>';
                    echo '<p><strong>Address:</strong> ' . htmlspecialchars($vendor['address']) . '</p>';
                    echo '<p><strong>City:</strong> ' . htmlspecialchars($vendor['city']) . '</p>';
                    echo '<p><strong>Delivery Time:</strong> ' . htmlspecialchars($vendor['delivery_time']) . '</p>';
                    echo '<a href="customer.php?user_id=' . urlencode($vendor['user_id']) . '"><button>Select Vendor</button></a>';
                    echo '</div>';
                }
            } else {
                echo "<p>No vendors available at this time.</p>";
            }
            ?>
        </div>
    </section>

</body>

</html>