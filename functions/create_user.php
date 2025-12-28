<?php
session_start();
require_once "../.env/_consts_.php";

$mysqli = new mysqli(DATABASE_HOST, DATABASE_UTILISATEUR, DATABASE_PASS, DATABASE_NOM_BASE);
if ($mysqli->connect_error) {
     $_SESSION['actionMessage'] = "Database connection failed: " . $mysqli->connect_error;
}
$mysqli->set_charset("utf8"); // Evite les problème convertion UTF8(site web) et Latin(Base raspisms)
// Assign values to variables
$usernameinput = isset($_POST["usernameinput"]) ? trim($_POST["usernameinput"]) : null;
$hashedPassword = isset($_POST["userPassword"]) ? trim($_POST["userPassword"]) : null;
$permission = isset($_POST["permission"]) ? intval($_POST["permission"]) : 0;
$additionalPassword = isset($_POST["password"]) ? trim($_POST["password"]) : '0'; // Treat as string, default to '0'

function gen_token() {
    return bin2hex(random_bytes(16)); // Generate a 32-character random token
}

do {
    $token = gen_token();
    $stmt_check = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE token = ?");
    $stmt_check->bind_param("s", $token);
    $stmt_check->execute();
    $stmt_check->bind_result($count);
    $stmt_check->fetch();
    $stmt_check->close();
} while ($count > 0); // Repeat until a unique token is generated

if (empty($usernameinput) || empty($hashedPassword)) {
     $_SESSION['actionMessage'] = "Invalid input data. Username and password are required.";
}

$stmt = $mysqli->prepare("INSERT INTO users (token, login, password, Permissions, volumeSize) VALUES (?, ?, ?, ?, ?)");

if ($stmt === false) {
     $_SESSION['actionMessage'] = "Prepare failed: " . htmlspecialchars($mysqli->error);
}

$additionalPasswordInt = (int)$additionalPassword;
$stmt->bind_param("sssii", $token, $usernameinput, $hashedPassword, $permission, $additionalPasswordInt);

$target_directory = '../DATA/' . $token;


if (!is_dir($target_directory)) {
    if (!mkdir($target_directory, 0775, true)) {
         $_SESSION['actionMessage'] = "Failed to create folders.";
    }
    if (function_exists('chown')) {
        chown($target_directory, 'www-data');
        chgrp($target_directory, 'www-data');
    }
}


if ($stmt->execute()) {
     $_SESSION['actionMessage'] = "New user created successfully.";
    $_SESSION["actionMessageType"] = "success";
} else {
     $_SESSION['actionMessage'] = "Error: " . $stmt->error;
}


$stmt->close();
$mysqli->close();
header("Location: ../adminPanel.php");
exit();
?>