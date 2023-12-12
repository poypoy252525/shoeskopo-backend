<?php

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['customer_id'])) {
    $customer_id = $_GET['customer_id'];

    $sql = "SELECT * FROM customers WHERE customer_id = $customer_id";
    $result = $conn->query($sql);


    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        header('Content-Type: application/json');
        echo json_encode($row);
    }

    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "SELECT * FROM customers";
    $result = $conn->query($sql);

    $data = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $address = $data['address'];
    $phone_number = $data['phone_number'];
    $customer_id = $data['customer_id'];

    $sql = "UPDATE `customers` SET `first_name`=?,`last_name`=?,`address`=?,`phone_number`=? WHERE customer_id = $customer_id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $first_name, $last_name, $address, $phone_number);

    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Information updated.']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Failed to update information to database.']);
    }
    exit();
}
