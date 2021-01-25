<?php
	session_start();
	require('src/log.php');
	if(isset($_SESSION['connect'])){
		header('location: index.php');
		exit();
	}
	if(!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password_two'])){
		// Connect BDD
		require('src/connect.php');
		// Variables
		$email = htmlspecialchars($_POST['email']);
		$password = htmlspecialchars($_POST['password']);
		$password_two = htmlspecialchars($_POST['password_two']);
		// Vérif password
		if($password != $password_two){
			header('location: inscription.php?error=1&message=Vos mots de passe ne sont pas identiques');
			exit();
		}
		// Adresse email valide
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			header('location: inscription.php?error=1&message=Votre adresse email est invalide');
			exit();
		}
		// Email déjà utiliser
		$req = $db->prepare("SELECT count(*) as numberEmail FROM user WHERE email = ?");
		$req->execute(array($email));
		while($email_verification = $req->fetch()){
			if($email_verification['numberEmail'] != 0){
				header('location: inscription.php?error=1&message=Votre adresse email est déjà utilisée par un autre utilisateur');
				exit();
			}
		}
		$secret = sha1($email).time();
		$secret = sha1($secret).time();
		// Cryptage password
		$password = "aq1".sha1($password."123")."25";
		// Envoie
		$req = $db->prepare("INSERT INTO user(email, password, secret) VALUES(?, ?, ?)");
		$req->execute(array($email, $password, $secret));
		header('location: inscription.php?success=1');
		exit();
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Inscription</title>
</head>
<body>
	<section>
		<div id="login-body">
			<h1>S'inscrire</h1>
			<?php
				if(isset($_GET['error'])){
					if(isset($_GET['message'])){
						echo'<div class="alert error">'.htmlspecialchars($_GET['message']).'</div>';
					}
				} elseif(isset($_GET['success'])){
					echo '<div class="alert success">Vous êtes désormais inscrit. <a href="index.php">Connectez-vous</a></div>';
				}
			?>
			<form method="post" action="inscription.php">
				<input type="email" name="email" placeholder="Votre adresse email" required />
				<input type="password" name="password" placeholder="Mot de passe" required />
				<input type="password" name="password_two" placeholder="Retapez votre mot de passe" required />
				<button type="submit">S'inscrire</button>
			</form>
			<p class="grey">Déjà inscrit ? <a href="index.php">Connectez-vous</a>.</p>
		</div>
	</section>
</body>
</html>