<?php
session_start();
include('config.php');

if (isset($_POST['choix']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $choix = $_POST['choix'];

    if ($choix == 'accepter') {
        // On valide l'invitation (status 2)
        $sql = "UPDATE users SET invitation_status = 2 WHERE id = :id";
    } else {
        // On annule tout (on remet l'id entreprise à NULL et status à 0)
        $sql = "UPDATE users SET company_id = NULL, invitation_status = 0 WHERE id = :id";
    }

    $req = $bdd->prepare($sql);
    $req->execute(['id' => $user_id]);

    echo '<script>alert("Réponse enregistrée !"); window.location.href="quest.php";</script>';
}
?>