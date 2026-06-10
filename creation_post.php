<?php
    session_start();

    require_once("connexion_check.php");

    require_once("utils.php");
    require_once("bd.php");

    $msgErreurTitre = null;
    $msgErreurDescription = null;
    $msgErreurCategories = null;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (empty($_POST["titre-post"])) {
            $msgErreurTitre = "Merci de renseigner un titre";
        }

        if (empty($_POST["description-post"])) {
            $msgErreurDescription = "Merci de renseigner une description";
        }

        if (!isset($_POST["categorie-post"])) {
            $msgErreurCategories = "Merci de sélectionner au moins une catégorie";
        }

        if (empty($msgErreurTitre) && empty($msgErreurDescription) && empty($msgErreurCategories)) {
            // Ajouter le post à la BD
            $sql = "INSERT INTO post (titre_post, description, id_user, date_post) VALUES (?, ?, ?, NOW())";
            $statement = $pdo->prepare($sql);
            $statement->execute([htmlspecialchars($_POST["titre-post"]), htmlspecialchars($_POST["description-post"]), $_SESSION["id_user"]]);

            $idPost = $pdo->lastInsertId();
            $idAuteur = $_SESSION['id_user'];

            // Attribuer les catégories au post dans la BD
            foreach ($_POST["categorie-post"] as $idCategorie) {
                $sql = "INSERT INTO post_categorie(id_post, id_categorie) VALUES(?, ?)";
                $statement = $pdo->prepare($sql);
                $statement->execute([$idPost, $idCategorie]);
            }

            $possedeImages = false;
            if (isset($_FILES['images'])) {
                foreach ($_FILES['images']['error'] as $erreur) {
                    if ($erreur === UPLOAD_ERR_OK) {
                        $possedeImages = true;
                        break;
                    }
                }
            }

            if ($possedeImages) {
                upload($_FILES["images"], "users/$idAuteur/posts/$idPost");
            }

            sleep(0.5); // Au cas où la création prenne un peu de temps
            header("Location: post.php?id=$idPost");
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <?php
        require_once("head.php");
    ?>
    <body id="authentification">
        <?php require_once("header.php") ?>

        <main>
            <h2>Créer un post</h2>

            <form action="" method="post" enctype="multipart/form-data">
                <fieldset id="choix-categorie">
                    <legend>Catégorie :</legend>
                    <?php
                    $sql = "SELECT id_categorie, nom_categorie FROM categorie";
                    $statement = $pdo->prepare($sql);
                    $statement->execute();

                    while ($row = $statement->fetch()) { ?>
                        <div>
                            <input type="checkbox" id="<?= $row["id_categorie"] ?>" name="categorie-post[]" value="<?= $row["id_categorie"] ?>" />
                            <label for="<?= $row["id_categorie"] ?>"><?= $row["nom_categorie"] ?></label>
                        </div>
                    <?php } ?>
                </fieldset>
                <?php echoMessageErreur($msgErreurCategories) ?>

                <label for="titre-post">Titre :</label>
                <input type="text" name="titre-post" value="<?= $_POST["titre-post"] ?? "" ?>" />
                <?php echoMessageErreur($msgErreurTitre) ?>

                <label for="description-post">Description :</label>
                <textarea type="text" id="description-post" name="description-post"><?= $_POST["description-post"] ?? "" ?></textarea>
                <?php echoMessageErreur($msgErreurDescription) ?>

                <input type="file" id="images" name="images[]" accept="image/png, image/jpeg" multiple/>
                
                <div id="form-end">
                    <button type="submit">Créer</button>
                </div>
            </form>
        </main>

        <?php require_once("footer.php") ?>
    </body>
</html>