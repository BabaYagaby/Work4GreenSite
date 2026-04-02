<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupération dynamique des données de l'utilisateur ET de son entreprise
$reqUser = $bdd->prepare("
    SELECT u.firstname, u.lastname, u.level, u.xp, c.companyname 
    FROM users u 
    LEFT JOIN company c ON u.company_id = c.id 
    WHERE u.id = ?
");
$reqUser->execute([$user_id]);
$user = $reqUser->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur introuvable.");
}

// Calcul de l'XP pour la barre de progression
$pourcentage = ($user['xp'] % 100);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Work4Green</title>
    <link rel="stylesheet" href="work4green.css">
</head>
<body>
    
<main class="app-container flex-col gap-md">

    <section class="card d-flex items-center justify-evenly gap-md" style="background: var(--main); box-shadow: none;">
        <div class="avatar big">
            <img src="./Images/perso-03.svg" alt="Avatar">
        </div>

        <div class="flex-col items-center">
            <div class="text-title"><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></div>
            <div class="text-subtitle text-third"><?= htmlspecialchars($user['companyname'] ?? 'Aucune entreprise') ?></div>

            <div class="badges mt-md">
                <img src="./Images/Badge-1.png" alt="Badge">
                <img src="./Images/Badge-2.png" alt="Badge">
                <img src="./Images/Badge-3.png" alt="Badge">
            </div>
        </div>
    </section>

    <section class="card flex-col gap-sm">
        <div class="d-flex justify-between items-center">
            <div class="text-title-lvl">Niveau <?= htmlspecialchars($user['level']) ?></div>
            <div class="text-subtitle"><?= htmlspecialchars($pourcentage) ?>/100 XP</div>
        </div>
        <div class="progress-wrapper">
            <div class="progress-fill" style="width: <?= htmlspecialchars($pourcentage) ?>%;"></div>
        </div>
    </section>

    <section class="tabs">
        <a href="profile.php" class="tab active">Avatar</a>
        <a href="profilestat.php" class="tab">Stats</a>
        <a href="profilesuccess.php" class="tab">Succès</a>
    </section>

    <section class="avatar-display">
        <div class="character">
            <img src="./Images/perso-03.svg" alt="Personnage en entier">
        </div>
    </section>

    <section class="d-flex gap-md">
        <a href="profilecustom.php" class="btn btn-outline flex-1">Modifier</a>
        <a href="profileinventory.php" class="btn btn-primary flex-1">Inventaire</a>
    </section>

</main>

<nav class="navbar">
    <div class="navbar-inner">
        <a class="nav-icon" href="profile.php"><img src="./Images/profile-1341-svgrepo-com.svg" alt="Profil"></a>
        <a class="nav-icon" href="quests.php"><img src="./Images/notebook-svgrepo-com.svg" alt="Quetes"></a>
        <a class="nav-icon" href="company.php"><img src="./Images/leaf-eco-svgrepo-com.svg" alt="W4G"></a>
        <a class="nav-icon" href="options.php"><img src="./Images/gear-svgrepo-com.svg" alt="Options"></a>
    </div>
</nav>

</body>
</html>