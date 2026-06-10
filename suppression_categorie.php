<?php
    session_start();
    require_once("head.php");
    require_once("bd.php");

    // Vérifier qu'il y a un compte connecté
    require_once("connexion_check.php");

    // Vérifier que c'est l'admin
    if ($_SESSION["email"] != "admin@localhost.fr") {
        header("Location: index.php");
    }

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        if (!empty($_GET["id"])) {
            $sql = "DELETE FROM categorie WHERE id_categorie = ?";
            $statement = $pdo->prepare($sql);
            $statement->execute([$_GET["id"]]);

            header("Location: admin_categories.php");
        }
    }
?>