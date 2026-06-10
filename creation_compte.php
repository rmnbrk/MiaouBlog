<?php session_start() ?>
<!DOCTYPE html>
<html lang="fr">
    <?php
        require_once("utils.php");
        require_once("bd.php");
        require_once("head.php");
    ?>
    <body id="authentification">
        <?php require_once("header.php") ?>

        <?php
            // Enregistrement
            $msgErreurEmail = null;
            $msgErreurPseudo = null;
            $msgErreurMotDePasse = null;

            if (!empty($_POST)) {
                if(!empty($_POST["email"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                    $_SESSION["email"] = trim(htmlspecialchars($_POST['email']));
                } else {
                    $msgErreurEmail = "Merci de renseigner un email valide";
                }

                if(!empty($_POST["pseudo"])) {
                    $_SESSION["pseudo"] = trim(htmlspecialchars($_POST['pseudo']));
                } else {
                    $msgErreurPseudo = "Merci de renseigner un pseudo";
                }

                if(!empty($_POST["motdepasse"])) {
                    $_SESSION["motdepasse"] = password_hash(trim(htmlspecialchars($_POST['motdepasse'])), PASSWORD_DEFAULT);
                } else {
                    $msgErreurMotDePasse = "Merci de renseigner un mot de passe";
                }

                if(empty($msgErreurEmail) && empty($msgErreurPseudo) && empty($msgErreurMotDePasse)) {
                    // Vérifier si l'email est déjà utilisé
                    $sql = "SELECT id_user FROM utilisateur WHERE email = ?";
                    $statement = $pdo->prepare($sql);
                    $statement->execute([$_SESSION["email"]]);

                    if ($statement->fetch()) {
                        $msgErreurEmail = "Un compte avec cet email existe déjà";
                    }

                    // Vérifier si le pseudo est déjà utilisé
                    $sql = "SELECT id_user FROM utilisateur WHERE pseudo = ?";
                    $statement = $pdo->prepare($sql);
                    $statement->execute([$_SESSION["pseudo"]]);

                    if ($statement->fetch()) {
                        $msgErreurPseudo = "Ce pseudo est déjà utilisé";
                    }

                    if (empty($msgErreurEmail) && empty($msgErreurPseudo)) {
                        $_SESSION["authentifie"] = true;

                        // Update de la db
                        $sql = "INSERT INTO utilisateur (email, pseudo, mot_de_passe) VALUES (?,?,?)";
                        $statement = $pdo->prepare($sql);
                        $statement->execute([$_SESSION["email"], $_SESSION["pseudo"], $_SESSION["motdepasse"]]);

                        $_SESSION["id_user"] = $pdo->lastInsertId();
                        
                        header("Location: index.php");
                        exit;
                    }
                }
            }
        ?>

        <main>
            <div class="authentification-header">
                <h2>S'inscrire</h2>
                <span>ou <a href="connexion_compte.php">se connecter</a></span>
            </div>

            <form action="" method="post">
                <label for="email">Email :</label>
                <input type="text" name="email" value="<?= $_SESSION["email"] ?? "" ?>">
                <?php echoMessageErreur($msgErreurEmail); ?>
                
                <label for="pseudo">Pseudo :</label>
                <input type="text" name="pseudo" value="<?= $_SESSION["pseudo"] ?? "" ?>">
                <?php echoMessageErreur($msgErreurPseudo); ?>

                <label for="motdepasse">Mot de passe :</label>
                <input type="password" name="motdepasse">
                <?php echoMessageErreur($msgErreurMotDePasse); ?>

                <button type="submit">S'inscrire</button>
            </form>
        </main>
        
        <?php require_once("footer.php") ?>
    </body>
</html>