<?php
session_start();
include('config.php');

// 1. Vérification de la session
if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. RÉCUPÉRATION COMPLÈTE DU PROFIL (Infos, Entreprise, Avatar, Badges)
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
    die("Utilisateur introuvable."); 
}

// 3. LOGIQUE DES QUÊTES ET PROGRESSION
// On compte le nombre total de quêtes terminées
$stmt = $bdd->prepare("SELECT COUNT(*) FROM user_quests WHERE user_id = ?");
$stmt->execute([$user_id]);
$totalQuests = $stmt->fetchColumn();

// Calcul de la barre de progression (XP actuelle vs palier de 100)
$pourcentage = ($user['xp'] % 100);

// Gestion de l'avatar (image par défaut si vide)
$ma_photo_avatar = (!empty($user['avatar_img'])) ? trim($user['avatar_img']) : './Images/perso-03.svg';

// 4. DÉFINITION DES SUCCÈS (Achievements)
$achievements = [
    ['id' => 1, 'name' => 'Bienvenue !', 'desc' => 'Avoir créé son compte.', 'req' => 0, 'type' => 'count', 'icon' => '🎉'],
    ['id' => 2, 'name' => 'Écolo en herbe', 'desc' => 'Terminer 5 quêtes.', 'req' => 5, 'type' => 'count', 'icon' => '🌱'],
    ['id' => 3, 'name' => 'Expert du tri', 'desc' => 'Terminer 10 quêtes.', 'req' => 10, 'type' => 'count', 'icon' => '♻️'],
    ['id' => 4, 'name' => 'Montée en puissance', 'desc' => 'Atteindre le niveau 5.', 'req' => 5, 'type' => 'level', 'icon' => '⚡'],
    ['id' => 5, 'name' => 'Pilier de l\'entreprise', 'desc' => 'Atteindre le niveau 10.', 'req' => 10, 'type' => 'level', 'icon' => '🏆'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Succès - Work4Green</title>
    <link rel="stylesheet" href="work4green.css">
</head>
<body>
    
<main class="app-container flex-col gap-md">

    <section class="card d-flex items-center justify-evenly gap-md" style="background: var(--main); box-shadow: none;">
        <div class="avatar big">
            <img src="<?= htmlspecialchars($ma_photo_avatar) ?>" alt="Avatar">
        </div>

        <div class="flex-col items-center">
            <div class="text-title"><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></div>
            <div class="text-subtitle text-third"><?= htmlspecialchars($user['companyname'] ?? 'Indépendant') ?></div>

            <div class="badges mt-md">
                <?php for($i=1; $i<=3; $i++): ?>
                    <?php if(!empty($user['badge_'.$i])): ?>
                        <img src="<?= htmlspecialchars($user['badge_'.$i]) ?>" alt="Badge <?= $i ?>">
                    <?php endif; ?>
                <?php endfor; ?>
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
        <a href="profile.php" class="tab">Avatar</a>
        <a href="profilestat.php" class="tab">Stats</a>
        <a href="profilesuccess.php" class="tab active">Succès</a>
    </section>

    <section class="flex-col gap-sm">
        <h2 class="text-title" style="margin-bottom: 5px;">Mes exploits</h2>
        
        <?php foreach ($achievements as $ach): 
            // Logique de déblocage
            $isUnlocked = ($ach['type'] == 'count') ? ($totalQuests >= $ach['req']) : ($user['level'] >= $ach['req']);
            $currentProgress = ($ach['type'] == 'count') ? $totalQuests : $user['level'];
        ?>
            <div class="achievement-card <?= $isUnlocked ? '' : 'locked' ?>">
                <div class="achievement-icon"><?= $ach['icon'] ?></div>
                <div class="achievement-info">
                    <div class="achievement-name"><?= $ach['name'] ?></div>
                    <div class="achievement-desc"><?= $ach['desc'] ?></div>
                </div>
                <div class="achievement-status">
                    <?php if ($isUnlocked): ?>
                        <span class="status-badge unlocked">Complété</span>
                    <?php else: ?>
                        <span class="status-progress"><?= $currentProgress ?> / <?= $ach['req'] ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
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