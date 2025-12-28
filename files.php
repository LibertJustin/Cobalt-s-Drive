<?php
session_start();
include("./login.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your files</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen bg-center bg-cover bg-no-repeat overflow-hidden">
<?php
    include("./universal_header.php");
?>
<div class="p-6">
        <h2 class="text-xl font-bold mb-4 dark:text-gray-50">Your Files</h2>
        <p class="dark:text-gray-50">Here you can manage your files.</p><br>
        <div class="text-left">
            <button id="openPopup" class="bg-[#8f2c24] text-white px-4 py-2 rounded hover:bg-red-700">Import File</button>
            <button id="openPopup2" class="bg-[#8f2c24] text-white px-4 py-2 rounded hover:bg-red-700">Create a new Directory</button>
        </div><br>
        
        <?php
        function trimUntilLastSlash($string) {
            $deletedString = '';
            while (!empty($string) && substr($string, -1) != '/') {
                $deletedString = substr($string, -1) . $deletedString;
                $string = substr($string, 0, -1);
            }
            return [$string, $deletedString];
        }
        function getDirectorySize($path) {
            $bytestotal = 0;
            $path = './DATA/'.$_SESSION['user_token'].'/'.$path;
            $path = realpath($path);
            if($path!==false && is_dir($path)){
                foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object) {
                    $bytestotal += $object->getSize();
                }
            }
            return $bytestotal;
        }
        function formatSize($bytes) {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $i = 0;
            while ($bytes >= 1024 && $i < count($units) - 1) {
                $bytes /= 1024;
                $i++;
            }
            return round($bytes, 2).' '.$units[$i];
        }
        function listDirectoriesInPath($path) {
            if (substr($path, -1) == '/') {
                $path = substr($path, 0, -1);
            }
            $count = 0;
            $directoriesNames = [];
            while (!empty($path) && $path != './' && $path != '.' && $path != '/') {
                if (is_dir('./DATA/'.$_SESSION['user_token'].trimUntilLastSlash($path)[0])) {
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
        
        if (!isset($_GET['Path']) || $_GET['Path'] == '') {
            echo '<script>let filepath = ""</script>';
            $directory = './DATA/'.$_SESSION['user_token'].'/'.'';
            $absolutePath = '/';
            $directorySize = getDirectorySize($_GET['Path']);
            $formattedSize = formatSize($directorySize);
            echo '<div class="bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg p-2"><h4>Path : <a href="./files.php?Path=" class="hover:underline">Home</a>/</h4><h5> Actual Directory Size : ' . $formattedSize . '</h5></div>';
        } else {
            echo '<script>let filepath = "'.$_GET['Path'].'"</script>';
            $directory = './DATA/'.$_SESSION['user_token'].'/'.$_GET['Path'];
            $directorySize = getDirectorySize($_GET['Path']);
            $formattedSize = formatSize($directorySize);
            $directories = array_reverse(listDirectoriesInPath('/'.$_GET['Path']));
            $absolutePath = '';
            $htmlElement = '';
            foreach ($directories as $dir) {
                $absolutePath .= $dir.'/';
                $htmlElement .= '<a href="./files.php?Path='.$absolutePath.'" class="hover:underline">'.$dir.'</a>/';
            }
            echo '<div class="bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg p-2"><h4>Path : <a href="./files.php?Path=" class="hover:underline">Home</a>/'.$htmlElement.'</h4><h5> Actual Directory Size : ' . $formattedSize . '</h5></div>';
            $shortedPath = substr($_GET['Path'], 0, -1);
            $prevDirectory = trimUntilLastSlash($shortedPath)[0];
            //echo '<div class="p-6 justify-center items-center flex flex-col"><div class="min-h-15 w-100 flex items-center justify-evenly bg-gray-800 rounded-lg"><a href="./files.php?Path='. $prevDirectory .'" class="bg-black text-white dark:text-white px-4 py-2 text-base h-8 flex items-center justify-center rounded hover:bg-gray-800 transition-colors">Go to parent directory</a></div></div>';

        }
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if (preg_match('/iphone/i',$ua) || preg_match('/android/i',$ua) || preg_match('/blackberry/i',$ua) || preg_match('/symb/i',$ua) || preg_match('/ipad/i',$ua) || preg_match('/ipod/i',$ua) || preg_match('/phone/i',$ua) ) {
            $mobile=1;
        } else {
            $mobile=0;
        }
        echo "<table class='table-auto w-full max-w-full dark:text-gray-50'>";
        echo "<thead>";
        echo "<tr>";
        if ($mobile==0) {
            echo "<th class='px-4 py-2'>Type</th>";
        }
        echo "<th class='px-4 py-2'>Name</th>";
        echo "<th class='px-4 py-2'>Size</th>";
        echo "<th class='px-4 py-2'>Actions</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        if (is_dir($directory)) {
            $files = scandir($directory);
            $directories = [];
            $regularFiles = [];

            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $filePath = $directory . $file;
                    if (is_dir($filePath)) {
                    $directories[] = $file;
                    } else {
                    $regularFiles[] = $file;
                    }
                }
            }
            
            // Sort directories and files alphabetically
            natcasesort($directories);
            natcasesort($regularFiles);
            $count = 0;
            // Display directories first
            foreach ($directories as $dir) {
                $directoryPath = /*'./DATA/' . $_SESSION['user_token'] . '/' . */$_GET['Path'] . $dir . '/';
                $directorySize = getDirectorySize($directoryPath);
                $formattedSize = formatSize($directorySize);
                echo "<tr>";
                if ($mobile==0) {
                    echo "<td class='border px-4 py-2 text-center'>DIRECTORY</td>";
                }
                $actualPath = $_GET['Path'] ? $_GET['Path'] : '';
                echo "<td class='border px-4 py-2 text-center'><a href='./files.php?Path=" . $actualPath . htmlspecialchars($dir) . '/' . "' class='hover:underline'>" . htmlspecialchars($dir) . "</a></td>";
                echo "<td class='border px-4 py-2 text-center'> " . $formattedSize . " </td>";
                echo "<td class='border px-4 py-2 flex flex-row justify-evenly flex-wrap'><a href='./files.php?Path=" . $actualPath . htmlspecialchars($dir) . '/' . "' class='hover:underline'>View Files</a><a href='./del.php?filePath=" . $directory . htmlspecialchars($dir) . '/' . "' class='text-red-500 hover:underline'>Delete</a></td>";
                echo "</tr>";
                $count++;
            }

            // Display files
            foreach ($regularFiles as $file) {
                $filePath = $_GET['Path'] . $file;
                $size = filesize('./DATA/'.$_SESSION['user_token'].'/'.$filePath);
                $size = $size < 1024 ? $size . ' bytes' : ($size < 1048576 ? round($size / 1024, 2) . ' KB' : ( $size < 1073741824 ? round($size / 1048576, 2) . ' MB' : round($size / 1073741824, 2) . ' GB'));
                echo "<tr>";
                if ($mobile==0) {
                    echo "<td class='border px-4 py-2 text-center'>FILE</td>";
                }
                echo "<td class='border px-4 py-2 text-center'><a href='./download.php?filePath=" . htmlspecialchars($filePath) . "' class='hover:underline'>" . htmlspecialchars($file) . "</a></td>";
                echo "<td class='border px-4 py-2 text-center'> " . $size . " </td>";
                echo "<td class='border px-4 py-2 flex flex-row justify-evenly  flex-wrap'><a href='./download.php?filePath=" . htmlspecialchars($filePath) . "' class='text-blue-500 hover:underline'>Download</a><a href='./del.php?filePath=" . htmlspecialchars($filePath) . "' class='text-red-500 hover:underline'>Delete</a></td>";
                echo "</tr>";
                $count++;
            }
            if ($count == 0) {
                echo "<tr>";
                echo "<td colspan='4' class='px-4 py-2 text-center'>No files or directories.</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr>";
            echo "<td colspan='4' class='border px-4 py-2 text-center'>No files or directories found.</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
        ?>
    </div>
    <div id="popupModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
            <div class="bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 p-6 rounded-lg shadow-lg w-1/3">
                <h2 class="text-xl font-bold mb-4">Import a file</h2>

                <!-- Formulaire -->
                <form id="importForm" method="POST" action="./functions/import.php" enctype="multipart/form-data">
                    <!-- Champ pour le titre -->
                    <label class="block mb-2">File to Upload :</label>
                    <input class='bg-[#8f2c24] text-white px-4 py-2 rounded hover:bg-red-700' type="file" name="fileToUpload" id="fileToUpload" required>
                    
                    <input type="hidden" name="Path" id="Path" value="<?php echo $_GET['Path']; ?>">
                    <!-- Boutons -->
                    <div class="mt-4 flex justify-between">
                        <button type="button" id="closePopup" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700">Cancel</button>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">Import</button>
                    </div>
                </form>
            </div>
        </div>
        <script>
            function sendmessage(message) {
                fetch('./functions/set_session_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ message: message })
                }).then((response) => {
                    if (response.status === 200) {
                        console.log('Message set in session.');
                        window.location.reload();
                    } else {
                        console.error('Failed to set message in session.');
                    }
                });
            }
            document.getElementById('importForm').addEventListener('submit', function(e) {
                const fileInput = document.getElementById('fileToUpload');
                const maxSize = 10485760000; // 100MB in bytes
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'pdf', 'docx', 'xlsx', 'pptx', 'odt', 'ods', 'odp', 'txt', 'rtf', 'mp3', 'wav', 'ogg', 'flac', 'mp4', 'avi', 'mkv', 'mov', 'webm', 'zip', '7z', 'rar', 'csv', 'json', 'xml', 'epub', 'mobi'];
                
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const fileExtension = file.name.split('.').pop().toLowerCase();
                    
                    if (!allowedExtensions.includes(fileExtension)) {
                        e.preventDefault();
                        sendmessage(`Invalid file extension: ${fileExtension}. Allowed types: images (jpg, png, gif, etc.), videos (mp4, avi, etc.), audio (mp3, wav, etc.), documents (pdf, docx, etc.), compressed files (zip, rar, etc.), data files (csv, json, etc.), and e-books (epub, mobi).`);
                    } else if (file.size > maxSize) {
                        e.preventDefault();
                        sendmessage('File size exceeds 100MB limit.');
                    }
                }
            });
        </script>
        <!-- Script JavaScript -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const openPopup = document.getElementById("openPopup");
                const closePopup = document.getElementById("closePopup");
                const popupModal = document.getElementById("popupModal");

                // Ouvrir la pop-up
                openPopup.addEventListener("click", function() {
                    popupModal.classList.remove("hidden");
                });

                // Fermer la pop-up
                closePopup.addEventListener("click", function() {
                    popupModal.classList.add("hidden");
                });
            });
        </script>
        <div id="popupModal2" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
            <div class="bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 p-6 rounded-lg shadow-lg w-1/3">
                <h2 class="text-xl font-bold mb-4">Create a directory</h2>

                <!-- Formulaire -->
                <form id="importForm2" method="POST" action="./functions/create_dir.php">
                    <!-- Champ pour le titre -->
                    <label class="block mb-2">Directory Name :</label>
                    <input class='w-full border p-2 rounded mb-4 dark:bg-gray-600' placeholder="Directory Name" type="text" name="dirName" id="dirName" required>
                    
                    <input type="hidden" name="Path" id="Path" value="<?php echo $_GET['Path'].'/'; ?>">
                    <!-- Boutons -->
                    <div class="mt-4 flex justify-between">
                        <button type="button" id="closePopup2" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700">Cancel</button>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">Create</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Script JavaScript -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const openPopup = document.getElementById("openPopup2");
                const closePopup = document.getElementById("closePopup2");
                const popupModal = document.getElementById("popupModal2");

                // Ouvrir la pop-up
                openPopup.addEventListener("click", function() {
                    popupModal.classList.remove("hidden");
                });

                // Fermer la pop-up
                closePopup.addEventListener("click", function() {
                    popupModal.classList.add("hidden");
                });
            });
        </script>
    <footer class="mt-8 text-center text-gray-500">
            <p>&copy; <?php echo date("Y"); ?> Cobalt's Drive. All rights reserved.</p><br>
    </footer>
</body>
</html>