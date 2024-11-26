<?php
session_start();
include 'php/db.php';

// Check if the user is logged in and is a vendor
if (!isset($_SESSION["currentUser"]) || $_SESSION["currentUser"]["role"] !== 'vendor') {
    die("Error: User not logged in.");
}

$user = $_SESSION["currentUser"];
$user_id = $user['id']; // Assuming 'id' is the primary key in 'users' table

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect POST data
    $monthlyPrice = $_POST['monthlyPrice'];
    $specialty = $_POST['specialty'];
    $deliveryTime = $_POST['deliveryTime'];
    $itemNames = $_POST['itemName'];
    $quantities = $_POST['quantity'];
    $prices = $_POST['price'];

    // Validate inputs (basic validation)
    if (empty($monthlyPrice) || empty($specialty) || empty($deliveryTime) || empty($itemNames) || empty($quantities) || empty($prices)) {
        die("Error: All fields are required.");
    }

    // Prepare the SQL statement to insert menu items
    $stmt = $conn->prepare("INSERT INTO menus (user_id, monthly_price, specialtys, delivery_time, item_name, quantity, price) VALUES (?, ?, ?, ?, ?, ?, ?)");

    // Check if the statement was prepared successfully
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }


    // Bind parameters with correct types: 
// 'i' for integer, 'd' for double (float), 's' for string
    $stmt->bind_param("idssssi", $user_id, $monthlyPrice, $specialty, $deliveryTime, $itemName, $quantity, $price);

    // Loop through each item and execute the prepared statement
    $success = true;
    for ($i = 0; $i < count($itemNames); $i++) {
        // Make sure the itemName is properly trimmed and validated
        $itemName = trim($itemNames[$i]);
        $quantity = intval($quantities[$i]);
        $price = floatval($prices[$i]);

        // Execute the statement for each item
        if (!$stmt->execute()) {
            // Log the error and set success to false
            error_log("Database insert error: " . $stmt->error);
            $success = false;
            break;
        }
    }


    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Check if the insertion was successful
    if ($success) {
        // Redirect with success message
        header("Location: vendor-dashboard.php?message=Menu+submitted+successfully");
        exit();
    } else {
        die("Error: Failed to submit menu items.");
    }
}
?>