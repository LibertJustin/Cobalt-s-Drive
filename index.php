<?php
session_start();
include("./login.php");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen bg-center bg-cover bg-no-repeat">
<?php include("./universal_header.php"); ?>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-2 dark:text-gray-50">Dashboard</h1>
        <p class="dark:text-gray-50">Welcome to the Dashboard. Here you can view your drive's informations.</p>
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

    $directoryPath = './DATA/'.$_SESSION['user_token'].'/';
    $directorySize = getDirectorySize($directoryPath);
    $formattedSize = formatSizeJSON($directorySize);
    echo '<script>let currentValue0='.$formattedSize[0].';let currentValue1="'.$formattedSize[1].'";</script>';
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
    echo "<h2 class='text-xl font-bold mb-4 dark:text-gray-50'>Your Directory Size</h2>";
    echo "</div>";
                // Connexion à la base de données
                include_once("./.env/_consts_.php");
                require_once("./db.php");
                $mysqli = new mysqli(DATABASE_HOST, DATABASE_UTILISATEUR, DATABASE_PASS, DATABASE_NOM_BASE);
                if ($mysqli->connect_error) {
                    die("Connection failed: " . $mysqli->connect_error);
                }
                $queryResult = execquery("SELECT volumeSize FROM users WHERE token = '".$_SESSION['user_token']."'");
                $token = $queryResult[0]["token"];
                $volumeSize = htmlspecialchars($queryResult[0]["volumeSize"]) ? [intval(htmlspecialchars($queryResult[0]["volumeSize"])),'GB'] : [500,'GB'];
                echo '<script>let vSize0='.$volumeSize[0].';let vSize1="'.$volumeSize[1].'";</script>';
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
        
        currentValue = convertToMo(currentValue0, currentValue1);
        let maxValue = convertToMo(vSize0, vSize1);
        
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
        label.textContent = `${currentValue0} ${currentValue1} / ${vSize0} ${vSize1}`;
    </script>
    <footer class="mt-8 text-center text-gray-500">
            <p>&copy; <?php echo date("Y"); ?> Cobalt's Drive. All rights reserved.</p><br>
    </footer>
</body>
</html>