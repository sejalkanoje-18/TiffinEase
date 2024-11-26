<?php
// Start the session if it hasn't been started
session_start();

// Include database connection
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

if (!isset($_SESSION["currentUser"])) {
    header("Location: signin.html");
    exit();
}


$user = $_SESSION["currentUser"];
$email = $user['email'];
$firstName = $user['firstName'];
$mobile = $user['mobile'];



// Check if vendor ID and monthly price are provided in the URL
if (isset($_GET['vendor_id']) && isset($_GET['monthly_price'])) {
    $vendor_id = intval($_GET['vendor_id']);
    $monthly_price = intval($_GET['monthly_price']);
} else {
    die("Error: Vendor ID or monthly price is missing.");
}

// Retrieve the extra amount from the URL, if provided
$extra_amount = isset($_GET['extra_amount']) ? intval($_GET['extra_amount']) : 0;

// Calculate the total amount
$total_amount = $monthly_price + $extra_amount;
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
        <h2>Payment Options and Billing Summary</h2>

        <!-- Billing Summary Section -->
        <div class="bill-summary">
            <h3>Billing Summary</h3>

            <div class="bill-item">
                <p>Vendor ID:</p>
                <p><?php echo htmlspecialchars($vendor_id); ?></p>
            </div>

            <div class="bill-item total">
                <p>Monthly Subscription Price</p>
                <p>Rs. <?php echo htmlspecialchars($monthly_price); ?></p>
            </div>

            <div class="bill-item">
                <p>Extra Charges for Added Items: (e.g., additional rotis)</p>
                <p>Rs. <?php echo htmlspecialchars($extra_amount); ?></p>
            </div>


            <div class="bill-item total">
                <p>Total</p>
                <p>Rs. <?php echo htmlspecialchars($total_amount); ?></p>
            </div>

            <button onclick="showPaymentOptions()" class="btn-bill-payment">Proceed to Payment</button>
        </div>

        <!-- Payment Options Section -->
        <div class="payment-options" id="payment-options" style="display: none;">
            <h2>Payment Options</h2>

            <div class="add-payment-method">
                <h3>Add a New Payment Method</h3>
                <form onsubmit="event.preventDefault(); addNewPaymentMethod();">
                    <label for="payment-type">Payment Type:</label>
                    <select id="payment-type" name="payment-type" required>
                        <option value="credit-card">Credit Card</option>
                        <option value="debit-card">Debit Card</option>
                        <option value="paypal">PayPal</option>
                    </select>

                    <label for="card-number">Card Number:</label>
                    <input type="text" id="card-number" name="card-number" required
                        placeholder="Enter your card number">

                    <label for="expiry-date">Expiry Date:</label>
                    <input type="month" id="expiry-date" name="expiry-date" required>

                    <label for="cvv">CVV:</label>
                    <input type="password" id="cvv" name="cvv" required placeholder="Enter CVV">

                </form>
            </div>

            <div class="proceed-payment">
                <h3>Proceed to Payment</h3>
                <button onclick="makePayment()" class="btn-make-payment">Pay Now</button>
            </div>
        </div>
    </section>

    <script src="js/customer-payment.js"></script>

    <script>
        function makePayment() {
            const vendor_id = <?php echo json_encode($vendor_id); ?>;
            const monthly_price = <?php echo json_encode($monthly_price); ?>;
            const extra_amount = <?php echo json_encode($extra_amount); ?>;
            const total_amount = <?php echo json_encode($total_amount); ?>;

            // Fetch user data from PHP session
            const email = <?php echo json_encode($email); ?>;
            const firstName = <?php echo json_encode($firstName); ?>;
            const mobile = <?php echo json_encode($mobile); ?>;


            // Generate a unique transaction ID
            const transaction_id = 'TXN' + Date.now() + Math.floor(Math.random() * 1000);

            // Get current date and time
            const payment_date = new Date().toISOString().slice(0, 19).replace('T', ' ');

            // Make an AJAX request to insert the payment record
            fetch('insert_payment_record.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    vendor_id: vendor_id,
                    monthly_price: monthly_price,
                    extra_amount: extra_amount,
                    total_amount: total_amount,
                    transaction_id: transaction_id,
                    payment_date: payment_date,
                    email: email,
                    firstName: firstName,
                    mobile: mobile
                }),
            })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === "success") { // Ensure exact match
                        const urlParams = new URLSearchParams({
                            vendor_id: vendor_id,
                            monthly_price: monthly_price,
                            extra_amount: extra_amount,
                            total_amount: total_amount,
                            transaction_id: transaction_id,
                            payment_date: payment_date
                        });
                        window.location.href = 'payment-receipt.php?' + urlParams.toString();
                    } else {
                        alert("Error: Unable to process the payment. " + data); // Print error from PHP
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred during the payment process.");
                });
        }

    </script>
</body>

</html>