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
    // $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($_POST['name']) || !isset($_POST['gender']) || !isset($_POST['email']) || !isset($_POST['status']) || !isset($_FILES['photo']['name'])) {
        echo json_encode(["status" => false, "message" => "All fields are required."]);
        exit;
    }

    // Validate input fields
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    $photo = $_FILES['photo']['name'];
    $photoTmp = $_FILES['photo']['tmp_name'];
    $photoEx = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

    if ($photoEx != 'jpg' && $photoEx != 'jpeg' && $photoEx != 'png') {
        echo json_encode(["status" => false, "message" => "Invalid photo not allowed."]);
        exit;
    }

    $rowSql = $pdo->prepare("SELECT email FROM users WHERE email='" . $email . "'");
    $rowSql->execute();

    if ($rowSql->rowCount() > 0) {
        echo json_encode(["status" => false, "message" => "Email already exist."]);
        exit;
    }

    try {
        $filename = time() . '.' . $photoEx;
        $move = move_uploaded_file($photoTmp, './uploads/' . $filename);
        if ($move) {
            // Prepare the SQL statement
            $sql = "INSERT INTO users (name, gender, email, status, photo) VALUES (:name, :gender, :email, :status, :photo)";
            $stmt = $pdo->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':photo', $filename);

            // Execute the query
            if ($stmt->execute()) {
                echo json_encode(["status" => true, "message" => "User added successfully."]);
            } else {
                echo json_encode(["status" => false, "message" => "Failed to add user."]);
            }
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => false, "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Invalid request method."]);
}
