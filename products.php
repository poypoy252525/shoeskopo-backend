<?php

require_once 'connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $sql = "SELECT * FROM products WHERE product_id = $id";
    $result = $conn->query($sql);

    $data = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    echo json_encode($data[0]);
    die();
}

if (isset($_GET['genders']) || isset($_GET['brands'])) {
    $sql = 'SELECT * FROM products WHERE 1=1'; 

    $selectedGenders = isset($_GET['genders']) ? $_GET['genders'] : array();
    $selectedBrands = isset($_GET['brands']) ? $_GET['brands'] : array();

    $params = array();
    $paramTypes = '';

    
    if (!empty($selectedGenders)) {
        $sql .= ' AND category IN (' . rtrim(str_repeat('?,', count($selectedGenders)), ',') . ')';
        $paramTypes .= str_repeat('s', count($selectedGenders));
        $params = array_merge($params, $selectedGenders);
    }

    if (!empty($selectedBrands)) {
        $sql .= ' AND brand IN (' . rtrim(str_repeat('?,', count($selectedBrands)), ',') . ')';
        $paramTypes .= str_repeat('s', count($selectedBrands));
        $params = array_merge($params, $selectedBrands);
    }

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        
        $stmt->bind_param($paramTypes, ...$params);
        $stmt->execute();

        $result = $stmt->get_result();

        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode($data);
        die();
    }
}


$sql = 'SELECT * FROM products';
$result = $conn->query($sql);

$data = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
