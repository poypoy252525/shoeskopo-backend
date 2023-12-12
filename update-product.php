<?php

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $color = isset($_POST['color']) ? $_POST['color'] : '';
    $brand = isset($_POST['brand']) ? $_POST['brand'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $featuredPlayer = isset($_POST['featured_player']) ? $_POST['featured_player'] : '';

    if (isset($_FILES['image'])) {
        $uploadedFile = $_FILES['image'];
        $tempFilePath = $uploadedFile['tmp_name'];

        $uniqueFilename = uniqid() . '_' . $uploadedFile['name'];

        $uploadDirectory = 'images/';

        $destination = $uploadDirectory . $uniqueFilename;
        move_uploaded_file($tempFilePath, $destination);

        $sql = "UPDATE products SET image_url=? WHERE product_id=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $destination, $id);

        if ($stmt->execute()) {

            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'Image updated successfully']);
        } else {

            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Error updating image']);
        }

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
