<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Secure path to users.txt
$users_file = "/home/WilsonWang/file_sharing_secure/users.txt";

// Ensure users.txt exists
if (!file_exists($users_file)) {
    touch($users_file);
    chmod($users_file, 640);
}

$error = "";

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $username = trim($_POST["username"]);
    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

    // Read valid usernames
    $users = file($users_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($users === false) {
        die("Error: Failed to read users file.");
    }

    if (in_array($username, $users)) {
        $_SESSION["username"] = $username;

        // Ensure user directory exists
        $userDir = "/home/WilsonWang/file_sharing_secure/uploads/$username";
        if (!is_dir($userDir)) {
            mkdir($userDir, 0770, true);
            chown($userDir, "apache");
        }

        header("Location: home.php");
        exit();
    } else {
        $error = "Invalid username. Try again.";
    }
}

// Handle new user registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $newUser = trim($_POST["new_username"]);
    $newUser = htmlspecialchars($newUser, ENT_QUOTES, 'UTF-8');

    // Validate username format (only letters, numbers, and underscores)
    if (!preg_match("/^[a-zA-Z0-9_]+$/", $newUser)) {
        $error = "Username can only contain letters, numbers, and underscores.";
    } else {
        $users = file($users_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Check if username already exists
        if (in_array($newUser, $users)) {
            $error = "Username already exists. Choose another.";
        } else {
            // Add new user securely
            file_put_contents($users_file, $newUser . PHP_EOL, FILE_APPEND | LOCK_EX);
            mkdir("/home/WilsonWang/file_sharing_secure/uploads/$newUser", 0770, true);
            chown("/home/WilsonWang/file_sharing_secure/uploads/$newUser", "apache");

            $_SESSION["username"] = $newUser;
            header("Location: home.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - File Sharing</title>
</head>
<body>
    <h2>Login</h2>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>
        <input type="submit" name="login" value="Login">
    </form>

    <h2>Create New Account</h2>
    <form method="POST">
        <label>New Username:</label>
        <input type="text" name="new_username" required>
        <input type="submit" name="register" value="Register">
    </form>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
</body>
</html>
