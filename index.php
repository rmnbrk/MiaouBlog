<!DOCTYPE html>
<html lang="fr">
    <?php
    session_start();
        require_once("head.php");
        require_once("bd.php");
    ?>
        <body id="explorer">
            <?php require_once("header.php"); ?>

            <main>
                <h2>Explorer</h2>

                <div class="filter-container">
                    <form method="GET" action="">
                        <select name="tri">
                            <option value="DESC" <?= (!isset($_GET['tri']) || $_GET['tri'] == 'DESC') ? 'selected' : '' ?>>
                                Du plus récent au plus ancien
                            </option>
                            <option value="ASC" <?= (isset($_GET['tri']) && $_GET['tri'] == 'ASC') ? 'selected' : '' ?>>
                                Du plus ancien au plus récent
                            </option>
                        </select>

                        <select name="categorie">
                            <option value="">Toutes les catégories</option>
                            <?php
                            $sqlCat = "SELECT * FROM categorie";
                            $stmtCat = $pdo->prepare($sqlCat);
                            $stmtCat->execute();
                            while ($cat = $stmtCat->fetch(PDO::FETCH_ASSOC)) {
                                $selected = (isset($_GET['categorie']) && $_GET['categorie'] == $cat['id_categorie']) ? 'selected' : '';
                                echo '<option value="' . $cat['id_categorie'] . '" ' . $selected . '>' . htmlspecialchars($cat['nom_categorie']) . '</option>';
                            }
                            ?>
                        </select>

                        <input 
                            type="text" 
                            name="pseudo" 
                            placeholder="Rechercher par pseudo..." 
                            value="<?= isset($_GET['pseudo']) ? htmlspecialchars($_GET['pseudo']) : '' ?>"
                        >

                        <button type="submit">Filtrer</button>
                    </form>
                    
                    <?php if (isset($_GET['tri']) || isset($_GET['categorie']) || isset($_GET['pseudo'])): ?>
                        <a href="index.php"><button type="button" class="reset">Réinitialiser</button></a>
                    <?php endif; ?>
                </div>

                <div class="post">
                    <?php
                    $ordre = isset($_GET['tri']) && $_GET['tri'] == 'ASC' ? 'ASC' : 'DESC';

                    $sql = "SELECT 
                                p.id_post,
                                p.id_user,
                                p.titre_post,
                                p.date_post,
                                u.pseudo,
                                GROUP_CONCAT(DISTINCT c.nom_categorie ORDER BY c.nom_categorie SEPARATOR ', ') AS categories
                            FROM post p
                            JOIN utilisateur u ON u.id_user = p.id_user
                            LEFT JOIN post_categorie pc ON pc.id_post = p.id_post
                            LEFT JOIN categorie c ON c.id_categorie = pc.id_categorie
                            WHERE 1=1";

                    $params = [];

                    if (!empty($_GET['categorie'])) {
                        $sql .= " AND p.id_post IN (
                                    SELECT id_post 
                                    FROM post_categorie 
                                    WHERE id_categorie = :c
                                )";
                        $params[':c'] = $_GET['categorie'];
                    }

                    if (!empty($_GET['pseudo'])) {
                        $sql .= " AND u.pseudo LIKE :pseudo";
                        $params[':pseudo'] = '%' . trim($_GET['pseudo']) . '%';
                    }

                    $sql .= " GROUP BY p.id_post ORDER BY p.date_post $ordre";
                    
                    $statement = $pdo->prepare($sql);
                    $statement->execute($params);

                    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) { 
                        $dir = "uploads/users/" . $row['id_user'] . "/posts/" . $row['id_post'];
                        $images = glob($dir . "/*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);
                        $imagePath = count($images) > 0 ? $images[0] : "images/post_image_defaut.png";
                        ?>
                        
                        <a href="post.php?id=<?= $row['id_post'] ?>">
                            <div class="post-card-container">
                                <div class="post-card" style="background-image: url('<?= htmlspecialchars($imagePath) ?>');"></div>
                                <div class="post-footer">
                                    <h3><?= htmlspecialchars($row['titre_post']) ?></h3>
                                    <p>Par <?= htmlspecialchars($row['pseudo']) ?></p>
                                    <p class="date">Publié le <?= htmlspecialchars($row['date_post']) ?>.</p>
                                </div>
                            </div>
                        </a>

                        <?php } 
                    if ($statement->rowCount() === 0) {
                        echo '<p>Aucun post trouvé pour le moment.</p>';
                    } ?>
                </div>
                
            </main>

            <?php require_once("footer.php") ?>
        </body>
</html>