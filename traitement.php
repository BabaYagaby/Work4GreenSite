<?php

include('config.php');


if(isset($_POST['envoyer'])){
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    $verified = $_POST['verification'];

    if ($password === $confirmpassword) {

        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        // On prépare la requête avec 4 paramètres : :lastname, :firstname, :email, :pass
        $requete = $bdd->prepare("INSERT INTO users (lastname, firstname, email, password, verified) VALUES (:lastname, :firstname, :email, :password, :verified)");
        
        // L'array DOIT contenir exactement les mêmes clés que les ":" ci-dessus
        $requete->execute(array(
            "lastname"  => $lastname,
            "firstname" => $firstname,
            "email"     => $email,
            "password"  => $password_hash, // 'pass' ici doit correspondre à ':pass' en haut
            "verified"  => $verified = 0
        ));

        echo '<script>alert("Inscription réussie !"); window.location.href="pendinginvitation.php";</script>';
        

    } else {
        echo '<script>alert("Les mots de passe ne correspondent pas."); window.location.href="registration.php";</script>';
    }
}
?>