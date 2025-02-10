<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION["username"]) || !isset($_GET["file"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];
$userDir = "/home/WilsonWang/file_sharing_secure/uploads/$username";
$file = htmlspecialchars(basename($_GET["file"]), ENT_QUOTES, 'UTF-8');
$filePath = "$userDir/$file";

$allowed_types = ['txt', 'jpg', 'jpeg', 'png', 'gif', 'pdf'];
$fileExt = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

if (!file_exists($filePath) || !in_array($fileExt, $allowed_types)) {
    die("File not found or unsupported format.");
}

if ($fileExt == 'pdf') {
    header("Content-Type: application/pdf");
    header("Content-Disposition: inline; filename=\"" . urlencode($file) . "\"");
} else {
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"" . urlencode($file) . "\"");
}

readfile($filePath);
exit();
?>
