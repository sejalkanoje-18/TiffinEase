<?php
session_start();
include 'php/db.php';

// Get data from the request
$data = json_decode(file_get_contents("php://input"), true);
$itemName = $data['itemName'];
$quantity = $data['quantity'];
$price = $data['price'];
$monthlyPrice = $data['monthlyPrice'];

// Insert data into the database
$sql = "INSERT INTO menu (item_name, quantity, price, monthly_price) VALUES ('$itemName', '$quantity', '$price', '$monthlyPrice')";

$response = [];
if ($conn->query($sql) === TRUE) {
    $response['success'] = true;
} else {
    $response['success'] = false;
    $response['error'] = $conn->error;
}

// Close the connection
$conn->close();

// Send response back to the client
header('Content-Type: application/json');
echo json_encode($response);
?>