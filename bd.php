<?php
    $host = "localhost";
    $dbname = "miaou_blog";
    $user = "root";
    $pass = "";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    } catch (Exception $e) {
        die("Erreur générale : " . $e->getMessage());
    }
?>