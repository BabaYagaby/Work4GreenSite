<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Récupération des infos utilisateur basiques (inchangé)
$reqUser = $bdd->prepare("
    SELECT u.firstname, u.lastname, u.level, u.xp, c.companyname 
    FROM users u 
    LEFT JOIN company c ON u.company_id = c.id 
    WHERE u.id = ?
");
$reqUser->execute([$user_id]);
$user = $reqUser->fetch(PDO::FETCH_ASSOC);

$pourcentage = ($user['xp'] % 100);

// ==========================================
// 2. LOGIQUE DU GRAPHIQUE (NOUVEAU)
// ==========================================

// Initialisation des tableaux pour les 7 jours (0 = Lundi, 6 = Dimanche)
$jours = ['Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.', 'Dim.'];
$xp_actuelle = array_fill(0, 7, 0);
$xp_derniere = array_fill(0, 7, 0);
$total_taches = 0;
$total_points_gagnes = 0;

// On récupère toutes les quêtes complétées par l'utilisateur avec leur XP
$reqStats = $bdd->prepare("
    SELECT uq.date_completion, q.quest_xp 
    FROM user_quests uq
    JOIN quest q ON uq.quest_id = q.quest_id
    WHERE uq.user_id = ? 
    AND uq.date_completion >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
");
$reqStats->execute([$user_id]);
$historique = $reqStats->fetchAll(PDO::FETCH_ASSOC);

// On trie les résultats dans la semaine actuelle ou la précédente
$debut_semaine_actuelle = strtotime('monday this week');
$debut_semaine_derniere = strtotime('monday last week');

foreach ($historique as $row) {
    $date_timestamp = strtotime($row['date_completion']);
    // 'N' donne le jour de 1 (Lundi) à 7 (Dimanche). On fait -1 pour l'index du tableau (0 à 6)
    $jour_index = date('N', $date_timestamp) - 1; 

    if ($date_timestamp >= $debut_semaine_actuelle) {
        // Semaine actuelle
        $xp_actuelle[$jour_index] += $row['quest_xp'];
        $total_taches++;
        $total_points_gagnes += $row['quest_xp'];
    } elseif ($date_timestamp >= $debut_semaine_derniere && $date_timestamp < $debut_semaine_actuelle) {
        // Semaine dernière
        $xp_derniere[$jour_index] += $row['quest_xp'];
    }
}

// Pour le CSS, il nous faut un point de référence maximum pour que la plus grande barre fasse 100% de hauteur
$max_xp = max(max($xp_actuelle), max($xp_derniere));
if ($max_xp == 0) $max_xp = 1; // Éviter la division par zéro si l'utilisateur n'a rien fait
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