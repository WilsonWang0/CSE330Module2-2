<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION["username"];
$userDir = "/home/WilsonWang/file_sharing_secure/uploads/$username";

// Handle file deletion securely
if (isset($_GET["delete"])) {
    $file = basename($_GET["delete"]);
    $file = htmlspecialchars($file, ENT_QUOTES, 'UTF-8');
    $filePath = "$userDir/$file";

    if (file_exists($filePath)) {
        unlink($filePath);
        header("Location: home.php");
        exit();
    }
}

// Get user files
$files = array_diff(scandir($userDir), array('.', '..'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Sharing</title>
    <script>
        function allowDrop(event) {
            event.preventDefault();
        }

        function dropFile(event) {
            event.preventDefault();
            let files = event.dataTransfer.files;
            let formData = new FormData();
            formData.append("file", files[0]);

            fetch("upload.php", {
                method: "POST",
                body: formData
            }).then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            }).catch(error => console.error("Error:", error));
        }

        function previewFile(file) {
            let preview = document.getElementById("filePreview");
            let iframe = document.getElementById("previewFrame");

            iframe.src = "preview.php?file=" + encodeURIComponent(file);
            preview.style.display = "block";
        }

        function closePreview() {
            document.getElementById("filePreview").style.display = "none";
        }
    </script>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>!</h2>

    <h3>Drag and Drop to Upload</h3>
    <div style="width:300px;height:100px;border:2px dashed gray;display:flex;align-items:center;justify-content:center;" 
         ondrop="dropFile(event)" ondragover="allowDrop(event)">
        Drop files here
    </div>

    <h3>Or Upload Manually</h3>
    <form method="POST" action="upload.php" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <input type="submit" value="Upload">
    </form>

    <h3>Your Files</h3>
    <ul>
        <?php foreach ($files as $file): ?>
            <li>
                <a href="view.php?file=<?php echo urlencode($file); ?>" target="_blank">
                    <?php echo htmlspecialchars($file, ENT_QUOTES, 'UTF-8'); ?>
                </a>
                <button onclick="previewFile('<?php echo urlencode($file); ?>')">🔍 Preview</button>
                <a href="home.php?delete=<?php echo urlencode($file); ?>" onclick="return confirm('Delete this file?');">🗑 Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <a href="logout.php">Logout</a>

    <div id="filePreview" style="display:none; position:fixed; top:10%; left:10%; width:80%; height:80%; background:#fff; border:1px solid #ccc; box-shadow:0 0 10px #000; padding:10px;">
        <button onclick="closePreview()" style="float:right;">Close</button>
        <iframe id="previewFrame" style="width:100%; height:90%; border:none;"></iframe>
    </div>
</body>
</html>
