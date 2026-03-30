<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <link href="" />
</head>

<?php
//une session pour garder les quetes choisi aléa et qu'on pûisse cumuler xp au fil de lma journée
    session_start();
    include('config.php');
//faire une tab avec les nom des taches et xp accordé et afficher random  taches +++(faire 2 daily et 1 weekly)
//table sql des quetes serait p^lus simple a mon big ass avis qu'une tab
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
    if (!isset($_SESSION['xpTotal'])) {
        $_SESSION['xpTotal'] = 0;
    }
    if (!isset($_SESSION['faites'])) {
        $_SESSION['faites'] = [];
    }

    print_r($_SESSION['quetes']);
    echo print_r($_SESSION['faites']);

//un if bien complexe pour verifier si on a valide une quete et si elle n'a pas déja eté validé on met dans la session la quete et cumule xp et le contraire quand on annule
    if (isset($_GET['tache']) && $_GET['action'] == 'valider' && !in_array($_GET['tache'], $_SESSION['faites'])) {
        foreach ($_SESSION['quetes'] as $i) {
            if ($toutesLesQuetes[$i]['nom'] == $_GET['tache']) {
                $_SESSION['xpTotal'] += $toutesLesQuetes[$i]['xp'];
                $_SESSION['faites'][] = $_GET['tache'];
                echo "La tâche <i>".$_GET['tache']."</i> a été validée <b> + ".$toutesLesQuetes[$i]['xp']."</b>";
                $xp_gagne = $toutesLesQuetes[$i]['xp']; // On récupère la vraie valeur de la quête
                $updateXP = $bdd->prepare("UPDATE users SET xp = xp + :xp WHERE id = :id");
                $updateXP->execute([
                    'xp' => $xp_gagne,
                    'id' => $_SESSION['user_id']
                ]);
                
                echo "Félicitations, vous avez gagné " . $xp_gagne . " XP !";

                if ($updateXP->rowCount() > 0) {
                    echo " -> Succès ! BDD mise à jour.";
                } else {
                    echo " -> Échec : Aucune ligne modifiée en BDD.";
                }
                
            }
        }
    }
    else if (isset($_GET['tache']) && $_GET['action'] == 'annuler' && in_array($_GET['tache'], $_SESSION['faites'])) {
    foreach ($_SESSION['quetes'] as $i) {
        if ($toutesLesQuetes[$i]['nom'] == $_GET['tache']) {
            // 1. On retire de la session
            $_SESSION['xpTotal'] -= $toutesLesQuetes[$i]['xp'];
            // On retire la quête du tableau 'faites'
            $_SESSION['faites'] = array_diff($_SESSION['faites'], [$_GET['tache']]);
            
            // 2. UPDATE BDD : - XP (On utilise le signe MOINS)
            $xp_perdu = $toutesLesQuetes[$i]['xp'];
            $updateXP = $bdd->prepare("UPDATE users SET xp = xp - :xp WHERE id = :id");
            $updateXP->execute(['xp' => $xp_perdu, 'id' => $_SESSION['user_id']]);
            
            echo "La tâche <i>".$_GET['tache']."</i> a été annulée (-".$xp_perdu." XP)";
        }
    }
}

//+++mettre en timer pour la journée afin de redonner de quettes a 00:00
    if (isset($_POST['nouveau'])) {
        session_destroy();
        header("Location: taches.php");
    }
?>

<body>
    <h2>Mon Score : <?= $_SESSION['xpTotal'] ?> XP</h2>
    <fieldset>
    <legend>Vos taches à faire svp</legend>
        <?php foreach ($_SESSION['quetes'] as $i): ?>
            <form method="get" action="#">
                <label for="<?= $toutesLesQuetes[$i]['nom'] ?>"> <?= $toutesLesQuetes[$i]['nom'] ?> </label>
                <p><?= $toutesLesQuetes[$i]['description']." + ". $toutesLesQuetes[$i]['xp'] ?></p>
                <input type="hidden" name="tache" id="<?= $toutesLesQuetes[$i]['nom'] ?>" value="<?= $toutesLesQuetes[$i]['nom'] ?>"/>
                <input type="submit" name="action" value="valider">
                <input type="submit" name="action" value="annuler">
            </form>
            <br>
        <?php endforeach ?>
    </fieldset> 

    <form method="post" action="#">
        <input type="submit" value="nouveau" name="nouveau">
    </form>

    <footer class="boutons">

        <a id="profile" href="profile.php">Profile</a>

        <a id="company" href="company.php">Entreprise</a>

        <a id="quests" href="quests.php">Quêtes</a>

        <a id="settings" href="settings.php">Paramètres</a>

    </footer>
</body>
</html>