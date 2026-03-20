<?php
session_start();
include('config.php');

// Récupérer l'ID de la société du patron connecté
$my_company_id = isset($_SESSION['company_id']) ? (int)$_SESSION['company_id'] : null;

if (isset($_POST['envoyer'])) {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("Veuillez entrer un email valide.");</script>';
    } else {
        // Vérifier que le patron est bien rattaché à une société
        if (empty($my_company_id) || $my_company_id === 0) {
            echo '<script>alert("Erreur : vous n\'êtes pas rattaché(e) à une société.");</script>';
        } else {
            // Vérifier si l'utilisateur existe
            $check = $bdd->prepare("SELECT id, company_id FROM users WHERE email = :email");
            $check->execute(['email' => $email]);
            $user = $check->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                echo '<script>alert("Erreur : Aucun utilisateur ne possède cet email.");</script>';
            } else {
                // Si l'utilisateur est déjà dans une société
                if (!empty($user['company_id']) && $user['company_id'] != 0) {
                    echo '<script>alert("Cet utilisateur appartient déjà à une société.");</script>';
                } else {
                    // Mettre à jour: définir company_id et statut d'invitation
                    $update = $bdd->prepare("UPDATE users SET company_id = :company_id, invitation_status = 1 WHERE id = :id");
                    $update->execute([
                        'company_id' => $my_company_id,
                        'id' => $user['id']
                    ]);

                    // Ici tu peux ajouter l'envoi d'un email contenant un lien d'acceptation si tu veux
                    echo '<script>alert("Invitation envoyée avec succès !");</script>';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envoi d'invitations</title>
</head>
<body>
    <form method="POST" action="">
        <label for="email">Email de la personne que vous souhaitez inviter</label>
        <input type="email" name="email" id="email" placeholder="exemple@mail.fr" required>
        <input type="submit" value="inviter" name="envoyer">
    </form>
</body>
</html>