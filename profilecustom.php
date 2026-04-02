<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: connection.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customisation - Work4Green</title>
    <link rel="stylesheet" href="work4green.css">
    <style>
        /* Styles spécifiques à cette page pour éviter de polluer le CSS global */
        .custom-page {
            background-color: var(--main);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .custom-header {
            padding: var(--space-md);
            background: var(--main);
            text-align: center;
        }

        /* Preview Section : Correction du socle */
        .preview-section {
            background-color: var(--dark);
            background-image: url('./Images/pattern.png');
            background-size: cover;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding-bottom: 20px;
        }

        .character-preview {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: center;
            align-items: flex-end;
        }

        .character-preview img {
            height: auto;
            width: 80%;
            object-fit: contain;
        }

        .preview-base {
            position: absolute;
            bottom: 10px;
            width: 180px;
            height: 40px;
            background: rgba(0, 0, 0, 0.38);
            transform: translateY(-30px);
            border-radius: 50%;
            z-index: 1;
        }

        /* Barre d'outils icons */
        .custom-tools {
            display: flex;
            justify-content: space-around;
            padding: 10px;
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .tool-btn {
            background: none;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }

        .tool-btn img {
            width: 28px;
            height: 28px;
            opacity: 0.6;
        }

        .tool-btn.active {
            border-bottom-color: var(--third);
        }

        .tool-btn.active img {
            opacity: 1;
        }

        /* Inventaire */
        .inventory-container {
            background-color: #FDF2E3;
            margin: 15px;
            border-radius: 20px;
            padding: 15px;
            flex: 1; /* Prend l'espace disponible */
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 100px; /* Espace pour la navbar fixe */
        }

        .inventory-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .inventory-item {
            background: white;
            border-radius: 15px;
            aspect-ratio: 1/1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }

        .inventory-item img {
            max-width: 80%;
            max-height: 80%;
        }

        .inventory-item.locked {
            opacity: 0.6;
            background: #e0e0e0;
        }

        .btn-save {
            text-align : center;
            background-color: var(--third);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            width: 100%;
            margin-top: auto;
        }

    </style>
</head>
<body class="custom-page">
    
    <header class="custom-header">
        <h1 class="text-title">Customisation</h1>
    </header>

    <main class="content-wrapper">
        <section class="preview-section">
            <div class="character-preview">
                <img src="./Images/perso-03.svg" alt="Mon Personnage">
            </div>
            <div class="preview-base"></div>
        </section>

        <nav class="custom-tools">
            <button class="tool-btn"><img src="./Images/paint-1-svgrepo-com.svg" alt="Couleurs"></button>
            <button class="tool-btn active"><img src="./Images/person-wave-svgrepo-com.svg" alt="Vêtements"></button>
            <button class="tool-btn"><img src="./Images/shopping-cart-01-svgrepo-com.svg" alt="Boutique"></button>
        </nav>

        <section class="inventory-container">
            <div class="inventory-header">
                <button class="arrow-btn"> &lt; </button>
                <span class="text-subtitle" style="color: var(--dark)">Poses</span>
                <button class="arrow-btn"> &gt; </button>
            </div>

            <div class="inventory-grid">
                
                <div class="inventory-item">

                    <img src="./Images/perso-01.svg" alt="Personnage">

                </div>



                <div class="inventory-item">

                    <img src="./Images/perso-02.svg" alt="Personnage">

                </div>



                <div class="inventory-item" style="border : solid 5px var(--third);">

                    <img src="./Images/perso-03.svg" alt="Personnage">

                </div>



                <div class="inventory-item">

                    <img src="./Images/perso-04.svg" alt="Personnage">

                </div>



                <div class="inventory-item">

                    <img src="./Images/perso-05.svg" alt="Personnage">

                </div>



                <div class="inventory-item">

                    <img src="./Images/perso-06.svg" alt="Personnage">

                </div>

               

                <div class="inventory-item">

                    <img src="./Images/perso-07.svg" alt="Personnage">

                </div>



                <div class="inventory-item">

                    <img src="./Images/perso-08.svg" alt="Personnage">

                </div>



                <div class="inventory-item locked">

                    <img src="./Images/perso-09.svg" alt="Personnage">

                </div>



                <div class="inventory-item locked">

                    <img src="./Images/perso-10.svg" alt="Personnage">

                </div>
                
                <div class="inventory-item locked">
                    <img src="./Images/perso-11.svg" alt="Locked">
                    <div class="lock-overlay"></div>
                </div>
            </div>

            <a href="profile.php" class="btn-save">Sauvegarder les modifications</a>
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