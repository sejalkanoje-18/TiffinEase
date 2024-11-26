<?php
session_start();
include 'php/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Admin credentials check
    if ($email === "admin@example.com" && $password === "admin123") {
        $_SESSION["role"] = "admin";
        header("Location: admin-dashboard.php");
        exit();
    }

    // User credentials check in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Password verification
            if (password_verify($password, $user['password'])) {
                $_SESSION["currentUser"] = $user; // Store entire user data in session

                // Redirect based on role
                switch ($user['role']) {
                    case 'customer':
                        header("Location: customer.php"); // Changed to .php for dynamic content
                        break;
                    case 'vendor':
                        header("Location: vendor-dashboard.php"); // Changed to .php for dynamic content
                        break;
                    default:
                        header("Location: signin.html"); // Redirect to signin on unknown role
                        break;
                }
                exit();
            } else {
                echo "<script>alert('Invalid password!'); window.location.href='signin.html';</script>";
            }
        } else {
            echo "<script>alert('User not found!'); window.location.href='signin.html';</script>";
        }

        $stmt->close();
    } else {
        // Log error (e.g., to a file) and notify user of internal error
        error_log("Database error: " . $conn->error);
        echo "<script>alert('Internal error. Please try again later.'); window.location.href='signin.html';</script>";
    }
}

$conn->close();
?>