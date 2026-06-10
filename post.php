<?php
    session_start();

    require_once("bd.php");
    require_once("utils.php");

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $postId = (int) $_GET['id'];

        // ------------------------------------ Récupérer les données du post correspondant --------------------------------------------------
        $sql = "SELECT * FROM post WHERE id_post = ?";
        $statement = $pdo->prepare($sql);
        $statement->execute([$postId]);

        // Si le post n'existe pas, renvoyer sur l'index
        if (!$postDataRow = $statement->fetch(PDO::FETCH_ASSOC)) {
            header("Location: index.php");
            exit;
        }

        // ----------------------------------------- Vérifier si l'utilisateur a déjà commenté -----------------------------------------------
        if (isset($_SESSION["authentifie"]) && $_SESSION["authentifie"]) {
            $sql = "SELECT id_commentaire FROM commentaire WHERE id_user = ? AND id_post = ?";
            $statement = $pdo->prepare($sql);
            $statement->execute([$_SESSION["id_user"], $postId]);
            $etatCommente = "non";
            
            if ($statement->fetch(PDO::FETCH_ASSOC)) {
                $etatCommente = "oui";
            }
        } else {
            $etatCommente = "pas_connecte";
        }

        // ------------------------------ Ajouter le nouveau commentaire si l'utilisateur a rempli le form -----------------------------------
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION["authentifie"]) && $_SESSION["authentifie"]) {
            if ($_SESSION["authentifie"] && !empty($_POST["commentaire-contenu"])) {
                if ($etatCommente == "non") {
                    $sql = "INSERT INTO commentaire(contenu, id_user, id_post) VALUES(?, ?, ?)";
                    $statement = $pdo->prepare($sql);
                    $statement->execute([trim(htmlspecialchars($_POST["commentaire-contenu"])), htmlspecialchars($_SESSION["id_user"]), $postId]);
                    $etatCommente = "oui";
                }
            }
        }

        // ------------------------------------ Récupérer les données de l'auteur ------------------------------------------------------------
        $sql = "SELECT pseudo FROM utilisateur WHERE id_user = ?";
        $statement = $pdo->prepare($sql);
        $statement->execute([$postDataRow["id_user"]]);

        // Si les données n'ont pas été récupérée renvoyer sur l'index
        if (!$postUserRow = $statement->fetch(PDO::FETCH_ASSOC)) {         
            header("Location: index.php");
            exit;
        }

        // ------------------------------------ Récupérer la ou les catégorie(s) du post -----------------------------------------------------
        $sql = "SELECT c.id_categorie, c.nom_categorie FROM post_categorie p JOIN categorie c ON p.id_categorie = c.id_categorie WHERE p.id_post = ?";
        $statement = $pdo->prepare($sql);
        $statement->execute([$postId]);

        // S'il n'a pas de catégorie on remplace par un placeholder
        if (!$postCategoriesRow = $statement->fetchAll(PDO::FETCH_ASSOC)) {         
            $postCategoriesRow = [["id_categorie" => null, "nom_categorie" => "Sans catégorie"]];
        }

        // ----------------------- Charger toutes les données du post utiles à la page dans un objet approprié -------------------------------
        $post = new Post(
            $postId,
            $postDataRow["titre_post"],
            $postDataRow["description"],
            $postDataRow["date_post"],
            $postUserRow["pseudo"],
            $postDataRow["id_user"],
            $postCategoriesRow
        );

        // ------ Charger les commentaires dans un tableau (le premier est celui de l'utilisateur connecté (s'il en a laissé un)) ------------
        $sql = "SELECT id_commentaire, contenu, pseudo, u.id_user 
                FROM commentaire c 
                JOIN utilisateur u ON c.id_user = u.id_user 
                WHERE id_post = ?";

        $params = [$postId];

        // Si l'utilisateur est connecté, on ajoute le tri
        if (isset($_SESSION['authentifie']) && $_SESSION['authentifie']) {
            $sql .= " ORDER BY (c.id_user = ?) DESC";
            $params[] = $_SESSION['id_user'];
        }

        $statement = $pdo->prepare($sql);
        $statement->execute($params);

        $commentaires = [];
        while ($commentaireRow = $statement->fetch(PDO::FETCH_ASSOC)) {
            $commentaires[] = new Commentaire($commentaireRow["id_commentaire"], $commentaireRow["id_user"], $commentaireRow["pseudo"], $commentaireRow["contenu"]);
        }
    } else {
        header("Location: index.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="fr">
    <?php
        require_once("utils.php");
        require_once("head.php");
    ?>
    <body id="post">
        <?php require_once("header.php") ?>

        <main>
            <section id="post-contenu">
                <h2><?= $post->getTitre(); ?></h2>

                <section id="post-infos">
                    <div id="post-categories">
                        <p><?= (count($post->getCategories()) == 1) ? "Catégorie :" : "Catégories :" ?></p>
                        <?php
                        foreach ($post->getCategories() as $categorie) {
                            ?>
                            <a href="index.php?categorie=<?= $categorie["id_categorie"] ?? "" ?>" class="post-categorie"><?= $categorie["nom_categorie"] ?></a>
                            <?php
                        }
                        ?>
                    </div>
                    <div>
                        <p>Auteur: <a href="profil.php?id=<?= $post->getAuteurId() ?>"><?= $post->getAuteur(); ?></a></p>
                        <p>Date: <?= $post->getDate(); ?></p>
                    </div>
                </section>

                <p><?= $post->getDescription(); ?></p>
            </section>

            <section id="post-images">
                <?php
                    $postId = $post->getId();
                    $auteurId = $post->getAuteurId();
                    $cheminDossier = "uploads/users/$auteurId/posts/$postId";

                    if (is_dir($cheminDossier)) {
                        if ($dossier = opendir($cheminDossier)) {
                            while (($fichier = readdir($dossier)) !== false) {
                                if ($fichier != '.' && $fichier != '..') {
                                    $cheminFichier = $cheminDossier . DIRECTORY_SEPARATOR . $fichier;
                                    echo "<img src='$cheminFichier' alt='Image du post' />";
                                }
                            }
                            closedir($dossier);
                        }
                    }
                ?>
            </section>

            <section id="post-commentaires">
                <?php
                if ($etatCommente == "non" || $etatCommente == "pas_connecte") {
                    ?>
                    
                    <form id="form-commentaires" action="post.php?id=<?= $postId ?>" method="post">
                        <label for="commentaire-contenu">Laisser un commentaire :</label>
                        <textarea name="commentaire-contenu" <?= $etatCommente == "pas_connecte" ? 'disabled' : '' ?>><?= $etatCommente == "pas_connecte" ? 'Connectez-vous pour commenter !' : '' ?></textarea>
                        <button type="submit">Envoyer</button>
                    </form>

                    <?php
                }

                foreach ($commentaires as $commentaire) {
                    ?>
                    <div class="commentaire">
                        <div>
                            <a href="profil.php?id=<?= $commentaire->getIdAuteur() ?>"><?= $commentaire->getAuteur(); ?></a>
                            <p><?= $commentaire->getContenu(); ?></p>
                        </div>
                        <?php
                        if (isset($_SESSION["authentifie"]) && $_SESSION["authentifie"] && $commentaire->getAuteur() == $_SESSION["pseudo"]) {
                            $idCommentaire = $commentaire->getId();
                            ?>
                            <a class="button" href=<?= "suppression_commentaire.php?id_commentaire=$idCommentaire" ?>><span class="material-symbols-outlined">delete</span></a>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
            </section>
        </main>
            
        <?php require_once("footer.php") ?>
    </body>
</html>