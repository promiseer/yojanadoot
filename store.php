<?php
$servername = "localhost";
$username = "root";
$password = 'P@$$ion@123';
$database = "site";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Failed to connect with the database: " . mysqli_connect_error());
}

$successMessage = "";
$errorUpload = false;

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $yid = isset($_POST["uid"]) ? mysqli_real_escape_string($conn, $_POST["uid"]) : "";
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
        $sql = "INSERT INTO iddata (yid, name, mobile_no, karyakshetra, bloodgroup, dob, aadhar, photo, aadhar_front, aadhar_back, voting_front, voting_back) VALUES ('$yid', '$name', '$mobile', '$karyakshetraValue', '$bloodgroup', '$dob', '$aadhar', '$photoPath', '$aadharFrontPath', '$aadharBackPath', '$votingFrontPath', '$votingBackPath')";
        if (mysqli_query($conn, $sql)) {
            $successMessage = "आपण योजनादूत ओळखपत्रासाठी यशस्वीरित्या नोंदणी केली आहे. लवकरच आपल्याला आपले ओळखपत्र प्राप्त होईल";
        } else {
            echo "कृपया पुन्हा प्रयत्न करा: " . mysqli_error($conn);
        }
    } else {
        echo "कृपया अपलोड संबंधित त्रुटीत सुधार करा.";
    }
}

function uploadFile($fileInputName, $targetDir, $userName)
{

    if (!isset($_FILES[$fileInputName]) || !is_uploaded_file($_FILES[$fileInputName]["tmp_name"])) {
        // File not uploaded or not available
        return null;
    }
    if ($_FILES[$fileInputName]=="photo") {
        echo $_FILES[$fileInputName];
    };

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


mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store and Success</title>
    <style>
        body {
            background: linear-gradient(rgba(255, 165, 0, 0.8), rgba(255, 69, 0, 0.8)), url('your-background-image.jpg');
            background-size: cover;
            margin: 0;
            padding: 0;
            color: black;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .success-container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        .go-to-home-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="success-container">
        <?php echo $successMessage; ?>
        <a href="index.php" class="go-to-home-button">Go to Homepage</a>
    </div>
</body>

</html>
