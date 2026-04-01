<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$aujourdhui = date('Y-m-d'); // Format 2026-04-01

// 1. SÉLECTION DES QUÊTES DU JOUR (Change à minuit)
// On utilise la date comme "Seed" pour que mt_srand génère les mêmes IDs toute la journée
mt_srand(strtotime($aujourdhui)); 

// On récupère toutes les quêtes dispo en BDD
$allQuestsReq = $bdd->query("SELECT * FROM quest");
$allQuests = $allQuestsReq->fetchAll(PDO::FETCH_ASSOC);

// On en choisit 4 au hasard (mais le hasard sera le même pour un utilisateur toute la journée)
$indices = array_rand($allQuests, 4);
$quetesDuJour = [];
foreach($indices as $idx) { $quetesDuJour[] = $allQuests[$idx]; }

// 2. RÉCUPÉRATION DES QUÊTES DÉJÀ FAITES AUJOURD'HUI
$reqFaites = $bdd->prepare("SELECT quest_id FROM user_quests WHERE user_id = :uid AND date_completion = :date");
$reqFaites->execute(['uid' => $user_id, 'date' => $aujourdhui]);
$faitesAujourdhui = $reqFaites->fetchAll(PDO::FETCH_COLUMN); // Récupère juste une liste d'IDs [1, 5, 8]

// 3. ACTION VALIDER
if (isset($_GET['quest_id']) && $_GET['action'] == 'valider') {
    $q_id = (int)$_GET['quest_id'];
    
    // Vérifier si elle est dans la liste du jour et pas encore faite
    if (!in_array($q_id, $faitesAujourdhui)) {
        // Trouver les points de cette quête
        foreach($quetesDuJour as $q) {
            if($q['quest_id'] == $q_id) {
                $xp_gagne = $q['quest_xp'];
                
                // A. Enregistrer dans l'historique du jour
                $ins = $bdd->prepare("INSERT INTO user_quests (user_id, quest_id, date_completion) VALUES (?, ?, ?)");
                $ins->execute([$user_id, $q_id, $aujourdhui]);

                // B. Update XP User et Niveau
                $bdd->prepare("UPDATE users SET xp = xp + ?, level = FLOOR((xp + ?) / 100) WHERE id = ?")
                    ->execute([$xp_gagne, $xp_gagne, $user_id]);

                // C. Update Entreprise
                $bdd->prepare("UPDATE company SET xp = xp + ? WHERE id = ?")
                    ->execute([$xp_gagne, $_SESSION['company_id']]);

                header("Location: quests.php");
                exit();
            }
        }
    }
}

// 4. ACTION ANNULER
if (isset($_GET['quest_id']) && $_GET['action'] == 'annuler') {
    $q_id = (int)$_GET['quest_id'];
    
    // On vérifie que la quête est bien dans la liste du jour
    foreach($quetesDuJour as $q) {
        if($q['quest_id'] == $q_id) {
            $xp_perdu = $q['quest_xp'];

            // A. On supprime la ligne de l'historique du jour
            $del = $bdd->prepare("DELETE FROM user_quests WHERE user_id = ? AND quest_id = ? AND date_completion = ?");
            $del->execute([$user_id, $q_id, $aujourdhui]);

            // Si une ligne a bien été supprimée, on retire les points
            if ($del->rowCount() > 0) {
                // B. Update XP User et Niveau (on utilise GREATEST pour ne pas descendre sous 0 XP)
                $bdd->prepare("UPDATE users SET xp = GREATEST(0, xp - ?), level = FLOOR(GREATEST(0, xp - ?) / 100) WHERE id = ?")
                    ->execute([$xp_perdu, $xp_perdu, $user_id]);

                // C. Update Entreprise
                $bdd->prepare("UPDATE company SET xp = GREATEST(0, xp - ?) WHERE id = ?")
                    ->execute([$xp_perdu, $_SESSION['company_id']]);
            }

            header("Location: quests.php");
            exit();
        }
    }
}

// --- Infos pour la barre (toujours après les updates) ---
$reqBarre = $bdd->prepare("SELECT xp, level FROM users WHERE id = ?");
$reqBarre->execute([$user_id]);
$u = $reqBarre->fetch();
$pourcentage = ($u['xp'] % 100);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Quêtes Éco</title>
    <style>
        .progress-bar { width: 100%; background: #eee; height: 15px; border-radius: 10px; }
    </style>
</head>
<body>

    <div class="progress-bar"><div class="fill"></div></div>
    <p>Niveau <?= $u['level'] ?> - <?= $u['xp'] % 100 ?>/100 XP</p>

    <fieldset>
    <legend>Quêtes du <?= date('d/m/Y') ?></legend>
    <?php foreach ($quetesDuJour as $q): ?>
        <div style="margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
            <strong><?= htmlspecialchars($q['quest_name']) ?> (+<?= $q['quest_xp'] ?> XP)</strong>
            <p><?= htmlspecialchars($q['quest_description']) ?></p>

            <?php if (in_array($q['quest_id'], $faitesAujourdhui)): ?>
                <span class="done">✅ Validée pour aujourd'hui</span>
                <br>
                <small>
                    <a href="quests.php?action=annuler&quest_id=<?= $q['quest_id'] ?>" 
                       style="color: #e74c3c; text-decoration: none;" 
                       onclick="return confirm('Voulez-vous vraiment annuler cette action ? L\'XP sera retirée.');">
                       Annuler la quête
                    </a>
                </small>
            <?php else: ?>
                <a href="quests.php?action=valider&quest_id=<?= $q['quest_id'] ?>">
                   Valider la tâche
                </a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</fieldset>

</body>
</html>