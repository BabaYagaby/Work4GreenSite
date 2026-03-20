<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>
<body>
    

<form method="POST" action="traitement.php">
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
    <input type="submit" value="s'inscrire" name="envoyer">
</form>


</body>
</html>