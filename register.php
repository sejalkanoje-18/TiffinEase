<?php
include 'php/db.php'; // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['first-name'];
    $lastName = $_POST['last-name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing password for security
    $role = $_POST['role'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $pincode = $_POST['pincode'];
    $state = $_POST['state'];

    // Check if email already exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.location.href='register.html';</script>";
    } else {
        // Insert new user into database
        $sql = "INSERT INTO users (firstName, lastName, email, mobile, password, role, address, city, pincode, state) VALUES ('$firstName', '$lastName', '$email', '$mobile', '$password', '$role', '$address', '$city', '$pincode', '$state')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration successful!'); window.location.href='signin.html';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "'); window.location.href='register.html';</script>";
        }
    }

    $conn->close(); // Close connection
}
?>