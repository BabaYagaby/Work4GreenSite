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
</head>
<body>
    <h1>Vos invitations en attente</h1>

    <?php if ($invitation): ?>
        <div style="border: 2px solid green; padding: 20px; border-radius: 8px;">
            <p>L'entreprise <strong><?php echo htmlspecialchars($invitation['companyname'] ?? 'Inconnue'); ?></strong> souhaite vous recruter !</p>
            
            <form method="POST" action="response_invitation.php">
                <button type="submit" name="choix" value="accepter" style="background: green; color: white; padding: 10px;">Accepter</button>
                <button type="submit" name="choix" value="refuser" style="background: red; color: white; padding: 10px;">Refuser</button>
            </form>
        </div>
    <?php else: ?>
        <p>Vous n'avez aucune invitation pour le moment (Statut actuel en BDD : 0).</p>
    <?php endif; ?>
</body>
</html>