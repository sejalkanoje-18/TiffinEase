<?php
session_start();

// Include database connection
include 'php/db.php';

if (!isset($_SESSION["currentUser"])) {
    header("Location: signin.html");
    exit();
}

$user = $_SESSION["currentUser"];

// Check if the form is submitted via AJAX
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["updateProfile"])) {
    // Check if the database connection is successful
    if (!$conn) {
        die("Database connection error: " . mysqli_connect_error());
    }

    // Collect POST data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];
    $userId = $user['id']; // Assuming there is an 'id' column for unique user identification

    // Prepare the SQL update statement
    $stmt = $conn->prepare("UPDATE users SET firstName = ?, lastName = ?, email = ?, mobile = ?, address = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $firstName, $lastName, $email, $mobile, $address, $userId);

    if ($stmt->execute()) {
        // Update session data
        $_SESSION["currentUser"]['firstName'] = $firstName;
        $_SESSION["currentUser"]['lastName'] = $lastName;
        $_SESSION["currentUser"]['email'] = $email;
        $_SESSION["currentUser"]['mobile'] = $mobile;
        $_SESSION["currentUser"]['address'] = $address;

        echo "Profile updated successfully!";
    } else {
        echo "Error updating profile: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
    exit(); // End execution after handling AJAX request
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - TiffinEase</title>
    <link rel="stylesheet" href="css/customer-profile.css">
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
                <li><a href="customer.php">My Orders</a></li>
                <li><a href="customer-payment.html">Payments</a></li>
                <li><a href="logout.php" id="logoutBtn">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Profile Section -->
    <section class="profile">
        <h2>Your Profile</h2>
        <div class="profile-container">
            <div class="profile-pic">
                <img src="image/user.png" alt="Profile Picture" id="profileImage">
                <button onclick="document.getElementById('fileInput').click();">Change Profile Picture</button>
                <input type="file" id="fileInput" accept="image/*" style="display: none;" onchange="loadFile(event)">
            </div>

            <div class="profile-info">
                <label for="first-name">First Name:</label>
                <input type="text" id="first-name" name="first-name"
                    value="<?php echo htmlspecialchars($user['firstName']); ?>" required>

                <label for="last-name">Last Name:</label>
                <input type="text" id="last-name" name="last-name"
                    value="<?php echo htmlspecialchars($user['lastName']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                    required>

                <label for="mobile">Mobile No:</label>
                <input type="tel" id="mobile" name="mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>"
                    required>

                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>"
                    required>

                <button class="btn-save" onclick="saveProfile()">Save Changes</button>
            </div>
        </div>
    </section>

    <div id="logoutMessage" class="hidden"></div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <h2>TiffinEase</h2>
            <p>Bringing homemade meals to your doorstep, every day.</p>
        </div>
        <p>&copy; 2024 TiffinEase. All Rights Reserved.</p>
    </footer>

    <script>
        // Function to preview the selected image
        function loadFile(event) {
            const image = document.getElementById('profileImage');
            image.src = URL.createObjectURL(event.target.files[0]);
        }

        // Function to save profile changes
        function saveProfile() {
            const firstName = document.getElementById('first-name').value;
            const lastName = document.getElementById('last-name').value;
            const email = document.getElementById('email').value;
            const mobile = document.getElementById('mobile').value;
            const address = document.getElementById('address').value;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "customer-profile.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert(xhr.responseText);
                }
            };

            xhr.send(`updateProfile=1&firstName=${encodeURIComponent(firstName)}&lastName=${encodeURIComponent(lastName)}&email=${encodeURIComponent(email)}&mobile=${encodeURIComponent(mobile)}&address=${encodeURIComponent(address)}`);
        }
    </script>
</body>

</html>