document.addEventListener("DOMContentLoaded", () => {
    // Logout logic
    const logoutBtn = document.getElementById("logoutBtn");
    const logoutMessage = document.getElementById("logoutMessage");

    logoutBtn.addEventListener("click", (e) => {
        e.preventDefault();

        // Clear user session or token (depending on your auth method)
        localStorage.removeItem("authToken");  // Example: If using tokens for authentication

        // Show logout message
        logoutMessage.classList.remove("hidden");

        // Redirect to Sign-In page after 3 seconds
        setTimeout(() => {
            window.location.href = "signin.html";  // Redirect to Sign-In page
        }, 3000); // 3000 ms = 3 seconds
    });
});
