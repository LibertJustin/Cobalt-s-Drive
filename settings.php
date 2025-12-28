<?php
session_start();
include("./login.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen bg-center bg-cover bg-no-repeat">
<?php include("./universal_header.php"); ?>

    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4 dark:text-gray-50">Settings</h1>
        <p class="dark:text-gray-50">Welcome to the settings page. Here you can manage your application settings.</p>
        <br><br>
        <div class="text-left">
            <button id="openPopup" class="bg-[#8f2c24] text-white px-4 py-2 rounded hover:bg-red-700">Change Password</button>
        </div>
    </div>
    <div id="popupModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3 dark:text-gray-50 dark:bg-gray-800">
            <h2 class="text-xl font-bold mb-4 dark:text-gray-50">Change your password :</h2>

            <h4 class="text-xl font-bold mb-4 text-red-500 dark:text-red-500">Pay attention, the only characters allowed are : NaN</h4>

            <!-- Formulaire -->
            <form id="padletForm" method="POST" action="./functions/change_password.php">
                <!-- Champ pour le mot de passe de l'utilisateur -->
                <label class="block mb-2">New Password :</label>
                <input type="password" name="userPassword" id="userPassword" class="w-full border p-2 rounded mb-4 dark:bg-gray-500" placeholder="New Password" required>

                <!-- Champ pour le Mot de Passe (caché par défaut) -->
                <label class="block mb-2">Password Confirmation :</label>
                <input type="password" name="userPassword2" id="userPassword2" class="w-full border p-2 rounded mb-4 dark:bg-gray-500" placeholder="Password Confirmation" required>

                <!-- Boutons -->
                <div class="mt-4 flex justify-between">
                    <button type="button" id="closePopup" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700">Change</button>
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
            const userPassword2 = document.getElementById("userPassword2");
            const padletForm = document.getElementById("padletForm");

            // Ouvrir la pop-up
            openPopup.addEventListener("click", function() {
                popupModal.classList.remove("hidden");
            });

            // Fermer la pop-up
            closePopup.addEventListener("click", function() {
                popupModal.classList.add("hidden");
            });

            // Vérifier que le titre est bien rempli avant d'envoyer le formulaire
            padletForm.addEventListener("submit", function(event) {
                const userPassword = document.getElementById("userPassword").value;
                const userPassword2 = document.getElementById("userPassword2").value;

                if (userPassword !== userPassword2) {
                    event.preventDefault();
                    alert("The passwords does not match !");
                }
            });
        });
    </script>
    <footer class="mt-8 text-center text-gray-500">
            <p>&copy; <?php echo date("Y"); ?> Cobalt's Drive. All rights reserved.</p><br>
    </footer>
</body>
</html>