function registerUser() {
    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm-password").value;
    const firstName = document.getElementById("first-name").value;
    const lastName = document.getElementById("last-name").value;
    const role = document.getElementById("role").value;
    const mobile = document.getElementById("mobile").value;
    const address = document.getElementById("address").value;
    const city = document.getElementById("city").value;
    const pincode = document.getElementById("pincode").value;
    const state = document.getElementById("state").value;

    // Check if passwords match
    if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return;
    }

    const user = {
        firstName,
        lastName,
        email,
        mobile,
        role,
        password, // Note: Storing plain passwords is not secure
        address,
        city,
        pincode,
        state
    };

    let users = JSON.parse(localStorage.getItem("users")) || [];
    if (users.some(u => u.email === email)) {
        alert("Email already registered!");
        return;
    }

    users.push(user);
    localStorage.setItem("users", JSON.stringify(users));
    localStorage.setItem("loggedInUser", JSON.stringify(user)); // Store current logged-in user
    alert("Registration successful!");

    // Redirect to respective dashboard based on role
    if (role == 'vendor') {
        window.location.href = 'vendor-dashboard.html';
    } else {
        window.location.href = 'customer.html';
    }
}