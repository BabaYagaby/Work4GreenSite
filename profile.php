<?php
session_start();
include('config.php');

// Vérification de la connexion
if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/**
 * RÉCUPÉRATION DES DONNÉES
 * On utilise des LEFT JOIN pour lier les IDs des badges/avatars 
 * aux images correspondantes dans items_catalog.
 */
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

if (!$user) {
    die("Erreur : Utilisateur introuvable dans la base de données.");
}

// Calcul de la barre de progression (XP sur 100)
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

    <div class="badges-row" style="display: flex; gap: 10px; justify-content: center; margin-top: 10px;">
        <?php for($i=1; $i<=3; $i++): ?>
            <div class="badge-slot" style="width: 45px; height: 45px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 1px dashed rgba(0,0,0,0.1);">
                <?php if(!empty($user['badge_'.$i])): ?>
                    <img src="<?= $user['badge_'.$i] ?>" alt="Badge <?= $i ?>" style="width: 35px; height: 35px; object-fit: contain;">
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>

    <section class="card d-flex items-center justify-evenly gap-md" style="background: var(--main); box-shadow: none; border-radius: 20px;">
        <div class="avatar big">
            <img src="<?= $user['avatar_img'] ?? './Images/perso.svg' ?>" alt="Avatar">
        </div>

        <div class="flex-col">
            <div class="text-title" style="font-size: 1.5rem; font-weight: bold;">
                <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?>
            </div>
            <div class="text-subtitle" style="opacity: 0.8;">
                <?= htmlspecialchars($user['companyname'] ?? 'Indépendant') ?>
            </div>
        </div>
    </section>

    <section class="card flex-col gap-sm">
        <div class="d-flex justify-between items-center">
            <div class="text-title-lvl" style="font-weight: bold;">Niveau <?= htmlspecialchars($user['level']) ?></div>
            <div class="text-subtitle"><?= htmlspecialchars($pourcentage) ?> / 100 XP</div>
        </div>
        <div class="progress-wrapper" style="background: #eee; border-radius: 10px; height: 12px; overflow: hidden;">
            <div class="progress-fill" style="width: <?= $pourcentage ?>%; background: var(--secondary); height: 100%; transition: width 0.5s;"></div>
        </div>
    </section>

    <section class="tabs" style="display: flex; justify-content: space-around; border-bottom: 1px solid #eee;">
        <a href="profile.php" class="tab active" style="padding: 10px; text-decoration: none; border-bottom: 3px solid var(--secondary); color: var(--secondary); font-weight: bold;">Avatar</a>
        <a href="profilestat.php" class="tab" style="padding: 10px; text-decoration: none; color: #888;">Stats</a>
        <a href="profilesuccess.php" class="tab" style="padding: 10px; text-decoration: none; color: #888;">Succès</a>
    </section>

    <section class="avatar-display" style="text-align: center; padding: 20px 0;">
        <div class="character-preview">
            <img src="<?= $user['avatar_img'] ?? './Images/perso.svg' ?>" alt="Grand personnage" style="max-height: 280px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">
        </div>
    </section>

    <section class="d-flex gap-md" style="padding: 0 10px;">
        <a href="profilecustom.php" class="btn btn-outline flex-1" style="text-align: center; padding: 12px; border: 1px solid #ddd; border-radius: 10px; text-decoration: none; color: #333;">Modifier</a>
        <a href="profileinventory.php" class="btn btn-primary flex-1" style="text-align: center; padding: 12px; background: var(--main); border-radius: 10px; text-decoration: none; color: black; font-weight: bold;">Inventaire</a>
    </section>

</main>

<nav class="navbar" style="position: fixed; bottom: 0; width: 100%; background: white; border-top: 1px solid #eee; padding: 10px 0;">
    <div class="navbar-inner" style="display: flex; justify-content: space-around; max-width: 500px; margin: 0 auto;">
        <a class="nav-icon" href="profile.php"><img src="./Images/profile-1341-svgrepo-com.svg" style="width: 25px;"></a>
        <a class="nav-icon" href="quests.php"><img src="./Images/notebook-svgrepo-com.svg" style="width: 25px;"></a>
        <a class="nav-icon" href="company.php"><img src="./Images/leaf-eco-svgrepo-com.svg" style="width: 25px;"></a>
        <a class="nav-icon" href="options.php"><img src="./Images/gear-svgrepo-com.svg" style="width: 25px;"></a>
    </div>
</nav>

</body>
</html>