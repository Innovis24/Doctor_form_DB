<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

// Database connection
$host = 'localhost';
$username = 'root'; // Default username
$password = ''; // Default password
$database = 'doctor_search';
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

// Determine the HTTP request method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($conn);
        break;
    case 'POST':
        handlePost($conn);
        break;
    case 'PUT':
        handlePut($conn);
        break;
    case 'DELETE':
        handleDelete($conn);
        break;
    default:
        echo json_encode(['error' => 'Invalid request method']);
}

$conn->close();

// Function to handle GET requests
function handleGet($conn) {
    $sql = 'SELECT * FROM user_list';
    $result = $conn->query($sql);
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    echo json_encode($users);

}
function handleDelete($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];

    $sql = "DELETE FROM user_list WHERE Sno = $id";
    if ($conn->query($sql)) {
        echo json_encode(['code'=>200,'message'=>'Record update successfully']);
    } else {
        echo json_encode(['error' => 'Error deleting user']);
    }
}

function handlePost($conn) {

    $inputData = file_get_contents("php://input");
    $data = json_decode($inputData, true);

    $Name = $conn->real_escape_string($data['name']);
    $userName = $conn->real_escape_string($data['userName']);
    $password = $conn->real_escape_string($data['password']);
    $userRole = $conn->real_escape_string($data['userRole']);
    $userStatus = $conn->real_escape_string($data['status']);
    $ID = $conn->real_escape_string($data['ID']);

        $sql = "INSERT INTO user_list (	RegNumber,Name,UserName, Password,UserRole,Status) VALUES ('$ID','$Name','$userName', '$password', '$userRole', '$userStatus')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(['code'=>200,'message'=>'Record Insert successfully']);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
}
function handlePut($conn) {
    $inputData = file_get_contents("php://input");
    $data = json_decode($inputData, true);

    $ID = $conn->real_escape_string($data['sno']);
    $Name = $conn->real_escape_string($data['name']);
    $userName = $conn->real_escape_string($data['userName']);
    $password = $conn->real_escape_string($data['password']);
    $userRole = $conn->real_escape_string($data['userRole']);
    $userStatus = $conn->real_escape_string($data['status']);


        $sql = "UPDATE user_list SET  Name='$Name', UserName = '$userName', Password = '$password', UserRole = '$userRole' , Status = '$userStatus'  WHERE  Sno = '$ID' ";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(['code'=>200,'message'=>'Record update successfully']);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
}
?>
