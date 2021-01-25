<?php
	session_start();
	require('src/log.php');
	if(!empty($_POST['email']) && !empty($_POST['password'])){	
		// Appel bdd
		require('src/connect.php');
		// Variables
		$email = htmlspecialchars($_POST['email']);
		$password = htmlspecialchars($_POST['password']);
		// Adresse email valide
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			header('location: index.php?error=1&message=Votre adresse email est invalide');
			exit();
		}
		// Cryptage password
		$password = "aq1".sha1($password."123")."25";
		// Email déjà utiliser
		$req = $db->prepare("SELECT count(*) as numberEmail FROM user WHERE email = ?");
		$req->execute(array($email));
		
		while($email_verification = $req->fetch()){
			if($email_verification['numberEmail'] != 1){
				header('location: index.php?error=1&message=Impossible de vous authentifiez');
				exit();
			}
		}
		// Connexion
		$req = $db->prepare("SELECT * FROM user WHERE email = ?");
		$req->execute(array($email));
		while($user = $req->fetch()){
			if($password == $user['password']){
				$_SESSION['connect'] = 1;
				$_SESSION['email'] = $user['email'];
				if(isset($_POST['auto'])){
					setcookie('auth',$user['secret'], time() + 365*24*3600, '/', null, false, true);
				}
				header('location: index.php?success=1');
				exit();
			}
			else {
				header('location: index.php?error=1&message=Impossible de vous authentifiez');
			}
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Connexion</title>
</head>
<body>
	<section>
		<div id="login-body">
			<?php if(isset($_SESSION['connect'])){?>
				<h1>Bonjour !</h1>
				<?php					
					if(isset($_GET['success'])){
						echo '<div class="alert success">Vous êtes maintenant connecté</div>';
				}?>
				<p>Home</p>
				<small><a href="logout.php">Déconnexion</a></small>
			<?php } else { ?>
			<h1>S'identifier</h1>
			<?php
					if(isset($_GET['error'])){
						if(isset($_GET['message'])){
							echo '<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
						}
					}
			?>
			<form method="post" action="index.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<button type="submit">S'identifier</button>
				<label id="option"><input type="checkbox" name="auto" checked />Se souvenir de moi</label>
			</form>
			<p class="grey">Première visite ? <a href="inscription.php">Inscrivez-vous</a>.</p>
			<?php } ?>
		</div>
	</section>
	<!-- <script src="main.js"></script> -->
</body>
</html>