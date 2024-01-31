<?php
// Assuming you have already connected to the database
$servername = "localhost";
$username = "root";
$password = 'P@$$ion@123';
$database = "site";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Failed to connect with database" . mysqli_connect_error());
}

// Assuming you have received mobile number and OTP from a form
$providedMobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
$providedOTP = isset($_POST['otp']) ? $_POST['otp'] : '';

$providedMobile = mysqli_real_escape_string($conn, $providedMobile);
$providedOTP = mysqli_real_escape_string($conn, $providedOTP);

// Check if both mobile and OTP are provided
if (!empty($providedMobile) && !empty($providedOTP)) {
    // Fetch the user record based on the provided mobile number and OTP
    $query = "SELECT * FROM kalyanpc WHERE `mobile_no` = '$providedMobile' AND otp = '$providedOTP'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $rowCount = mysqli_num_rows($result);

        if ($rowCount == 1) {
            // Mobile number and OTP match, login successful
            // Start the session if needed

            // Redirect to the dashboard
            header("Location: dashboard.html?mobilenumber=".$providedMobile);
            exit();
        } else {
            // Mobile number and/or OTP do not match, show error
            $error_message = "अवैध मोबाइल नंबर किंवा OTP";
        }
    } else {
        // Error in the SQL query
        $error_message = "Error in the SQL query: " . mysqli_error($conn);
    }
} else {
    // Handle the case where mobile or OTP is not provided
    $error_message = "मोबाइल नंबर आणि OTP आवश्यक आहे.";
}

// Function to read and update visitor count
function updateVisitorCount()
{
    $countFile = 'visitor_count.txt';

    // Read current count
    $count = (file_exists($countFile)) ? (int)file_get_contents($countFile) : 0;

    // Increment count
    $count++;

    // Write updated count back to the file
    file_put_contents($countFile, $count);

    return $count;
}

// Update the count when the page is accessed
$visitorCount = updateVisitorCount();
?>

<!DOCTYPE html>
<html lang="mr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>मोबाइल नंबर आणि संकेतशब्द</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Baloo&display=swap">

    <style>
        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-image: url('img2.jpeg');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
            text-shadow: 2px 2px 2px black;
        }

        .container {
            background-color: rgba(37, 33, 33, 0.6);
            padding: 15px;
            border-radius: 10px;
            display: flex;
            width: 230px;
            position: fixed;
            bottom: 70px;
            left: 50px;
            flex-direction: column;
            align-items: center;
            border: 2px solid white; /* Add white border */
        }

        .submit-button {
            width: 50%;
            padding: 10px 20px;
            margin-bottom: 4px;
            background-color: transparent; /* Make input background transparent */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: 2px solid white;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .submit-button:hover {
            background-color: #ff8c00;
            transform: scale(1.05); /* Scale up the button on hover */
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: white;
        }

        input[type="text"],
        input[type="password"] {
            color: white;
            width: 90%;
            padding: 5px;
            margin-bottom: 10px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            margin-top: 3px;
            margin-bottom: 15px;
            /* Add a border-bottom to the inputs for a subtle separation */
            border-bottom: 2px solid #fff;
            background: transparent; /* Make input background transparent */
        }

        @media only screen and (max-width: 767px) {
            body {
                background-image: url('img.jpeg');
                /* Mobile background image */
            }

            .container {
                width: 250px;
                /* Adjust the width for mobile */
                padding: 35px;
                /* Adjust the padding for mobile */
                bottom: 150px;
                /* Adjust the bottom position for mobile */
                left: 20px;
                /* Adjust the left position for mobile */
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <?php if (isset($error_message)) : ?>
            <p style="color: white;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="index.php" method="post">
            <label for="mobile">मोबाईल क्रमांक</label>
            <input type="text" id="mobile" name="mobile" placeholder="मोबाईल क्रमांक..." required>

            <label for="otp">OTP</label>
            <input type="password" id="otp" name="otp" placeholder="OTP प्रविष्ट करा." required>

            <button type="submit" class="submit-button">Login</button>
        </form>
    </div>

    <!-- Display the visitor count at the bottom -->
    <div id="liveCounter" style="position: fixed; bottom: 10px; left: 10%; color: black;">
        Loading...
    </div>

    <!-- JavaScript for live counter update -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        function updateLiveCounter() {
            $.ajax({
                url: 'update_counter.php',
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                    // Update the content of the element with the new visitor count
                    $('#liveCounter').text('Total Visitors: ' + response.visitorCount);
                },
                error: function () {
                    console.error('Failed to fetch visitor count');
                }
            });
        }

        // Update the live counter every 5 seconds
        setInterval(updateLiveCounter, 5000);

        // Initial update when the page loads
        updateLiveCounter();
    </script>
</body>

</html>
