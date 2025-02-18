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
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        handleGet($conn); // Call getReports function for 'getReports' action
        break;
    case 'POST':
        handlePost($conn);
        break;
    case 'DELETE':
        $action = isset($_GET['action']) ? $_GET['action'] : 'default';
        if ($action === 'deleteuser') {
            handleDelete($conn);
        } elseif ($action === 'deleteImage') {
            handleImgDelete($conn);
        } 
    break;

    default:
        echo json_encode(['error' => 'Invalid request method']);
}
$conn->close();
// Function to handle GET requests
function handleGet($conn) {
    $query = "SELECT * FROM registration_form";
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
    
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : null;
    $fathername = isset($_POST['fatherName']) ? $conn->real_escape_string($_POST['fatherName']) : null;
    $dateofbirth = isset($_POST['dob']) ? $conn->real_escape_string($_POST['dob']) : null;
    $gender = isset($_POST['gender']) ? $conn->real_escape_string($_POST['gender']) : null;
    $phonenumber = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : null;
    $email =  isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : null;
    $address =  isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : null;
    $city =  isset($_POST['city']) ? $conn->real_escape_string($_POST['city']) : null;
    $state =  isset($_POST['state']) ? $conn->real_escape_string($_POST['state']) : null;
    $qualification = isset($_POST['qualification']) ? $conn->real_escape_string($_POST['qualification']) : null;
    $specialization = isset($_POST['specialization']) ? $conn->real_escape_string($_POST['specialization']) : null;
    $registrationNumber = isset($_POST['regNumber']) ? $conn->real_escape_string($_POST['regNumber']) : null;
    $yearofregistration = isset($_POST['regYear']) ? $conn->real_escape_string($_POST['regYear']) : null;
    $employmenttype = isset($_POST['employmentType']) ? $conn->real_escape_string($_POST['employmentType']) : null;
    $uprnnumber = isset($_POST['uprn']) ? $conn->real_escape_string($_POST['uprn']) : null;
    $universityname = isset($_POST['university']) ? $conn->real_escape_string($_POST['university']) : null;
    $stateofmedicine = isset($_POST['stateOfMedicine']) ? $conn->real_escape_string($_POST['stateOfMedicine']) : null;
    $yearofqualification = isset($_POST['yearOfQualification']) ? $conn->real_escape_string($_POST['yearOfQualification']) : null;
    $id =  isset($_POST['Sno']) ? $conn->real_escape_string($_POST['Sno']) : null;
    
    $postgraduate = json_decode($_POST['postgraduate'],true);
    $postgraduatesJson = json_encode($postgraduate);

    $id =  isset($_POST['Sno']) ? $conn->real_escape_string($_POST['Sno']) : null;
    $galleryImagePaths = [];
    if (!$id)     //Add new user
    {
    $fileTmpPath = $_FILES['images']['tmp_name'];
    $fileName = $_FILES['images']['name'];
    $fileSize = $_FILES['images']['size'];
    $fileType = $_FILES['images']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

       // Specify the upload directory
       $uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/Doctor_search/uploads/';
       $dest_path = $uploadFileDir . $newFileName;

       if (move_uploaded_file($fileTmpPath, $dest_path)) {
           $imagePath = "uploads/" . $newFileName;  // Store relative path
       } else {
           echo json_encode(['error' => 'Failed to move uploaded file.']);
           return;
       }
   
       if (isset($_FILES['galleryImages'])) {
        $galleryImages = $_FILES['galleryImages'];
        $fileCount = count($galleryImages['name']); // Number of files uploaded
        $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/Doctor_search/gallery_img/'; // Gallery upload directory
    
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }
    
        $galleryImagePaths = [];
    
        for ($i = 0; $i < $fileCount; $i++) {
            $fileName = $galleryImages['name'][$i];
            $fileTmpName = $galleryImages['tmp_name'][$i];
            $fileError = $galleryImages['error'][$i];
    
            if ($fileError === 0) {
                $newFileName = uniqid('', true) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
                $dest_path = $uploadDirectory . $newFileName;
    
                if (move_uploaded_file($fileTmpName, $dest_path)) {
                    $galleryImagePaths[] = "gallery_img/" . $newFileName; // Store relative path
                }
            }
        }  
        // echo json_encode(['code'=>200,'message'=>'Record inserted successfully']);
    } else {
        // echo json_encode(['error' => 'No images uploaded.']);
    }

    $galleryImagePathsString = implode(",", $galleryImagePaths);
       

        $InsertQuery = "INSERT INTO registration_form (Name, Fathername,DOB,Gender,Phonenumber,Email,Address,City,State,Qualification,Specialization,RegistrationNumber,Yearofregistration,Employmenttype,Uprnnumber,Universityname,Stateofmedicine,Yearofqualification,image_name, image_path,gallery_image_paths,Postgraduation) VALUES ('$name', '$fathername', '$dateofbirth','$gender','$phonenumber','$email','$address','$city','$state','$qualification','$specialization','$registrationNumber','$yearofregistration','$employmenttype','$uprnnumber','$universityname','$stateofmedicine','$yearofqualification','$fileName','$imagePath','$galleryImagePathsString','$postgraduatesJson')";
            if ($conn->query($InsertQuery)) {
                
                echo json_encode(['code'=>200,'message'=>'Record inserted successfully']);
            } else {
                echo json_encode(['error' => "Error inserting record " . $conn->error]);
                return;
            }
       
    }
    else{
        //update new user
        $image_name =  isset($_POST['image_name']) ? $conn->real_escape_string($_POST['image_name']) : null;
        $image_path =  isset($_POST['image_path']) ? $conn->real_escape_string($_POST['image_path']) : null;
        $image =  isset($_POST['images']) ? $conn->real_escape_string($_POST['images']) : null;
        
        if($image_name ===  $image){   //if image name same
            // echo json_encode(['same img']);
            $sql = "SELECT gallery_image_paths FROM registration_form WHERE Sno = '$id'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $existingGalleryPathsString = $row['gallery_image_paths']; // Existing paths
                
                // Convert the string to an array (assuming it's a comma-separated string)
                $existingGalleryPaths = explode(",", $existingGalleryPathsString);
            } else {
                $existingGalleryPaths = [];  // No existing paths
            }

            if (isset($_FILES['galleryImages'])) {
                $galleryImages = $_FILES['galleryImages'];
                $fileCount = count($galleryImages['name']); // Number of files uploaded
                $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/Doctor_search/gallery_img/'; // Gallery upload directory
                $galleryImagePaths = []; 
               
        
                for ($i = 0; $i < $fileCount; $i++) {
                    $fileName = $galleryImages['name'][$i];
                    $fileTmpName = $galleryImages['tmp_name'][$i];
                    $fileError = $galleryImages['error'][$i];
        
                    if ($fileError === 0) {
                        $newFileName = uniqid('', true) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
                        $dest_path = $uploadDirectory . $newFileName;
        
                        if (move_uploaded_file($fileTmpName, $dest_path)) {
                            $galleryImagePaths[] = "gallery_img/" . $newFileName; // Store relative path
                        }
                    }
                }
            }
              // Ensure $existingGalleryPaths is always an array
                if (!is_array($existingGalleryPaths)) {
                    $existingGalleryPaths = !empty($existingGalleryPaths) ? explode(",", $existingGalleryPaths) : [];
                }

             
                $mergedGalleryPaths = array_merge($existingGalleryPaths, $galleryImagePaths);
                
             
                // Convert back to a comma-separated string for database storage
                $galleryImagePathsString = implode(",", $mergedGalleryPaths);
                         


            $update_sql = "UPDATE registration_form SET 
            Name = '$name', 
                        Fathername = '$fathername', 
                        DOB = '$dateofbirth' , Gender = '$gender' ,
                        Phonenumber = '$phonenumber' , Email = '$email' ,
                        Address = '$address' , City = '$city' ,
                        State = '$state' , Qualification = '$qualification' ,
                        Specialization = '$specialization' , RegistrationNumber = '$registrationNumber' ,
                        Yearofregistration = '$yearofregistration',Employmenttype = '$employmenttype', 
                        Uprnnumber = '$uprnnumber', 
                        Universityname = '$universityname' , Stateofmedicine = '$stateofmedicine' ,
                        Yearofqualification = '$yearofqualification' ,image_name = '$image_name', image_path ='$image_path',
                        gallery_image_paths = '$galleryImagePathsString',
                        Postgraduation='$postgraduatesJson'
                        WHERE  Sno = '$id' ";
            
            
                if ($conn->query($update_sql)) {
                    echo json_encode(['code'=>200,'message'=>'Record update successfully']);
                } else {
                    echo json_encode(['error' => 'Error while update record ' . $conn->error]);
                }

        }
        else{              
            // echo json_encode(['notsame img']);                //if update image name
            //WHILE UPDATE IMAGE
            $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/Doctor_search/' . $image_path;
            // Delete the image file if it exists
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }


            $fileTmpPath = $_FILES['images']['tmp_name'];
            $fileName = $_FILES['images']['name'];
            $fileSize = $_FILES['images']['size'];
            $fileType = $_FILES['images']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        
               // Specify the upload directory
               $uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/Doctor_search/uploads/';
               $dest_path = $uploadFileDir . $newFileName;
        
               if (move_uploaded_file($fileTmpPath, $dest_path)) {
                   $imagePath = "uploads/" . $newFileName;  // Store relative path
               } else {
                   echo json_encode(['error' => 'Failed to move uploaded file.']);
                   return;
               }

               $sql = "SELECT gallery_image_paths FROM registration_form WHERE Sno = '$id'";
               $result = $conn->query($sql);
   
               if ($result->num_rows > 0) {
                   $row = $result->fetch_assoc();
                   $existingGalleryPathsString = $row['gallery_image_paths']; // Existing paths
                   
                   // Convert the string to an array (assuming it's a comma-separated string)
                   $existingGalleryPaths = explode(",", $existingGalleryPathsString);
               } else {
                   $existingGalleryPaths = [];  // No existing paths
               }

                 if (isset($_FILES['galleryImages'])) {
                    $galleryImages = $_FILES['galleryImages'];
                    $fileCount = count($galleryImages['name']); // Number of files uploaded
                    $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/Doctor_search/gallery_img/'; // Gallery upload directory
                    $galleryImagePaths = []; 
                   
            
                    for ($i = 0; $i < $fileCount; $i++) {
                        $fileName = $galleryImages['name'][$i];
                        $fileTmpName = $galleryImages['tmp_name'][$i];
                        $fileError = $galleryImages['error'][$i];
            
                        if ($fileError === 0) {
                            $newFileName = uniqid('', true) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
                            $dest_path = $uploadDirectory . $newFileName;
            
                            if (move_uploaded_file($fileTmpName, $dest_path)) {
                                $galleryImagePaths[] = "gallery_img/" . $newFileName; // Store relative path
                            }
                        }
                    }
                }
                  
                $mergedGalleryPaths = array_merge($existingGalleryPaths, $galleryImagePaths);
                
             
                // Convert back to a comma-separated string for database storage
                $galleryImagePathsString = implode(",", $mergedGalleryPaths);
    
               $update_sql = "UPDATE registration_form SET 
               Name = '$name', 
                           Fathername = '$fathername', 
                           DOB = '$dateofbirth' , Gender = '$gender' ,
                           Phonenumber = '$phonenumber' , Email = '$email' ,
                           Address = '$address' , City = '$city' ,
                           State = '$state' , Qualification = '$qualification' ,
                           Specialization = '$specialization' , RegistrationNumber = '$registrationNumber' ,
                           Yearofregistration = '$yearofregistration',Employmenttype = '$employmenttype', 
                           Uprnnumber = '$uprnnumber', 
                           Universityname = '$universityname' , Stateofmedicine = '$stateofmedicine' ,
                           Yearofqualification = '$yearofqualification' ,image_name = '$fileName', image_path ='$imagePath',
                           gallery_image_paths = '$galleryImagePathsString',
                           Postgraduation='$postgraduatesJson'
                           WHERE  Sno = '$id' ";               
                   if ($conn->query($update_sql)) {
                       echo json_encode(['code'=>200,'message'=>'Record update successfully']);
                   } else {
                       echo json_encode(['error' => 'Error while update record ' . $conn->error]);
                   }
        }       
    }
}
// Function to handle DELETE requests
function handleDelete($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $Sno = isset($data['Sno']) ? $conn->real_escape_string($data['Sno']) : null;
    $image_path =  isset($data['image_path']) ? $conn->real_escape_string($data['image_path']) : null;
    if (!$Sno) {
        echo json_encode(['error' => 'Sno is required for deletion']);
        return;
    }

    $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/Doctor_search/' . $image_path;
    // Delete the image file if it exists
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }


    $deleteQuery = "DELETE FROM registration_form WHERE Sno = '$Sno'";
    if ($conn->query($deleteQuery) === TRUE) {
        echo json_encode(['message' => "Record with Sno $Sno deleted successfully"]);
    } else {
        echo json_encode(['error' => "Error deleting record with Sno $Sno: " . $conn->error]);
    }
}

// Function to handle DELETE requests
function handleImgDelete($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $Sno = isset($data['Sno']) ? $conn->real_escape_string($data['Sno']) : null;
    $image_path =  isset($data['imageName']) ? $conn->real_escape_string($data['imageName']) : null;

    if (!$Sno || !$image_path) {
        echo json_encode(['error' => 'Missing required parameters']);
        exit;
    }

   $query = "SELECT gallery_image_paths FROM registration_form WHERE Sno = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $Sno);
    $stmt->execute();
    $stmt->bind_result($existingPaths);
    $stmt->fetch();
    $stmt->close();

    if (!$existingPaths) {
        echo json_encode(['error' => 'No images found for this record']);
        exit;
    }
    $imageArray = explode(",", $existingPaths);
        $updatedImageArray = array_filter($imageArray, function ($img) use ($image_path) {
            return trim($img) !== trim($image_path);
        });
        $newImagePaths = implode(",", $updatedImageArray);

        $updateQuery = "UPDATE registration_form SET gallery_image_paths = ? WHERE Sno = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("si", $newImagePaths, $Sno);
        $updateStmt->execute();
        $updateStmt->close();

                // 🔹 Step 4: Delete the actual image file
        $imagePath = $_SERVER['DOCUMENT_ROOT'] . "/Doctor_search/" . $image_path;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        echo json_encode(['message' => "Image deleted successfully"]);
}

?>