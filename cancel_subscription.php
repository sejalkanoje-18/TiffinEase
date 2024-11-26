<?php
// cancel_subscription.php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id']; // Get user ID from session

    // Update the subscription status in the database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "tiffin_service";

    $db = new mysqli($servername, $username, $password, $dbname);
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Update the subscription status
    $query = "UPDATE users SET subscription_status = 'canceled' WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $db->close();
}
?>