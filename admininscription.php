<?php
session_start(); // Toujours au début
include('config.php');

if(isset($_POST['envoyer'])){
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    
    // On définit verified à 1 car c'est un chef d'entreprise qui s'inscrit ici
    $verified = 1; 

    if($password === $confirmpassword) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // 1. On insère l'utilisateur
        $requete = $bdd->prepare("INSERT INTO users (lastname, firstname, email, password, verified) VALUES (:lastname, :firstname, :email, :password, :verified)");
        
        $requete->execute(array(
            "lastname"  => $lastname,
            "firstname" => $firstname,
            "email"     => $email,
            "password"  => $password_hash,
            "verified"  => $verified
        ));

        // 2. Récupération de l'ID pour la session
        $new_user_id = $bdd->lastInsertId();

        // 3. Ouverture de session
        $_SESSION['user_id'] = $new_user_id;
        $_SESSION['firstname'] = $firstname;
        $_SESSION['verified'] = $verified; 

        echo '<script>alert("Inscription réussie !"); window.location.href="companycreation.php";</script>';
        exit();

    } else {
        echo '<script>alert("Les mots de passe ne correspondent pas.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Manager - Work4Green</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <img id="logo" src="Images/Logo2.png" alt="Logo de Work4Green">

    <main class="form-container" style="top: -20px;"> <form class="card" method="POST" action="">
            <h1>S'inscrire en tant que chef·fe d'entreprise</h1>

            <label for="lastname">Nom</label>
            <input type="text" name="lastname" id="lastname" placeholder="Votre nom" required>

            <label for="firstname">Prénom</label>
            <input type="text" name="firstname" id="firstname" placeholder="Votre prénom" required>

            <label for="email">Email professionnel</label>
            <input type="email" name="email" id="email" placeholder="exemple@entreprise.fr" required>

            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" placeholder="Mot de passe" required>

            <label for="confirmpassword">Confirmer le mot de passe</label>
            <input type="password" name="confirmpassword" id="confirmpassword" placeholder="Répétez le mot de passe" required>

            <label for="verification">Code d'achat</label>
            <input type="text" name="verification" id="verification" placeholder="Code envoyé par email">

            <input class="btn-primary" type="submit" value="S'inscrire" name="envoyer">
            
            <div style="text-align: center; margin-top: 10px;">
                <a href="connection.php" style="font-size: 0.85rem; color: var(--secondary); font-weight: bold; text-decoration: underline;">
                    Déjà un compte ? Se connecter
                </a>
            </div>

            <div style="text-align: center; margin-top: 10px;">
                <a href="index.php" style="font-size: 0.85rem; color: var(--secondary); font-weight: bold; text-decoration: underline;">
                    ← Retour
                </a>
            </div>
        </form>
    </main>

</body>
</html>