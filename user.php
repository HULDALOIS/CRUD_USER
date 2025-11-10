<?php
require_once "db.php";
header("Content-Type: application/json");

// Ambil method HTTP (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql = "SELECT * FROM users WHERE id = $id";
            $result = $conn->query($sql);
            echo json_encode($result->fetch_assoc());
        } else {
            $result = $conn->query("SELECT * FROM users");
            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
            echo json_encode($users);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $username = $conn->real_escape_string($data['username']);
        $email = $conn->real_escape_string($data['email']);
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
        if ($conn->query($sql)) {
            echo json_encode(["message" => "User added successfully"]);
        } else {
            echo json_encode(["error" => $conn->error]);
        }
        break;

    case 'PUT':
        if (!isset($_GET['id'])) {
            echo json_encode(["error" => "Missing user ID"]);
            exit;
        }
        $id = intval($_GET['id']);
        $data = json_decode(file_get_contents("php://input"), true);
        $username = $conn->real_escape_string($data['username']);
        $email = $conn->real_escape_string($data['email']);

        if (!empty($data['password'])) {
            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username='$username', email='$email', password='$password' WHERE id=$id";
        } else {
            $sql = "UPDATE users SET username='$username', email='$email' WHERE id=$id";
        }

        if ($conn->query($sql)) {
            echo json_encode(["message" => "User updated successfully"]);
        } else {
            echo json_encode(["error" => $conn->error]);
        }
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            echo json_encode(["error" => "Missing user ID"]);
            exit;
        }
        $id = intval($_GET['id']);
        $sql = "DELETE FROM users WHERE id=$id";
        if ($conn->query($sql)) {
            echo json_encode(["message" => "User deleted successfully"]);
        } else {
            echo json_encode(["error" => $conn->error]);
        }
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
        break;
}

$conn->close();
?>
