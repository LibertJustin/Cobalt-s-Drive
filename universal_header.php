<script>
    // Add meta tag to head using JavaScript
    let meta = document.createElement('link');
    meta.rel = 'icon';
    meta.type='image/x-icon';
    meta.href="./medias/favicon_small.png";
    document.head.appendChild(meta);
</script>
<script src="https://cdn.tailwindcss.com"></script>
<header class="sticky top-0 right-0 left-0 flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 shadow-md">
        <!-- Logo -->
        <a href="./"><img class="w-[75px] mr-2" src="./medias/favicon_nobg.png" alt="Logo"></a>
        <a href="./"><p class="text-[#8f2c24] font-bold text-2xl">Cobalt's Drive</p></a>
        <!-- Menu déroulant-->
        <div class="relative">
            <button class="bg-[#8f2c24] text-white px-4 py-2 rounded hover:bg-red-700" id="menuButton">
                <?= isset($_SESSION["login"]) ? $_SESSION["login"] : "User"; ?>
            </button>
            <!-- Contenu du menu déroulant -->
            <div class="absolute right-0 mt-2 w-48 bg-gray-50 dark:bg-gray-900 border rounded shadow-lg opacity-0 invisible transition-all duration-200 ease-in-out" id="menuDropdown">
                <a href="./" class="block w-full px-4 py-2 text-gray-800 dark:text-gray-50 hover:bg-gray-100 dark:hover:bg-gray-600">Dashboard</a>
                <?php if ($_SESSION["admin"] == 1) { ?>
                    <a href="./adminPanel.php" class="block w-full px-4 py-2 text-gray-800 dark:text-gray-50 hover:bg-gray-100 dark:hover:bg-gray-600">Admin Panel</a>
                <?php } ?>
                <a href="./files.php" class="block w-full px-4 py-2 text-gray-800 dark:text-gray-50 hover:bg-gray-100 dark:hover:bg-gray-600">Your files</a>
                <a href="./settings.php" class="block w-full px-4 py-2 text-gray-800 dark:text-gray-50 hover:bg-gray-100 dark:hover:bg-gray-600">Settings</a>
                <a href="./index.php?logout=true" class="block w-full px-4 py-2 text-gray-800 dark:text-gray-50 hover:bg-gray-100 dark:hover:bg-gray-600">Logout</a> <!-- Permet de se déconnecter (fonctionnel) -->
            </div>
        </div>
</header>

<!-- Script du menu déroulant -->

<script>
    const menuButton = document.getElementById('menuButton');
    const menuDropdown = document.getElementById('menuDropdown');

    menuButton.addEventListener('click', (event) => {
        event.stopPropagation();
        menuDropdown.classList.toggle('opacity-0');
        menuDropdown.classList.toggle('invisible');
    });

    document.addEventListener('click', (event) => {
        if (!menuButton.contains(event.target) && !menuDropdown.contains(event.target)) {
            menuDropdown.classList.add('opacity-0', 'invisible');
        }
    });

    menuDropdown.addEventListener('click', (event) => {
        event.stopPropagation();
    });
</script>
<?php
if (isset($_SESSION["actionMessage"]) && $_SESSION["actionMessage"] != '') {
    if (isset($_SESSION["actionMessageType"]) && $_SESSION["actionMessageType"] == 'success') {
        echo '<div id="message" class="bg-green-700 hover:bg-green-600 text-white p-4 rounded-lg mb-4 fixed inset-x-0 top-1 left-[10%] right-[10%] bg-gray-800 flex items-center justify-center cursor-pointer" onclick="hideMessage()">' . $_SESSION["actionMessage"] . '</div>';
    } else {
        echo '<div id="message" class="bg-red-700 hover:bg-red-600 text-white p-4 rounded-lg mb-4 fixed inset-x-0 top-1 left-[10%] right-[10%] bg-gray-800 flex items-center justify-center cursor-pointer" onclick="hideMessage()">' . $_SESSION["actionMessage"] . '</div>';
    }
    echo '<script>setTimeout(() => {document.getElementById("message").classList.add("hidden");}, 60000);</script>';
    echo '<script>function hideMessage() {document.getElementById("message").classList.add("hidden");};</script>';
    unset($_SESSION['actionMessage']);
    unset($_SESSION['actionMessageType']);
}
?>