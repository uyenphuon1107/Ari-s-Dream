<?php
require_once 'sql.php';

displayHeader();

session_start();

// Display add form or navigate to login page
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_SESSION['username'])) {
        displayAddForm();
    } else {
        header("Location: login.php");
    }
}

// Handle logout request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['username']) && isset($_POST['isLoggedOut'])) {
    // Unset all session variables
    $_SESSION = array();
    // Destroy the session 
    session_destroy();
    // Redirect to home page
    header("Location: index.php");
}


// Handle file upload request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
    displayHeader();

    $username = $_SESSION['username'];
    $poem = $_POST['name'];
    $maxSize = 10485760; // 10MB
    $files = $_FILES['files'];

    if ($poem === '') {
        displayAddForm("Poem must be filled");
        exit();
    }

    for ($i = 0; $i < count($files['name']); $i++) {
        $imageName = $files['name'][$i];
        $imagePath = $files['tmp_name'][$i];
        $imageSize = $files['size'][$i];
        $extension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        if ($extension === 'jpeg' || $extension === 'jpg') {
            if ($imageSize <= $maxSize) {

                $binaryImage = file_get_contents($imagePath); // reading binary data

                $result = storeData($poem, $binaryImage, $username);

                if ($result) {
                    echo "Data was stored";
                } else if (!$result) {
                    // affected row <= 0
                    echo "Something went wrong! No data was stored";
                    exit();
                } else {
                    echo "ERROR: " . $result;
                    exit();
                }
            } else {
                echo "<div style='color:red;'>ERROR! File size must not be more than 10MB.</div>";
                exit();
            }
        } else {
            displayAddForm("Invalid Image Format");
            exit();
        }
    }
    // data was stored. Redirect to the another page.
    header('Location: display.php');
}

echo "</body></html>";
exit();


function displayHeader()
{
    echo <<<_HTML
        <html>
        <head>
            <title>Ari's Dream</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body>
    _HTML;
}


function displayLogout()
{
    echo <<<HTML
    <form class='mx-[20%]' action='' method='post' enctype='multipart/form-data'>
        <input type='hidden' name='isLoggedOut'>
        <br>
        <input 
        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded"
        type="submit" 
        value="Logout">
    </form>
    HTML;
}

function displayAddForm($error = null)
{
    $username = $_SESSION['username'];

    echo "<h1 class='text-3xl font-bold text-center'>Hello $username! Create your dreams here</h1>";
    echo <<<HTML
        <style>
            .box{
                background: url(clouds.png);
                background-color: white;
                padding-top: 100px;
            }
        </style> 
    <body class = 'box'>
        <form class='mx-[20%]' method='post' action='' enctype='multipart/form-data'>
        <div class="flex flex-col">
            <label class="font-bold" for="name">Poem</label>
            <input class="border border-gray-400 p-2 rounded" type="text" id="name" name="name">
        </div>
        <br>
        
        <div class="flex flex-col">
            <label class="font-bold" for="files">Upload Image(s)</label>  
            <input class="border border-gray-400 p-2 rounded" type='file' id="files" name='files[]' multiple>
        </div>
        <br>
        <div>
            <input class="bg-sky-500 text-white py-2 px-4 rounded hover:bg-blue-600" type='submit' value='Add'>
            <a href="display.php" class='bg-sky-500 text-white py-2 px-4 rounded hover:bg-blue-600'>Show Dreams</a></button>
        </div>
    </body>
    HTML;
    if (isset($error)) {
        echo "<div style='color:red;'>$error</div>";
    }
    echo "</form>";
    displayLogout();
}
