<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION["username"])) {
    die("Error: Not authorized.");
}

$username = $_SESSION["username"];
$userDir = "/home/WilsonWang/file_sharing_secure/uploads/$username";

if (!isset($_FILES["file"])) {
    die("Error: No file uploaded.");
}

$file = $_FILES["file"];
$fileName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "", basename($file["name"]));
$targetPath = "$userDir/$fileName";

if (move_uploaded_file($file["tmp_name"], $targetPath)) {
    chmod($targetPath, 0660);
    echo "File uploaded successfully!";
} else {
    echo "File upload failed.";
}
?>
