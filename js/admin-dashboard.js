document.addEventListener("DOMContentLoaded", () => {
    // Authorization check removed for testing purposes

    // Logout functionality
    const logoutButton = document.getElementById("logoutButton");
    logoutButton.addEventListener("click", () => {
        sessionStorage.removeItem("currentUser");
        alert("Successfully logged out.");
        window.location.href = "signin.html"; // Redirect to Sign-In page
    });
});