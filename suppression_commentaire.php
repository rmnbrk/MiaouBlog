<?php
    session_start();

    require_once("bd.php");

    // On vérifie que l'utilisateur est connecté
    if (!isset($_SESSION['id_user'])) {
        header('Location: index.php');
        exit;
    }
    $idAuteur = $_SESSION['id_user'];

    // On vérifie que l'id du commentaire est renseigné
    if (!isset($_GET['id_commentaire']) || !is_numeric($_GET['id_commentaire'])) {
        header('Location: index.php');
        exit;
    }
    $idCommentaire = (int)$_GET['id_commentaire'];

    // On vérifie que le commentaire existe et appartient à l'utilisateur
    $sql = "SELECT id_user, id_post FROM commentaire WHERE id_commentaire = ?";
    $statement = $pdo->prepare($sql);
    $statement->execute([$idCommentaire]);
    $commentaire = $statement->fetch();

    if (!$commentaire || $commentaire['id_user'] != $idAuteur) {
        // Le commentaire n'existe pas ou l'utilisateur essaye de supprimer un commentaire qu'il n'a pas écris
        header("Location: index.php");
        exit;
    }

    // On supprime le commentaire
    $sql = "DELETE FROM commentaire WHERE id_commentaire = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idCommentaire]);

    header('Location: post.php?id=' . $commentaire["id_post"]);
    exit;
?>