<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$aujourdhui = date('Y-m-d');

// --- 1. SÉLECTION DES QUÊTES DU JOUR (Fixe pour 24h) ---
mt_srand(strtotime($aujourdhui)); 
$allQuestsReq = $bdd->query("SELECT * FROM quest");
$allQuests = $allQuestsReq->fetchAll(PDO::FETCH_ASSOC);

$indices = array_rand($allQuests, min(4, count($allQuests)));
$quetesDuJour = [];
foreach((array)$indices as $idx) { $quetesDuJour[] = $allQuests[$idx]; }

// --- 2. RÉCUPÉRATION DES QUÊTES DÉJÀ FAITES ---
$reqFaites = $bdd->prepare("SELECT quest_id FROM user_quests WHERE user_id = :uid AND date_completion = :date");
$reqFaites->execute(['uid' => $user_id, 'date' => $aujourdhui]);
$faitesAujourdhui = $reqFaites->fetchAll(PDO::FETCH_COLUMN);

// --- 3. ACTION : VALIDER UNE QUÊTE ---
if (isset($_GET['quest_id']) && $_GET['action'] == 'valider') {
    $q_id = (int)$_GET['quest_id'];
    
    if (!in_array($q_id, $faitesAujourdhui)) {
        foreach($quetesDuJour as $q) {
            if($q['quest_id'] == $q_id) {
                $xp_gagne = $q['quest_xp'];
                
                // A. Enregistrement
                $ins = $bdd->prepare("INSERT INTO user_quests (user_id, quest_id, date_completion) VALUES (?, ?, ?)");
                $ins->execute([$user_id, $q_id, $aujourdhui]);

                // B. Update User (XP + Level)
                $bdd->prepare("UPDATE users SET xp = xp + ?, level = FLOOR((xp + ?) / 100) WHERE id = ?")
                    ->execute([$xp_gagne, $xp_gagne, $user_id]);

                // C. Update Entreprise
                if(isset($_SESSION['company_id'])){
                    $bdd->prepare("UPDATE company SET xp = xp + ? WHERE id = ?")
                        ->execute([$xp_gagne, $_SESSION['company_id']]);
                }

                // D. DÉBLOCAGE D'ITEMS (Nouveau niveau)
                $stmt = $bdd->prepare("SELECT level FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $newLevel = $stmt->fetchColumn();

                $reqUnlock = $bdd->prepare("
                    SELECT id FROM items_catalog 
                    WHERE level_required <= ? 
                    AND id NOT IN (SELECT item_id FROM user_inventory WHERE user_id = ?)
                ");
                $reqUnlock->execute([$newLevel, $user_id]);
                $itemsToGive = $reqUnlock->fetchAll(PDO::FETCH_ASSOC);

                if ($itemsToGive) {
                    $insertInv = $bdd->prepare("INSERT INTO user_inventory (user_id, item_id, obtained_at) VALUES (?, ?, NOW())");
                    foreach ($itemsToGive as $item) {
                        $insertInv->execute([$user_id, $item['id']]);
                    }
                    header("Location: quests.php?unlocked=1");
                } else {
                    header("Location: quests.php");
                }
                exit();
            }
        }
    }
}

// --- 4. ACTION : ANNULER UNE QUÊTE ---
if (isset($_GET['quest_id']) && $_GET['action'] == 'annuler') {
    $q_id = (int)$_GET['quest_id'];
    foreach($quetesDuJour as $q) {
        if($q['quest_id'] == $q_id) {
            $xp_perdu = $q['quest_xp'];
            $del = $bdd->prepare("DELETE FROM user_quests WHERE user_id = ? AND quest_id = ? AND date_completion = ?");
            $del->execute([$user_id, $q_id, $aujourdhui]);

            if ($del->rowCount() > 0) {
                $bdd->prepare("UPDATE users SET xp = GREATEST(0, xp - ?), level = FLOOR(GREATEST(0, xp - ?) / 100) WHERE id = ?")
                    ->execute([$xp_perdu, $xp_perdu, $user_id]);
            }
            header("Location: quests.php");
            exit();
        }
    }
}

// --- 5. INFOS POUR L'AFFICHAGE ---
$reqBarre = $bdd->prepare("
    SELECT u.xp, u.level, av.image_path 
    FROM users u
    LEFT JOIN items_catalog av ON u.current_avatar_id = av.id
    WHERE u.id = ?
");
$reqBarre->execute([$user_id]);
$u = $reqBarre->fetch();

$pourcentage = ($u['xp'] % 100);
$avatar_img = !empty($u['image_path']) ? $u['image_path'] : './Images/perso.svg';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quêtes - Work4Green</title>
    <link rel="stylesheet" href="work4green.css">
</head>
<body>

    <?php if(isset($_GET['unlocked'])): ?>
        <script>alert("Bravo ! Ton nouveau niveau a débloqué de nouveaux objets !");</script>
    <?php endif; ?>

    <div class="app-container flex-col gap-md">
        
        <header class="card flex-col gap-sm" style="margin-top: 10px;">
            <div class="d-flex justify-between items-center">
                <h1 class="text-title-lvl" style="font-weight: bold; margin: 0;">Niveau <?= htmlspecialchars($u['level']) ?></h1>
                <span class="text-subtitle"><?= htmlspecialchars($pourcentage) ?>/100 XP</span>
            </div>
            <div class="progress-wrapper" style="background: #eee; border-radius: 10px; height: 12px; overflow: hidden;">
                <div class="progress-fill" style="width: <?= $pourcentage ?>%; background: var(--secondary); height: 100%; transition: width 0.5s;"></div>
            </div>
        </header>

        <section class="avatar-mini-display" style="text-align: center; margin: 10px 0;">
            <img src="<?= $avatar_img ?>" alt="Mon Avatar" style="max-height: 140px; filter: drop-shadow(0 5px 10px rgba(0,0,0,0.1));">
        </section>

        <section class="flex-col gap-md" style="padding-bottom: 100px;">
            <h2 class="text-title" style="font-size: 1.2rem; margin-left: 5px;">Quêtes du jour</h2>
            
            <?php foreach ($quetesDuJour as $q): ?>
                <article class="card-dark flex-col gap-sm" style="background: #fdfdfd; border: 1px solid #eee; border-radius: 15px; padding: 15px;">
                    <div class="d-flex justify-between items-start">
                        <h3 class="text-subtitle" style="font-weight: bold; color: #333; margin: 0; flex: 1;">
                            <?= htmlspecialchars($q['quest_name']) ?>
                        </h3>
                        <span class="badge-xp" style="background: var(--main); padding: 4px 8px; border-radius: 8px; font-size: 0.8rem; font-weight: bold;">
                            +<?= htmlspecialchars($q['quest_xp']) ?> XP
                        </span>
                    </div>
                    
                    <p class="text-body" style="color: #666; font-size: 0.9rem; margin: 10px 0;">
                        <?= htmlspecialchars($q['quest_description']) ?>
                    </p>

                    <?php if (in_array($q['quest_id'], $faitesAujourdhui)): ?>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <div class="btn btn-success" style="background: #e8f5e9; color: #2e7d32; flex: 1; text-align: center; padding: 10px; border-radius: 10px; font-weight: bold;">
                                ✅ Validée
                            </div>
                            <a href="quests.php?action=annuler&quest_id=<?= $q['quest_id'] ?>" 
                               style="color: #d32f2f; text-decoration: none; font-size: 0.8rem; font-weight: bold; border: 1px solid #d32f2f; padding: 10px; border-radius: 10px;"
                               onclick="return confirm('Annuler cette quête ?');">
                               Annuler
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="quests.php?action=valider&quest_id=<?= $q['quest_id'] ?>" 
                           class="btn btn-primary" 
                           style="background: var(--main); color: black; text-align: center; padding: 12px; border-radius: 10px; text-decoration: none; font-weight: bold; display: block;">
                           Valider la tâche
                        </a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
    </div>

    <nav class="navbar" style="position: fixed; bottom: 0; left: 0; right: 0; background: white; border-top: 1px solid #eee; padding: 12px 0;">
        <div class="navbar-inner" style="display: flex; justify-content: space-around; max-width: 500px; margin: 0 auto;">
            <a class="nav-icon" href="profile.php"><img src="./Images/profile-1341-svgrepo-com.svg" style="width: 25px;"></a>
            <a class="nav-icon" href="quests.php"><img src="./Images/notebook-svgrepo-com.svg" style="width: 25px;"></a>
            <a class="nav-icon" href="company.php"><img src="./Images/leaf-eco-svgrepo-com.svg" style="width: 25px;"></a>
            <a class="nav-icon" href="options.php"><img src="./Images/gear-svgrepo-com.svg" style="width: 25px;"></a>
        </div>
    </nav>
</body>
</html>