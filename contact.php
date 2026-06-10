<?php 
    session_start();
    require_once("bd.php");
?>

<!DOCTYPE html>
<html lang="fr">
    <?php
        require_once("head.php");
        require_once("utils.php");

        $msgErreurNom = null;
        $msgErreurEmail = null;
        $msgErreurMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = htmlspecialchars($_POST['nom']);
            $email = htmlspecialchars($_POST['email']);
            $message_text = htmlspecialchars($_POST['message']);
                    
            if (empty($nom)) {
                $msgErreurNom = "Le nom est obligatoire";
            }
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $msgErreurEmail = "L'adresse email n'est pas valide";
            }
            
            if (empty($message_text)) {
                $msgErreurMessage = "Le message est obligatoire";
            }
            
            if (empty($msgErreurNom) && empty($msgErreurEmail) && empty($msgErreurMessage)) {
                try {
                    $sql = "INSERT INTO message (id_user, nom, email, message) 
                            VALUES (:id_user, :nom, :email, :message)";
                    
                    $stmt = $pdo->prepare($sql);
                    
                    $id_user = null;
                    if (isset($_SESSION['user_id'])) {
                        $id_user = $_SESSION['user_id'];
                    }
                    
                    // Exécution
                    $stmt->execute([
                        ':id_user' => $id_user,
                        ':nom' => $nom,
                        ':email' => $email,
                        ':message' => $message_text
                    ]);
                    
                    $message_succes = "Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.";
                    
                } catch (PDOException $e) {
                    $erreurs[] = "Une erreur est survenue lors de l'enregistrement du message : " . $e->getMessage();
                }
            }
        }
    ?>
    <body id="contact">
        <?php require_once("header.php") ?>
        <main>
            <h2>Contact</h2>
            <p>Merci de remplir le formulaire ci-dessous et nous répondrons dans les plus brefs délais.</p>

            <?php if (isset($message_succes)): ?>
                <div class="succes" style="background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 8px; margin: 15px 0; border: 1px solid #4caf50;">
                    <?php echo $message_succes; ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="<?php echo isset($nom) ? $nom : ''; ?>">
                <?php echoMessageErreur($msgErreurNom); ?>
                
                <label for="email">Adresse mail</label>
                <input type="email" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>">
                <?php echoMessageErreur($msgErreurEmail); ?>
                
                <textarea id="message" name="message" placeholder="Écrivez ici votre demande..." ><?php echo isset($message_text) ? $message_text : ''; ?></textarea>
                <?php echoMessageErreur($msgErreurMessage); ?>

                <button type="submit">Envoyer</button>
            </form>
        </main>
        <?php require_once("footer.php") ?>
    </body>
</html>