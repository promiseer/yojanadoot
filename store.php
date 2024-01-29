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

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $mobile = mysqli_real_escape_string($conn, $_POST["mobile"]);

    // Check if 'karyakshetra' key exists in $_POST array
    $karyakshetra = isset($_POST["karyakshetra"]) ? mysqli_real_escape_string($conn, $_POST["karyakshetra"]) : "";

    $bloodgroup = mysqli_real_escape_string($conn, $_POST["bloodgroup"]);
    $dob = mysqli_real_escape_string($conn, $_POST["dob"]);
    $aadhar = mysqli_real_escape_string($conn, $_POST["aadhar"]);

    // Retrieve uid and ac from the kalyanpc table based on mobile number
    $uidAndKaryakshetraQuery = "SELECT uid, karyakshetra, ac FROM kalyanpc WHERE mobile_no = '$mobile'";
    $uidAndKaryakshetraResult = mysqli_query($conn, $uidAndKaryakshetraQuery);

    if ($uidAndKaryakshetraResult) {
        $row = mysqli_fetch_assoc($uidAndKaryakshetraResult);

        if ($row !== null && array_key_exists('uid', $row) && array_key_exists('karyakshetra', $row)) {
            $uid = $row['uid'];
            $karyakshetra = $row['karyakshetra'];
            $ac = $row['ac'];

            // Handle file uploads
            $photoPath = uploadFile("photo", "uploads/self/", $uid);
            $aadharFrontPath = uploadFile("aadharFront", "uploads/", $name);
            $aadharBackPath = uploadFile("aadharBack", "uploads/", $name);
            $votingFrontPath = uploadFile("votingFront", "uploads/", $name);
            $votingBackPath = uploadFile("votingBack", "uploads/", $name);

            // Proceed with the SQL query and other logic
            $sql = "INSERT INTO iddata (uid, name, mobile_no, karyakshetra, ac, bloodgroup, dob, aadhar, photo, aadhar_front, aadhar_back, voting_front, voting_back) VALUES ('$uid', '$name', '$mobile', '$karyakshetra', '$ac', '$bloodgroup', '$dob', '$aadhar', '$photoPath', '$aadharFrontPath', '$aadharBackPath', '$votingFrontPath', '$votingBackPath')";

            if (mysqli_query($conn, $sql)) {
                $successMessage = "आपण योजनादूत ओळखपत्रासाठी यशस्वीरित्या नोंदणी केली आहे. लवकरच आपल्याला आपले ओळखपत्र प्राप्त होईल";
            } else {
                echo "कृपया पुन्हा प्रयत्न करा: " . mysqli_error($conn);
            }
        } else {
            echo "No matching record found for mobile_no: $mobile";
        }
    }
}

function uploadFile($fileInputName, $targetDir, $userName)
{
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
    }

    // Create a resized copy of the image
    // $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
    // $sourceImage = imagecreatefromjpeg($_FILES[$fileInputName]["tmp_name"]);

    // imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    // Save the resized image as a JPEG with a specified quality (adjust as needed)
    // $compressionQuality = 80; // 0 to 100
    // imagejpeg($resizedImage, $targetFile, $compressionQuality);

    // // Free up memory
    // imagedestroy($resizedImage);
    // imagedestroy($sourceImage);

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





<!-- <?php
        // Assuming you have a database connection
        $servername = "localhost";
        $username = "root";
        $password = 'P@$$ion@123';
        $database = "site";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $database);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Retrieve form data
            $name = isset($_POST['name']) ? $_POST['name'] : '';
            $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
            $karyakshetra = isset($_POST['karyakshetra']) ? $_POST['karyakshetra'] : '';
            $bloodgroup = isset($_POST['bloodgroup']) ? $_POST['bloodgroup'] : '';
            $dob = isset($_POST['dob']) ? $_POST['dob'] : '';
            $aadhar = isset($_POST['aadhar']) ? $_POST['aadhar'] : '';
            // Here, you can use the form data to generate the ID card and save it to a file

            // For example, create a string containing the ID card information
            $idCardInfo = "नाव: $name\nमोबाईल क्रमांक: $mobile\nकार्यक्षेत्र (जिल्हा): $karyakshetra\nरक्तगट: $bloodgroup\nजन्म दिनांक: $dob\nआधार कार्ड क्रमांक: $aadhar";

            // Create an image resource from the existing "doot" ID card image
            $idCardImage = imagecreatefromjpeg("doot.jpeg");

            // Set font color to black
            $fontColor = imagecolorallocate($idCardImage, 0, 0, 0);

            // Set font size
            $fontSize = 16;

            // Set the position to start drawing text on the image
            $x = 50;
            $y = 150;

            // Break the ID card information into lines and draw on the image
            $idCardInfoLines = explode("\n", $idCardInfo);
            foreach ($idCardInfoLines as $line) {
                imagettftext($idCardImage, $fontSize, 0, $x, $y, $fontColor, 'path/to/font.ttf', $line);
                $y += 30; // Increase the Y position for the next line
            }

            // Save the generated ID card image to a file
            imagejpeg($idCardImage, "path/to/generated_id_card.jpg");

            // Free up memory
            imagedestroy($idCardImage);

            // Optionally, you can store the file path or other information in the database

            // Close the database connection
            $conn->close();
        }
        ?> -->