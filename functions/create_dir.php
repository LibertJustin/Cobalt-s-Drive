<?php
session_start();
function createDirectory($path) {
    if (!is_dir($path)) {
        if (mkdir($path, 0775, true)) {
            $_SESSION['actionMessage'] = "Directory created successfully";
            $_SESSION["actionMessageType"] = "success";
        } else {
            $_SESSION['actionMessage'] = "Failed to create directory";
        }
    } else {
        $_SESSION['actionMessage'] = "Directory already exists";
    }
}
function trimUntilLastSlash($string) {
    $deletedString = '';
    while (!empty($string) && substr($string, -1) != '/') {
        $deletedString = substr($string, -1) . $deletedString;
        $string = substr($string, 0, -1);
    }
    return [$string, $deletedString];
}
function listDirectoriesInPath($path) {
    if (substr($path, -1) == '/') {
        $path = substr($path, 0, -1);
    }
    $count = 0;
    $directoriesNames = [];
    while (!empty($path) && $path != './' && $path != '.' && $path != '/') {
        if (is_dir(trimUntilLastSlash($path)[0])) {
            $count = 0;
            $directoriesNames[] = trimUntilLastSlash($path)[1];
            $path = substr(trimUntilLastSlash($path)[0], 0, -1);
        }
        $count++;
        if ($count > 10) {
            break;
        }
    }
    return $directoriesNames;
}
$directories = array_reverse(listDirectoriesInPath($_GET['filePath']));
$filteredDirectories = array_slice($directories, 2, count($directories) - 3);
$combinedDirectories = implode('/', $filteredDirectories);
if ($combinedDirectories != '') {
    $combinedDirectories .= '/';
}
echo $combinedDirectories;
$targetDir = "../DATA/" . $_SESSION['user_token'] . "/";
$targetDir = isset($_POST['Path']) ? $targetDir.$_POST['Path'] : $targetDir;
$targetDir = $targetDir.$_POST['dirName'].'/';
createDirectory($targetDir);
$dir = isset($_POST['Path']) ? $_POST['Path'] : '';
header("Location: ../files.php?Path=".$_GET['filePath'].$dir);
exit();
?>