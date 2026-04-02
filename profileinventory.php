<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- LOGIQUE UNIQUE POUR ÉQUIPER ---
if (isset($_GET['equip_id'])) {
    $item_id = (int)$_GET['equip_id'];
    
    // Vérification de possession
    $check = $bdd->prepare("SELECT 1 FROM user_inventory WHERE user_id = ? AND item_id = ?");
    $check->execute([$user_id, $item_id]);

    if ($check->fetch()) {
        // CAS 1 : C'est un Badge et on a choisi un Slot
        if (isset($_GET['slot'])) {
            $slot = (int)$_GET['slot'];
            if ($slot >= 1 && $slot <= 3) {
                $column = "fav_badge_" . $slot;
                $update = $bdd->prepare("UPDATE users SET $column = ? WHERE id = ?");
                $update->execute([$item_id, $user_id]);
            }
        } 
        // CAS 2 : C'est un Avatar (le type est passé dans l'URL)
        elseif (isset($_GET['type']) && $_GET['type'] == 'avatar') {
            $update = $bdd->prepare("UPDATE users SET current_avatar_id = ? WHERE id = ?");
            $update->execute([$item_id, $user_id]);
        }
    }
    header("Location: profileinventory.php");
    exit();
}

// --- RÉCUPÉRATION DES DONNÉES ---
$reqInv = $bdd->prepare("SELECT c.* FROM items_catalog c JOIN user_inventory i ON c.id = i.item_id WHERE i.user_id = ?");
$reqInv->execute([$user_id]);
$items = $reqInv->fetchAll(PDO::FETCH_ASSOC);

$reqUser = $bdd->prepare("SELECT current_avatar_id, fav_badge_1, fav_badge_2, fav_badge_3 FROM users WHERE id = ?");
$reqUser->execute([$user_id]);
$currentUser = $reqUser->fetch();

$avatars = array_filter($items, function($i) { return $i['type'] == 'avatar'; });
$badges = array_filter($items, function($i) { return $i['type'] == 'badge'; });
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Inventaire - Work4Green</title>
    <link rel="stylesheet" href="work4green.css">
    <style>
        .inventory-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 30px; }
        .item-card { background: white; border-radius: 12px; padding: 15px; text-align: center; border: 2px solid #eee; }
        .item-card.active { border-color: #4CAF50; background: #f0fff0; }
        .item-card img { width: 50px; height: 50px; object-fit: contain; margin-bottom: 10px; }
        .slot-buttons { display: flex; gap: 5px; justify-content: center; margin-top: 10px; }
        .btn-slot { font-size: 10px; padding: 4px 8px; background: #eee; text-decoration: none; color: #333; border-radius: 4px; }
        .btn-slot.selected { background: #4CAF50; color: white; }
        .btn-equip { display: inline-block; padding: 5px 15px; background: var(--main); color: black; border-radius: 5px; text-decoration: none; }
    </style>
</head>
<body>
<main class="app-container">
    <h1 class="text-title">Mon Inventaire</h1>

    <section>
        <h2 class="text-subtitle">Mes Avatars</h2>
        <div class="inventory-grid">
            <?php foreach($avatars as $av): ?>
                <?php $is_active = ($av['id'] == $currentUser['current_avatar_id']); ?>
                <div class="item-card <?= $is_active ? 'active' : '' ?>">
                    <img src="<?= htmlspecialchars($av['image_path']) ?>">
                    <p><?= htmlspecialchars($av['name']) ?></p>
                    <?php if($is_active): ?> <span style="color:#4CAF50">ÉQUIPÉ</span>
                    <?php else: ?> <a href="?equip_id=<?= $av['id'] ?>&type=avatar" class="btn-equip">Choisir</a> <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section>
        <h2 class="text-subtitle">Mes Badges</h2>
        <div class="inventory-grid">
            <?php foreach($badges as $bg): ?>
                <div class="item-card">
                    <img src="<?= htmlspecialchars($bg['image_path']) ?>">
                    <p><?= htmlspecialchars($bg['name']) ?></p>
                    <div class="slot-buttons">
                        <a href="?equip_id=<?= $bg['id'] ?>&slot=1" class="btn-slot <?= ($currentUser['fav_badge_1'] == $bg['id']) ? 'selected' : '' ?>">Slot 1</a>
                        <a href="?equip_id=<?= $bg['id'] ?>&slot=2" class="btn-slot <?= ($currentUser['fav_badge_2'] == $bg['id']) ? 'selected' : '' ?>">Slot 2</a>
                        <a href="?equip_id=<?= $bg['id'] ?>&slot=3" class="btn-slot <?= ($currentUser['fav_badge_3'] == $bg['id']) ? 'selected' : '' ?>">Slot 3</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <div style="text-align: center;"><a href="profile.php" class="btn btn-outline">Retour au profil</a></div>
</main>
</body>
</html>