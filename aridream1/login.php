<?php

require_once 'sql.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    showLoginForm();
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    loginHandler($username, $password);
}

function showLoginForm($error = null)
{
    echo <<<HTML
    <html>
    
    <head>
        <title>Login</title>
        <link rel='stylesheet' href='style.css'>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <style>
        body{
            background: url(clouds.png);
            background-color: white;
            margin: 0;
        }
    </style>
    <body>
        <div class="flex items-center justify-center h-screen">
            <div class="bg-slate-200 p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-medium mb-6 text-gray-900">Login</h2>
                <form method="post" action="" enctype='multipart/form-data'>
                    <div class="space-y-3">
                        <div>
                            <label class="text-gray-700" for="username">Username</label>
                            <input class="block border border-gray-300 p-2 rounded-lg w-full" type="text" name="username" placeholder="Username" />
                        </div>
                        <div>
                            <label class="text-gray-700" for="password">Password</label>
                            <input class="block border border-gray-300 p-2 rounded-lg w-full" type="password" name="password" placeholder="Password" />
                        </div>
                        <button class="block bg-blue-500 hover:bg-blue-600 active:bg-blue-700 focus:bg-blue-700 text-white font-bold p-2 rounded-lg w-full mt-4" type="submit">
                            Sign In
                        </button>
    
                    </div>
                    <div class="mt-4 text-xs text-center text-gray-500">
                        Don't have an account yet? <a href="signup.php" class="text-blue-600 hover:underline">Sign up</a>
                    </div>
    HTML;

    if (isset($error)) {
        echo "<div style='color:red;'>$error</div>";
    }

    echo "</form></div></div></body></html>";
}

function loginHandler($username, $password)
{
    $result = validate($username, $password);
    if ($result === "") {
        // login
        $login = login($username, $password);

        if (is_string($login)) {
            showLoginForm("ERROR: " . $login);
        } else if ($login) {
            $_SESSION['username'] = $username;
            header("Location: index.php");
        } else {
            showLoginForm("Invalid Credentials");
        }
    } else {
        showLoginForm($result);
    }
}

function validate($username, $password)
{
    $allowed = '/^[a-zA-Z0-9_]*$/';
    $fail = "";

    if ($username === '' || $password === '') {
        $fail .= "<div style='color:red;'>Username and password must be filled</div>";
    } else if (strlen($username) > 255 || strlen($password) > 255) {
        $fail .= "<div style='color:red;'>Username/password is too long</div>";
    } else if (!preg_match($allowed, $username)) {
        $fail .= "<div style='color:red;'>Invalid characters in username</div>";
    }

    return $fail;
}
