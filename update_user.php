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
    // Get the raw input data
    // $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($_POST['name']) || !isset($_POST['gender']) || !isset($_POST['email']) || !isset($_POST['status'])) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    // Validate input fields
    $id = $_POST['id'];
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $status = $_POST['status'];

    $rowSql = $pdo->prepare("SELECT photo FROM users WHERE id=$id");
    $rowSql->execute();
    $row = $rowSql->fetch();

    try {
        if (isset($_FILES['photo']['name'])) {
            $photo = $_FILES['photo']['name'];
            $photoTmp = $_FILES['photo']['tmp_name'];
            $photoEx = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

            if ($photoEx != 'jpg' && $photoEx != 'jpeg' && $photoEx != 'png') {
                echo json_encode(["status" => "error", "message" => "Invalid photo not allowed."]);
                exit;
            }
            unlink('./uploads/' . $row['photo']);
            $filename = time() . '.' . $photoEx;
            move_uploaded_file($photoTmp, './uploads/' . $filename);
        } else {
            $filename = $row['photo'];
        }
        // Prepare the SQL statement
        $sql = "UPDATE users SET name=:name, gender=:gender, email=:email, status=:status, photo=:photo WHERE id=:id";
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':photo', $filename);

        // Execute the query
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                echo json_encode(["status" => "success", "message" => "User updated successfully."]);
            } else {
                echo json_encode(["status" => "danger", "message" => "Nothing changed."]);
            }
        } else {
            echo json_encode(["status" => "success", "message" => "Failed to add user."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "success", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "success", "message" => "Invalid request method."]);
}
