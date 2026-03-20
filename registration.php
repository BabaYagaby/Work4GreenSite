<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <img id="logo" src="Images/Logo2.png" alt="Logo de Work4Green Sombre">

    <main class="form-container">
        <form class="card" method="POST" action="traitement.php">
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