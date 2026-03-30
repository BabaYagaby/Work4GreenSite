<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

// 1. RÉCUPÉRATION DES INFOS POUR LA BARRE (Avant les calculs)
$reqBarre = $bdd->prepare("SELECT xp, level FROM users WHERE id = :id");
$reqBarre->execute(['id' => $_SESSION['user_id']]);
$userBarre = $reqBarre->fetch();

$xp_perso = $userBarre['xp'];
$lvl_perso = $userBarre['level'];

// Calcul pour la barre (Palier de 100)
$xp_dans_niveau = $xp_perso % 100;
$pourcentage_perso = ($xp_dans_niveau / 100) * 100;

// 2. LOGIQUE DES QUÊTES
$toutesLesQuetes = [
    ["nom" => "Déplacement non polluant", "description" => "Vélo, marche ou trottinette", "xp" => 50],
    ["nom" => "Déplacement transports en commun", "description" => "Bus, train ou tram", "xp" => 40],
    ["nom" => "Déplacement covoiturage", "description" => "Voyager à plusieurs en voiture", "xp" => 80],
    ["nom" => "Éteindre les appareils électriques", "description" => "Veille et lumières coupées", "xp" => 60],
    ["nom" => "Nettoyage de la boîte mail professionnelle", "description" => "Suppression des mails inutiles", "xp" => 30],
];

if (!isset($_SESSION['quetes'])) {
    $_SESSION['quetes'] = array_rand($toutesLesQuetes, 3);
}

// 3. ACTION VALIDER
if (isset($_GET['tache']) && $_GET['action'] == 'valider' && !in_array($_GET['tache'], $_SESSION['faites'])) {
    foreach ($_SESSION['quetes'] as $i) {
        if ($toutesLesQuetes[$i]['nom'] == $_GET['tache']) {
            $xp_gagne = $toutesLesQuetes[$i]['xp'];
            
            // Update Entreprise
            $updateC = $bdd->prepare("UPDATE company SET xp = xp + :xp WHERE id = :company_id");
            $updateC->execute(['xp' => $xp_gagne, 'company_id' => $_SESSION['company_id']]);

            // Update User (XP + Calcul Niveau auto)
            $new_total_xp = $xp_perso + $xp_gagne;
            $new_lvl = floor($new_total_xp / 100);

            $updateU = $bdd->prepare("UPDATE users SET xp = xp + :xp, level = :lvl WHERE id = :id");
            $updateU->execute(['xp' => $xp_gagne, 'lvl' => $new_lvl, 'id' => $_SESSION['user_id']]);

            $_SESSION['faites'][] = $_GET['tache'];
            header("Location: quests.php"); // On recharge pour actualiser la barre
            exit();
        }
    }
}

// 4. ACTION ANNULER
else if (isset($_GET['tache']) && $_GET['action'] == 'annuler' && in_array($_GET['tache'], $_SESSION['faites'])) {
    foreach ($_SESSION['quetes'] as $i) {
        if ($toutesLesQuetes[$i]['nom'] == $_GET['tache']) {
            $xp_perdu = $toutesLesQuetes[$i]['xp'];
            
            // Recalcul niveau
            $new_total_xp = max(0, $xp_perso - $xp_perdu);
            $new_lvl = floor($new_total_xp / 100);

            $bdd->prepare("UPDATE company SET xp = xp - :xp WHERE id = :cid")->execute(['xp' => $xp_perdu, 'cid' => $_SESSION['company_id']]);
            $bdd->prepare("UPDATE users SET xp = xp - :xp, level = :lvl WHERE id = :id")->execute(['xp' => $xp_perdu, 'lvl' => $new_lvl, 'id' => $_SESSION['user_id']]);

            $_SESSION['faites'] = array_diff($_SESSION['faites'], [$_GET['tache']]);
            header("Location: quests.php");
            exit();
        }
    }
}

if (isset($_POST['nouveau'])) {
    unset($_SESSION['quetes']);
    unset($_SESSION['faites']);
    header("Location: quests.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Quêtes & Niveau</title>
</head>
<body>

    <div class="quest-lvl-container">
        <div style="display: flex; justify-content: space-between; font-weight: bold;">
            <span>Niveau <?= $lvl_perso ?></span>
            <span><?= $xp_dans_niveau ?> / 100 XP</span>
        </div>
        <div class="progress-bar-bg">
            <div class="progress-bar-fill"></div>
        </div>
        <small>Total accumulé : <?= $xp_perso ?> XP</small>
    </div>

    <fieldset>
        <legend><h2>Vos quêtes du jour</h2></legend>
        <?php foreach ($_SESSION['quetes'] as $i): ?>
            <div style="border-bottom: 1px solid #eee; padding: 10px 0;">
                <strong><?= $toutesLesQuetes[$i]['nom'] ?></strong>
                <p style="margin: 5px 0; color: #666;"><?= $toutesLesQuetes[$i]['description'] ?> (<b>+<?= $toutesLesQuetes[$i]['xp'] ?> XP</b>)</p>
                
                <form method="get" action="">
                    <input type="hidden" name="tache" value="<?= $toutesLesQuetes[$i]['nom'] ?>"/>
                    <?php if(!in_array($toutesLesQuetes[$i]['nom'], $_SESSION['faites'])): ?>
                        <button type="submit" name="action" value="valider" class="btn-valider">Valider</button>
                    <?php else: ?>
                        <span style="color: #2ecc71; font-weight: bold;">✅ Complétée</span>
                        <button type="submit" name="action" value="annuler" class="btn-annuler" style="margin-left: 10px;">Annuler</button>
                    <?php endif; ?>
                </form>
            </div>
        <?php endforeach ?>
    </fieldset> 

    <form method="post" action="#" style="margin-top: 20px;">
        <input type="submit" value="Renouveler les quêtes" name="nouveau">
    </form>

    <footer class="boutons">
        <a href="profile.php">Profile</a>
        <a href="company.php">Entreprise</a>
        <a href="quests.php">Quêtes</a>
        <a href="settings.php">Paramètres</a>
    </footer>
</body>
</html>