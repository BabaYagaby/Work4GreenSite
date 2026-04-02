<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// REQUÊTE : On récupère l'invitation en cours
$query = $bdd->prepare("
    SELECT u.*, c.companyname 
    FROM users u 
    LEFT JOIN company c ON u.company_id = c.id 
    WHERE u.id = :id AND u.invitation_status = 1
");
$query->execute(['id' => $user_id]);
$invitation = $query->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Invitations - Work4Green</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Styles spécifiques pour les actions d'invitation (non présents dans style.css de base) */
        .invitation-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            margin-top: 20px;
        }
        
        .btn-accept {
            background-color: #4CAF50 !important; /* Un vert plus "positif" que le dark */
            color: white !important;
            border: none;
            padding: 14px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-decline {
            background-color: #f44336 !important; /* Rouge pour refuser */
            color: white !important;
            border: none;
            padding: 14px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn-accept:hover, .btn-decline:hover {
            transform: scale(1.02);
            filter: brightness(1.1);
        }

        .empty-state {
            text-align: center;
            padding: 20px;
        }
        
        .loader {
            border: 4px solid var(--input);
            border-top: 4px solid var(--secondary);
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 2s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <img id="logo" src="Images/Logo2.png" alt="Logo de Work4Green">

    <main class="form-container">
        <div class="card">
            <h1>Mes Invitations</h1>

            <?php if ($invitation): ?>
                <div class="invitation-content" style="text-align: center;">
                    <p style="color: var(--dark); margin-bottom: 15px;">
                        Bonne nouvelle ! l'entreprise 
                        <strong style="color: var(--secondary); font-size: 1.1rem; display: block; margin: 5px 0;">
                            <?= htmlspecialchars($invitation['companyname'] ?? 'Inconnue'); ?>
                        </strong> 
                        souhaite vous recruter dans son équipe.
                    </p>
                    
                    <form method="POST" action="response_invitation.php" class="invitation-actions">
                        <button class="btn-accept" type="submit" name="choix" value="accepter">
                            Accepter l'invitation
                        </button>
                        <button class="btn-decline" type="submit" name="choix" value="refuser">
                            Refuser
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p style="color: var(--dark); opacity: 0.7;">
                        Vous n'avez aucune invitation en attente pour le moment.
                    </p>
                    <div class="loader"></div>
                    <p style="font-size: 0.8rem; color: var(--secondary);">
                        En attente d'une action de votre chef·fe d'entreprise...
                    </p>
                    
                    <a href="logout.php" style="display: inline-block; margin-top: 25px; color: var(--dark); font-size: 0.85rem; font-weight: bold; text-decoration: underline;">
                        Se déconnecter
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>