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
// $username =  "doctor"
// $password = 'Doctor@143'
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        handleGet($conn); // Call getReports function for 'getReports' action
        break;
    case 'POST':
        handlePost($conn);
        break;
    case 'DELETE':
        handleDelete($conn);
        break;
    case 'PUT':
        handlePut($conn);
        break;
   
    default:
        echo json_encode(['error' => 'Invalid request method']);
}
$conn->close();
// Function to handle GET requests
function handleGet($conn) {
    $query = "SELECT hospital_details.*, registration_form.Name 
              FROM hospital_details 
              INNER JOIN registration_form 
              ON hospital_details.RegNumber = registration_form.RegistrationNumber";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $records = [];
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
        echo json_encode($records);
    } else {
        echo json_encode(['code'=>400,'message' => 'No records found']);
    }
}

// Function to handle POST requests
function handlePost($conn) {
    
     
    $inputData = file_get_contents("php://input");
    $data = json_decode($inputData, true);

    $Name = $conn->real_escape_string($data['name']);
    $City = $conn->real_escape_string($data['city']);
    $address = $conn->real_escape_string($data['address']);
    $hosDetails = $conn->real_escape_string($data['hosDetails']);
    $regNumber = $conn->real_escape_string($data['regnumber']);

        $sql = "INSERT INTO hospital_details (	HospitalName,City, Address,HospitalDetails,RegNumber) VALUES ('$Name','$City', '$address', '$hosDetails','$regNumber')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(['code'=>200,'message'=>'Record Insert successfully']);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
}

function handleDelete($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];

    $sql = "DELETE FROM hospital_details WHERE Sno = $id";
    if ($conn->query($sql)) {
        echo json_encode(['code'=>200,'message'=>'Record update successfully']);
    } else {
        echo json_encode(['error' => 'Error deleting user']);
    }
}
function handlePut($conn) {
    $inputData = file_get_contents("php://input");
    $data = json_decode($inputData, true);

    $ID = $conn->real_escape_string($data['ID']);
    $Name = $conn->real_escape_string($data['name']);
    $City = $conn->real_escape_string($data['city']);
    $address = $conn->real_escape_string($data['address']);
    $hosDetails = $conn->real_escape_string($data['hosDetails']);


        $sql = "UPDATE hospital_details SET  HospitalName='$Name', City = '$City', Address = '$address', HospitalDetails = '$hosDetails' WHERE  Sno = '$ID' ";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(['code'=>200,'message'=>'Record update successfully']);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
}

?>