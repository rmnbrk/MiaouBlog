<?php session_start() ?>
<!DOCTYPE html>
<html lang="fr">
    <?php
        require_once("utils.php");
        require_once("head.php");
        require_once("bd.php");

        if (!(isset($_GET["id"]) && is_numeric($_GET["id"]))) {
            header("Location: index.php");
        }

        $sql = "SELECT p.*, GROUP_CONCAT(c.nom_categorie SEPARATOR ', ') as nom_categorie 
                FROM post p 
                LEFT JOIN post_categorie pc ON p.id_post = pc.id_post
                LEFT JOIN categorie c ON pc.id_categorie = c.id_categorie 
                WHERE p.id_user = ? 
                GROUP BY p.id_post
                ORDER BY p.date_post DESC";
        
        $statement = $pdo->prepare($sql);
        $statement->execute([$_GET["id"]]);
        $posts = $statement->fetchAll();

        // Récupérer le pseudo correspondant au profil
        $sql = "SELECT pseudo FROM utilisateur WHERE id_user = ?";
        $statement = $pdo->prepare($sql);
        $statement->execute([$_GET["id"]]);
        $auteur = $statement->fetch(PDO::FETCH_ASSOC);
    ?>
    <body id="authentification">
        <?php require_once("header.php") ?>

        <main>
            <h2>Profil de <?= $auteur["pseudo"] ?></h2>
            <!-- gérer mes posts (droite) -->
                <section class="mon-compte-section section-posts">
                    <h3>Posts</h3>
                    
                    <?php if (empty($posts)): ?>
                        <p class="aucun-contenu">Vous n'avez pas encore créé de posts.</p>
                    <?php else: ?>
                        <div class="liste-posts">
                            <?php foreach ($posts as $post): ?>
                                <a href="post.php?id=<?= $post["id_post"] ?>">
                                    <div class="item-post">
                                        <div class="post-info">
                                            <h4><?= htmlspecialchars($post["titre_post"]) ?></h4>
                                            <p class="post-meta">
                                                <span class="categorie"><?= htmlspecialchars($post["nom_categorie"] ?? "Sans catégorie") ?></span>
                                                <span class="date"><?= date("d/m/Y", strtotime($post["date_post"])) ?></span>
                                            </p>
                                            <p class="description"><?= htmlspecialchars(substr($post["description"], 0, 100)) ?>...</p>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
        </main>
        
        <?php require_once("footer.php") ?>
    </body>
</html>