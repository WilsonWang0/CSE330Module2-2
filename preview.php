<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION["username"]) || !isset($_GET["file"])) {
    die("Unauthorized access.");
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

// Correctly embed PDFs
if ($fileExt == 'pdf') {
    header("Content-Type: application/pdf");
    header("Content-Disposition: inline; filename=\"" . urlencode($file) . "\"");
    readfile($filePath);
    exit();
}

// Display images or text files
header("Content-Type: text/html");
if ($fileExt == 'txt') {
    echo "<pre>" . htmlspecialchars(file_get_contents($filePath)) . "</pre>";
} elseif (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
    echo "<img src='view.php?file=" . urlencode($file) . "' style='max-width:100%;'>";
}
?>
