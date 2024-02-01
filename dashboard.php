<?php
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
// Handle GET request for fetching karyakshetra
if ($_SERVER["REQUEST_METHOD"] == "GET")
    // Get mobile number from the AJAX request
    $mobileNumber = $_GET['mobilenumber'];

// Prepare and execute the SQL query
$sql = "SELECT karyakshetra, ac  FROM kalyanpc WHERE mobile_no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $mobileNumber);
$stmt->execute();
$stmt->bind_result($district, $ac);
$stmt->fetch();
// Fetch the result
// if ($stmt->fetch()) {
//     echo $district; // Return the district
// } else {
//     echo "District not found";
// }


// Close the connection
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="mr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>आपली माहिती</title>
    <style>
        body {
            /* background-image: url('img2.jpeg'); */
            background-size: 100% 100%;
            font-family: 'Arial', sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;

        }

        .container {
            background-color: rgba(87, 83, 83, 0.6);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(240, 224, 224, 0.1);
            margin: 50px auto;

            max-width: 500px;
        }

        .form-group-row {
            margin-bottom: 1px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-row input[type="text"]::placeholder {
            background-color: transparent;
            /* Replace with your desired background color */
            padding: 5px;
            /* Optional: Add padding for better visual appearance */
            color: #000000;
            /* Replace with your desired color code for black */
            font-weight: bold;
        }

        .form-row input[type="tel"]::placeholder {
            color: #000000;
            /* Replace with your desired color code for black */
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #e3dbdb;
            border-radius: 4px;
            background-color: transparent;
            font-weight: bold;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: rgb(253, 250, 250);
        }

        video {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            white-space: nowrap;
            margin-top: 00px;
            margin-right: 300px;

        }


        .checkbox-label {
            margin-left: 10px;
            max-width: calc(100% -30px);
            /* Adjust the max-width as needed */
            color: rgb(15, 14, 14);
        }

        .preview-button {
            background-color: #3498db;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;

        }

        #previewSection {
            margin-top: 20px;
            padding: 15px;
            background-color: rgba(158, 150, 150, 0.6);
            border-radius: 5px;
            display: none;
        }

        #previewSection h2 {
            color: #000000fa;
        }

        #uploadedImages {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        #uploadedImages img {
            max-width: 100px;
            max-height: 100px;
        }

        #idCardSection {
            margin-top: 20px;
            padding: 15px;
            background-color: #ecf0f1;
            border-radius: 5px;
            display: none;
        }

        #idCardSection h2 {
            color: #3498db;
        }

        #idCard {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        #idCard img {
            max-width: 150px;
            max-height: 150px;
        }

        #idCard p {
            text-align: center;
        }

        #submitBtn {
            background-color: #2ecc71;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        #webcamPopup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border: 5px solid #ccc;
            z-index: 1000;
        }

        .loader {
            position: absolute;
            left: 0;
            right: 0;
            margin: 0 auto;
            bottom: 50%;
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 2s linear infinite;
        }

        .object-fit-contain {
            object-fit: contain;
            /* Adjust this property based on your requirements */
            width: 100%;
            height: 100%;
        }

        .video-container {
            position: relative;
            width: 100%;
            padding-top: 56.25%;
            /* Adjust this value to maintain the aspect ratio (16:9 in this case) */
        }

        #webcam {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media only screen and (max-width: 767px) {
            body {
                background-image: url('img.jpeg');
                /* Mobile background image */
            }
        }
    </style>
    <script>
        function showPreview() {
            console.log('showPreview function called', mobile);
            var acceptTermsCheckbox = document.getElementById("acceptTerms");
            var previewSection = document.getElementById("previewSection");

            if (acceptTermsCheckbox.checked) {
                // Checkbox is checked, proceed to show preview

                // Retrieve values from form fields 
                var name = document.getElementById("name").value;
                var mobile = document.getElementById("mobile").value;
                var karyakshetra = document.getElementById("karyakshetra").value;
                var bloodgroup = document.getElementById("bloodgroup").value;
                var dob = document.getElementById("dob").value;
                var aadhar = document.getElementById("aadhar").value;

                // Display values in the preview section
                document.getElementById("previewName").innerText = "नाव: " + name;
                document.getElementById("previewMobile").innerText = "मोबाईल क्रमांक: " + mobile;
                document.getElementById("previewKaryakshetra").innerText = "कार्यक्षेत्र (जिल्हा): " + karyakshetra;
                document.getElementById("previewBloodgroup").innerText = "रक्तगट: " + bloodgroup;
                document.getElementById("previewDob").innerText = "जन्म दिनांक: " + dob;
                document.getElementById("previewAadhar").innerText = "आधार कार्ड क्रमांक: " + aadhar;

                // Display uploaded images in the preview section
                var aadharFrontImg = document.getElementById("aadharFront");
                var aadharBackImg = document.getElementById("aadharBack");
                var votingFrontImg = document.getElementById("votingFront");
                var votingBackImg = document.getElementById("votingBack");

                var uploadedImagesContainer = document.getElementById("uploadedImages");
                uploadedImagesContainer.innerHTML = ""; // Clear existing images

                function displayImage(fileInput) {
                    if (fileInput.files.length > 0) {

                        var previewImage = document.createElement("img");
                        previewImage.src = URL.createObjectURL(fileInput.files[0]);
                        uploadedImagesContainer.appendChild(previewImage);
                    }


                }
                if (profileImage.length) {
                    let previewImage = document.createElement("img");
                    previewImage.src = URL.createObjectURL(profileImage[0]);
                    uploadedImagesContainer.appendChild(previewImage);
                }
                // displayImage(document.getElementById("photo")); // Display uploaded photo

                displayImage(aadharFrontImg);
                displayImage(aadharBackImg);
                displayImage(votingFrontImg);
                displayImage(votingBackImg);

                // Show the preview section
                previewSection.style.display = "block";
                // runDetectFaceScript(imagePath);
            } else {
                // Checkbox is not checked, hide preview
                previewSection.style.display = "none";
            }
        }

        function fetchKaryakshetra(mobileNumber) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        var karyakshetraInput = document.getElementById("karyakshetra");
                        karyakshetraInput.value = xhr.responseText; // Update the value
                    } else {
                        console.error("Error fetching karyakshetra: " + xhr.status);
                    }
                }
            };

            xhr.open("GET", "your_php_file.php?mobilenumber=" + mobileNumber, true);
            xhr.send();

        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" />

    <script src="faceDetection.js" defer></script>
    <!-- Import TensorFlow.js library -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs/dist/tf.min.js" type="text/javascript"></script>
    <!-- Load the coco-ssd model to use to recognize things in images -->
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/blazeface"></script>


</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 ">
                <!-- <div class="card"> -->
                <form action="store.php" method="POST" enctype="multipart/form-data" class="needs-validation">



                    <div class="form-group-row m-3">

                        <label for="photo">स्वत:चा फोटो: </label>

                        <div class="form-row mb-3">
                            <input type="file" class="form-control-m my-2  w-30 " id="photo" name="photo" label="upload" accept="image/*" required>
                            <button id="webcamButton" type="button" class="webcamButton btn btn-secondary btn-m my-2 w-100" data-bs-toggle="modal" data-bs-target="#exampleModal"> <i class="bi bi-camera"></i> कॅमेरा</button>

                        </div>

                        <div class="form-row align-items-center justify-content-center">
                            <div class="image-area m-4"><img id="imagePreview" src="#" alt="" class="img-fluid rounded shadow-sm mx-auto w-50 h-30"></div>

                        </div>
                        <button type="button" id="uploadPhoto" class="btn btn-primary w-100"> <i class="bi bi-upload"></i> अपलोड करा</button>








                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <div class="video-container" id="liveView">
                                            <video id="webcam" class="object-fit-contain" autoplay muted></video>
                                        </div>
                                        <div class="loader" id="loader"></div>
                                    </div>
                                    <div class="modal-footer justify-content-center">
                                        <button type="button" class="btn btn-danger " data-bs-dismiss="modal" id="closePopup">Close</button>
                                        <button type="button" class="btn btn-primary" id="capture">Capture</button>
                                        <button type="button" class="btn btn-success" id="save" disabled>save</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="form-group-row mb-3">
                        <label class="form-label" for="name">नाव:</label>
                        <input type="text" id="name" class="form-control-m" name="name" placeholder="संपूर्ण नाव" required>
                    </div>


                    <div class="form-group-row mb-3">
                        <label for="mobile">&#9743; मोबाईल क्रमांक:</label>
                        <input required class="form-control-m" type="tel" id="mobile" name="mobile" placeholder="मोबाईल क्रमांक">
                    </div>

                    <div class="form-group-row mb-3">
                        <label for="karyakshetra">कार्यक्षेत्र (जिल्हा):</label>
                        <input required type="text" class="form-control-m" id="karyakshetra" name="karyakshetra" placeholder="तुमचं कार्यक्षेत्र" value='<?php echo $district ?>' disabled>
                    </div>

                    <div class="form-group-row mb-3">
                        <label for="vidhansabha">विधानसभा:</label>
                        <input required type="text" class="form-control-m" id="ac" name="ac" placeholder="तुमचं विधानसभा" value='<?php echo $ac ?>' disabled>
                    </div>

                    <div class="form-group-row mb-3">
                        <label for="bloodgroup">रक्तगट:</label>
                        <select id="bloodgroup" name="bloodgroup">
                            <option value="" disabled selected>रक्तगट निवडा..</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>


                    <div class="form-group-row mb-3">
                        <label for="dob">जन्म दिनांक :</label>
                        <input required class="form-control-m" type="date" id="dob" name="dob" placeholder="DD-MM-YYYY" pattern="\d{2}-\d{2}-\d{4}" title="Enter a date in the format DD-MM-YYYY">
                    </div>

                    <div class="form-group-row mb-3">
                        <label for="aadhar">आधार कार्ड क्रमांक:</label>
                        <input required class="form-control-m" type="text" id="aadhar" name="aadhar" placeholder="आधार कार्ड क्रमांक">

                    </div>

                    <div class="form-group-row mb-3">
                        <label for="aadharFront">आधार कार्ड फोटो अपलोड करा (पुढील बाजू):</label>
                        <input required class="form-control-m" type="file" id="aadharFront" name="aadharFront" accept="image/*">
                    </div>

                    <div class="form-group-row mb-3">
                        <label for="aadharBack">आधार कार्ड फोटो अपलोड करा (मागील बाजू):</label>
                        <input required class="form-control-m" type="file" id="aadharBack" name="aadharBack" accept="image/*">
                    </div>

                    <div class="form-group-row mb-3">
                        <label for="votingFront">मतदान कार्ड फोटो अपलोड करा (पुढील बाजू):</label>
                        <input required class="form-control-m" type="file" id="votingFront" name="votingFront" accept="image/*">
                    </div>

                    <div class="form-group-row mb-5">
                        <label for="votingBack">मतदान कार्ड फोटो अपलोड करा (मागील बाजू):</label>
                        <input required class="form-control-m" type="file" id="votingBack" name="votingBack" accept="image/*">
                    </div>

                    <div class="form-group-row mb-2">
                        <label class="checkbox-label-m" for="acceptTerms">
                            मी सर्व <a href="termsandcondition.html" target="_blank">अटी आणि शर्ती</a> स्वीकारत आहे.
                            
                        </label>
                        <input class="form-control-m" type="checkbox" id="acceptTerms" name="acceptTerms">

                       
                    </div>



                    <button type="button" class="preview-button" onclick="showPreview()">PREVIEW</button>

                    <div id="previewSection">
                        <h2>दिलेली माहिती तपासा...</h2>
                        <p id="previewphoto"></p>
                        <p id="previewName"></p>
                        <p id="previewMobile"></p>
                        <p id="previewKaryakshetra"></p>
                        <p id="previewBloodgroup"></p>
                        <p id="previewDob"></p>
                        <p id="previewAadhar"></p>
                        <div id="uploadedImages"></div>
                        <button type="submit" id="submitBtn" onclick="store.php">समावेश करा.</button>
                    </div>
                </form>
                <!-- </div> -->
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>

</html>