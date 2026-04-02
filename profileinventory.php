<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- LOGIQUE UNIQUE POUR ÉQUIPER (Inchangée) ---
if (isset($_GET['equip_id'])) {
    $item_id = (int)$_GET['equip_id'];
    $check = $bdd->prepare("SELECT 1 FROM user_inventory WHERE user_id = ? AND item_id = ?");
    $check->execute([$user_id, $item_id]);

    if ($check->fetch()) {
        if (isset($_GET['slot'])) {
            $slot = (int)$_GET['slot'];
            if ($slot >= 1 && $slot <= 3) {
                $column = "fav_badge_" . $slot;
                $update = $bdd->prepare("UPDATE users SET $column = ? WHERE id = ?");
                $update->execute([$item_id, $user_id]);
            }
        } 
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Inventaire - Work4Green</title>
    <link rel="stylesheet" href="work4green.css">
    <style>
        /* Ajustements spécifiques à l'inventaire pour compléter work4green.css */
        .inventory-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); 
            gap: var(--space-md); 
            margin-bottom: var(--space-lg); 
        }

        .item-card { 
            background: var(--neutral); 
            border-radius: var(--radius-box); 
            padding: var(--space-md); 
            text-align: center; 
            display: flex; 
            flex-direction: column; 
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
            border: 2px solid transparent;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .item-card.active { 
            border-color: var(--secondary); 
            background: var(--light); 
        }

        .item-img-container {
            background: var(--main);
            border-radius: 12px;
            padding: 10px;
            margin-bottom: 10px;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .item-card img { 
            max-width: 60px; 
            max-height: 60px; 
            object-fit: contain; 
        }

        .item-name {
            font-weight: bold;
            font-size: 0.9rem;
            color: var(--dark);
            margin-bottom: 12px;
        }

        .slot-buttons { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 4px; 
            width: 100%;
        }

        .btn-slot { 
            font-size: 10px; 
            padding: 6px 2px; 
            background: var(--input); 
            color: var(--dark); 
            border-radius: 6px; 
            font-weight: bold;
            text-transform: uppercase;
        }

        .btn-slot.selected { 
            background: var(--secondary); 
            color: var(--neutral); 
        }

        .equipped-label {
            background: var(--secondary);
            color: var(--neutral);
            font-size: 0.75rem;
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 8px;
            width: 100%;
        }

        .btn-equip-small {
            background: var(--dark);
            color: var(--neutral);
            font-size: 0.75rem;
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 8px;
            width: 100%;
            cursor: pointer;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: var(--space-md);
            margin-top: var(--space-lg);
        }

        .section-header hr {
            flex: 1;
            border: none;
            border-top: 2px solid var(--third);
            opacity: 0.3;
        }
    </style>
</head>
<body>
<main class="app-container">
    <header class="mb-lg" style="text-align: center;">
        <h1 class="text-title" style="font-size: 1.8rem;">Mon Inventaire</h1>
    </header>

    <section>
        <div class="section-header">
            <h2 class="text-title-lvl" style="white-space: nowrap;">Mes Avatars</h2>
            <hr>
        </div>
        
        <div class="inventory-grid">
            <?php foreach($avatars as $av): ?>
                <?php $is_active = ($av['id'] == $currentUser['current_avatar_id']); ?>
                <div class="item-card <?= $is_active ? 'active' : '' ?>">
                    <div class="item-img-container">
                        <img src="<?= htmlspecialchars($av['image_path']) ?>" alt="<?= htmlspecialchars($av['name']) ?>">
                    </div>
                    <span class="item-name"><?= htmlspecialchars($av['name']) ?></span>
                    
                    <?php if($is_active): ?> 
                        <div class="equipped-label">ÉQUIPÉ</div>
                    <?php else: ?> 
                        <a href="?equip_id=<?= $av['id'] ?>&type=avatar" class="btn-equip-small">Choisir</a> 
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section>
        <div class="section-header">
            <h2 class="text-title-lvl" style="white-space: nowrap;">Mes Badges</h2>
            <hr>
        </div>

        <div class="inventory-grid">
            <?php foreach($badges as $bg): ?>
                <div class="item-card">
                    <div class="item-img-container">
                        <img src="<?= htmlspecialchars($bg['image_path']) ?>" alt="<?= htmlspecialchars($bg['name']) ?>">
                    </div>
                    <span class="item-name"><?= htmlspecialchars($bg['name']) ?></span>
                    
                    <div class="slot-buttons">
                        <a href="?equip_id=<?= $bg['id'] ?>&slot=1" class="btn-slot <?= ($currentUser['fav_badge_1'] == $bg['id']) ? 'selected' : '' ?>">S1</a>
                        <a href="?equip_id=<?= $bg['id'] ?>&slot=2" class="btn-slot <?= ($currentUser['fav_badge_2'] == $bg['id']) ? 'selected' : '' ?>">S2</a>
                        <a href="?equip_id=<?= $bg['id'] ?>&slot=3" class="btn-slot <?= ($currentUser['fav_badge_3'] == $bg['id']) ? 'selected' : '' ?>">S3</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <footer class="mt-md">
        <a href="profile.php" class="btn btn-outline">Retour au profil</a>
    </footer>
</main>

<div class="navbar">
  <div class="navbar-inner">
        <a class="nav-icon" href="profile.php"><img src="./Images/profile-1341-svgrepo-com.svg" alt="Profil"></a>
        <a class="nav-icon" href="quests.php"><img src="./Images/notebook-svgrepo-com.svg" alt="Quetes"></a>
        <a class="nav-icon" href="company.php"><img src="./Images/leaf-eco-svgrepo-com.svg" alt="W4G"></a>
        <a class="nav-icon" href="options.php"><img src="./Images/gear-svgrepo-com.svg" alt="Options"></a>
  </div>
</div>

</body>
</html>