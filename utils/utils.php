<?php
try {
    $bdd = new PDO(
        'mysql:host=localhost;dbname=ludotheque;charset=utf8mb4',
        'root',
        'root'
    );
} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}

