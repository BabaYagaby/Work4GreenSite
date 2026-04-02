<?php
session_start();
// include('config.php'); // À décommenter dans ton environnement de production

// --- CONNEXION BDD ---
$host = '127.0.0.1';
$db   = 'work4green';
$user = 'root';
$pass = ''; // Mets ton mot de passe si tu en as un
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $bdd = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
// ------------------------------------------------------------------

// --- GESTION DE LA SESSION ---
if (!isset($_SESSION['user_id'])) {
    $user_id = 1; 
    $my_company_id = 1;
} else {
    $user_id = $_SESSION['user_id'];
    $my_company_id = $_SESSION['company_id'];
}
// -----------------------------------------------------

// 1. Infos de l'entreprise de l'utilisateur
$reqMaBoite = $bdd->prepare("SELECT * FROM company WHERE id = ?");
$reqMaBoite->execute([$my_company_id]);
$ma_boite = $reqMaBoite->fetch();

if (!$ma_boite) {
    die("Erreur : Aucune entreprise affiliée trouvée.");
}

$nom_entreprise = $ma_boite['companyname'];
$xp_entreprise = $ma_boite['xp'];
$niveau_entreprise = $ma_boite['level'];

// 2. Calculs pour l'affichage de l'arbre
$xp_par_niveau = 1000;
$progression = $xp_entreprise % $xp_par_niveau;
$pourcentage = ($progression / $xp_par_niveau) * 100;

// --- LOGIQUE D'ÉVOLUTION DE L'ARBRE ---
$image_stade_1 = "./Images/arbre1.png"; 
$image_stade_2 = "./Images/arbre2.png"; 
$image_stade_3 = "./Images/arbre3.png"; 

$image_arbre_actuelle = $image_stade_1;
$nom_stade_arbre = "Jeune pousse";

if ($niveau_entreprise >= 10) {
    $image_arbre_actuelle = $image_stade_3;
    $nom_stade_arbre = "Arbre ancestral";
} elseif ($niveau_entreprise >= 5) {
    $image_arbre_actuelle = $image_stade_2;
    $nom_stade_arbre = "Arbre en croissance";
}

// 3. Récupération du Leaderboard (Top 10)
$reqLeader = $bdd->query("SELECT * FROM company ORDER BY xp DESC LIMIT 10");
$classement = $reqLeader->fetchAll();

// 4. CORRECTION DU RANG : Calcul du rang réel sur toute la BDD
// On compte combien d'entreprises ont plus d'XP que la tienne, et on ajoute 1
$reqMonRang = $bdd->prepare("SELECT COUNT(*) + 1 as rang FROM company WHERE xp > ?");
$reqMonRang->execute([$xp_entreprise]);
$resRang = $reqMonRang->fetch();
$mon_rang = $resRang['rang'];

// Nombre total d'entreprises pour l'affichage (ex: 1 / 45)
$reqTotal = $bdd->query("SELECT COUNT(*) as total FROM company");
$total_entreprises = $reqTotal->fetch()['total'];

// Attribution de la lettre basée sur le rang réel
if ($mon_rang <= 3) {
    $rang_lettre = "A";
} elseif ($mon_rang <= 10) {
    $rang_lettre = "B";
} else {
    $rang_lettre = "C";
}

// 5. Récupération des membres de l'entreprise
$reqMembres = $bdd->prepare("
    SELECT u.firstname, u.lastname, av.image_path as avatar_img
    FROM users u
    LEFT JOIN items_catalog av ON u.current_avatar_id = av.id
    WHERE u.company_id = ?
    ORDER BY u.firstname ASC
");
$reqMembres->execute([$my_company_id]);
$membres_entreprise = $reqMembres->fetchAll();

// Données d'impact
$jours_anciennete = "1 mois et 3 jours";
$co2_evite = floor($xp_entreprise * 0.42); 

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>L'arbre du clan - Work4Green</title>
    <style>
        :root {
            --main: #FFEDD5; --secondary: #334C36; --third: #739272;
            --dark: #1C2E20; --light: #E0EBCD; --neutral: #FFFFFF;
            --space-sm: 8px; --space-md: 16px; --space-lg: 24px;
            --radius-box: 16px; --radius-btn: 12px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, sans-serif; }
        body { background-color: var(--main); color: var(--dark); padding-bottom: 100px; }
        .app-container { max-width: 600px; margin: 0 auto; padding: var(--space-md); padding-top: 3vh; display: flex; flex-direction: column; gap: var(--space-md); }
        
        .page-header h1 { font-size: 1.8rem; font-weight: 800; color: var(--dark); line-height: 1.2; }
        .page-header h2 { font-size: 1.2rem; font-weight: 600; color: var(--secondary); opacity: 0.9; }

        .tree-card { background-color: var(--third); border-radius: var(--radius-box); padding: var(--space-lg); position: relative; color: var(--dark); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .info-card { background: var(--third); border-radius: var(--radius-box); padding: var(--space-md); text-align: center; font-weight: bold; font-size: 1.1rem; color: var(--dark); }
        
        .tree-stats { font-weight: bold; font-size: 0.9rem; margin-bottom: 20px; line-height: 1.4; }
        .tree-stats span { display: block; opacity: 0.85; font-weight: 600; font-size: 0.85rem; }
        .tree-illustration { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 200px; margin: 20px 0; }
        .tree-illustration img { max-width: 150px; height: auto; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.1)); transition: all 0.3s ease-in-out; }
        .tree-name { font-size: 1.4rem; font-weight: bold; margin-top: 15px; text-align: center; color: var(--dark); }
        
        .progress-container { display: flex; flex-direction: column; gap: 5px; }
        .progress-labels { display: flex; justify-content: space-between; font-weight: bold; font-size: 0.85rem; color: var(--dark); }
        .progress-wrapper { background: var(--neutral); border-radius: 999px; height: 16px; width: 100%; overflow: hidden; }
        .progress-fill { height: 100%; background: var(--secondary); border-radius: 999px; transition: width 0.5s ease; }

        .toggle-group { display: flex; gap: 10px; margin-top: 10px; margin-bottom: 5px; }
        .toggle-btn { flex: 1; padding: 14px; border-radius: var(--radius-btn); font-weight: bold; font-size: 1rem; border: none; cursor: pointer; transition: all 0.2s; background: var(--secondary); color: var(--light); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .toggle-btn.active { background: var(--dark); color: var(--neutral); }

        .leaderboard-title { font-size: 1.5rem; font-weight: bold; margin-bottom: 5px; color: var(--dark); }
        .leaderboard-subtitle { font-size: 0.85rem; font-weight: 600; margin-bottom: 20px; opacity: 0.9; color: var(--dark); }
        .leaderboard-list { display: flex; flex-direction: column; gap: 10px; }
        .lb-item { display: flex; align-items: center; gap: 15px; padding: 10px 0; border-bottom: 2px dashed rgba(28, 46, 32, 0.2); }
        .lb-item:last-child { border-bottom: none; }
        .lb-rank { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1rem; flex-shrink: 0; background: var(--light); color: var(--dark); border: 2px solid var(--secondary); }
        .lb-rank.gold { background: #FDE047; border-color: #EAB308; color: #854D0E; } 
        .lb-rank.silver { background: #E2E8F0; border-color: #94A3B8; color: #334155; } 
        .lb-rank.bronze { background: #FDBA74; border-color: #F97316; color: #9A3412; } 
        .lb-details { flex: 1; display: flex; flex-direction: column; }
        .lb-level { font-size: 0.75rem; font-weight: bold; opacity: 0.8; color: var(--dark); }
        .lb-name { font-size: 1.1rem; font-weight: 800; color: var(--dark); }
        .lb-score { font-size: 0.85rem; font-weight: 600; opacity: 0.9; color: var(--dark); }

        .members-grid { display: flex; flex-direction: column; gap: 8px; margin-top: 15px; }
        .member-row { display: flex; align-items: center; gap: 12px; background: var(--main); padding: 8px 12px; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .member-avatar { width: 40px; height: 40px; border-radius: 50%; background: var(--third); display: flex; align-items: center; justify-content: center; overflow: hidden; border: 2px solid var(--dark); flex-shrink: 0; }
        .member-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .member-name { font-weight: bold; font-size: 1rem; color: var(--dark); }

        .d-none { display: none !important; }

        .navbar { position: fixed; bottom: 0; left: 0; width: 100%; background: var(--neutral); box-shadow: 0 -4px 20px rgba(0,0,0,0.08); z-index: 1000; }
        .navbar-inner { max-width: 600px; margin: 0 auto; display: flex; justify-content: space-around; align-items: center; height: 70px; }
        .nav-icon { padding: 10px; transition: transform 0.2s; display: flex; align-items: center; justify-content: center; }
        .nav-icon img { width: 28px; height: 28px; }
    </style>
</head>
<body>

<main class="app-container">

    <header class="page-header">
        <h1>L'arbre du clan</h1>
        <h2><?= htmlspecialchars($nom_entreprise) ?></h2>
    </header>

    <div class="toggle-group">
        <button id="btn-tree" class="toggle-btn active" onclick="switchTab('tree')">Votre arbre</button>
        <button id="btn-leaderboard" class="toggle-btn" onclick="switchTab('leaderboard')">Classement</button>
    </div>

    <section id="view-tree">
        <div class="tree-card">
            <div class="tree-stats">
                <?= $jours_anciennete ?>
                <span><?= $xp_entreprise ?> points totaux</span>
                <span><?= $co2_evite ?> kg de CO₂ évités</span>
            </div>

            <div class="tree-illustration">
                <img src="<?= htmlspecialchars($image_arbre_actuelle) ?>" alt="Stade de l'arbre">
                <div class="tree-name"><?= $nom_stade_arbre ?></div>
            </div>

            <div class="progress-container">
                <div class="progress-wrapper">
                    <div class="progress-fill" style="width: <?= $pourcentage ?>%;"></div>
                </div>
                <div class="progress-labels">
                    <span>Niveau <?= $niveau_entreprise ?></span>
                    <span><?= $progression ?> / <?= $xp_par_niveau ?> EXP.</span>
                </div>
            </div>
        </div>
    </section>

    <section id="view-leaderboard" class="d-none">
        <div class="tree-card">
            <h3 class="leaderboard-title">Classement des clans</h3>
            <p class="leaderboard-subtitle">Position : <?= $mon_rang ?> / <?= $total_entreprises ?></p>
            
            <div class="leaderboard-list">
                <?php 
                $rank = 1;
                foreach($classement as $boite): 
                    $rankClass = "";
                    if($rank == 1) $rankClass = "gold";
                    elseif($rank == 2) $rankClass = "silver";
                    elseif($rank == 3) $rankClass = "bronze";
                ?>
                <div class="lb-item">
                    <div class="lb-rank <?= $rankClass ?>"><?= $rank ?></div>
                    <div class="lb-details">
                        <span class="lb-level">Niveau <?= htmlspecialchars($boite['level']) ?></span>
                        <span class="lb-name"><?= htmlspecialchars($boite['companyname']) ?></span>
                        <span class="lb-score"><?= htmlspecialchars($boite['xp']) ?> pts</span>
                    </div>
                </div>
                <?php $rank++; endforeach; ?>
            </div>
        </div>
    </section>

    <div id="view-rank" class="info-card">
        Votre entreprise est...<br>
        <span style="font-size: 1.3rem;">Rang <?= $rang_lettre ?></span>
    </div>

    <div id="view-members" class="tree-card" style="padding: var(--space-md);">
        <h3 class="leaderboard-subtitle" style="margin-bottom: 5px; font-size: 1rem;">Membres du clan</h3>
        <div class="members-grid">
            <?php foreach($membres_entreprise as $membre): 
                $avatarSrc = !empty($membre['avatar_img']) ? $membre['avatar_img'] : './Images/perso-03.svg';
            ?>
            <div class="member-row">
                <div class="member-avatar">
                    <img src="<?= htmlspecialchars($avatarSrc) ?>" alt="Avatar">
                </div>
                <div class="member-name">
                    <?= htmlspecialchars($membre['firstname'] . ' ' . $membre['lastname']) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</main>

<nav class="navbar">
    <div class="navbar-inner">
        <a class="nav-icon" href="profile.php"><img src="./Images/profile-1341-svgrepo-com.svg" alt="Profil"></a>
        <a class="nav-icon" href="quests.php"><img src="./Images/notebook-svgrepo-com.svg" alt="Quêtes"></a>
        <a class="nav-icon" href="company.php"><img src="./Images/leaf-eco-svgrepo-com.svg" alt="W4G"></a>
        <a class="nav-icon" href="options.php"><img src="./Images/gear-svgrepo-com.svg" alt="Options"></a>
    </div>
</nav>

<script>
    function switchTab(tab) {
        const viewTree = document.getElementById('view-tree');
        const viewLeaderboard = document.getElementById('view-leaderboard');
        const viewRank = document.getElementById('view-rank');
        const viewMembers = document.getElementById('view-members');
        const btnTree = document.getElementById('btn-tree');
        const btnLeaderboard = document.getElementById('btn-leaderboard');

        if (tab === 'tree') {
            viewTree.classList.remove('d-none');
            viewRank.classList.remove('d-none');
            viewMembers.classList.remove('d-none');
            viewLeaderboard.classList.add('d-none');
            btnTree.classList.add('active');
            btnLeaderboard.classList.remove('active');
        } else {
            viewTree.classList.add('d-none');
            viewRank.classList.add('d-none');
            viewMembers.classList.add('d-none');
            viewLeaderboard.classList.remove('d-none');
            btnLeaderboard.classList.add('active');
            btnTree.classList.remove('active');
        }
    }
</script>

</body>
</html>