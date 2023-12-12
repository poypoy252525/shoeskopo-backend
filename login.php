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
        
        $row = $result->fetch_assoc();
        $stored_password_hash = $row['password_hash'];

        if (password_verify($password, $stored_password_hash)) {
            
            header('Content-Type: application/json');
            echo json_encode($row);
            die();
        } else {
            
            header('Content-Type: application/json');
            echo json_encode(['status'=> 'error', 'message' => 'Invalid password']);
            die();
        }
    } else {
        
        header('Content-Type: application/json');
        echo json_encode(['status'=> 'error', 'message' => 'User not found']);
        die();
    }
}

