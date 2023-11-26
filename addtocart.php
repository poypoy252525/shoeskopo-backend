<?php

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === "GET") {
    
    $customerId = $_GET['customerId'];
    $sql = "SELECT * FROM addtocart JOIN products ON productId = products.product_id  WHERE customerId = $customerId";

    $result = $conn->query($sql);
    $data = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Error fetching data.']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $productId = $data['productId'];
    $customerId = $data['customerId'];
    $quantity = $data['quantity'];

    $sql = "INSERT INTO `addtocart`(`productId`, `customerId`, `quantity`) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $productId, $customerId, $quantity);

    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Data inserted successfully']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Error inserting data']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'];

    $sql = "DELETE FROM addtocart WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        // Send the deleted product details as JSON response
        http_response_code(200); // OK
        echo json_encode($deletedProduct);
    } else {
        // The delete operation failed
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Unable to delete cart']);
    }
}