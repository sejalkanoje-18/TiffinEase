<?php
// Start the session
session_start();

// Include the database connection file
$servername = "localhost"; // Database server
$username = "root"; // Database username
$password = ""; // Database password
$dbname = "tiffin_service"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in and session variables are set
if (!isset($_SESSION["currentUser"])) {
    die("Error: User not logged in.");
}

$user = $_SESSION["currentUser"];

// Extract user details
$email = $user['email'];
$firstName = $user['firstName'];
$mobile = $user['mobile'];

// Retrieve POST data sent from the makePayment function
$vendor_id = isset($_POST['vendor_id']) ? intval($_POST['vendor_id']) : null;
$monthly_price = isset($_POST['monthly_price']) ? intval($_POST['monthly_price']) : 0;
$extra_amount = isset($_POST['extra_amount']) ? intval($_POST['extra_amount']) : 0;
$total_amount = isset($_POST['total_amount']) ? intval($_POST['total_amount']) : 0;
$transaction_id = isset($_POST['transaction_id']) ? $_POST['transaction_id'] : '';
$payment_date = isset($_POST['payment_date']) ? $_POST['payment_date'] : '';

if (!$vendor_id || !$transaction_id || !$payment_date) {
    echo "Error: Required payment details are missing.";
    exit();
}

// Prepare the SQL statement to insert payment data into the database
$sql = "INSERT INTO payments (vendor_id, monthly_price, extra_amount, total_amount, transaction_id, payment_date, email, firstName, mobile)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

// Prepare and execute the SQL statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiisssss", $vendor_id, $monthly_price, $extra_amount, $total_amount, $transaction_id, $payment_date, $email, $firstName, $mobile);


if ($stmt->execute()) {
    echo "success";
} else {
    echo "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>