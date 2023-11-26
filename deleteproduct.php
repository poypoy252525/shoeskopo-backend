<?php

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    
    // Assuming you're passing the id through the request parameters
    $productId = $_GET['id'];

    // Fetch the product details before the deletion
    $selectSql = 'SELECT * FROM products WHERE product_id = ?';
    $selectStmt = $conn->prepare($selectSql);
    $selectStmt->bind_param('i', $productId);
    $selectStmt->execute();
    $result = $selectStmt->get_result();

    // Check if the product was found
    if ($result->num_rows > 0) {
        $deletedProduct = $result->fetch_assoc();

        // Close the select statement
        $selectStmt->close();

        // Now, proceed with the deletion
        $deleteSql = 'DELETE FROM products WHERE product_id = ?';
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param('i', $productId);

        // Execute the deletion
        if ($deleteStmt->execute()) {
            // Send the deleted product details as JSON response
            http_response_code(200); // OK
            echo json_encode($deletedProduct);
        } else {
            // The delete operation failed
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Unable to delete product']);
        }

        // Close the delete statement
        $deleteStmt->close();
    } else {
        // Product not found, send a 404 (Not Found) response
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Product not found']);
    }

    // Close the connection
    $conn->close();
}
