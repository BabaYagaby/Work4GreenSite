<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

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

    <img id="logo" src="Images/Logo2.png" alt="Logo de Work4Green Sombre">

    <main class="form-container">
        <form class="card" method="POST" action="">
            <h1>Créer un compte</h1>

            <label for="lastname">Nom</label>
            <input type="text" name="lastname" id="lastname" placeholder="Nom" required>

            <label for="firstname">Prénom</label>
            <input type="text" name="firstname" id="firstname" placeholder="Prénom" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="exemple@mail.fr" required>

            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" placeholder="Mot de passe" required>

            <label for="confirmpassword">Confirmez le mot de passe</label>
            <input type="password" name="confirmpassword" id="confirmpassword" placeholder="Confirmation du mot de passe" required>

            <div class="checkbox-row">
                <input type="checkbox" id="checkbox" name="checkbox" required />
                <label for="checkbox"><a href="terms.html">J'accepte les termes et conditions d'utilisation</a></label>
            </div>

            <input class="btn-primary" type="submit" value="S'inscrire" name="envoyer">
        </form>
    </main>


</body>
</html>