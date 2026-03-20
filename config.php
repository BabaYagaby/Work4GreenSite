<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "work4green";

    try {
        $bdd = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage()); 
    }

?>