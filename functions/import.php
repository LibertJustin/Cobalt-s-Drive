<?php
session_start();
// Define the target directory where files will be uploaded
$baseDir = "../DATA/" . $_SESSION['user_token'] . "/";
$targetDir = isset($_POST['Path']) ? $baseDir.$_POST['Path'] : $baseDir;
// Check if a file was uploaded
$pathParam = isset($_POST['Path']) ? $_POST['Path'] : '';
echo '<button onclick="redirectTo()">Go back to your files</button><script>function redirectTo() {window.location.href = "../files.php?Path=' . htmlspecialchars($pathParam, ENT_QUOTES) . '";}</script><br><br>';
// Check for empty $_FILES or $_POST due to exceeded post_max_size or upload_max_filesize
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_FILES) && empty($_POST)) {
    $_SESSION["actionMessage"]="File upload error: The uploaded file is too large (Maximum 4GB).";
}
function getDirectorySize($path) {
    $bytestotal = 0;
    $path = realpath($path);
    if($path!==false && is_dir($path)){
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
            $bytestotal += $object->getSize();
        }
    }
    return $bytestotal;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {
    // Check for upload errors
    if ($_FILES['fileToUpload']['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
        ];
        $error = $_FILES['fileToUpload']['error'];
        $message = isset($errorMessages[$error]) ? $errorMessages[$error] : 'Unknown upload error.';
        $_SESSION["actionMessage"]="File upload error: " . $message;
    }

    $targetFile = $targetDir . basename($_FILES['fileToUpload']['name']);
    $uploadOk = 1;
    //echo $targetDir.' '.$targetFile.'<br><br>';
    // Check if the target directory exists, if not, create it
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }

    // Check if the file already exists
    if (file_exists($targetFile)) {
        $_SESSION["actionMessage"]="Sorry, file already exists.";
        $uploadOk = 0;
    }

    require_once('../db.php');
    $sql = "SELECT volumeSize FROM users WHERE token ='".$_SESSION['user_token']."'";
    $result = execquery($sql);
    $size = $result[0]['volumeSize'] == 0 ? 500 : $result[0]['volumeSize'];
    $size = (int)$size;
    $size = $size * 1024 * 1024 * 1024;
    $folderTooBig = getDirectorySize($baseDir)+$_FILES['fileToUpload']['size'] > $size;

    if ($folderTooBig) {
        $_SESSION["actionMessage"]="Sorry, your file exceed your directory size limits.";
        $uploadOk = 0;
    }

    // Check file size (limit to 5GB for example)
    if ($_FILES['fileToUpload']['size'] > 3900000000) {
        $_SESSION["actionMessage"]="Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats (optional, e.g., only images)
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'pdf', 'docx', 'xlsx', 'pptx', 'odt', 'ods', 'odp', 'txt', 'rtf', 'mp3', 'wav', 'ogg', 'flac', 'mp4', 'avi', 'mkv', 'mov', 'webm', 'zip', '7z', 'rar', 'csv', 'json', 'xml', 'epub', 'mobi'];
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    if (!in_array($fileType, $allowedTypes)) {
        $_SESSION["actionMessage"]="Sorry, only JPG, JPEG, PNG, GIF, TXT, PDF, and Compressed files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        //$_SESSION["actionMessage"]="Sorry, your file was not uploaded.";
        header("Location: ../files.php?Path=".$_POST['Path']);
        exit();
    } else {
        // Try to move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetFile)) {
            $_SESSION["actionMessage"] = "The file " . htmlspecialchars(basename($_FILES['fileToUpload']['name'])) . " has been uploaded.";
            $_SESSION["actionMessageType"] = "success";
        } else {
            $_SESSION["actionMessage"]="Sorry, there was an error uploading your file.";
        }
    }
}

header("Location: ../files.php?Path=".$_POST['Path']);
exit();
?>