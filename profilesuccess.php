<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Récupération des données utilisateur (inchangé)
$reqUser = $bdd->prepare("
    SELECT u.firstname, u.lastname, u.level, u.xp, c.companyname 
    FROM users u 
    LEFT JOIN company c ON u.company_id = c.id 
    WHERE u.id = ?
");
$reqUser->execute([$user_id]);
$user = $reqUser->fetch(PDO::FETCH_ASSOC);

// 2. Calcul du nombre de quêtes terminées pour les succès
$stmt = $bdd->prepare("SELECT COUNT(*) FROM user_quests WHERE user_id = ?");
$stmt->execute([$user_id]);
$totalQuests = $stmt->fetchColumn();

$pourcentage = ($user['xp'] % 100);

// Définition des succès (Demo)
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