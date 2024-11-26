<?php
session_start();
include 'php/db.php'; // Include your database connection file

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
    <title>Vendor Profile - TiffinEase</title>
    <link rel="stylesheet" href="css/vendor-profile.css">
</head>

<body>
    <header>
        <div class="logo">
            <h1>TiffinEase</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="vendor-dashboard.php">Manage Orders</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="vendor-profile-section">
        <h2>Your Profile</h2>
        <form id="vendor-profile-form">
            <!-- Mess/Organization Name -->
            <label for="organization-name">Mess/Organization Name:</label>
            <input type="text" id="organization-name" name="organization-name" value="Vendor's Mess" required>

            <!-- First Name -->
            <label for="first-name">First Name:</label>
            <input type="text" id="first-name" name="first-name"
                value="<?php echo htmlspecialchars($user['firstName']); ?>" required>

            <!-- Last Name -->
            <label for="last-name">Last Name:</label>
            <input type="text" id="last-name" name="last-name"
                value="<?php echo htmlspecialchars($user['lastName']); ?>" required>

            <!-- Email -->
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                required>

            <!-- Phone Number -->
            <label for="mobile">Phone Number:</label>
            <input type="tel" id="mobile" name="mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>"
                required>

            <!-- Address -->
            <label for="address">Address:</label>
            <textarea id="address" name="address" required><?php echo htmlspecialchars($user['address']); ?></textarea>

            <button type="button" class="btn-save" onclick="saveProfile()">Save Changes</button>
        </form>
    </section>

    <script>
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