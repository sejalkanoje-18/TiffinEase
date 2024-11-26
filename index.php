<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TiffinEase - Homemade Meals Delivered</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <h1>TiffinEase</h1>
        </div>
        <nav>
            <ul>
                <?php
                // Check if the user is logged in
                if (isset($_SESSION['currentUser'])) {
                    $userRole = $_SESSION['currentUser']['role']; // Assuming 'role' is stored in session
                
                    // Menu for customer
                    if ($userRole == 'customer') {
                        echo '
                            <li><a href="#home">Home</a></li>
                            <li><a href="customer.php">My Orders</a></li>
                            <li><a href="logout.php">Log out</a></li>
                        ';
                    }
                    // Menu for vendor
                    elseif ($userRole == 'vendor') {
                        echo '
                            <li><a href="#home">Home</a></li>
                            <li><a href="vendor-dashboard.php">Tiffin Services</a></li>
                            <li><a href="logout.php">Log out</a></li>
                        ';
                    }
                } else {
                    // Default menu for users who are not logged in
                    echo '
                        <li><a href="#home">Home</a></li>
                        <li><a href="signin.html">Sign In</a></li>
                        <li><a href="register.html">Sign Up</a></li>
                    ';
                }
                ?>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h2>Delicious Homemade Meals, Delivered to Your Doorstep</h2>
            <p>Healthy, affordable, and customizable meal plans for students and professionals.</p>
            <a href="signin.html" class="btn">Get Started</a>
        </div>
        <div class="hero-image">
            <img src="Frontend/images/hero4.jpg" alt="Delicious Meal">
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <h2>Why Choose TiffinEase?</h2>
        <div class="features-container">
            <div class="feature-box">
                <i class="icon fas fa-utensils"></i> <!-- Example icon using Font Awesome -->
                <h3>Homemade Meals</h3>
                <p>Enjoy delicious, freshly cooked homemade meals every day.</p>
            </div>
            <div class="feature-box">
                <i class="icon fas fa-truck"></i>
                <h3>On-time Delivery</h3>
                <p>Reliable and on-time delivery to your location.</p>
            </div>
            <div class="feature-box">
                <i class="icon fas fa-leaf"></i>
                <h3>Healthy Options</h3>
                <p>Choose from a range of nutritious and balanced meal options.</p>
            </div>
            <div class="feature-box">
                <i class="icon fas fa-users"></i>
                <h3>Support Local Vendors</h3>
                <p>Help support local tiffin vendors and small businesses.</p>
            </div>
            <div class="feature-box">
                <i class="icon fas fa-mobile-alt"></i>
                <h3>User Friendly</h3>
                <p>Order and manage your meals easily using our platform.</p>
            </div>
            <div class="feature-box">
                <i class="icon fas fa-thumbs-up"></i>
                <h3>Customizable Plans</h3>
                <p>Flexibility to customize meal plans according to your preferences.</p>
            </div>
        </div>
    </section>


    <!-- Testimonials Section -->
    <!-- Testimonials Section -->
    <section class="testimonials">
        <h2>What Our Customers Say</h2>
        <div class="testimonial-container">
            <div class="testimonial-item">
                <p>"TiffinEase has completely changed my lunch experience at work. The meals are fresh, delicious, and
                    always on time."</p>
                <h3>- Varun (Working professional)</h3>
            </div>
            <div class="testimonial-item">
                <p>"As a student, it’s tough to find affordable, healthy food. TiffinEase is a lifesaver!"</p>
                <h3>- Diya (Student)</h3>
            </div>
            <div class="testimonial-item">
                <p>"Partnering with TiffinEase has boosted my small tiffin service. I can reach more customers and grow
                    my business easily."</p>
                <h3>- Raj Mishra (Vendor)</h3>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <h2>TiffinEase</h2>
            <p>Bringing homemade meals to your doorstep, everyday.</p>
        </div>
        <p>&copy; 2024 TiffinEase. All Rights Reserved.</p>
    </footer>
</body>

</html>