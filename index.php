<?php
session_start();
include('utils/utils.php');

// Récupérer tous les jeux par ordre alphabétique
try {
    $requete = $bdd->prepare("
        SELECT
            jeu.titre,
            jeu.joueurs_min,
            jeu.joueurs_max,
            jeu.duree,
            categorie.nom_categorie,
            membre.prenom
        FROM jeu
        INNER JOIN categorie
            ON jeu.id_categorie = categorie.id_categorie
        INNER JOIN membre
            ON jeu.id_membre = membre.id_membre
        ORDER BY jeu.titre ASC
    ");

    $requete->execute();
    $jeux = $requete->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}

include('includes/header.php');
?>

<main>
    <h1>Bienvenue dans la ludothèque !</h1>

    <p>
        Retrouve les jeux de société disponibles et découvre de nouveaux jeux.
    </p>

    <?php if (isset($_SESSION['id_membre'])) { ?>
        <p>
            Bonjour <?= htmlspecialchars($_SESSION['prenom'], ENT_QUOTES, 'UTF-8') ?> !
        </p>
        <p>Tu peux ajouter un jeu à la ludothèque grâce au menu.</p>
    <?php } else { ?>
        <p>Connecte-toi pour pouvoir ajouter un jeu.</p>
    <?php } ?>

    <h2>Liste des jeux</h2>

    <?php if (count($jeux) > 0) { ?>

        <?php foreach ($jeux as $jeu) { ?>
            <article>
                <h3>
                    <?= htmlspecialchars($jeu['titre'], ENT_QUOTES, 'UTF-8') ?>
                </h3>

                <p>
                    Catégorie :
                    <?= htmlspecialchars($jeu['nom_categorie'], ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p>
                    Nombre de joueurs :
                    <?= htmlspecialchars(
                        formatJoueurs($jeu['joueurs_min'], $jeu['joueurs_max']),
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

                <p>
                    Durée :
                    <?= (int) $jeu['duree'] ?> minutes
                    (<?= htmlspecialchars(categorieDuree($jeu['duree']), ENT_QUOTES, 'UTF-8') ?>)
                </p>

                <p>
                    Ajouté par :
                    <?= htmlspecialchars($jeu['prenom'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            </article>

            <hr>
        <?php } ?>

    <?php } else { ?>
        <p>Aucun jeu n'est disponible pour le moment.</p>
    <?php } ?>
</main> 

<?php include('includes/footer.php'); ?>
