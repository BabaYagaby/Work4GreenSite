<?php
session_start();
include('config.php');

// 1. Vérification de la session
if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Récupération du profil complet (Infos de base + Entreprise + Avatar + Badges)
// On utilise la version du deuxième bloc qui est plus exhaustive
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

// 3. Gestion de l'avatar par défaut et calcul de progression
$ma_photo_avatar = (!empty($user['avatar_img'])) ? trim($user['avatar_img']) : './Images/perso-03.svg';
$pourcentage = ($user['xp'] % 100);

// ==========================================
// 4. LOGIQUE DU GRAPHIQUE (Activité sur 14 jours)
// ==========================================

// Initialisation des tableaux pour les 7 jours (0 = Lundi, 6 = Dimanche)
$jours = ['Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.', 'Dim.'];
$xp_actuelle = array_fill(0, 7, 0);
$xp_derniere = array_fill(0, 7, 0);
$total_taches = 0;
$total_points_gagnes = 0;

// Récupération des quêtes complétées
$reqStats = $bdd->prepare("
    SELECT uq.date_completion, q.quest_xp 
    FROM user_quests uq
    JOIN quest q ON uq.quest_id = q.quest_id
    WHERE uq.user_id = ? 
    AND uq.date_completion >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
");
$reqStats->execute([$user_id]);
$historique = $reqStats->fetchAll(PDO::FETCH_ASSOC);

// Dates pivots pour le tri
$debut_semaine_actuelle = strtotime('monday this week');
$debut_semaine_derniere = strtotime('monday last week');

foreach ($historique as $row) {
    $date_timestamp = strtotime($row['date_completion']);
    // Index 0 à 6 pour Lundi à Dimanche
    $jour_index = date('N', $date_timestamp) - 1; 

    if ($date_timestamp >= $debut_semaine_actuelle) {
        // Semaine en cours
        $xp_actuelle[$jour_index] += $row['quest_xp'];
        $total_taches++;
        $total_points_gagnes += $row['quest_xp'];
    } elseif ($date_timestamp >= $debut_semaine_derniere && $date_timestamp < $debut_semaine_actuelle) {
        // Semaine précédente
        $xp_derniere[$jour_index] += $row['quest_xp'];
    }
}

// Calcul du maximum pour l'échelle visuelle du graphique
$max_xp = max(max($xp_actuelle), max($xp_derniere));
if ($max_xp == 0) $max_xp = 1; 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Stats - Work4Green</title>
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
        <a href="profilestat.php" class="tab active">Stats</a>
        <a href="profilesuccess.php" class="tab">Succès</a>
    </section>

    <section class="card flex-col gap-md">
        <div class="d-flex justify-between items-center">
            <h2 class="text-title" style="font-size: 1.5rem;">Statistiques</h2>
            <select class="stat-select">
                <option>Semaine </option>
                <option>Mois </option>
            </select>
        </div>

        <div class="chart-container">
            <?php for ($i = 0; $i < 7; $i++): 
                $hauteur_actuelle = ($xp_actuelle[$i] / $max_xp) * 100;
                $hauteur_derniere = ($xp_derniere[$i] / $max_xp) * 100;
            ?>
            <div class="chart-column">
                <div class="bars">
                    <div class="bar current" style="height: <?= $hauteur_actuelle ?>%;"></div>
                    <div class="bar previous" style="height: <?= $hauteur_derniere ?>%;"></div>
                </div>
                <div class="chart-label"><?= $jours[$i] ?></div>
            </div>
            <?php endfor; ?>
        </div>

        <div class="d-flex justify-between items-end mt-md">
            <div class="chart-legend">
                <div><span class="legend-box current"></span> Semaine actuelle</div>
                <div><span class="legend-box previous"></span> Semaine dernière</div>
            </div>
            <div class="text-title" style="font-size: 1rem; text-align: right;">
                +<?= $total_points_gagnes ?> points<br>
                <span class="text-body" style="color: var(--dark); font-weight: bold;">
                    <?= $total_taches ?> tâches réalisées cette semaine
                </span>
            </div>
        </div>
    </section>

    <section class="stats-grid">
        <div class="stat-box box-dark">
            <div class="stat-number">18</div>
            <div class="stat-label">kg CO₂ évités</div>
        </div>
        <div class="stat-box box-medium">
            <div class="stat-number">42</div>
            <div class="stat-label">km à vélo</div>
        </div>
        <div class="stat-box box-light">
            <div class="stat-number">156</div>
            <div class="stat-label">arbres plantés</div>
        </div>
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