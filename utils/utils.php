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

function formatJoueurs($min, $max) {
    if ($min == 1 && $max == 1) {
        return 'Solo';
    } elseif ($min == $max) {
        return $min . ' joueurs';
    } else {
        return $min . ' à ' . $max . ' joueurs';
    }
}

function categorieDuree($duree) {
    if ($duree < 30) {
        return 'Partie rapide';
    } elseif ($duree <= 90) {
        return 'Partie moyenne';
    } else {
        return 'Partie longue';
    }
}


