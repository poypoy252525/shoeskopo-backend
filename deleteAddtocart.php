<?php

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $customerId = $_GET['id'];

    $sql = "DELETE FROM addtocart WHERE customerId = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $customerId);
    
    if ($stmt->execute()) {
        // Send the deleted product details as JSON response
        http_response_code(200); // OK
        echo json_encode(['status' => 'success', 'message' => 'Add to cart deleted']);
    } else {
        // The delete operation failed
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Unable to delete cart']);
    }
}