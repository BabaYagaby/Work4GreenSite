<?php
session_start();
include('config.php');

// Sécurité : Vérifier que l'utilisateur est connecté et est un gérant
if (!isset($_SESSION['user_id']) || $_SESSION['verified'] != 1) {
    header("Location: connection.php");
    exit();
}

$my_company_id = $_SESSION['company_id'];
$from = isset($_GET['from']) ? $_GET['from'] : 'options';

if (isset($_POST['envoyer'])) {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("Veuillez entrer un email valide.");</script>';
    } else {
        if (empty($my_company_id)) {
            echo '<script>alert("Erreur : vous n\'êtes pas rattaché(e) à une société.");</script>';
        } else {
            $check = $bdd->prepare("SELECT id, company_id FROM users WHERE email = :email");
            $check->execute(['email' => $email]);
            $user = $check->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                echo '<script>alert("Erreur : Aucun utilisateur ne possède cet email.");</script>';
            } else {
                if (!empty($user['company_id']) && $user['company_id'] != 0) {
                    echo '<script>alert("Cet utilisateur appartient déjà à une société.");</script>';
                } else {
                    $update = $bdd->prepare("UPDATE users SET company_id = :company_id, invitation_status = 1 WHERE id = :id");
                    $update->execute([
                        'company_id' => $my_company_id,
                        'id' => $user['id']
                    ]);
                    echo '<script>alert("Invitation envoyée avec succès à ' . $email . ' !");</script>';
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
    <title>Inviter mon équipe - Work4Green</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <img id="logo" src="Images/Logo2.png" alt="Logo de Work4Green">

    <main class="form-container"> 
        <form class="card" method="POST" action="">
            <h1>Agrandir l'équipe</h1>
            
            <p style="color: var(--dark); font-size: 0.9rem; margin-bottom: 20px; text-align: center; opacity: 0.8;">
                Saisissez l'adresse email de votre employé pour l'ajouter à votre entreprise.
            </p>

            <label for="email">Email de l'employé</label>
            <input type="email" name="email" id="email" placeholder="exemple@mail.fr" required>

            <input class="btn-primary" type="submit" value="Inviter" name="envoyer">

            <div style="text-align: center; margin-top: 15px;">
                <?php if ($from === 'reg'): ?>
                    <a href="quests.php" style="color: var(--secondary); font-weight: bold; text-decoration: underline; font-size: 0.85rem;">
                        Terminer et accéder à l'application
                    </a>
                <?php else: ?>
                    <a href="options.php" style="color: var(--secondary); font-weight: bold; text-decoration: underline; font-size: 0.85rem;">
                        Retour à l'application
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </main>

</body>
</html>