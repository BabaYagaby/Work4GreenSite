<?php
session_start();
include('config.php');

// 1. Sécurité : Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Récupérer les infos réelles de l'utilisateur (verified et company_id)
$stmtUser = $bdd->prepare("SELECT verified, company_id FROM users WHERE id = ?");
$stmtUser->execute([$user_id]);
$currentUser = $stmtUser->fetch();

$verified = (int)$currentUser['verified'];
$my_company_id = $currentUser['company_id'];

// 3. Traitement du formulaire
if (isset($_POST['envoyer'])) {
    $email = trim($_POST['email']);

    // Sécurité PHP : on vérifie à nouveau si l'utilisateur est bien vérifié
    if ($verified !== 1) {
        echo '<script>alert("Action interdite : votre compte n\'est pas vérifié.");</script>';
    } 
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("Veuillez entrer un email valide.");</script>';
    } 
    elseif (empty($my_company_id)) {
        echo '<script>alert("Erreur : vous n\'êtes pas rattaché(e) à une société.");</script>';
    } 
    else {
        // Vérifier si l'utilisateur invité existe
        $check = $bdd->prepare("SELECT id, company_id FROM users WHERE email = :email");
        $check->execute(['email' => $email]);
        $invitedUser = $check->fetch(PDO::FETCH_ASSOC);

        if (!$invitedUser) {
            echo '<script>alert("Erreur : Aucun utilisateur ne possède cet email.");</script>';
        } 
        elseif (!empty($invitedUser['company_id'])) {
            echo '<script>alert("Cet utilisateur appartient déjà à une société.");</script>';
        } 
        else {
            // Mise à jour de l'invité
            $update = $bdd->prepare("UPDATE users SET company_id = :company_id, invitation_status = 1 WHERE id = :id");
            $update->execute([
                'company_id' => $my_company_id,
                'id' => $invitedUser['id']
            ]);
            echo '<script>alert("Invitation envoyée avec succès !");</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envoi d'invitations - Work4Green</title>
    <link rel="stylesheet" href="work4green.css">
</head>
<body>

    <div class="app-container">
        <h1>Inviter un collaborateur</h1>

        <?php if ($verified === 1): ?>
            
            <section class="card">
                <form method="POST" action="" class="flex-col gap-md">
                    <label for="email">Email de la personne que vous souhaitez inviter</label>
                    <input type="email" name="email" id="email" placeholder="exemple@mail.fr" required>
                    <input type="submit" value="Envoyer l'invitation" name="envoyer" class="btn btn-primary">
                </form>
            </section>

        <?php else: ?>

            <section class="card-error" style="background: #fff3f3; color: #d32f2f; padding: 20px; border-radius: 10px; border: 1px solid #f8d7da; text-align: center;">
                <p>⚠️ <strong>Accès restreint</strong></p>
                <p>Votre compte doit être vérifié par l'administration pour pouvoir inviter des employés.</p>
                <a href="profile.php" class="btn btn-outline" style="margin-top: 10px; display: inline-block;">Retour au profil</a>
            </section>

        <?php endif; ?>
    </div>

</body>
</html>