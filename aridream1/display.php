<?php
require_once 'sql.php';

displayHeader();
session_start();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Handle Delete
    if (isset($_POST['delete-btn'])) {
        $id = $_POST['id'];
        $result = deleteData($id);

        if (is_string($result)) {
            echo "ERROR: " . $result;
        } else if ($result) {
            header("Location: display.php");
        } else {
            echo "<div style='color:red;'>ERROR: Error Delete the dream.</div>";
        }
    }


    // Handle favorite/un-favorite
    if (isset($_POST['favorite'])) {
        $id = $_POST['id'];

        $result = toggleFavorite($id);

        if (is_string($result)) {
            echo "Error: " . $result;
        } else if ($result) {
            header("Location: display.php");
        } else {
            echo "<div style='color:red;'>Error: Please try again later.</div>";
        }
    }
    displayContentTable($username);
} else {
    header('Location: index.php');
}

exit();


function displayContentTable($username)
{

    $data = getImagesByUsername($username);

    echo "<div id='dream-table' class='max-w-4xl mx-auto p-4'>";

    if (is_string($data)) {
        echo "<p class='text-red-500 font-bold text-center'>ERROR: " . $data . "</p>";
    } else {

        if (count($data) === 0) {
            echo "<p class='italic text-center'>(Empty Content)</p>";
            echo "</div>";
            echo "<div class='flex justify-center mt-8'>";
            echo "<button class='bg-rose-500 text-white font-bold py-2 px-4 rounded hover:bg-rose-600'>";
            echo "<a style='text-decoration: none' href='index.php'>Add More Dreams</a>";
            echo "</button>";
            echo "</div>";
        } else {

            $dreams = count($data);

            echo "<h1 class='p-2 mb-2 text-3xl font-bold text-center text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-rose-500 to-yellow-500'>Hello $username! You have $dreams dreams.</h1>";

            echo "<div class='flex flex-wrap justify-center'>";

            foreach ($data as $row) {
                $id = $row['id'];
                $poem = $row['poem'];

                $is_favorite = $row['favorite'];

                echo "<div class='w-1/3 p-2'>";
                echo "<div class='bg-rose-100 hover:bg-gray-200 shadow-xl rounded-lg'>";
                echo "<div class='p-2 font-bold text-lg text-center'>$poem</div>";
                echo "<img alt=$poem class='w-64 h-64 object-cover mx-auto' src='data:image/jpeg;base64," . base64_encode($row['image']) . "'/>";

                echo "<div class='p-4 flex justify-between'>";
                // Favorite button
                echo "<form action='' method='post'>";
                echo "<input type='hidden' name='id' value='$id'>";
                echo "<input type='hidden' name='favorite'>";
                if (!$is_favorite) {
                    echo '<button type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 icon" onclick="toggleFill(this)">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                    </svg> 
                    </button>';
                    echo "</form>";
                } else {
                    echo '<button type="submit">';
                    echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">';
                    echo '<path fill="rgb(243, 58, 106)" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>';
                    echo '</svg>';
                    echo '</button>';
                    echo "</form>";
                }

                echo "<form action='' method='post'>";
                echo "<input type='hidden' name='id' value='$id'>";
                echo "<input class='bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded text-sm' type='submit' name='delete-btn' value='Delete'>";
                echo "</form>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }

            echo "</div>";
            echo "<div class='flex justify-center mt-8'>";
            echo "<button class='bg-rose-500 text-white font-bold py-2 px-4 rounded hover:bg-rose-600'>";
            echo "<a style='text-decoration: none' href='index.php'>Add More Dreams</a>";
            echo "</button>";
            echo "</div>";
        }
    }

    echo "</div>";
}

function displayHeader()
{
    echo "<html>
    <head>
        <title>Ari's Dream</title>
        <script src='https://cdn.tailwindcss.com'></script>
    </head>
    <body class='bg-fuchsia-100'>";
}
