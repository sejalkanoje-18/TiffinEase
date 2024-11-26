<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TiffinEase</title>
    <link rel="stylesheet" href="css/admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<style>
    /* Style for the table */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        padding: 10px;
        font-family: Arial, sans-serif;
    }

    table th,
    table td {
        padding: 12px 15px;
        border: 1px solid #ddd;
        text-align: left;
    }

    table th {
        background-color: #4CAF50;
        color: white;
        font-weight: bold;
        text-transform: uppercase;
    }

    table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    table tbody tr:hover {
        background-color: #f1f1f1;
    }

    /* Add rounded corners and shadow */
    .table-container {
        overflow-x: auto;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }

    /* Hide the tables by default */
    #userTable,
    #userTable1,
    #ordersTable,
    #reportMessage {
        display: none;
    }
</style>

<body>

    <?php
    // Database configuration
    $host = 'localhost';  // Replace with your database host
    $dbname = 'tiffin_service';  // Replace with your database name
    $username = 'root';  // Replace with your database username
    $password = '';  // Replace with your database password
    
    try {
        // Establishing a database connection
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Query to count total users with the role 'Customer'
        $stmt = $pdo->prepare("SELECT COUNT(*) AS customerCount FROM users WHERE role = :role");
        $stmt->execute(['role' => 'customer']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $customerCount = $result['customerCount'];

        // Query to count total users with the role 'Vendor'
        $stmt = $pdo->prepare("SELECT COUNT(*) AS venderCount FROM users WHERE role = :role");
        $stmt->execute(['role' => 'vendor']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $venderCount = $result['venderCount'];

        // Query to fetch customer details
        $stmt = $pdo->prepare("SELECT * FROM users WHERE role = :role");
        $stmt->execute(['role' => 'customer']);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Query to fetch vendor details
        $stmt = $pdo->prepare("SELECT * FROM users WHERE role = :role");
        $stmt->execute(['role' => 'vendor']);
        $vender = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Query to count total orders in the 'payments' table
        $stmt = $pdo->prepare("SELECT COUNT(*) AS orderCount FROM payments");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $orderCount = $result['orderCount'];

        // Query to calculate the total revenue
        $stmt = $pdo->prepare("SELECT SUM(total_amount) AS totalRevenue FROM payments");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalRevenue = $result['totalRevenue'];


        // Fetch distinct users based on user_id from the menus table
        $stmt = $pdo->prepare("
    SELECT DISTINCT u.firstName, u.lastName, u.email, u.mobile, u.address, u.city, u.pincode, u.state 
    FROM users u
    INNER JOIN menus m ON u.id = m.user_id
");
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);



    } catch (PDOException $e) {
        $customerCount = "Error retrieving data";  // Display error message if database connection fails
        $venderCount = "Error retrieving data";  // Display error message if database connection fails
        $orderCount = "Error retrieving data";  // Display error message if database connection fails
        $totalRevenue = "Error retrieving data";  // Display error message if database connection fails
        $orders = false;  // Display error message if database query fails
    }
    ?>

    <!-- Header -->
    <header>
        <div class="logo">
            <h1>TiffinEase Admin</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="logout.php" id="logoutButton"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Dashboard Overview Section -->
    <section class="dashboard-overview">
        <div class="overview-cards">
            <div class="card">
                <h3>Users</h3>
                <p id="customerCount"><?php echo $customerCount; ?></p>
                <i class="fas fa-users"></i>
            </div>
            <div class="card">
                <h3>Vendors</h3>
                <p id="venderCount"><?php echo $venderCount; ?></p>
                <i class="fas fa-store"></i>
            </div>
            <div class="card">
                <h3>Orders</h3>
                <p id="orderCount"><?php echo $orderCount; ?></p>
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="card">
                <h3>Revenue</h3>
                <p id="revenue"><?php echo htmlspecialchars($totalRevenue); ?></p>
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
    </section>

    <!-- Admin Actions Section -->
    <section class="dashboard-actions">
        <div class="actions-container">
            <h2>Manage TiffinEase</h2>
            <button onclick="toggleTable('userTable')">View Users</button>
            <button onclick="toggleTable('userTable1')">View Vendors</button>
            <button onclick="toggleTable('ordersTable')">View Orders</button>
            <button onclick="showReportMessage()">View Reports</button>
        </div>

        <div class="table-container">
            <table id="userTable" class="display expandable-table">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>Pin Code</th>
                        <th>State</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($customers === false) {
                        echo "<tr><td colspan='8'>Error retrieving data</td></tr>";
                    } elseif (count($customers) > 0) {
                        foreach ($customers as $row) {
                            echo "<tr>";
                            echo '<td>' . htmlspecialchars($row['firstName']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['lastName']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['mobile']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['address']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['city']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['pincode']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['state']) . '</td>';
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No results found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="table-container">
            <table id="userTable1" class="display expandable-table">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>Pin Code</th>
                        <th>State</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($vender === false) {
                        echo "<tr><td colspan='8'>Error retrieving data</td></tr>";
                    } elseif (count($vender) > 0) {
                        foreach ($vender as $row) {
                            echo "<tr>";
                            echo '<td>' . htmlspecialchars($row['firstName']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['lastName']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['mobile']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['address']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['city']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['pincode']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['state']) . '</td>';
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>No results found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Orders Table -->
        <div class="table-container">
            <table id="ordersTable" class="display expandable-table">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Address</th>
                        <th>City</th>
                        <th>Pin Code</th>
                        <th>State</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($orders === false) {
                        echo "<tr><td colspan='8'>Error retrieving data</td></tr>";
                    } else {
                        foreach ($orders as $row) {
                            echo "<tr>";
                            echo '<td>' . htmlspecialchars($row['firstName']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['lastName']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['mobile']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['address']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['city']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['pincode']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['state']) . '</td>';
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Report message div -->
        <div id="reportMessage" class="table-container">
            <p>No Reports Found</p>
        </div>

    </section>

    <script>
        function toggleTable(tableId) {
            ['userTable', 'userTable1', 'ordersTable', 'reportMessage'].forEach(id => {
                document.getElementById(id).style.display = "none";
            });
            document.getElementById(tableId).style.display = "table";
        }

        function showReportMessage() {
            toggleTable('reportMessage');
        }
    </script>

    <script src="js/admin-dashboard.js"></script>
</body>

</html>