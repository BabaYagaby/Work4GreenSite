<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de votre entreprise</title>
</head>
<body>
    
<?php
include('config.php');
session_start();

$verified = isset($_SESSION['verified']) ? $_SESSION['verified'] : 0;
$user_id = $_SESSION['user_id']; // On récupère l'ID du gars connecté

if(isset($_POST['envoyer'])){
    $companyname = $_POST['companyname'];

    if($verified === 1) {
        // 1. On crée l'entreprise
        $requete = $bdd->prepare("INSERT INTO company (companyname) VALUES (:companyname)");
        $requete->execute(array("companyname" => $companyname));

        // 2. ON RÉCUPÈRE L'ID de l'entreprise qu'on vient de créer
        $new_company_id = $bdd->lastInsertId();

        // 3. ON MET À JOUR l'utilisateur pour le lier à cette entreprise
        $updateUser = $bdd->prepare("UPDATE users SET company_id = :company_id WHERE id = :user_id");
        $updateUser->execute([
            "company_id" => $new_company_id,
            "user_id" => $user_id
        ]);

        // 4. On met à jour la session pour que sendinvitation.php sache qu'il a une entreprise
        $_SESSION['company_id'] = $new_company_id;

        echo '<script>alert("Entreprise créée et liée à votre compte !"); window.location.href="sendinvitation.php";</script>';
    } else {
        echo '<script>alert("Erreur : vous n\'êtes pas vérifié.");</script>';
    }
}
?>

<form method="POST" action="">
    <label for="companyname">Nom de l'entreprise</label>
    <input type="text" name="companyname" id="companyname" placeholder="Nom de votre entreprise" required>
    <input type="submit" value="s'inscrire" name="envoyer">
</form>


</body>
</html>