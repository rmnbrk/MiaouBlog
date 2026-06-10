<!DOCTYPE html>
<html lang="fr">
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

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!empty($_POST["nom-categorie"])) {
                $sql = "INSERT INTO categorie(nom_categorie) VALUES(?)";
                $statement = $pdo->prepare($sql);
                $statement->execute([$_POST["nom-categorie"]]);
            }
        }
    ?>
        <body id="admin_categories">
            <?php require_once("header.php"); ?>

            <main>
                <h2>Gestion des catégories</h2>
                
                <?php
                $sql = "SELECT * FROM categorie";
                $statement = $pdo->prepare($sql);
                $statement->execute();
                ?>
                <section>
                <?php
                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <div class="categorie">
                        <p><?= $row["nom_categorie"] ?></p>
                        <a href="suppression_categorie.php?id=<?= $row["id_categorie"] ?>"><span class="material-symbols-outlined">delete</span></a>
                    </div>
                    <?php
                } ?>
                </section>
                <form action="" method="post">
                    <p>Nouvelle catégorie :</p>
                    <input type="text" name="nom-categorie">
                    <button>Créer</button>
                </form>
                <?php
                ?>
            </main>

            <?php require_once("footer.php") ?>
        </body>
</html>