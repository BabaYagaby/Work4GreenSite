<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Work4Green</title>
</head>
<body>

    <?php
    session_start(); // 1. INDISPENSABLE pour que PHP se souvienne de l'utilisateur
    include('config.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $lastname = $_POST['lastname'];
        $firstname = $_POST['firstname'];
        $enteredpassword = $_POST['password'];

        if (!empty($lastname) && !empty($firstname) && !empty($enteredpassword)) {
            
            $req = $bdd->prepare("SELECT * FROM users WHERE lastname = :lastname AND firstname = :firstname");
            $req->execute([
                'lastname'  => $lastname,
                'firstname' => $firstname,
            ]);

            $user = $req->fetch();

            if ($user && password_verify($enteredpassword, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                // AJOUTE CETTE LIGNE (vérifie bien le nom de la colonne dans ta table users)
                $_SESSION['company_id'] = $user['company_id']; 
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['verified'] = $user['verified'];

                echo '<script>
                    alert("Connexion réussie ! Bienvenue ' . htmlspecialchars($firstname) . '");
                    window.location.href="pendinginvitation.php";
                </script>';

            } else {
                echo '<script>alert("Identifiants incorrects.");</script>';
            }
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
        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" placeholder="Mot de passe" required>
        <br>
        <input type="submit" value="Se connecter" name="envoyer">
    </form>

</body>
</html>