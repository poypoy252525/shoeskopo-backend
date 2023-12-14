<?php

require_once 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $customer_id = $data['customer_id'];
    $order_date_string = $data['order_date'];

    // Convert the ISO string date to a DateTime object
    $order_date = new DateTime($order_date_string);

    $payment_method = $data['payment_method'];
    $status = $data['status'];
    $order_items = $data['order_items']; // Assuming order_items is an array of items

    // Start a transaction to ensure atomicity
    $conn->begin_transaction();

    try {
        // Insert into orders table
        $sql_order = "INSERT INTO `orders`(`customer_id`, `order_date`, `payment_method`, `status`) VALUES (?, ?, ?, ?)";
        $stmt_order = $conn->prepare($sql_order);

        // Check if the statement is prepared successfully
        if (!$stmt_order) {
            throw new Exception('Error preparing the order query: ' . $conn->error);
        }

        // Convert DateTime object to a string in a format suitable for your database
        $formatted_order_date = $order_date->format('Y-m-d H:i:s');

        // Bind parameters for orders table
        $stmt_order->bind_param('isss', $customer_id, $formatted_order_date, $payment_method, $status);

        // Execute the statement
        $stmt_order->execute();

        // Check for errors during execution
        if ($stmt_order->error) {
            throw new Exception('Error executing the order query: ' . $stmt_order->error);
        }

        // Get the last inserted order_id
        $order_id = $stmt_order->insert_id;

        // Close the statement for orders table
        $stmt_order->close();

        // Insert into orderitems table
        $sql_orderitem = "INSERT INTO `orderitems`(`order_id`, `product_id`, `quantity`, `subtotal`) VALUES (?, ?, ?, ?)";
        $stmt_orderitem = $conn->prepare($sql_orderitem);

        // Check if the statement is prepared successfully
        if (!$stmt_orderitem) {
            throw new Exception('Error preparing the order item query: ' . $conn->error);
        }

        // Bind parameters for orderitems table
        foreach ($order_items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $subtotal = $item['subtotal'];

            $stmt_orderitem->bind_param('iiid', $order_id, $product_id, $quantity, $subtotal);

            // Execute the statement for orderitems table
            $stmt_orderitem->execute();

            // Check for errors during execution
            if ($stmt_orderitem->error) {
                throw new Exception('Error executing the order item query: ' . $stmt_orderitem->error);
            }
        }

        // Close the statement for orderitems table
        $stmt_orderitem->close();

        // Commit the transaction
        $conn->commit();

        $result = ['status' => 'success', 'message' => 'Order and order items inserted successfully'];
        echo json_encode($result);
    } catch (Exception $e) {
        // Rollback the transaction in case of any errors
        $conn->rollback();

        $result = ['status' => 'error', 'message' => $e->getMessage()];
        echo json_encode($result);
    } finally {
        // Close the database connection
        $conn->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['customer_id'])) {
    $customer_id = $_GET['customer_id'];

    if (isset($_GET['type']) && $_GET['type'] === 'totalspent') {
        $sql = "SELECT SUM(orderitems.subtotal) AS total_spent FROM `orders` JOIN orderitems ON orders.order_id = orderitems.order_id WHERE customer_id = $customer_id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            header('Content-Type: application/json');
            echo json_encode($row);
        }

        
        exit();
    }

    $sql = "SELECT * FROM orders WHERE customer_id = $customer_id";

    $result = $conn->query($sql);

    $data = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $sql = "SELECT * FROM orders JOIN customers ON orders.customer_id = customers.customer_id";

    $result = $conn->query($sql);

    $data = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);

    $status = $data['status'];
    $order_id = $data['order_id'];

    $sql = "UPDATE `orders` SET status = ? WHERE order_id = $order_id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $status);

    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Status updated successfully.']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Something went wrong.']);
    }
}


?>
