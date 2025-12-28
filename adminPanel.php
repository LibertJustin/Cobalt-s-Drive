<?php
session_start();
include("./login.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body class="bg-gray-50 dark:bg-gray-900 bg-center bg-cover bg-no-repeat">
<?php include("./universal_header.php");
if (!$_SESSION["admin"] == 1) {
    header("Location: index.php");
    exit();
}
?>
    <div class="p-6">
        <div class="text-left">
            <button id="openPopup" class="bg-[#8f2c24] text-white px-4 py-2 rounded hover:bg-red-700">New User</button>
        </div>
        <br><br>
        <h1 class="text-2xl font-bold mb-2 dark:text-gray-50">Admin Panel</h1>
        <p class="dark:text-gray-50">Welcome to the admin panel. Here you can manage users and settings.</p>
    </div>
    <?php
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

    function formatSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2).''.$units[$i];
    }
    function formatSizeJSON($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return [round($bytes, 2),$units[$i]];
    }

    $directoryPath = './DATA/';
    $directorySize = getDirectorySize($directoryPath);
    $formattedSize = formatSizeJSON($directorySize);
    echo '<script>let currentValueArray='.json_encode($formattedSize).';</script>';
    $directoryPath2 = '../html/';
    $directorySize2 = getDirectorySize($directoryPath2);
    $formattedSize2 = formatSize($directorySize2);
    ?>
    <style>
        canvas {
            display: block;
            margin: 20px auto;
        }
        #label {
            text-align: center;
            font-family: Arial, sans-serif;
            color: white;
            background-color: #1a1a1a;
            padding: 10px;
            width: 200px;
            margin: 0 auto;
        }
    </style>
    <?php
    echo "<div class='p-6'>";
    echo "<h2 class='text-xl font-bold mb-4 dark:text-gray-50'>DATA Directory Size</h2>";
    echo "</div>";
    ?>
    <canvas id="gauge" width="200" height="120"></canvas>
    <div id="label"></div>
    <script>
        function convertToMo(value, unit) {
            if (unit === "TB") return value * 1000000;
            if (unit === "GB") return value * 1000;
            if (unit === "MB") return value;
            if (unit === "KB") return value / 1000;
            if (unit === "B") return value / 1000000;
            return value;
        }
        let maxValue = convertToMo(1.5,"TB");
        //currentValueArray = JSON.parse(currentValueArray);
        currentValue = convertToMo(currentValueArray[0], currentValueArray[1]);
        console.log(currentValue,currentValueArray[1],currentValueArray[0]);
        // Calculer le pourcentage
        let percentage = (currentValue / maxValue) * 100;
        if (percentage > 100) percentage = 100; // Limiter à 100%

        // Dessiner la jauge
        const canvas = document.getElementById("gauge");
        const ctx = canvas.getContext("2d");
        const centerX = canvas.width / 2;
        const centerY = canvas.height;
        const radius = 80;
        const startAngle = Math.PI; // Commencer à 180 degrés (bas)
        const fullAngle = 2 * Math.PI; // Cercle complet

        // Dessiner le fond de la jauge (gris)
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, startAngle, 0);
        ctx.lineWidth = 15;
        ctx.strokeStyle = "#444";
        ctx.stroke();

        // Dessiner la progression (vert)
        const progressAngle = startAngle + (percentage / 100) * Math.PI; // Progression en radians
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, startAngle, progressAngle);
        ctx.strokeStyle = "#00ff00";
        ctx.stroke();

        // Afficher le texte (ex: 13.4 Mo / 2.0 Go)
        const label = document.getElementById("label");
        label.textContent = `${currentValueArray[0]} ${currentValueArray[1]} / ${1.5} TB`;
    </script>
    <div class="p-6">
        <h2 class="text-xl font-bold mb-4 dark:text-gray-50">User List</h2>
        <table class="min-w-full bg-gray-200 border border-gray-900 dark:bg-gray-800 dark:border-gray-200">
            <thead>
                <tr>
                    <td class="py-2 px-4 border border-gray-500 font-bold dark:text-gray-50">Username</td>
                    <td class="py-2 px-4 border border-gray-500 font-bold dark:text-gray-50">Permissions</td>
                    <td class="py-2 px-4 border border-gray-500 font-bold dark:text-gray-50">Volume Size</td>
                    <td class="py-2 px-4 border border-gray-500 font-bold dark:text-gray-50">Actual Volume</td>
                </tr>
            </thead>
            <tbody>
                <?php
                // Connexion à la base de données
                include_once("./.env/_consts_.php");
                $mysqli = new mysqli(DATABASE_HOST, DATABASE_UTILISATEUR, DATABASE_PASS, DATABASE_NOM_BASE);
                if ($mysqli->connect_error) {
                    die("Connection failed: " . $mysqli->connect_error);
                }

                // Requête pour récupérer les utilisateurs
                $sql = "SELECT token, login, Permissions, volumeSize FROM users";
                $result = $mysqli->query($sql);
                if ($result->num_rows > 0) {
                    $result->data_seek(0); // Reset result pointer
                    $admins = [];
                    $users = [];

                    // Separate administrators and users
                    while ($row = $result->fetch_assoc()) {
                        if (htmlspecialchars($row["Permissions"]) == 1) {
                            $admins[] = $row;
                        } else {
                            $users[] = $row;
                        }
                    }

                    // Sort administrators alphabetically by login
                    usort($admins, function ($a, $b) {
                        return strcmp($a["login"], $b["login"]);
                    });

                    // Sort users alphabetically by login
                    usort($users, function ($a, $b) {
                        return strcmp($a["login"], $b["login"]);
                    });

                    // Display administrators
                    foreach ($admins as $row) {
                        $token = $row["token"];
                        $permissions = 'Administrator';
                        $volumeSize = htmlspecialchars($row["volumeSize"]) ? htmlspecialchars($row["volumeSize"]).' GB' : 'No Limits';
                        echo "<tr>";
                        echo "<td class='py-2 px-4 border border-gray-500 dark:text-gray-50'>" . htmlspecialchars($row["login"]) . "</td>";
                        echo "<td class='py-2 px-4 border border-gray-500 dark:text-gray-50'>" . $permissions . "</td>";
                        echo "<td class='py-2 px-4 border border-gray-500 dark:text-gray-50'>" . $volumeSize . "</td>";
                        $directoryPath = './DATA/'.$token.'/';
                        $directorySize = getDirectorySize($directoryPath);
                        $formattedSize = formatSize($directorySize);
                        echo "<td class='py-2 px-4 border border-gray-500 dark:text-gray-50'>" . $formattedSize . "</td>";
                        echo "</tr>";
                    }

                    // Display users
                    foreach ($users as $row) {
                        $token = $row["token"];
                        $permissions = 'User';
                        $volumeSize = htmlspecialchars($row["volumeSize"]) ? htmlspecialchars($row["volumeSize"]).' GB' : 'No Limits';
                        echo "<tr>";
                        echo "<td class='py-2 px-4 border border-gray-500 dark:text-gray-50'>" . htmlspecialchars($row["login"]) . "</td>";
                        echo "<td class='py-2 px-4 border border-gray-500 dark:text-gray-50'>" . $permissions . "</td>";
                        echo "<td class='py-2 px-4 border border-gray-500 dark:text-gray-50'>" . $volumeSize . "</td>";
                        $directoryPath = './DATA/'.$token.'/';
                        $directorySize = getDirectorySize($directoryPath);
                        $formattedSize = formatSize($directorySize);
                        echo "<td class='py-2 px-4 border border-gray-500 dark:text-gray-50'>" . $formattedSize . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' class='py-2 px-4 border border-gray-500 dark:text-gray-50'>No users found</td></tr>";
                }

                $mysqli->close();
                ?>
            </tbody>
        </table>
    <!-- Boîte modale pour la création d'un Padlet -->
    <div id="popupModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
        <div class="bg-gray-200 dark:bg-gray-800 p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-4">Create a new user</h2>

            <!-- Formulaire -->
            <form id="padletForm" method="POST" action="./functions/create_user.php">
                <!-- Champ pour le titre -->
                <label class="block mb-2">Username :</label>
                <input type="text" name="usernameinput" id="usernameinput" class="w-full border p-2 rounded mb-4" placeholder="" required>

                <!-- Champ pour le mot de passe de l'utilisateur -->
                <label class="block mb-2">User Password :</label>
                <input type="password" name="userPassword" id="userPassword" class="w-full border p-2 rounded mb-4" placeholder="" required>

                <!-- Sélecteur de permissions -->
                <label class="block mb-2">Permissions :</label>
                <select name="permission" id="Permission" class="w-full border p-2 rounded mb-4">
                    <option value="0">User</option>
                    <option value="1">Administrator</option>
                </select>

                <!-- Champ pour le Mot de Passe (caché par défaut) -->
                <label id="lblPwd" class="block mb-2 hidden">Mot de Passe :</label>
                <input type="text" name="password" id="padletPwd" class="w-full border p-2 rounded mb-4" placeholder="User's drive volume in GiB" value="0" required>

                <!-- Boutons -->
                <div class="mt-4 flex justify-between">
                    <button type="button" id="closePopup" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const openPopup = document.getElementById("openPopup");
            const closePopup = document.getElementById("closePopup");
            const popupModal = document.getElementById("popupModal");
            const padletPermission = document.getElementById("Permission");
            const padletPwd = document.getElementById("padletPwd");
            const lblPwd = document.getElementById("lblPwd");
            const padletForm = document.getElementById("padletForm");

            // Ouvrir la pop-up
            openPopup.addEventListener("click", function() {
                popupModal.classList.remove("hidden");
            });

            // Fermer la pop-up
            closePopup.addEventListener("click", function() {
                popupModal.classList.add("hidden");
            });

            // Afficher/Masquer le champ "Mot de Passe" en fonction de la permission sélectionnée
            padletPermission.addEventListener("change", function() {
                if (this.value == "0") {
                    lblPwd.classList.remove("hidden");
                    padletPwd.classList.remove("hidden");
                } else {
                    lblPwd.classList.add("hidden");
                    padletPwd.classList.add("hidden");
                    padletPwd.value = "";
                }
            });

            // Vérifier que le titre est bien rempli avant d'envoyer le formulaire
            padletForm.addEventListener("submit", function(event) {
                const title = document.getElementById("usernameinput").value.trim();
                if (!title) {
                    event.preventDefault();
                    alert("Please enter an username !");
                }
            });
        });
    </script>
    </div>
    <footer class="mt-8 text-center text-gray-500">
            <p>&copy; <?php echo date("Y"); ?> Cobalt's Drive. All rights reserved.</p><br>
    </footer>
</body>
</html>