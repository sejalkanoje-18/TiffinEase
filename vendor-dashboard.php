<?php
session_start();
include 'php/db.php';

// Check if the user is logged in and is a vendor
if (!isset($_SESSION["currentUser"]) || $_SESSION["currentUser"]["role"] !== 'vendor') {
    header("Location: signin.html");
    exit();
}

$user = $_SESSION["currentUser"];
$user_id = $user['id']; // Assuming 'id' is the primary key in 'users' table

// Fetch menu items for this vendor from the database
$menuItems = [];
$sql = "SELECT * FROM menus WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $menuItems[] = $row;
}
$stmt->close();


// Fetch orders for this vendor from the payments table
$orders = [];
$sqlOrders = "SELECT id, transaction_id, firstName, mobile, status FROM payments WHERE vendor_id = ?";
$stmtOrders = $conn->prepare($sqlOrders);
$stmtOrders->bind_param("i", $user_id);
$stmtOrders->execute();
$resultOrders = $stmtOrders->get_result();
while ($row = $resultOrders->fetch_assoc()) {
    $orders[] = $row;
}
$stmtOrders->close();


// Fetch monthly sales performance
$salesPerformance = [];
$sqlSales = "SELECT DATE_FORMAT(payment_date, '%Y-%m') AS month, COUNT(id) AS number_of_orders, SUM(total_amount) AS total_sales 
             FROM payments WHERE vendor_id = ? GROUP BY month ORDER BY month DESC";
$stmtSales = $conn->prepare($sqlSales);
$stmtSales->bind_param("i", $user_id);
$stmtSales->execute();
$resultSales = $stmtSales->get_result();
while ($row = $resultSales->fetch_assoc()) {
    $salesPerformance[] = $row;
}
$stmtSales->close();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiffin Vendor Dashboard - TiffinEase</title>
    <link rel="stylesheet" href="css/vendor-dashboard.css">

    <style>
        /* Styling for input boxes */
        #addMenuForm input[type="text"],
        #addMenuForm input[type="number"] {
            width: 200px;
            padding: 10px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        /* Hover and focus effect for input boxes */
        #addMenuForm input[type="text"]:focus,
        #addMenuForm input[type="number"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
        }

        /* Styling for buttons */
        #addMenuForm button {
            padding: 10px 15px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
    </style>
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
                <li><a href="vendor-profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero-vendor">
        <h1 id="welcome-message" style="color: black;">Welcome, <?php echo htmlspecialchars($user['firstName']); ?>!
        </h1>
        <p style="color: black;">Manage your menu, orders, and earnings efficiently.</p>
    </section>

    <!-- Manage Menus Section -->
    <section class="manage-menus">
        <h2>Add Your Menus</h2>

        <!-- Add Menu Item Form -->
        <form id="addMenuForm" action="submit_menu.php" method="POST">
            <label for="monthlyPrice">Monthly Price:</label>
            <input type="number" name="monthlyPrice" id="monthlyPrice" required step="0.01">
            <label for="specialty">Specialty:</label>
            <input type="text" name="specialty" id="specialty" required>
            <label for="deliveryTime">Delivery Time:</label>
            <input type="text" name="deliveryTime" id="deliveryTime" required><br>

            <div id="itemContainer">
                <h3>Menu Items</h3>
                <div class="item">
                    <label>Item Name:</label>
                    <input type="text" name="itemName[]" required>
                    <label>Quantity:</label>
                    <input type="number" name="quantity[]" required>
                    <label>Price:</label>
                    <input type="number" name="price[]" required step="0.01">
                    <button type="button" class="remove-item" onclick="removeItem(this)">Remove</button>
                </div>
            </div>

            <button type="button" onclick="addItem()">Add Another Item</button><br><br>
            <button type="submit">Submit Menu</button><br><br><br><br>
        </form>

        <!-- Menu Table -->
        <table id="menuTable">
            <thead>
                <tr>
                    <th>Monthly Price</th>
                    <th>Specialty</th>
                    <th>Delivery Time</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <!-- <th>Actions</th> -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menuItems as $menuItem): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($menuItem['monthly_price']); ?></td>
                        <td><?php echo htmlspecialchars($menuItem['specialtys']); ?></td>
                        <td><?php echo htmlspecialchars($menuItem['delivery_time']); ?></td>
                        <td><?php echo htmlspecialchars($menuItem['item_name']); ?></td>
                        <td><?php echo htmlspecialchars($menuItem['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($menuItem['price']); ?></td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </section>

    <!-- Manage Orders Section -->
    <section class="manage-orders">
        <h2>Your Orders</h2>
        <table id="ordersTable">
            <thead>
                <tr>

                    <th>ID</th>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Customer Mobile</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['transaction_id']); ?></td>
                        <td><?php echo htmlspecialchars($order['firstName']); ?></td>
                        <td><?php echo htmlspecialchars($order['mobile']); ?></td>
                        <td>
                            <input type="text" id="status_<?php echo $order['id']; ?>"
                                value="<?php echo htmlspecialchars($order['status']); ?>" />
                        </td>
                        <td>
                            <button type="button" onclick="updateOrderStatus(<?php echo $order['id']; ?>)">Update</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>


    <!-- Sales Performance Section -->
    <section class="vendor-payments">
        <h2>Your Earnings & Sales Performance</h2>
        <table id="sales-performance-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Number of Orders</th>
                    <th>Total Sales</th>
                    <!-- <th>Average Order Value</th> -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salesPerformance as $performance): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($performance['month']); ?></td>
                        <td><?php echo htmlspecialchars($performance['number_of_orders']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($performance['total_sales'], 2)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <h2>TiffinEase</h2>
            <p>Bringing homemade meals to your doorstep, every day.</p>
        </div>
        <p>&copy; 2024 TiffinEase. All Rights Reserved.</p>
    </footer>

    <script>
        function addItem() {
            const container = document.getElementById('itemContainer');
            const newItem = document.createElement('div');
            newItem.classList.add('item');
            newItem.innerHTML = `
                <label>Item Name:</label>
                <input type="text" name="itemName[]" required>
                <label>Quantity:</label>
                <input type="number" name="quantity[]" required>
                <label>Price:</label>
                <input type="number" name="price[]" required step="0.01">
                <button type="button" class="remove-item" onclick="removeItem(this)">Remove</button>
            `;
            container.appendChild(newItem);
        }

        function removeItem(button) {
            const item = button.parentElement;
            item.remove();
        }


        function updateOrderStatus(id) {
            // Get the updated status value from the input field
            var updatedStatus = document.getElementById('status_' + id).value;

            // Create a new XMLHttpRequest object
            var xhr = new XMLHttpRequest();

            // Set up the request
            xhr.open('POST', 'update_status.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            // Set up a callback function to handle the response
            xhr.onload = function () {
                if (xhr.status === 200) {
                    alert('Order status updated successfully!');
                    // Optionally, you can reload the page or update the status without reloading
                } else {
                    alert('Failed to update order status.');
                }
            };

            // Send the request with the transaction ID and updated status as parameters
            xhr.send('id=' + id + '&status=' + encodeURIComponent(updatedStatus));
        }
    </script>
</body>

</html>