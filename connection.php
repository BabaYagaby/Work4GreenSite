<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Work4Green</title>
    <link rel="stylesheet" href="style.css">
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

                // 1. On remplit la session avec les données de la BDD
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['company_id'] = $user['company_id'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['verified'] = $user['verified'];

                // 2. On détermine la page de destination
                // Si l'utilisateur a une entreprise (id différent de 0)
                if ($user['company_id'] != 0) {
                    $page_destination = "quests.php";
                } else {
                    $page_destination = "pendinginvitation.php";
                }

                // 3. Une seule alerte et une seule redirection
                echo '<script>
                    alert("Connexion réussie ! Bienvenue ' . htmlspecialchars($user['firstname']) . '");
                    window.location.href="' . $page_destination . '";
                </script>';
                exit();

            } else {
                echo '<script>alert("Identifiants incorrects.");</script>';
            }
        }
    }
    ?>

    <img id="logo" src="Images/Logo2.png" alt="Logo de Work4Green Sombre">

    <main class="form-container">
        <form class="card" method="POST" action="">
            <h1>Se connecter</h1>

            <label for="lastname">Nom</label>
            <input type="text" name="lastname" id="lastname" placeholder="Nom" required>

            <label for="firstname">Prénom</label>
            <input type="text" name="firstname" id="firstname" placeholder="Prénom" required>

            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" placeholder="Mot de passe" required>

            <input class="btn-primary" type="submit" value="Se connecter" name="envoyer">
        </form>
    </main>

</body>
</html>