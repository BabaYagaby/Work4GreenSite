<?php
include('config.php');
session_start();

// Sécurité : on vérifie que l'utilisateur est bien connecté et est un manager
if (!isset($_SESSION['user_id']) || $_SESSION['verified'] != 1) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(isset($_POST['envoyer'])){
    $companyname = htmlspecialchars($_POST['companyname']);

    // 1. On crée l'entreprise
    $requete = $bdd->prepare("INSERT INTO company (companyname, xp, level) VALUES (:companyname, 0, 1)");
    $requete->execute(array("companyname" => $companyname));

    // 2. ON RÉCUPÈRE L'ID de l'entreprise
    $new_company_id = $bdd->lastInsertId();

    // 3. ON MET À JOUR l'utilisateur
    $updateUser = $bdd->prepare("UPDATE users SET company_id = :company_id WHERE id = :user_id");
    $updateUser->execute([
        "company_id" => $new_company_id,
        "user_id" => $user_id
    ]);

    // 4. Mise à jour session
    $_SESSION['company_id'] = $new_company_id;

    // Redirection vers l'invitation avec le paramètre 'from=reg' pour le bouton dynamique
    echo '<script>
        alert("Entreprise créée avec succès !"); 
        window.location.href="sendinvitation.php?from=reg";
    </script>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer votre entreprise - Work4Green</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <img id="logo" src="Images/Logo2.png" alt="Logo de Work4Green">

    <main class="form-container">
        <form class="card" method="POST" action="">
            <h1>Votre Entreprise</h1>


            <label for="companyname">Nom de l'entreprise</label>
            <input type="text" name="companyname" id="companyname" placeholder="Ex: Green Corp, Ma Société..." required>

            <input class="btn-primary" type="submit" value="Créer" name="envoyer">

        </form>
    </main>

</body>
</html>