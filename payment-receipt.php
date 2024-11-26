<?php
session_start();

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

// Retrieve the parameters from the URL
$vendor_id = isset($_GET['vendor_id']) ? intval($_GET['vendor_id']) : 0;
$monthly_price = isset($_GET['monthly_price']) ? intval($_GET['monthly_price']) : 0;
$extra_amount = isset($_GET['extra_amount']) ? intval($_GET['extra_amount']) : 0;
$total_amount = isset($_GET['total_amount']) ? intval($_GET['total_amount']) : 0;
$transaction_id = isset($_GET['transaction_id']) ? $_GET['transaction_id'] : ''; // Treat as string to ensure correct output
$payment_date = isset($_GET['payment_date']) ? $_GET['payment_date'] : ''; // Date should be string format

// Payment success message
$payment_status = "Payment Successful";

// Check if the required data is available in the URL for debugging
if (empty($transaction_id) || empty($payment_date)) {
    die("Error: Missing transaction ID or payment date.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment and Billing</title>
    <link rel="stylesheet" href="css/customer-payment.css"> <!-- Link to your CSS file -->
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
                <li><a href="customer.php">My Order</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <!-- Payment and Billing Section -->
    <section class="payment-billing">

        <h2>Payment Receipt</h2>

        <!-- Billing Summary Section -->
        <div class="bill-summary">
            <h3>Payment Details</h3>

            <div class="bill-item">
                <p>Vendor ID:</p>
                <p><?php echo htmlspecialchars($vendor_id); ?></p>
            </div>

            <div class="bill-item">
                <p>Transaction ID:</p>
                <p><?php echo htmlspecialchars($transaction_id); ?></p>
            </div>

            <div class="bill-item">
                <p>Date & Time:</p>
                <p><?php echo htmlspecialchars($payment_date); ?></p>
            </div>

            <div class="bill-item total">
                <p>Monthly Subscription Price:</p>
                <p>Rs. <?php echo htmlspecialchars($monthly_price); ?></p>
            </div>

            <div class="bill-item">
                <p>Extra Charges:</p>
                <p>Rs. <?php echo htmlspecialchars($extra_amount); ?></p>
            </div>

            <div class="bill-item total">
                <p>Total Amount:</p>
                <p>Rs. <?php echo htmlspecialchars($total_amount); ?></p>
            </div>

            <button class="btn-bill-payment"><?php echo htmlspecialchars($payment_status); ?></button>
        </div>

        <section class="receipt-section">
            <h2></h2>

            <button onclick="window.print()" class="btn-print">Print Receipt</button>
        </section>

    </section>

</body>

</html>