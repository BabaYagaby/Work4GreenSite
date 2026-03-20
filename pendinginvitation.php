<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// REQUÊTE : On utilise LEFT JOIN pour ne pas disparaître si l'ID entreprise est bizarre
$query = $bdd->prepare("
    SELECT u.*, c.companyname 
    FROM users u 
    LEFT JOIN company c ON u.company_id = c.id 
    WHERE u.id = :id AND u.invitation_status = 1
");
$query->execute(['id' => $user_id]);
$invitation = $query->fetch(); // Ici on crée la variable $invitation
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Invitations</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <img id="logo" src="Images/Logo2.png" alt="Logo de Work4Green Sombre">

    <main class="content">
        <h1>Vos invitations en attente</h1>

        <?php if ($invitation): ?>
            <section class="invitation-card">
                <p>L'entreprise <strong><?php echo htmlspecialchars($invitation['companyname'] ?? 'Inconnue'); ?></strong> souhaite vous recruter !</p>

                <form method="POST" action="response_invitation.php" class="invitation-actions">
                    <button class="btn-accept" type="submit" name="choix" value="accepter">Accepter</button>
                    <button class="btn-decline" type="submit" name="choix" value="refuser">Refuser</button>
                </form>
            </section>
        <?php else: ?>
            <p class="muted">Vous n'avez aucune invitation pour le moment.</p>
        <?php endif; ?>
    </main>
</body>
</html>