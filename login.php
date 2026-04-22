<?php
// Include the database configuration file
require_once 'config.php';

// Set the response headers
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw input data
    // $input = json_decode(file_POST_contents('php://input'), true);
    if (isset($_POST['email']) && !empty($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && isset($_POST['password']) && !empty($_POST['password'])) {
        // Validate input fields
        $email = trim($_POST['email']);
        $password = trim(md5($_POST['password']));

        $rowSql = $pdo->prepare("SELECT * FROM users WHERE email='" . $email . "' AND password='" . $password . "'");
        $rowSql->execute();

        if ($rowSql->rowCount() === 0) {
            echo json_encode(["status" => false, "message" => "Invalid user credentials."]);
            exit;
        } else {
            $data = $rowSql->fetch(PDO::FETCH_ASSOC);
            echo json_encode(["status" => true, "message" => "Login successfully.", "data" => $data]);
            exit;
        }
    } else {
        echo json_encode(["status" => false, "message" => "Please provide valid user credentials."]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Invalid request method."]);
}
