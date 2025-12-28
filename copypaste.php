<?php
// Function to copy a file to a specified directory
function copyFileToDirectory($filePath, $destinationDirectory) {
    // Check if the file exists
    if (!file_exists($filePath)) {
        die("Error: File does not exist.\n");
    }

    // Check if the destination directory exists, if not create it
    if (!is_dir($destinationDirectory)) {
        if (!mkdir($destinationDirectory, 0775, true)) {
            die("Error: Failed to create destination directory.\n");
        }
    }

    // Get the base name of the file
    $fileName = basename($filePath);

    // Construct the destination path
    $destinationPath = rtrim($destinationDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;

    // Check if the file already exists in the destination
    while (file_exists($destinationPath)) {
        $destinationPath = $destinationPath . ' (1)';
    }

    // Copy the file
    if (copy($filePath, $destinationPath)) {
        echo "File copied successfully to $destinationPath\n";
    } else {
        die("Error: Failed to copy the file.\n");
    }
}

// Example usage
$filePath = './DATA/fbdsasw548dfdfkd/test.bin'; // Replace with the source file path
$destinationDirectory = './DATA/59bbf2f747e982b57ffbac13a2c5ef8c'; // Replace with the destination directory

//copyFileToDirectory($filePath, $destinationDirectory);


/**
 * Recursively copy a directory and its contents to a destination directory.
 *
 * @param string $filePath Source directory path
 * @param string $destinationDirectory Destination directory path
 * @return bool True on success, false on failure
 */
function copyDirectory($filePath, $destinationDirectory) {
    // Normalize paths to remove trailing slashes
    $filePath = rtrim($filePath, DIRECTORY_SEPARATOR);
    $destinationDirectory = rtrim($destinationDirectory, DIRECTORY_SEPARATOR);

    // Check if source directory exists and is a directory
    if (!is_dir($filePath)) {
        trigger_error("Source path '$filePath' is not a directory or does not exist.", E_USER_WARNING);
        return false;
    }

    // Create destination directory if it doesn't exist
    if (!is_dir($destinationDirectory)) {
        if (!mkdir($destinationDirectory, 0755, true)) {
            trigger_error("Failed to create destination directory '$destinationDirectory'.", E_USER_WARNING);
            return false;
        }
    }

    // Open the source directory
    $dir = opendir($filePath);
    if ($dir === false) {
        trigger_error("Failed to open source directory '$filePath'.", E_USER_WARNING);
        return false;
    }

    // Iterate through all items in the source directory
    while (($item = readdir($dir)) !== false) {
        // Skip '.' and '..' directories
        if ($item === '.' || $item === '..') {
            continue;
        }

        $sourceItem = $filePath . DIRECTORY_SEPARATOR . $item;
        $destItem = $destinationDirectory . DIRECTORY_SEPARATOR . $item;

        // If it's a directory, recurse
        if (is_dir($sourceItem)) {
            if (!copyDirectory($sourceItem, $destItem)) {
                closedir($dir);
                return false;
            }
        } else {
            // If it's a file, copy it
            if (!copy($sourceItem, $destItem)) {
                trigger_error("Failed to copy file '$sourceItem' to '$destItem'.", E_USER_WARNING);
                closedir($dir);
                return false;
            }
        }
    }

    closedir($dir);
    return true;
}

// Example usage
$filePath = './DATA/fbdsasw548dfdfkd'; // Replace with the source file path
$destinationDirectory = './DATA/b176f2fdc9cc9cd6e73a93e49acc63ee'; // Replace with the destination directory
$source = $filePath;
$destination = $destinationDirectory;

if (copyDirectory($source, $destination)) {
    echo "Directory copied successfully.\n";
} else {
    echo "Failed to copy directory.\n";
}

?>