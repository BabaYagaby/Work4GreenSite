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

        if ($password === $confirmpassword) {
            
            // --- ÉTAPE DE VÉRIFICATION DE L'EMAIL ---
            $checkEmail = $bdd->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $checkEmail->execute(['email' => $email]);
            $emailExists = $checkEmail->fetchColumn();

            if ($emailExists > 0) {
                echo '<script>alert("Cet email est déjà utilisé par un autre compte."); window.location.href="registration.php";</script>';
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                // 1. Inscription de l'utilisateur
                // On ajoute current_avatar_id = 3 directement à l'inscription pour qu'il soit équipé
                $requete = $bdd->prepare("INSERT INTO users (lastname, firstname, email, password, verified, company_id, invitation_status, xp, level, current_avatar_id) VALUES (:lastname, :firstname, :email, :password, :verified, 0, 0, 0, 0, 3)");
                
                $requete->execute(array(
                    "lastname"  => $lastname,
                    "firstname" => $firstname,
                    "email"     => $email,
                    "password"  => $password_hash,
                    "verified"  => 0 
                ));

                // 2. Récupération de l'ID du nouvel utilisateur
                $new_user_id = $bdd->lastInsertId();

                // ça donne l'avatar de base direct
                $addInventory = $bdd->prepare("INSERT INTO user_inventory (user_id, item_id, obtained_at) VALUES (?, 3, NOW())");
                $addInventory->execute([$new_user_id]);

                echo '<script>alert("Inscription réussie !"); window.location.href="pendinginvitation.php";</script>';
            }

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

        <div style="text-align: center; margin-top: 10px;">
        <a href="index.php" style="font-size: 0.85rem; color: var(--secondary); font-weight: bold; text-decoration: underline;">
            ← Retour
        </a>
        </div>
        </form>
    </main>


</body>
</html>