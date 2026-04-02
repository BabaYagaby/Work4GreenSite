<?php 
// Il est préférable de mettre l'include tout en haut
include('config.php'); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue - Work4Green</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <img id="logo" src="Images/Logo.png" alt="Logo de Work4Green">

    <img id="fond" src="Images/FondWork4Green.png" alt="">
    
    <main class="boutons">
        <p>Plus de cent mille entreprises font une différence grâce à notre application.</p>

        <a id="registration" href="registration.php">S'inscrire</a>
        <a id="connection" href="connection.php">Se connecter</a>
        
        <a id="admininscription" href="admininscription.php">
            S'inscrire en tant que chef·fe d'entreprise
        </a>
    </main>

</body>
</html>