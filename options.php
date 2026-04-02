<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupération des infos de l'utilisateur pour vérifier s'il est "verified" (chef)
$req = $bdd->prepare("SELECT verified, firstname, lastname FROM users WHERE id = ?");
$req->execute([$user_id]);
$user = $req->fetch();

$is_manager = ($user['verified'] == 1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Options - Work4Green</title>
    <link rel="stylesheet" href="work4green.css">
</head>
<body>

<main class="app-container flex-col gap-md">
    
    <header class="mb-md">
        <h1 class="text-title" style="font-size: 1.8rem;">Réglages</h1>
        <p class="text-subtitle" style="color: var(--third)">Gérez votre compte et l'application</p>
    </header>

    <section class="flex-col gap-sm">
        <div class="text-small" style="text-transform: uppercase; font-weight: bold; color: var(--secondary); margin-left: 5px;">Mon Profil</div>
        <div class="card flex-col gap-md" style="background: white;">
            <div class="d-flex justify-between items-center">
                <div class="d-flex items-center gap-sm">
                    <div style="font-size: 1.2rem;">👤</div>
                    <div class="text-dark" style="font-weight: 600;"><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></div>
                </div>
                <a href="" class="text-third" style="font-size: 0.8rem; font-weight: bold;">Modifier</a>
            </div>
            <div class="d-flex justify-between items-center">
                <div class="d-flex items-center gap-sm">
                    <div style="font-size: 1.2rem;">🔒</div>
                    <div class="text-dark" style="font-weight: 600;">Mot de passe</div>
                </div>
                <a href="" class="text-third" style="font-size: 0.8rem; font-weight: bold;">Changer</a>
            </div>
        </div>
    </section>

    <?php if ($is_manager): ?>
    <section class="flex-col gap-sm">
        <div class="text-small" style="text-transform: uppercase; font-weight: bold; color: var(--secondary); margin-left: 5px;">Administration</div>
        <div class="card" style="background: var(--secondary); border: none;">
            <div class="flex-col gap-sm">
                <div class="text-white" style="font-weight: bold;">Gestion d'équipe</div>
                <p class="text-white" style="font-size: 0.8rem; opacity: 0.8;">En tant que gérant, vous pouvez ajouter de nouveaux employés à votre entreprise.</p>
                <a href="sendinvitation.php" class="btn btn-primary" style="background: var(--neutral); color: var(--dark); margin-top: 10px;">
                    ✉️ Inviter des employés
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="flex-col gap-sm">
        <div class="text-small" style="text-transform: uppercase; font-weight: bold; color: var(--secondary); margin-left: 5px;">Application</div>
        <div class="card flex-col gap-md" style="background: white;">
            <div class="d-flex justify-between items-center">
                <div class="d-flex items-center gap-sm">
                    <div style="font-size: 1.2rem;">🔔</div>
                    <div class="text-dark" style="font-weight: 600;">Notifications</div>
                </div>
                <div class="status-badge" style="background: #eee; color: #888;">Activé</div>
            </div>
            <div class="d-flex justify-between items-center">
                <div class="d-flex items-center gap-sm">
                    <div style="font-size: 1.2rem;">🌙</div>
                    <div class="text-dark" style="font-weight: 600;">Mode sombre</div>
                </div>
                <div class="status-badge" style="background: #eee; color: #888;">Bientôt</div>
            </div>
        </div>
    </section>

    <section class="mt-md">
        <a href="logout.php" class="btn btn-logout">
            Se déconnecter
        </a>
    </section>

</main>

<nav class="navbar">
    <div class="navbar-inner">
        <a class="nav-icon" href="profile.php"><img src="./Images/profile-1341-svgrepo-com.svg" alt="Profil"></a>
        <a class="nav-icon" href="quests.php"><img src="./Images/notebook-svgrepo-com.svg" alt="Quetes"></a>
        <a class="nav-icon" href="company.php"><img src="./Images/leaf-eco-svgrepo-com.svg" alt="W4G"></a>
        <a class="nav-icon" href="options.php"><img src="./Images/gear-svgrepo-com.svg" alt="Options"></a>
    </div>
</nav>

</body>
</html>