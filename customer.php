<?php
session_start(); // Start the session to access session variables

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




// Check if user_id is set in session
if (isset($_SESSION['id'])) {
    $current_user_id = $_SESSION['id'];

    // Query to get the current user's first name
    $user_query = "SELECT firstName FROM users WHERE id = ?";
    $user_stmt = $db->prepare($user_query);
    $user_stmt->bind_param("i", $current_user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $current_user = $user_result->fetch_assoc();
    $user_stmt->close();
}


// Check if user_id is provided in the URL
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']); // Get user_id and convert to integer
    $_SESSION['selected_vendor'] = $user_id; // Save selected vendor to session
} elseif (isset($_SESSION['selected_vendor'])) {
    $user_id = $_SESSION['selected_vendor']; // Retrieve user_id from session if set
} else {
}

// Query to get the details of the specific vendor based on user_id
$query = "SELECT users.firstName AS vendor_name, menus.monthly_price, menus.specialtys, menus.delivery_time, menus.item_name, menus.quantity, menus.price
          FROM menus
          JOIN users ON menus.user_id = users.id
          WHERE users.id = ?";

$query = "SELECT users.id AS user_id, users.firstName AS vendor_name, menus.monthly_price, menus.specialtys, menus.delivery_time, menus.item_name, menus.quantity, menus.price
          FROM menus
          JOIN users ON menus.user_id = users.id
          WHERE users.id = ?";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id); // Bind the user_id parameter
$stmt->execute();
$result = $stmt->get_result();
$vendor = $result->fetch_assoc();
$stmt->close();


// Query to fetch menu items for the selected vendor
$menu_query = "SELECT item_name, quantity, price FROM menus WHERE user_id = ?";
$menu_stmt = $db->prepare($menu_query);
$menu_stmt->bind_param("i", $user_id); // Bind the user_id parameter
$menu_stmt->execute();
$menu_result = $menu_stmt->get_result();



// Query to fetch the payment history for the selected vendor
$payment_query = "SELECT transaction_id, payment_date, firstName, mobile, status FROM payments WHERE vendor_id = ?";
$payment_stmt = $db->prepare($payment_query);
$payment_stmt->bind_param("i", $user_id); // Bind the user_id parameter (which corresponds to vendor_id)
$payment_stmt->execute();
$payment_result = $payment_stmt->get_result();


?>

<?php
// Retrieve the extra amount from the URL, if provided
$extra_amount = isset($_GET['extra_amount']) ? intval($_GET['extra_amount']) : 0;

// You can now use $extra_amount in the payment calculations or display it as needed
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - TiffinEase</title>
    <link rel="stylesheet" href="css/customer.css">
</head>

<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <h1>TiffinEase</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="customer-payment.html">Payment</a></li>
                <li><a href="customer-profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li> <!-- Link to PHP logout script -->
            </ul>
        </nav>
    </header>

    <!-- Main Section -->
    <section class="dashboard">
        <div class="welcome-message">
            <h2 id="welcome-customer-name">Welcome,
                <?php echo isset($current_user['firstName']) ? htmlspecialchars($current_user['firstName']) : 'User'; ?>!
            </h2>
            <p>We are glad to have you back. Check out the latest tiffin options below.</p>
        </div>

        <!-- Menu Section -->
        <section class="today-menu">
            <h2>Extra Item Menu</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Extra Charges</th>
                        <th>Quantity</th>
                        <th>Add</th>
                    </tr>
                </thead>
                <tbody>

                    <?php while ($menu_item = $menu_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($menu_item['item_name']); ?></td>
                            <td>Rs. <?php echo htmlspecialchars($menu_item['price']); ?> => of every single thing</td>
                            <td>
                                <div class="quantity-controls">
                                    <button onclick="decreaseQuantity(this)">-</button>
                                    <input type="number" value="<?php echo htmlspecialchars($menu_item['quantity']); ?>"
                                        min="1" readonly>
                                    <button onclick="increaseQuantity(this)">+</button>
                                </div>
                            </td>
                            <td><button class="btn-add"
                                    onclick="addMenuItemPrice(<?php echo htmlspecialchars($menu_item['price']); ?>)">Add</button>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <!-- Subscription Section -->
        <section class="subscriptions">
            <h2>Your Subscriptions</h2>
            <div class="subscription-overview">
                <h3>Current Subscription Plan</h3>
                <div class="plan-detail">
                    <?php if ($vendor): ?>
                        <p><strong><?php echo htmlspecialchars($vendor['vendor_name']); ?> Tiffin Vendor</strong></p>
                        <p><strong>Specialty:</strong> <?php echo htmlspecialchars($vendor['specialtys']); ?></p>
                        <p><strong>Price:</strong> Rs. <?php echo htmlspecialchars($vendor['monthly_price']); ?>/month</p>
                        <p><strong>Delivery Time:</strong> <?php echo htmlspecialchars($vendor['delivery_time']); ?></p>
                    <?php else: ?>
                        <p>Subscription Plan not selected.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="manage-subscription">
                <h3>Manage Your Subscription</h3>
                <div class="subscription-card">
                    <h4>Cancel Subscription</h4>
                    <button class="btn-cancel" onclick="cancelSubscription()">Cancel Now</button>
                </div>
                <div class="subscription-card">
                    <h4>Extend Subscription</h4>
                    <?php if ($vendor): ?>
                        <p class="monthly-fee"> Rs. <?php echo htmlspecialchars($vendor['monthly_price']); ?>/month</p>
                    <?php else: ?>
                        <p>Vendor details not available.</p>
                    <?php endif; ?>
                    <button class="btn-extend"
                        onclick="extendSubscription(<?php echo htmlspecialchars($vendor['monthly_price']); ?>)">Extend
                        for Another
                        Month</button>
                </div>
                <div class="subscription-card">
                    <h4>Explore Other Tiffin Vendors</h4>
                    <button class="btn-explore" onclick="window.location.href='vendors.php';">See Vendors</button>
                </div>
                <div class="subscription-card">
                    <h4>Bill & Payment Options</h4>
                    <h5 id="total-monthly-price">Total Payment Price: Rs.
                        <?php echo htmlspecialchars($vendor['monthly_price']); ?>
                    </h5>

                    <button class="btn-payment"
                        onclick="proceedToPayment(<?php echo htmlspecialchars($vendor['user_id']); ?>, <?php echo htmlspecialchars($vendor['monthly_price']); ?>)">
                        Make Payment
                    </button>


                </div>
            </div>
        </section>

        <!-- Order History -->
        <div class="order-history">
            <h3>Your Order History</h3>
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Payment Date</th>
                        <th>Name</th>
                        <th>Mobile No.</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($payment = $payment_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($payment['transaction_id']); ?></td>
                            <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>

                            <td><?php echo htmlspecialchars($payment['firstName']); ?></td>
                            <td><?php echo htmlspecialchars($payment['mobile']); ?></td>

                            <td><?php echo htmlspecialchars($payment['status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <h2>TiffinEase</h2>
            <p>Bringing homemade meals to your doorstep, every day.</p>
        </div>
        <p>&copy; 2024 TiffinEase. All Rights Reserved.</p>
    </footer>

    <!-- <script src="js/customer.js"></script> -->

    <script>
        // JavaScript functions to increase and decrease quantity
        function increaseQuantity(button) {
            const input = button.previousElementSibling;
            input.value = parseInt(input.value) + 1;
        }

        function decreaseQuantity(button) {
            const input = button.nextElementSibling;
            if (parseInt(input.value) > 1) { // Ensure quantity doesn’t go below 1
                input.value = parseInt(input.value) - 1;
            }
        }



        // Initial monthly price from PHP
        let totalMonthlyPrice = <?php echo htmlspecialchars($vendor['monthly_price']); ?>;
        let extraAmount = 0; // Variable to track extra amount added

        // Function to update the displayed monthly price
        function updateMonthlyPrice() {
            document.getElementById('total-monthly-price').innerText = 'Total Monthly Price: Rs. ' + (totalMonthlyPrice + extraAmount);
        }

        // Function to handle adding menu item price to total extra amount
        function addMenuItemPrice(price) {
            extraAmount += price;
            updateMonthlyPrice();
        }

        // Function to handle extending the subscription by adding vendor monthly price
        function extendSubscription(price) {
            totalMonthlyPrice += price;
            updateMonthlyPrice();
        }

        // Function to navigate to the payment page with total extra amount included
        // Function to navigate to the payment page with total extra amount included
        function proceedToPayment(vendor_id, monthly_price) {
            // Get the user ID from PHP and pass it along to the payment page
            const userId = <?php echo json_encode($user_id); ?>; // Encode the user_id in JSON format for JavaScript

            // Navigate to the payment page, including user_id, extraAmount, and monthly_price in the URL
            window.location.href = 'customer-payment.php?vendor_id=' + vendor_id + '&monthly_price=' + monthly_price + '&extra_amount=' + extraAmount + '&user_id=' + userId;
        }




    </script>

</body>

</html>

<?php
$menu_stmt->close();
$db->close();
?>