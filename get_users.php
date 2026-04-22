<?php
// Include the database configuration file
require_once 'config.php';

// Set headers for JSON response
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Base SQL query
$sql = "SELECT * FROM users WHERE 1=1";

// Dynamic query building based on GET parameters
$params = [];
if (!empty($_GET['id'])) {
    $sql .= " AND id = :id";
    $params[':id'] = $_GET['id'];
}

$sql .= " ORDER BY id DESC";

// Prepare and execute the query
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// Fetch the results
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the results as JSON
if (!empty($users)) {
    echo json_encode(["status" => true, "data" => $users]);
} else {
    echo json_encode(["status" => false, "message" => "No users found."]);
}
