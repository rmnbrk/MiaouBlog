<!DOCTYPE html>
<html lang="fr">
    <?php
    session_start();
        require_once("head.php");
        require_once("bd.php");
        require_once("utils.php");

        if (!isset($_SESSION["authentifie"]) || !$_SESSION["authentifie"]) {
            header("Location: connexion_compte.php");
            exit;
        }

        $msgErreurPseudo = null;
        $msgErreurEmail = null;
        $msgErreurMotDePasse = null;
        $msgSucces = null;

        $sql = "SELECT pseudo, email FROM utilisateur WHERE id_user = ?";
        $statement = $pdo->prepare($sql);
        $statement->execute([$_SESSION["id_user"]]);
        $user = $statement->fetch();

        if (isset($_POST["modifier_pseudo"])) {
            if (!empty($_POST["nouveau_pseudo"])) {
                $nouveauPseudo = trim(htmlspecialchars($_POST["nouveau_pseudo"]));
                
                // Vérifier si le pseudo est déjà utilisé
                $sql = "SELECT id_user FROM utilisateur WHERE pseudo = ? AND id_user != ?";
                $statement = $pdo->prepare($sql);
                $statement->execute([$nouveauPseudo, $_SESSION["id_user"]]);
                
                if ($statement->fetch()) {
                    $msgErreurPseudo = "Ce pseudo est déjà utilisé";
                } else {
                    $sql = "UPDATE utilisateur SET pseudo = ? WHERE id_user = ?";
                    $statement = $pdo->prepare($sql);
                    $statement->execute([$nouveauPseudo, $_SESSION["id_user"]]);
                    $_SESSION["pseudo"] = $nouveauPseudo;
                    $msgSucces = "Pseudo modifié avec succès";
                    $user["pseudo"] = $nouveauPseudo;
                }
            } else {
                $msgErreurPseudo = "Merci de renseigner un pseudo";
            }
        }

        if (isset($_POST["modifier_email"])) {
            if (!empty($_POST["nouveau_email"])) {
                $nouvelEmail = trim(htmlspecialchars($_POST["nouveau_email"]));
                
                // Vérifier si l'email est déjà utilisé
                $sql = "SELECT id_user FROM utilisateur WHERE email = ? AND id_user != ?";
                $statement = $pdo->prepare($sql);
                $statement->execute([$nouvelEmail, $_SESSION["id_user"]]);
                
                if ($statement->fetch()) {
                    $msgErreurEmail = "Un compte avec cet email existe déjà";
                } else {
                    $sql = "UPDATE utilisateur SET email = ? WHERE id_user = ?";
                    $statement = $pdo->prepare($sql);
                    $statement->execute([$nouvelEmail, $_SESSION["id_user"]]);
                    $_SESSION["email"] = $nouvelEmail;
                    $msgSucces = "Email modifié avec succès";
                    $user["email"] = $nouvelEmail;
                }
            } else {
                $msgErreurEmail = "Merci de renseigner un email";
            }
        }

        if (isset($_POST["modifier_motdepasse"])) {
            if (!empty($_POST["nouveau_motdepasse"])) {
                $nouveauMotDePasse = password_hash(trim(htmlspecialchars($_POST["nouveau_motdepasse"])), PASSWORD_DEFAULT);
                
                $sql = "UPDATE utilisateur SET mot_de_passe = ? WHERE id_user = ?";
                $statement = $pdo->prepare($sql);
                $statement->execute([$nouveauMotDePasse, $_SESSION["id_user"]]);
                $msgSucces = "Mot de passe modifié avec succès";
            } else {
                $msgErreurMotDePasse = "Merci de renseigner un mot de passe";
            }
        }

        if (isset($_POST["confirmer_suppression"])) {
            // Supprimer les commentaires de l'utilisateur
            $sql = "DELETE FROM commentaire WHERE id_user = ?";
            $statement = $pdo->prepare($sql);
            $statement->execute([$_SESSION["id_user"]]);
            
            // Supprimer les associations post_categorie des posts de l'utilisateur
            $sql = "DELETE FROM post_categorie WHERE id_post IN (SELECT id_post FROM post WHERE id_user = ?)";
            $statement = $pdo->prepare($sql);
            $statement->execute([$_SESSION["id_user"]]);
            
            // Supprimer les posts de l'utilisateur
            $sql = "DELETE FROM post WHERE id_user = ?";
            $statement = $pdo->prepare($sql);
            $statement->execute([$_SESSION["id_user"]]);
            
            // Supprimer l'utilisateur
            $sql = "DELETE FROM utilisateur WHERE id_user = ?";
            $statement = $pdo->prepare($sql);
            $statement->execute([$_SESSION["id_user"]]);
            
            session_destroy();
            header("Location: index.php");
            exit;
        }

        $sql = "SELECT p.*, GROUP_CONCAT(c.nom_categorie SEPARATOR ', ') as nom_categorie 
                FROM post p 
                LEFT JOIN post_categorie pc ON p.id_post = pc.id_post
                LEFT JOIN categorie c ON pc.id_categorie = c.id_categorie 
                WHERE p.id_user = ? 
                GROUP BY p.id_post
                ORDER BY p.date_post DESC";
        
        $statement = $pdo->prepare($sql);
        $statement->execute([$_SESSION["id_user"]]);
        $mesPosts = $statement->fetchAll();

        if (isset($_POST["supprimer_post"])) {
            $idPost = intval($_POST["id_post"]);
            
            // Vérifier que le post appartient à l'utilisateur
            $sql = "SELECT id_user FROM post WHERE id_post = ?";
            $statement = $pdo->prepare($sql);
            $statement->execute([$idPost]);
            $post = $statement->fetch();
            
            if ($post && $post["id_user"] == $_SESSION["id_user"]) {
                // Supprimer les commentaires du post
                $sql = "DELETE FROM commentaire WHERE id_post = ?";
                $statement = $pdo->prepare($sql);
                $statement->execute([$idPost]);
                
                // Supprimer les associations post_categorie
                $sql = "DELETE FROM post_categorie WHERE id_post = ?";
                $statement = $pdo->prepare($sql);
                $statement->execute([$idPost]);
                
                // Supprimer le post
                $sql = "DELETE FROM post WHERE id_post = ?";
                $statement = $pdo->prepare($sql);
                $statement->execute([$idPost]);

                // Supprimer les images associées
                $idUser = $post["id_user"];
                $cheminDossier = "uploads/users/$idUser/posts/$idPost";
                $fichiers = scandir($cheminDossier);
                foreach ($fichiers as $fichier) {
                    $cheminComplet = $cheminDossier . DIRECTORY_SEPARATOR . $fichier;

                    if (is_file($cheminComplet)) {
                        unlink($cheminComplet);
                    }
                }
                rmdir($cheminDossier);
                
                $msgSucces = "Post supprimé avec succès";
                header("Location: compte.php");
                exit;
            }
        }
    ?>
    <body id="mon-compte">
        <?php require_once("header.php"); ?>

        <main>
            <h2>Mon compte</h2>

            <?php if ($msgSucces): ?>
                <p class="message-succes"><?= $msgSucces ?></p>
            <?php endif; ?>

            <div class="mon-compte-grid">
                <!-- modifier mes informations (gauche) -->
                <section class="mon-compte-section section-informations">
                    <h3>Modifier mes informations</h3>

                    <form action="" method="post" class="form-modification">
                        <label for="nouveau_pseudo">Modifier mon pseudo :</label>
                        <div class="form-input-group">
                            <input type="text" name="nouveau_pseudo" placeholder="Nouveau pseudo" value="<?= htmlspecialchars($user["pseudo"]) ?>">
                            <button type="submit" name="modifier_pseudo">Valider</button>
                        </div>
                        <?php echoMessageErreur($msgErreurPseudo); ?>
                    </form>

                    <form action="" method="post" class="form-modification">
                        <label for="nouveau_email">Modifier mon adresse mail :</label>
                        <div class="form-input-group">
                            <input type="email" name="nouveau_email" placeholder="Nouvelle adresse mail" value="<?= htmlspecialchars($user["email"]) ?>">
                            <button type="submit" name="modifier_email">Valider</button>
                        </div>
                        <?php echoMessageErreur($msgErreurEmail); ?>
                    </form>

                    <form action="" method="post" class="form-modification">
                        <label for="nouveau_motdepasse">Modifier mon mot de passe :</label>
                        <div class="form-input-group">
                            <input type="password" name="nouveau_motdepasse" placeholder="Nouveau mot de passe">
                            <button type="submit" name="modifier_motdepasse">Valider</button>
                        </div>
                        <?php echoMessageErreur($msgErreurMotDePasse); ?>
                    </form>

                    <form action="" method="post" class="form-suppression" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.');">
                        <button type="submit" name="confirmer_suppression" class="btn-supprimer">Supprimer mon compte</button>
                    </form>
                </section>

                <!-- gérer mes posts (droite) -->
                <section class="mon-compte-section section-posts">
                    <h3>Mes posts</h3>
                    
                    <?php if (empty($mesPosts)): ?>
                        <p class="aucun-contenu">Vous n'avez pas encore créé de posts.</p>
                    <?php else: ?>
                        <div class="liste-posts">
                            <?php foreach ($mesPosts as $post): ?>
                                <div class="item-post">
                                    <div class="post-info">
                                        <h4><?= htmlspecialchars($post["titre_post"]) ?></h4>
                                        <p class="post-meta">
                                            <span class="categorie"><?= htmlspecialchars($post["nom_categorie"] ?? "Sans catégorie") ?></span>
                                            <span class="date"><?= date("d/m/Y", strtotime($post["date_post"])) ?></span>
                                        </p>
                                        <p class="description"><?= htmlspecialchars(substr($post["description"], 0, 100)) ?>...</p>
                                    </div>
                                    <div class="post-actions">
                                        <a href="post.php?id=<?= $post["id_post"] ?>&retour=compte" class="btn-voir">Voir</a>
                                        <form action="" method="post" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce post ?');">
                                            <input type="hidden" name="id_post" value="<?= $post["id_post"] ?>">
                                            <button type="submit" name="supprimer_post" class="btn-supprimer-petit">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </main>

        <?php require_once("footer.php") ?>
    </body>
</html>