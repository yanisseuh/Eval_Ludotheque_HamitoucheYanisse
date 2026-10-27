<?php
session_start();

// Vider les variables de session
$_SESSION = [];

// Détruire la session
session_destroy();

// Rediriger vers la page d'accueil
header('Location: index.php');
exit;
