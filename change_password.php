<?php
// Include the database configuration file
require_once 'config.php';

// Set the response headers
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Access-Control-Allow-Methods: PUT");
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
    $method = 'PUT';
}

// Check if the request method is POST
if ($method === 'PUT') {
    if(isset($_GET['id']) && !empty($_GET['id'])) {
        try {
        if (!isset($_POST['old_password']) || !isset($_POST['new_password']) || !isset($_POST['confirm_password'])) {
            echo json_encode(["status" => false, "message" => "All fields are required."]);
            exit;
        }

        // Validate input fields
        $id = $_GET['id'];
        $old_password = trim($_POST['old_password']);
        $new_password = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);

        $rowSql = $pdo->prepare("SELECT password FROM users WHERE id=$id AND password='".md5($old_password)."'");
        $rowSql->execute();
        $rowNum = $rowSql->rowCount();
        $row = $rowSql->fetch();

        if ($rowNum === 0) {
            echo json_encode(["status" => false, "message" => "Old password does not match."]);
            exit;
        }

        if ($new_password !== $confirm_password) {
            echo json_encode(["status" => false, "message" => "Password confirmation failed."]);
            exit;
        }

        $confirm_password = md5($confirm_password);
            
            // Prepare the SQL statement
            $sql = "UPDATE users SET password=:password WHERE id=:id";
            $stmt = $pdo->prepare($sql);
            

            // Bind parameters
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':password', $confirm_password);

            // Execute the query
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    echo json_encode(["status" => true, "message" => "Password changed successfully."]);
                } else {
                    echo json_encode(["status" => false, "message" => "Nothing changed."]);
                }
            } else {
                echo json_encode(["status" => false, "message" => "Failed to add user."]);
            }
        } catch (PDOException $e) {
            echo json_encode(["status" => false, "message" => $e->getMessage()]);
        }
    }else {
            echo json_encode(["status" => false, "message" => ""]);
            exit;
        }
} else {
    echo json_encode(["status" => false, "message" => "Invalid request method."]);
}
