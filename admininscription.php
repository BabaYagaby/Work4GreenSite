<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription chef-fe d'entreprise</title>
</head>
<body>
    
<?php

include('config.php');


session_start(); // Toujours au début pour les sessions

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

        // 2. IMPORTANT : On récupère l'ID que la BDD vient de créer
        $new_user_id = $bdd->lastInsertId();

        // 3. On ouvre la session immédiatement
        $_SESSION['user_id'] = $new_user_id;
        $_SESSION['verified'] = $verified; 

        echo '<script>alert("Inscription réussie !"); window.location.href="companycreation.php";</script>';

    } else {
        echo '<script>alert("Les mots de passe ne correspondent pas.");</script>';
    }
}

?>

<form method="POST" action="">
    <label for="lastname">Nom</label>
    <input type="text" name="lastname" id="lastname" placeholder="Nom" required>
    <br>
    <label for="firstname">Prénom</label>
    <input type="text" name="firstname" id="firstname" placeholder="Prénom" required>
    <br>
    <label for="email">Email</label>
    <input type="text" name="email" id="email" placeholder="exemple@mail.fr" required>
    <br>
    <label for="password">Mot de passe</label>
    <input type="password" name="password" id="password" placeholder="Mot de passe" required>
    <br>
    <label for="confirmpassword">Confirmez le mot de passe</label>
    <input type="password" name="confirmpassword" id="confirmpassword" placeholder="Confirmation du mot de passe" required>
    <br>
    <label for="verification">Code de vérification envoyé à votre email</label>
    <input type="text" name="verification" id="verification" placeholder="Votre code">
    <br>
    <input type="submit" value="s'inscrire" name="envoyer">
</form>


</body>
</html>