<?php

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get data from the POST fields
    $name = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $color = $_POST['color'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $featuredPlayer = $_POST['featured_player'];

    // Check if the 'image' file input is set in the request
    if (isset($_FILES['image'])) {
        // Handle the image file
        $uploadedFile = $_FILES['image'];
        $tempFilePath = $uploadedFile['tmp_name'];

        // Generate a unique filename for the uploaded image
        $uniqueFilename = uniqid() . '_' . $uploadedFile['name'];

        // Specify the directory where you want to save the images
        $uploadDirectory = 'images/';

        // Move the uploaded file to the desired location
        $destination = $uploadDirectory . $uniqueFilename;
        move_uploaded_file($tempFilePath, $destination);

        // Prepare the SQL statement with placeholders for the image URL
        $sql = "INSERT INTO products (name, description, category, color, featured_player, image_url, brand, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        // Use prepared statement to bind parameters
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $name, $description, $category, $color, $featuredPlayer, $destination, $brand, $price);

        // Execute the statement
        if ($stmt->execute()) {
            // Data insertion successful
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Data inserted successfully']);
        } else {
            // Data insertion failed
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error inserting data']);
        }

        // Close the statement
        $stmt->close();
    } else {
        // Handle the case where 'image' is not set in the request
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Image not found in the request']);
    }

}

