<header>
    <div id="header-top">
        <a href="index.php">
            <div id="header-title">
                <h1>MIAOU BLOG</h1>
                <img src="images/miaou_paw.png" alt="Image de patte de chat">
            </div>
        </a>
        <?php
            if (isset($_SESSION["authentifie"])) {
                if ($_SESSION["authentifie"] === true) {
                    echo "<div><a href='compte.php'>" . $_SESSION["pseudo"] . "</a><a href='deconnexion_compte.php'><span class='material-symbols-outlined'>logout</span></a></div>";
                }
            } else {
                echo "<div><a href='connexion_compte.php'>S'inscrire / Se connecter</a></div>";
            }
        ?>
    </div>
    <nav>
        <ul>
            <li><a href="index.php">Explorer</a></li>
            <li><a href="creation_post.php">Créer un post</a></li>
            <li><a href="contact.php">Contact</a></li>
            <?php
            if (isset($_SESSION["authentifie"]) && $_SESSION["authentifie"] && $_SESSION["email"] == "admin@localhost.fr") {
                ?>
                <li><a href="admin_categories.php">Gestion des catégories</a></li>
                <?php
            }
            ?>
        </ul>
    </nav>
</header>