<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type"); 


$servername = "localhost";
$username = "root";
$password = ""; 
$database = "shoeskopo";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
