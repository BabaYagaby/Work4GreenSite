<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

// On récupère les infos de progression de l'utilisateur
$req = $bdd->prepare("SELECT xp, level, firstname FROM users WHERE id = :id");
$req->execute(['id' => $_SESSION['user_id']]);
$user = $req->fetch();

// --- LOGIQUE DE NIVEAU ---
$xp_par_niveau = 100; 
$xp_actuelle = $user['xp'];

// Calcul du niveau actuel et de la progression
$niveau_calcule = floor($xp_actuelle / $xp_par_niveau);
$progression_dans_le_niveau = $xp_actuelle % $xp_par_niveau; // Le reste pour la barre
$pourcentage = ($progression_dans_le_niveau / $xp_par_niveau) * 100;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Niveau de <?php echo htmlspecialchars($user['firstname']); ?></title>
    <style>
        .container { width: 400px; margin: 50px auto; text-align: center; font-family: sans-serif; }
        .lvl-badge { font-size: 2em; font-weight: bold; color: #2ecc71; }
        .progress-bg { background: #e0e0e0; border-radius: 20px; height: 25px; width: 100%; margin: 15px 0; overflow: hidden; }
        .progress-fill { 
            background: linear-gradient(90deg, #2ecc71, #27ae60); 
            height: 100%; 
            width: <?php echo $pourcentage; ?>%; 
            transition: width 0.5s ease-in-out;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Progression de l'entreprise</h1>
    <p>Chef d'entreprise : <strong><?php echo htmlspecialchars($user['firstname']); ?></strong></p>
    
    <div class="lvl-badge">Niveau <?php echo $niveau_calcule; ?></div>
    
    <div class="progress-bg">
        <div class="progress-fill"></div>
    </div>
    
    <p><?php echo $progression_dans_le_niveau; ?> / <?php echo $xp_par_niveau; ?> XP</p>
    <p><small>Total accumulé : <?php echo $xp_actuelle; ?> XP</small></p>
</div>

    <footer class="boutons">

        <a id="profile" href="profile.php">Profile</a>

        <a id="company" href="company.php">Entreprise</a>

        <a id="quests" href="quests.php">Quêtes</a>

        <a id="settings" href="settings.php">Paramètres</a>

    </footer>

</body>
</html>