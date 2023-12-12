<?php

require_once 'connection.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from the POST fields
    $id = $_POST['id'];
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $color = isset($_POST['color']) ? $_POST['color'] : '';
    $brand = isset($_POST['brand']) ? $_POST['brand'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $featuredPlayer = isset($_POST['featured_player']) ? $_POST['featured_player'] : '';

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

        // Prepare the SQL statement for updating the image with the given ID
        $sql = "UPDATE products SET image_url=? WHERE product_id=?";

        // Use prepared statement to bind parameters
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $destination, $id);

        // Execute the statement
        if ($stmt->execute()) {
            // Image update successful
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Image updated successfully']);
        } else {
            // Image update failed
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error updating image']);
        }

        // Close the statement
        $stmt->close();
    }

    // Prepare the SQL statement for updating other fields with the given ID
    $sql = "UPDATE products SET name=?, description=?, category=?, color=?, featured_player=?, brand=?, price=? WHERE product_id=?";

    // Use prepared statement to bind parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $name, $description, $category, $color, $featuredPlayer, $brand, $price, $id);

    // Execute the statement
    if ($stmt->execute()) {
        // Other fields update successful
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Other fields updated successfully']);
    } else {
        // Other fields update failed
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Error updating other fields']);
    }

    // Close the statement
    $stmt->close();
}

?>
