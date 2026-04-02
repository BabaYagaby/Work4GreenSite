<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$my_company_id = $_SESSION['company_id'];

// 1. Infos du membre connecté
$reqPerso = $bdd->prepare("SELECT xp, firstname FROM users WHERE id = :id");
$reqPerso->execute(['id' => $user_id]);
$user = $reqPerso->fetch();

// 2. XP Totale de l'entreprise (Somme)
$xp_entreprise = 0;
if ($my_company_id > 0) {
    $reqTotal = $bdd->prepare("SELECT SUM(xp) as total_xp_boite FROM users WHERE company_id = :comp_id");
    $reqTotal->execute(['comp_id' => $my_company_id]);
    $res = $reqTotal->fetch();
    $xp_entreprise = $res['total_xp_boite'] ?? 0;

    // 3. CLASSEMENT DES MEMBRES (Leaderboard)
    $reqLeader = $bdd->query("SELECT companyname, xp FROM company ORDER BY xp DESC LIMIT 10");
    $classementEntreprises = $reqLeader->fetchAll();

    // On récupère aussi le nom de l'entreprise de l'utilisateur pour la mettre en valeur
    $reqNomCo = $bdd->prepare("SELECT companyname FROM company WHERE id = :id");
    $reqNomCo->execute(['id' => $my_company_id]);
    $ma_boite = $reqNomCo->fetch();
    $nom_ma_boite = $ma_boite['companyname'] ?? "";
    }

// 4. Calculs Barre de niveau Entreprise
$xp_par_niveau = 500; 
$niveau_calcule = floor($xp_entreprise / $xp_par_niveau);
$progression = $xp_entreprise % $xp_par_niveau;
$pourcentage = ($progression / $xp_par_niveau) * 100;
$image_arbre = "arbre_petit.png"; // Par défaut

if ($niveau_calcule >= 15) {
    $image_arbre = "foret.png"; // Optionnel : si tu veux un stade encore après
} elseif ($niveau_calcule >= 10) {
    $image_arbre = "arbre3.png";
} elseif ($niveau_calcule >= 5) {
    $image_arbre = "arbre2.png";
} else {
    $image_arbre = "arbre1.png";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Work4Green - Entreprise</title>

</head>
<body>

    <div class="card">
        <h1>🌍 Notre Entreprise</h1>
        <div style="text-align:center; font-size: 1.2em; font-weight: bold;">Niveau <?= $niveau_calcule ?></div>
        
        <div class="progress-bg">
            <div class="progress-fill"></div>
        </div>
        <p style="text-align:center; margin:0;"><?= $progression ?> / <?= $xp_par_niveau ?> XP avant le prochain niveau</p>
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
                foreach($classementEntreprises as $entreprise): 
                    // On vérifie si c'est l'entreprise de l'utilisateur pour mettre une couleur
                    $est_mon_entreprise = ($entreprise['companyname'] == $nom_ma_boite);
                ?>
                    <tr class="<?= $est_mon_entreprise ? 'current-user' : '' ?>">
                        <td class="rank">
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
                <?php $rank++; endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card" style="text-align: center;">
        <h1>🌍 Notre Entreprise</h1>
        
        <div class="tree-container">
            <img src="images/<?= $image_arbre ?>" alt="Évolution de l'entreprise" style="width: 150px; height: auto; margin-bottom: 10px;">
        </div>

        <div style="font-size: 1.2em; font-weight: bold; color: #27ae60;">
            Niveau <?= $niveau_calcule ?>
        </div>
        
        <div class="progress-bg">
            <div class="progress-fill" style="width: <?= $pourcentage ?>%;"></div>
        </div>
        <p style="margin:0;"><?= $progression ?> / <?= $xp_par_niveau ?> XP avant le prochain niveau</p>
    </div>

    <footer class="boutons">
        <a href="profile.php">Profil</a>
        <a href="company.php">Entreprise</a>
        <a href="quests.php">Quêtes</a>
        <a href="settings.php">Paramètres</a>
    </footer>

</body>
</html>