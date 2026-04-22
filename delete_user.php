<?php
// Include the database configuration file
require_once 'config.php';

// Set headers for JSON response
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Read the input JSON payload
$id = $_GET['del'];

// Check if at least one filter is provided
if (!isset($_GET['del'])) {
    echo json_encode(["status" => false, "message" => "Id is required."]);
    exit();
}

// Base SQL query for deleting
$sql = "DELETE FROM users WHERE id=" . $id;

// Prepare and execute the query
$stmt = $pdo->prepare($sql);
$stmt->execute();

// Check if any rows were deleted
if ($stmt->execute()) {
    echo json_encode(["status" => true, "message" => "User deleted."]);
} else {
    echo json_encode(["status" => false, "message" => "No matching records found."]);
}
