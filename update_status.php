<?php
// Include database connection
include 'php/db.php';

// Check if transaction_id and status are passed in the POST request
if (isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Prepare and execute the SQL query to update the status
    $sql = "UPDATE payments SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id); // "si" means string and integer
    if ($stmt->execute()) {
        echo "Status updated successfully!";
    } else {
        echo "Error updating status.";
    }
    $stmt->close();
}
?>