<?php

require 'includes/connexion_bdd.php';


// Filtres PHP (utilisés notamment avec filter_var)
// https://www.php.net/manual/fr/filter.filters.php

require 'includes/sujets.php';

$vide = '';
$erreurs = [];


if (isset($_POST["email"])) {
	$email = $_POST["email"];
}
if (isset($_POST["prenom"])) {
	$prenom = $_POST["prenom"];
}
if (isset($_POST["nom"])) {
	$nom = $_POST["nom"];
}
if (isset($_POST["sujet"])) {
	$sujet = $_POST["sujet"];
}
if (isset($_POST["contenu"])) {
	$contenu = $_POST["contenu"];
}

if (empty($_POST) === false) {

	// Vérification des données saisies
	if (empty($_POST['email'])) {
		$erreurs['email'] = 'Veuillez saisir une adresse email.';
	} else {
		if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
			$erreurs['email'] = 'Veuillez saisir une adresse email valide.';
		}
	}

	if (empty($_POST['contenu'])) {
		$erreurs['contenu'] = 'Veuillez saisir un contenu.';
	} else {
		if (strlen($_POST['contenu']) > 2000) {
			$erreurs['contenu'] = 'Le contenu ne doit pas dépasser 2000 caractères.';
		}
	}

	$expressionReguliere = '/[\d\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/';

	if (empty($_POST['prenom']) === false) {
		if (preg_match($expressionReguliere, $_POST['prenom'])) {
			$erreurs['prenom'] = 'Le prénom ne doit pas contenir de chiffres et de caractères spéciaux.';
		}
	}

	if (empty($_POST['nom']) === false) {
		if (preg_match($expressionReguliere, $_POST['nom'])) {
			$erreurs['nom'] = 'Le nom ne doit pas contenir de chiffres et de caractères spéciaux.';
		}
	}

	if (isset($sujets[$_POST['sujet']]) === false) {
		$erreurs['sujet'] = 'Veuillez préciser un sujet valide.';
	}

	// Insertion des données si aucune erreur
	if(empty($erreurs)){
	// METTRE CODE AVEC INSERT ICI
		$query = $connexion -> prepare('INSERT INTO contact (contact_nom, contact_prenom, contact_email, contact_sujet, contact_contenu) VALUES ( :nom, :prenom, :email, :sujet, :contenu)');

		$query->bindParam(':nom', $nom);
		$query->bindParam(':prenom', $prenom);
		$query->bindParam(':email', $email);	
		$query->bindParam(':sujet', $sujet);
		$query->bindParam(':contenu', $contenu);
		$query->execute();

		//debugage
		$query->errorInfo();
	}
}

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Contactez-moi</title>
</head>
<body>

	<?php
		require 'includes/header.php';
	?>

	<form action="#" method="POST">
		
		<div>
			<label for="email">Email <span style="color: red;">*</span></label>
			<?= isset($erreurs['email']) ? $erreurs['email'] : null; ?>
			<input  type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES) : ''; ?>">
		</div>

		<div>
			<label for="prenom">Prénom</label>
			<input type="text" name="prenom" value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom'], ENT_QUOTES) : ''; ?>">
			<?= isset($erreurs['prenom']) ? $erreurs['prenom'] : null; ?>
		</div>

		<div>
			<label for="nom">Nom</label>
			<input type="text" name="nom" value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom'], ENT_QUOTES) : ''; ?>">
			<?= isset($erreurs['nom']) ? $erreurs['nom'] : null; ?>
		</div>

		<div>
			<label for="sujet">Sujet <span style="color: red;">*</span></label>
			<select name="sujet">
				<?php foreach ($sujets as $valeur => $nom) { ?>
				<option value="<?= $valeur ?>"><?= $nom ?></option>
				<?php } ?>
			</select>
			<?= isset($erreurs['sujet']) ? $erreurs['sujet'] : null; ?>
		</div>

		<div>
			<label for="contenu">Contenu <span style="color: red;">*</span></label>
			<textarea name="contenu"><?php echo isset($_POST['contenu']) ? $_POST['contenu'] : 'votre message...'; ?></textarea>
			<?= isset($erreurs['contenu']) ? $erreurs['contenu'] : null; ?>
		</div>

		<div>
			<input type="submit" name="validation">
		</div>
	</form>
</body>
</html>
