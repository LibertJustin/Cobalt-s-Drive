<?php
session_start();
// Gestion de la Deconnexion

if (isset($_GET["logout"])) {
	unset($_SESSION["tries"]); 	
	unset( $_SESSION['user_login'] );
	setcookie('user_login', "000", time(), null, null, false, true); // On supprime un cookie
    header( "location: index.php" ); 	
	exit;
}   

if (!isset($_SESSION["tries"])) {
	$_SESSION["tries"] = 0;
}

$tries = $_SESSION["tries"];
if ($tries>=10 || isset($_COOKIE['tries'])){
	http_response_code(403); // Forbidden
	setcookie('tries', 10, time() + 3600, null, null, false, true); // On écrit un cookie
	echo "Trop de tentatives de connexion. Veuillez réessayer plus tard.";
	exit;
}


function loginValid() {
		
	require_once "./db.php"; 
	global $message;
    
//	Si l'utilisateur est déja connecté
if ( isset( $_SESSION['user_login'] ) ) {
	return true;
} else {
	// Demande une authentification a chaque fois sur PC mais utilise les cookie sur mobile pendant 365 jours
	// Verifie si sur mobile 
	$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
	if (preg_match('/iphone/i',$ua) ) {
		$mobile=1;
		if (stripos($ua,'gsa')) {
			header("Location: ./phones.php");
			exit();
		}
	} elseif (preg_match('/android/i',$ua) || preg_match('/blackberry/i',$ua) || preg_match('/symb/i',$ua) || preg_match('/ipad/i',$ua) || preg_match('/ipod/i',$ua) || preg_match('/phone/i',$ua) ) {
		$mobile=1;
	} else {
		$mobile=0;
	}
    
	// Si mobile et cookie existe alors on recupere les variables de sessions
	if ((isset($_COOKIE['user_login'])) and ($mobile==1)) {
		//if (isset($_COOKIE['user_login_prof'])) {

		$queryResult = execquery( "Select count(1) as cnt From users Where token=".$_COOKIE['user_id']." and login='".$_COOKIE['user_login']."'" );
		$result = ( $queryResult[0]['cnt'] >= 1 ); 

		if ( $result ) {
			$_SESSION['user_login'] = $_COOKIE['user_login'];
			$_SESSION['user_id'] = $_COOKIE['user_id'];
			$_SESSION['message']="";
			return true;
		} else {
			$_SESSION['message']="Erreur de connexion, merci de réessayer";
			setcookie('user_login', "000", time(), null, null, false, true); // On écrit un cookie
			return false;	
		}

	} else {
        
		// Sinon on teste la page de connexion
		// Verification				
		if (!((empty($_POST['login'])) or (empty($_POST['password'])))) {
			// Nettoyage la chaine $_POST pour eviter injection sql
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
			$login=nettoie_chaine($_POST['login']);
			$password=nettoie_chaine($_POST['password']);
            if ($password!=$_POST['password']) {
				$_SESSION['message']="Identifiant ou mot de passe invalide.";
				$_SESSION["tries"]++;
				return false;
			}
			// Si login vide
			if ($login=="") {
				$message="";
				return false;		
			}
            
			$queryResult = execquery( "Select count(1) as cnt From users Where login='".$login."' And password='".$password."'");
			
			$result = ( $queryResult[0]['cnt'] >= 1 );
            
			if ( $result ) {
				$_SESSION['user_login'] = $login;
				// Recupere l'id de l'utilisateur dans $_SESSION['user_id']
				$queryResult = execquery( "Select * From users Where login='".$login."' And password='".$password."'" );
				$_SESSION['login'] = $queryResult[0]['login'];
				$_SESSION['user_token'] = $queryResult[0]['token'];
				$_SESSION['user_id'] = $queryResult[0]['id'];
                $_SESSION['admin'] = $queryResult[0]['Permissions']==1 || $queryResult[0]['Permissions']=='1' ? true : false;
				$_SESSION['volumeSize'] = $queryResult[0]['volumeSize'];
				//echo $_SESSION['user_id'];
				// Met en memoire les cookie pour connexion prolongée
				setcookie('user_login', $_SESSION['user_login'], time() + 365*24*3600, null, null, false, true); // On écrit un cookie
				setcookie('user_id', $_SESSION['user_id'], time() + 365*24*3600, null, null, false, true); // On écrit un cookie
				$_SESSION['message']= "";
				unset($_SESSION["tries"]);
				unset($_COOKIE['tries']);

			} else { 
				$_SESSION['message']= "Id or Password invalid.";
                $_SESSION["tries"]++;
			} 
			return $result;	
		} else {
			//$_SESSION['message_log']="";
			return false; 
		}
	}
}
}

$_SESSION['message']="";
if ( !loginValid() ) {

?>
	
	<!DOCTYPE html>
	<html>
	<head>
		<!-- Baptiste -->
		<meta charset="UTF-8" />
		<title>Login</title>
		<script src="../JS/java.js"></script>
		<script src="https://cdn.tailwindcss.com"></script>
	</head>
	
	<body class="flex-auto items-center font-mono m-0 p-0 box-border bg-center bg-cover bg-no-repeat">
		<script>
			// Add meta tag to head using JavaScript
			let meta = document.createElement('link');
			meta.rel = 'icon';
			meta.type='image/x-icon';
			meta.href="./medias/favicon_small.png";
			document.head.appendChild(meta);
		</script>
		<section class="bg-gray-50 dark:bg-gray-900">
	
			<div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
	
				<a href="./" class="flex items-center mb-6 text-2xl font-semibold text-[#8f2c24] dark:text-white">
				<img class="w-[8rem] mr-2" src="./medias/favicon.png" alt="logo"></a>
	
				<div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
	
					<div class="p-6 space-y-4 md:space-y-6 sm:p-8">
	
						<h1 class="text-xl text-center font-bold leading-tight tracking-tight text-[#8f2c24] md:text-2xl dark:text-white">Connexion</h1>
						<?php
						if (isset($_SESSION["message"]) && $_SESSION["message"] != '') {
							echo '<span id="span" class="text-lg p-2 rounded font-bold leading-10 tracking-tight text-[#fff] bg-[#8f2c24] dark:text-white">'. $_SESSION["message"] .'</span>';
						}
						?>
						<form class="space-y-4 md:space-y-6" action="" data-ajax="false" method="post">
							<div>
								<label for="login" class="block mb-2 text-sm font-medium text-[#8f2c24] dark:text-white">Identifiant</label>
								<input type="login" name="login" id="login" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="xxxx" required="">
							</div>
							<div>
								<label for="password" class="block mb-2 text-sm font-medium text-[#8f2c24] dark:text-white">Mot de passe</label>
								<input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
							</div>
							<div class="flex items-center justify-end">
								<button onclick="mdpperdu()" type="button" class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500 dark:text-white">Mot de passe perdu</button>
	
							</div>
							<button type="submit" value="OK" class="w-full text-white bg-[#8f2c24] hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Se connecter</button>
	
						</form>
	
					</div>
	
				</div>
	
			</div>
	
		</section>


		<script>
		function mdpperdu() {
			alert("Afin de vous connecter, merci d'utiliser vos identifiants de session. En cas d'oubli, merci de bien vouloir contacter le service informatique.");
		}
		span = document.getElementById("span");
		if (tries == 0) {
			span.classList.add("hidden");
		}
		</script>
		<footer class="mt-8 text-center text-gray-500">
				<p>&copy; <?php echo date("Y"); ?> Cobalt's Drive. All rights reserved.</p>
		</footer>
	</body>
	</html>
<?php
	exit;
	}
?>