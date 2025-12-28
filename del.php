<?php
session_start();
$filePath = isset($_GET['filePath']) ? './DATA/'.$_SESSION['user_token'].'/'.$_GET['filePath'] : './DATA/'.$_SESSION['user_token'].'/'.'';
$isTokenPresent = isset($_SESSION['user_token']) && strpos($filePath, $_SESSION['user_token']) !== false;
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

if ($isTokenPresent) {
    if (isset($_GET['filePath'])) {
        $filePath = './DATA/'.$_SESSION['user_token'].'/'.$_GET['filePath'];

        // Validate the file path to prevent directory traversal attacks
        $realBase = realpath(__DIR__);
        $realUserPath = realpath($filePath);

        if ($realUserPath && strpos($realUserPath, $realBase) === 0) {
            if (file_exists($realUserPath)) {
                if (is_dir($realUserPath)) {
                    // Recursively delete directory
                    function deleteDir($dir) {
                        $items = array_diff(scandir($dir), array('.', '..'));
                        foreach ($items as $item) {
                            $path = "$dir/$item";
                            if (is_dir($path)) {
                                deleteDir($path);
                            } else {
                                unlink($path);
                            }
                        }
                        return rmdir($dir);
                    }
                    if (deleteDir($realUserPath)) {
                        $_SESSION['actionMessage'] = "Directory deleted successfully.";
                        $_SESSION["actionMessageType"] = "success";
                    } else {
                         $_SESSION['actionMessage'] = "Error deleting the directory.";
                    }
                } else {
                    if (unlink($realUserPath)) {
                         $_SESSION['actionMessage'] = "File deleted successfully.";
                         $_SESSION["actionMessageType"] = "success";
                    } else {
                         $_SESSION['actionMessage'] = "Error deleting the file.";
                    }
                }
            } else {
                 $_SESSION['actionMessage'] = "File or directory does not exist.";
            }
        } else {
             $_SESSION['actionMessage'] = "Invalid file or directory path.";
        }
    } else {
         $_SESSION['actionMessage'] = "No file or directory path specified.";
    }
} else {
     $_SESSION['actionMessage'] = "You are not allowed to delete this file or directory";
}
header("Location: ../files.php?Path=".$combinedDirectories);
exit();
?>