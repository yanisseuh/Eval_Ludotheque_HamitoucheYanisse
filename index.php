<?php
session_start();
include('utils/utils.php');

include('includes/header.php');
?>

<main>
    <h1>Bienvenue dans la ludothèque !</h1>

    <p>Retrouve les jeux de société disponibles et découvre de nouveaux jeux.</p>

    <?php if (isset($_SESSION['id_membre'])) { ?>
        <p>
            Bonjour
            <?= htmlspecialchars($_SESSION['prenom'], ENT_QUOTES, 'UTF-8') ?> !
        </p>
        <p>Tu peux ajouter un jeu à la ludothèque grâce au menu.</p>
    <?php } else { ?>
        <p>Connecte-toi pour pouvoir ajouter un jeu.</p>
    <?php } ?>
</main>

<?php
include('includes/footer.php');
?>
