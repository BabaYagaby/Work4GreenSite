<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- ACTION : ÉQUIPER UN OBJET ---
if (isset($_GET['equip_id']) && isset($_GET['type'])) {
    $item_id = (int)$_GET['equip_id'];
    $type = $_GET['type'];

    // On vérifie d'abord que l'utilisateur possède bien cet objet
    $check = $bdd->prepare("SELECT 1 FROM user_inventory WHERE user_id = ? AND item_id = ?");
    $check->execute([$user_id, $item_id]);

    if ($check->fetch()) {
        if ($type == 'avatar') {
            $update = $bdd->prepare("UPDATE users SET current_avatar_id = ? WHERE id = ?");
            $update->execute([$item_id, $user_id]);
        } 
        // Note : pour le badge, on pourrait ajouter une colonne 'favorite_badge_id' dans users
        elseif ($type == 'badge') {
            $update = $bdd->prepare("UPDATE users SET favorite_badge_id = ? WHERE id = ?");
            $update->execute([$item_id, $user_id]);
        }
    }
    header("Location: profile.php");
    exit();
}

// --- RÉCUPÉRATION DE L'INVENTAIRE ---
// On récupère tous les objets possédés par l'utilisateur
$reqInv = $bdd->prepare("
    SELECT c.* FROM items_catalog c
    JOIN user_inventory i ON c.id = i.item_id
    WHERE i.user_id = ?
");
$reqInv->execute([$user_id]);
$items = $reqInv->fetchAll(PDO::FETCH_ASSOC);

// On sépare les avatars et les badges pour l'affichage
$avatars = array_filter($items, function($i) { return $i['type'] == 'avatar'; });
$badges = array_filter($items, function($i) { return $i['type'] == 'badge'; });
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Inventaire - Work4Green</title>
    <link rel="stylesheet" href="work4green.css">
</head>
<body>

<main class="app-container">
    <h1 class="text-title">Mon Inventaire</h1>

    <section class="card">
        <h2>Skins / Avatars</h2>
        <div class="inventory-grid">
            <?php foreach($avatars as $av): ?>
                <div class="item-card">
                    <img src="<?= htmlspecialchars($av['image_path']) ?>" alt="<?= htmlspecialchars($av['name']) ?>">
                    <p><?= htmlspecialchars($av['name']) ?></p>
                    <a href="?equip_id=<?= $av['id'] ?>&type=avatar" class="btn-equip">Équiper</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="card mt-md">
        <h2>Mes Badges</h2>
        <div class="inventory-grid">
            <?php foreach($badges as $bg): ?>
                <div class="item-card">
                    <img src="<?= htmlspecialchars($bg['image_path']) ?>" alt="<?= htmlspecialchars($bg['name']) ?>">
                    <p><?= htmlspecialchars($bg['name']) ?></p>
                    <a href="?equip_id=<?= $bg['id'] ?>&type=badge" class="btn-equip">Épingler</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <div>
        <a href="profile.php" class="btn btn-outline">Retour au profil</a>
    </div>
</main>

</body>
</html>