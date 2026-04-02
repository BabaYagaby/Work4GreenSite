<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$my_company_id = $_SESSION['company_id'] ?? 0;

// Initialisation des variables pour éviter les erreurs "Undefined variable"
$xp_entreprise = 0;
$niveau_calcule = 0;
$nom_ma_boite = "Aucune entreprise";
$classementEntreprises = [];

// 1. Infos du membre connecté
$reqPerso = $bdd->prepare("SELECT xp, firstname FROM users WHERE id = :id");
$reqPerso->execute(['id' => $user_id]);
$user = $reqPerso->fetch();

// 2. Récupération des données de l'entreprise
if ($my_company_id > 0) {
    // Requête pour l'entreprise de l'utilisateur
    $reqBoite = $bdd->prepare("SELECT companyname, xp, level FROM company WHERE id = :id");
    $reqBoite->execute(['id' => $my_company_id]);
    $infoBoite = $reqBoite->fetch();

    if ($infoBoite) {
        $nom_ma_boite = $infoBoite['companyname'];
        $xp_entreprise = (int)$infoBoite['xp']; 
        $niveau_calcule = (int)$infoBoite['level']; 
    }

    // Requête pour le Top 10 des entreprises
    $reqLeader = $bdd->query("SELECT companyname, xp FROM company ORDER BY xp DESC LIMIT 10");
    $classementEntreprises = $reqLeader->fetchAll(PDO::FETCH_ASSOC);
}

// 3. Calculs de progression
$xp_par_niveau = 500; 
$progression = $xp_entreprise % $xp_par_niveau;
$pourcentage = ($progression / $xp_par_niveau) * 100;

// 4. Choix de l'image de l'arbre
if ($niveau_calcule >= 10) {
    // Niveau 10 et plus (10 à 14, 15, etc.)
    $image_arbre = "arbre3.png";
} elseif ($niveau_calcule >= 5) { 
    // Niveau 5 à 9
    $image_arbre = "arbre2.png";
} else {
    // Niveau 0 à 4
    $image_arbre = "arbre1.png";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Work4Green - Entreprise</title>
    <link rel="stylesheet" href="work4green.css">
    <style>
        .card { border: 1px solid #ddd; padding: 20px; margin: 10px; border-radius: 10px; }
        .progress-bg { background: #eee; width: 100%; height: 20px; border-radius: 10px; overflow: hidden; margin: 10px 0; }
        .progress-fill { background: #27ae60; height: 100%; transition: width 0.5s; }
        .current-user { background-color: #eafff2; font-weight: bold; }
        .leaderboard { width: 100%; border-collapse: collapse; }
        .leaderboard td, .leaderboard th { padding: 10px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>

    <div class="card" style="text-align: center;">
        <h1>🌍 <?= htmlspecialchars($nom_ma_boite) ?></h1>
        
        <div class="tree-container">
            <img src="images/<?= $image_arbre ?>" alt="Évolution" style="width: 150px; height: auto; margin-bottom: 10px;">
        </div>

        <div style="font-size: 1.5em; font-weight: bold; color: #27ae60;">
            Niveau <?= $niveau_calcule ?>
        </div>
        
        <div class="progress-bg">
            <div class="progress-fill" style="width: <?= $pourcentage ?>%;"></div>
        </div>
        <p style="margin:0;"><?= $progression ?> / <?= $xp_par_niveau ?> XP avant le prochain niveau</p>
    </div>

    <div class="card">
        <h2>🏆 Top 10 des Entreprises Vertes</h2>
        <table class="leaderboard">
            <thead>
                <tr>
                    <th>Rang</th>
                    <th>Entreprise</th>
                    <th>Score Total</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $rank = 1; 
                if (!empty($classementEntreprises)):
                    foreach($classementEntreprises as $entreprise): 
                        $est_mon_entreprise = ($entreprise['companyname'] == $nom_ma_boite);
                ?>
                    <tr class="<?= $est_mon_entreprise ? 'current-user' : '' ?>">
                        <td>
                            <?php 
                            if($rank == 1) echo "🥇";
                            elseif($rank == 2) echo "🥈";
                            elseif($rank == 3) echo "🥉";
                            else echo $rank;
                            ?>
                        </td>
                        <td><?= htmlspecialchars($entreprise['companyname']) ?></td>
                        <td><strong><?= $entreprise['xp'] ?> XP</strong></td>
                    </tr>
                <?php $rank++; endforeach; 
                else: ?>
                    <tr><td colspan="3">Aucun classement disponible.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer class="boutons" style="text-align:center; padding: 20px;">
        <a href="profile.php">Profil</a> | 
        <a href="company.php">Entreprise</a> | 
        <a href="quests.php">Quêtes</a> | 
        <a href="options.php">Paramètres</a>
    </footer>

</body>
</html>