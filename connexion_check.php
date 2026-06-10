<?php
    if (!(isset($_SESSION["authentifie"]) && $_SESSION["authentifie"])) {
        header("Location: connexion_compte.php");
        exit;
    }
?>