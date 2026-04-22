<?php
// Include the database configuration file
require_once 'config.php';

// Set the response headers
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get the raw input data
    // $input = json_decode(file_get_contents('php://input'), true);
    if (isset($_GET['email']) && !empty($_GET['email']) && filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
        // Validate input fields
        $email = $_GET['email'];

        $rowSql = $pdo->prepare("SELECT email FROM users WHERE email='" . $email . "'");
        $rowSql->execute();

        if ($rowSql->rowCount() === 0) {
            echo json_encode(["status" => true, "message" => ""]);
            exit;
        } else {
            echo json_encode(["status" => false, "message" => "Email already exist."]);
            exit;
        }
    } else {
        echo json_encode(["status" => false, "message" => ""]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Invalid request method."]);
}
