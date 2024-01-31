<?php
$servername = "localhost";
$username = "root";
$password = '';
$database = "site";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Failed to connect with the database: " . mysqli_connect_error());
}

$successMessage = "";
$errorUpload = false;

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $yid = isset($_POST["uid"]) ? mysqli_real_escape_string($conn, $_POST["uid"]) : "1";
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $mobile = mysqli_real_escape_string($conn, $_POST["mobile"]);

    // Initialize additional form fields
    $bloodgroup = isset($_POST["bloodgroup"]) ? mysqli_real_escape_string($conn, $_POST["bloodgroup"]) : "";
    $dob = isset($_POST["dob"]) ? mysqli_real_escape_string($conn, $_POST["dob"]) : "";
    $aadhar = isset($_POST["aadhar"]) ? mysqli_real_escape_string($conn, $_POST["aadhar"]) : "";

    // Determine the value for karyakshetra based on the selected option
    $karyakshetraValue = isset($_POST["gavacheNaav"]) && $_POST["gavacheNaav"] == "yes" ? $_POST["gavacheNaav"] : "";

    // Handle file uploads
    $photoPath = uploadFile("photo", "uploads/self/", $name);
    $aadharFrontPath = uploadFile("aadharFront", "uploads/", $name);
    $aadharBackPath = uploadFile("aadharBack", "uploads/", $name);
    $votingFrontPath = uploadFile("votingFront", "uploads/", $name);
    $votingBackPath = uploadFile("votingBack", "uploads/", $name);

    // Check for file upload errors
    if ($photoPath === null || $aadharFrontPath === null || $aadharBackPath === null || $votingFrontPath === null || $votingBackPath === null) {
        $errorUpload = true;
    }

    if (!$errorUpload) {
        // Proceed with the SQL query and other logic
        $sql = "INSERT INTO iddata (uid, name, mobile_no, karyakshetra, bloodgroup, dob, aadhar, photo, aadhar_front, aadhar_back, voting_front, voting_back) VALUES ('$yid', '$name', '$mobile', '$karyakshetraValue', '$bloodgroup', '$dob', '$aadhar', '$photoPath', '$aadharFrontPath', '$aadharBackPath', '$votingFrontPath', '$votingBackPath')";
        if (mysqli_query($conn, $sql)) {
            $successMessage = "आपण योजनादूत ओळखपत्रासाठी यशस्वीरित्या नोंदणी केली आहे. लवकरच आपल्याला आपले ओळखपत्र प्राप्त होईल";
            $response = array("success" => true, "message" => $successMessage);
        } else {
            $errorMessage = "कृपया अपलोड संबंधित त्रुटीत सुधार करा!.";
            $response = array("success" => false, "message" => $errorMessage);
        }
    } else {
        $errorMessage = "कृपया अपलोड संबंधित त्रुटीत सुधार करा!!!.";
        $response = array("success" => false, "message" => $errorMessage);
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}

function uploadFile($fileInputName, $targetDir, $userName)
{
    if (!isset($_FILES[$fileInputName]) || !is_uploaded_file($_FILES[$fileInputName]["tmp_name"])) {
        // File not uploaded or not available
        return null;
    }

    $originalFileName = basename($_FILES[$fileInputName]["name"]);
    $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

    // Use the user's name to construct the new filename
    $targetFileName = $userName . "_" . uniqid() . "." . $fileExtension;
    $targetFile = $targetDir . $targetFileName;

    // Get image dimensions
    list($width, $height) = getimagesize($_FILES[$fileInputName]["tmp_name"]);

    // Set a maximum width and height for resizing
    $maxWidth = 800;
    $maxHeight = 600;

    // Calculate the new dimensions
    $newWidth = $width;
    $newHeight = $height;

    if ($width > $maxWidth || $height > $maxHeight) {
        $aspectRatio = $width / $height;

        if ($width > $height) {
            $newWidth = $maxWidth;
            $newHeight = $maxWidth / $aspectRatio;
        } else {
            $newHeight = $maxHeight;
            $newWidth = $maxHeight * $aspectRatio;
        }

        // Create a resized copy of the image
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        $sourceImage = imagecreatefromjpeg($_FILES[$fileInputName]["tmp_name"]);

        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save the resized image as a JPEG with a specified quality (adjust as needed)
        $compressionQuality = 80; // 0 to 100
        $result = imagejpeg($resizedImage, $targetFile, $compressionQuality);

        // Free up memory
        imagedestroy($resizedImage);
        imagedestroy($sourceImage);

        if (!$result) {
            return null;
        }
    } else {
        // No need to resize, just move the uploaded file
        if (!move_uploaded_file($_FILES[$fileInputName]["tmp_name"], $targetFile)) {
            echo "कृपया अपलोड संबंधित त्रुटीत सुधार करा: " . $_FILES[$fileInputName]['error'];
            return null;
        }
    }

    return $targetFile;
}


mysqli_close($conn)
?>
