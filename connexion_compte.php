<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
    <?php
        require_once("utils.php");
        require_once("bd.php");
        require_once("head.php");
    ?>
    <body id="authentification">
        <?php require_once("header.php"); ?>

        <?php
            $msgErreurIdentifiant = null;
            $msgErreurMotDePasse = null;

            if (!empty($_POST)) {
                if (!empty($_POST['identifiant'])) {
                    $_SESSION["identifiant"] = trim(htmlspecialchars($_POST['identifiant']));
                } else {
                    $msgErreurIdentifiant = "Merci de renseigner un pseudo ou une adresse mail";
                }

                if (empty($_POST["motdepasse"])) {
                    $msgErreurMotDePasse = "Merci de renseigner un mot de passe";
                }

                if (empty($msgErreurIdentifiant) && empty($msgErreurMotDePasse)) {
                    $sql = "SELECT mot_de_passe, id_user, pseudo, email FROM utilisateur WHERE pseudo = ? OR email = ?";
                    $statement = $pdo->prepare($sql);
                    $statement->execute([$_SESSION["identifiant"], $_SESSION["identifiant"]]);

                    if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                        if (password_verify(trim(htmlspecialchars($_POST["motdepasse"])), $row["mot_de_passe"])) {
                            $_SESSION["authentifie"] = true;
                            $_SESSION["id_user"] = $row["id_user"];
                            $_SESSION["pseudo"] = $row["pseudo"];
                            $_SESSION["email"] = $row["email"];
                            unset($_SESSION["identifiant"]);
                            header("Location: index.php");
                            exit;
                        } else {
                            $msgErreurMotDePasse = "Le mot de passe est incorrect";
                        }
                    } else {
                        $msgErreurIdentifiant = "Il n'existe pas de compte avec cet identifiant";
                    }
                }
            }
        ?>

        <main>
            <div id="authentification-header">
                <h2>Se connecter</h2>
                <span>ou <a href="creation_compte.php">créer un compte</a></span>
            </div>

            <form action="" method="post">
                <label for="identifiant">Pseudo ou adresse mail :</label>
                <input type="text" id="identifiant" name="identifiant" value="<?= $_SESSION["identifiant"] ?? "" ?>">
                <?php echoMessageErreur($msgErreurIdentifiant); ?>

                <label for="motdepasse">Mot de passe :</label>
                <input type="password" id="motdepasse" name="motdepasse">
                <?php echoMessageErreur($msgErreurMotDePasse); ?>

                <button type="submit">Se connecter</button>
            </form>
        </main>

        <?php require_once("footer.php"); ?>
    </body>
</html>