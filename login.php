<?php

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'];
    $password = $data['password'];

    $sql = "SELECT * FROM customers WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, now verify the password
        $row = $result->fetch_assoc();
        $stored_password_hash = $row['password_hash'];

        if (password_verify($password, $stored_password_hash)) {
            // Passwords match, user is authenticated
            header('Content-Type: application/json');
            echo json_encode(['customer_id'=> $row['customer_id'], 'email' => $email]);
            die();
        } else {
            // Passwords do not match
            header('Content-Type: application/json');
            echo json_encode(['status'=> 'error', 'message' => 'Invalid password']);
            die();
        }
    } else {
        // User not found
        header('Content-Type: application/json');
        echo json_encode(['status'=> 'error', 'message' => 'User not found']);
        die();
    }
}

