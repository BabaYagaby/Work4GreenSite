<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. On récupère les infos du user + son entreprise + ses objets ÉQUIPÉS
$req = $bdd->prepare("
    SELECT u.*, c.companyname, 
           av.image_path as avatar_img,
           bg.image_path as badge_img
    FROM users u 
    LEFT JOIN company c ON u.company_id = c.id 
    LEFT JOIN items_catalog av ON u.current_avatar_id = av.id
    LEFT JOIN items_catalog bg ON u.favorite_badge_id = bg.id
    WHERE u.id = ?
");
$req->execute([$user_id]);
$user = $req->fetch(PDO::FETCH_ASSOC);

if (!$user) { die("Utilisateur introuvable."); }

// 2. On récupère la liste des badges POSSÉDÉS (pour la petite collection)
$reqBadges = $bdd->prepare("
    SELECT c.image_path 
    FROM items_catalog c
    JOIN user_inventory i ON c.id = i.item_id
    WHERE i.user_id = ? AND c.type = 'badge'
    LIMIT 3
");
$reqBadges->execute([$user_id]);
$mesBadges = $reqBadges->fetchAll();

// Calcul de l'XP
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
    
<main class="app-container flex-col gap-lg">

    <section class="card d-flex items-center justify-evenly gap-md" style="background: var(--neutral); box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
        <div class="avatar big">
            <img src="<?= $user['avatar_img'] ?? './images/perso.svg' ?>" alt="Avatar">
        </div>

        <div class="flex-col items-center">
            <div class="text-title"><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></div>
            <div class="text-subtitle text-third"><?= htmlspecialchars($user['companyname'] ?? 'Aucune entreprise') ?></div>

            <div class="badges mt-md" style="display: flex; gap: 5px;">
                <?php foreach($mesBadges as $b): ?>
                    <img src="<?= $b['image_path'] ?>" alt="Badge" style="width: 25px; height: 25px;">
                <?php endforeach; ?>
                <?php if(empty($mesBadges)): ?>
                    <small style="color: #aaa;">Aucun badge</small>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="card flex-col gap-sm">
        <div class="d-flex justify-between items-center">
            <div class="text-title-lvl">Niveau <?= htmlspecialchars($user['level']) ?></div>
            <div class="text-subtitle"><?= htmlspecialchars($pourcentage) ?>/100 XP</div>
        </div>
        <div class="progress-wrapper">
            <div class="progress-fill" style="width: <?= $pourcentage ?>%;"></div>
        </div>
    </section>

    <section class="tabs">
        <a href="profile.php" class="tab active">Avatar</a>
        <a href="profilestat.php" class="tab">Stats</a>
        <a href="profilesuccess.php" class="tab">Succès</a>
    </section>

    <section class="avatar-display" style="text-align: center;">
        <div class="character">
            <img src="<?= $user['avatar_img'] ?? './images/perso.svg' ?>" alt="Personnage" style="max-height: 250px;">
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