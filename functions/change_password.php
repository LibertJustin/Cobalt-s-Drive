<?php
session_start();
require_once "../.env/_consts_.php";

$mysqli = new mysqli(DATABASE_HOST, DATABASE_UTILISATEUR, DATABASE_PASS, DATABASE_NOM_BASE);

if (!isset($_SESSION['user_token'])) {
    die('User token not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['userPassword'] ?? '';
    $confirmPassword = $_POST['userPassword2'] ?? '';

    if (empty($newPassword) || empty($confirmPassword)) {
        die('Password fields cannot be empty.');
    }

    if ($newPassword !== $confirmPassword) {
        die('Passwords do not match.');
    }
    function nettoie_chaine($str1) {
        // Permet de supprimer tous les caractères interdits par sécurité
        $caracteres = "aàbcdeéèfghiïjklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_ @.!:;,=)(&+-*";
        $str_ok="";
        for ($i = 0; $i < strlen($str1); $i++) {
            if (!(stristr($caracteres, $str1[$i]) === FALSE)) {
                $str_ok=$str_ok.$str1[$i];
            }				
        }	
        return $str_ok;	
    }
    // Hash the new password
    $hashedPassword = $newPassword;

    $hashedPassword=nettoie_chaine($hashedPassword);

    if ($mysqli->connect_error) {
        die('Database connection failed: ' . $mysqli->connect_error);
    }

    // Update the password in the database
    $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE token = ?");
    $stmt->bind_param('ss', $hashedPassword, $_SESSION['user_token']);

    if ($stmt->execute()) {
        echo 'Password updated successfully.';
    } else {
        echo 'Failed to update password.';
    }

    $stmt->close();
    $mysqli->close();
    header("Location: ../settings.php");
    exit();
} else {
    die('Invalid request method.');
}
?>