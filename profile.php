<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. LA REQUÊTE (On récupère bien l'image de l'avatar avec le JOIN)
$req = $bdd->prepare("
    SELECT u.*, c.companyname, 
           av.image_path as avatar_img,
           b1.image_path as badge_1,
           b2.image_path as badge_2,
           b3.image_path as badge_3
    FROM users u 
    LEFT JOIN company c ON u.company_id = c.id 
    LEFT JOIN items_catalog av ON u.current_avatar_id = av.id
    LEFT JOIN items_catalog b1 ON u.fav_badge_1 = b1.id
    LEFT JOIN items_catalog b2 ON u.fav_badge_2 = b2.id
    LEFT JOIN items_catalog b3 ON u.fav_badge_3 = b3.id
    WHERE u.id = ?
");
$req->execute([$user_id]);
$user = $req->fetch(PDO::FETCH_ASSOC);

if (!$user) { die("Utilisateur introuvable."); }

// 2. LA VARIABLE UNIQUE POUR L'IMAGE
// Si avatar_img est vide en BDD, on met le perso par défaut
$ma_photo_avatar = (!empty($user['avatar_img'])) ? trim($user['avatar_img']) : './Images/perso.svg';

$pourcentage = ($user['xp'] % 100);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil - Work4Green</title>
    <link rel="stylesheet" href="work4green.css">
</head>
<body>
    
<main class="app-container flex-col gap-md">

    <div class="badges-row" style="display: flex; gap: 10px; justify-content: center; margin-top: 15px;">
        <?php for($i=1; $i<=3; $i++): ?>
            <div class="badge-slot" style="width: 45px; height: 45px; background: rgba(0,0,0,0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 1px dashed #ccc;">
                <?php if(!empty($user['badge_'.$i])): ?>
                    <img src="<?= $user['badge_'.$i] ?>" style="width: 35px;">
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>

    <section class="card d-flex items-center justify-evenly gap-md" style="background: var(--main); border-radius: 20px; padding: 15px;">
        <div class="avatar-circle" style="width: 70px; height: 70px; border-radius: 50%; overflow: hidden; background: white;">
            <img src="<?= $ma_photo_avatar ?>" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div class="info">
            <div style="font-weight: bold; font-size: 1.2rem;"><?= htmlspecialchars($user['firstname']) ?></div>
            <div style="font-size: 0.9rem; opacity: 0.8;"><?= htmlspecialchars($user['companyname'] ?? 'Indépendant') ?></div>
        </div>
    </section>

    <section class="card flex-col gap-sm">
        <div class="d-flex justify-between">
            <span>Niveau <?= $user['level'] ?></span>
            <span><?= $pourcentage ?>/100 XP</span>
        </div>
        <div style="background: #eee; height: 10px; border-radius: 5px; overflow: hidden;">
            <div style="width: <?= $pourcentage ?>%; background: var(--secondary); height: 100%;"></div>
        </div>
    </section>

    <nav class="tabs" style="display: flex; justify-content: space-around; border-bottom: 1px solid #eee;">
        <a href="#" class="active" style="padding: 10px; border-bottom: 3px solid var(--secondary); text-decoration: none; color: black; font-weight: bold;">Avatar</a>
        <a href="profilestat.php" style="padding: 10px; text-decoration: none; color: #888;">Stats</a>
        <a href="profilesuccess.php" style="padding: 10px; text-decoration: none; color: #888;">Succès</a>
    </nav>

    <section class="avatar-display" style="text-align: center; padding: 40px 0; width: 100%; display: flex; justify-content: center; align-items: center;">
    <div class="character-preview" style="width: 100%; max-width: 300px;">
        <img src="<?= $ma_photo_avatar ?>" 
             alt="Grand personnage" 
             style="display: block; margin: 0 auto; width: auto; height: auto; max-height: 280px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.2)); position: relative; z-index: 10;">
    </div>
    </section>

    <section class="d-flex gap-md" style="padding: 0 10px;">
        <a href="profilecustom.php" class="btn-outline flex-1" style="text-align:center; padding:10px; border:1px solid #ccc; border-radius:10px; text-decoration:none; color:black;">Modifier</a>
        <a href="profileinventory.php" class="btn-primary flex-1" style="text-align:center; padding:10px; background:var(--main); border-radius:10px; text-decoration:none; color:black; font-weight:bold;">Inventaire</a>
    </section>

</main>

<nav class="navbar" style="position: fixed; bottom: 0; width: 100%; background: white; border-top: 1px solid #eee; padding: 10px 0;">
    <div style="display: flex; justify-content: space-around;">
        <a href="profile.php"><img src="./Images/profile-1341-svgrepo-com.svg" width="25"></a>
        <a href="quests.php"><img src="./Images/notebook-svgrepo-com.svg" width="25"></a>
        <a href="company.php"><img src="./Images/leaf-eco-svgrepo-com.svg" width="25"></a>
        <a href="options.php"><img src="./Images/gear-svgrepo-com.svg" width="25"></a>
    </div>
</nav>

</body>
</html>