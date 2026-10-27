<?php
session_start();
include('utils/utils.php');

// Vérifier que le membre est connecté
if (!isset($_SESSION['id_membre'])) {
    header('Location: connexion.php');
    exit;
}

$erreurs = [];
$message = '';

$titre = '';
$joueurs_min = '';
$joueurs_max = '';
$duree = '';
$id_categorie = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupérer et nettoyer les champs
    $titre = trim($_POST['titre'] ?? '');
    $joueurs_min = $_POST['joueurs_min'] ?? '';
    $joueurs_max = $_POST['joueurs_max'] ?? '';
    $duree = $_POST['duree'] ?? '';
    $id_categorie = $_POST['id_categorie'] ?? '';

    // 1. Vérifier que tous les champs sont remplis
    if (
        $titre === '' ||
        $joueurs_min === '' ||
        $joueurs_max === '' ||
        $duree === '' ||
        $id_categorie === ''
    ) {
        $erreurs[] = 'Tous les champs sont obligatoires.';
    }

    // 2. Vérifier que les nombres sont des entiers
    $minValide = filter_var($joueurs_min, FILTER_VALIDATE_INT);
    $maxValide = filter_var($joueurs_max, FILTER_VALIDATE_INT);
    $dureeValide = filter_var($duree, FILTER_VALIDATE_INT);
    $categorieValide = filter_var($id_categorie, FILTER_VALIDATE_INT);

    if (
        $minValide === false ||
        $maxValide === false ||
        $dureeValide === false ||
        $categorieValide === false
    ) {
        $erreurs[] = 'Les joueurs, la durée et la catégorie doivent être des nombres entiers.';
    }

    // 3. Vérifier le nombre minimum de joueurs
    if ($minValide !== false && $minValide < 1) {
        $erreurs[] = 'Le nombre minimum de joueurs doit être au moins égal à 1.';
    }

    // 4. Vérifier que le maximum est supérieur ou égal au minimum
    if (
        $minValide !== false &&
        $maxValide !== false &&
        $maxValide < $minValide
    ) {
        $erreurs[] = 'Le nombre maximum doit être supérieur ou égal au minimum.';
    }

    // 5. Vérifier que la durée est positive
    if ($dureeValide !== false && $dureeValide <= 0) {
        $erreurs[] = 'La durée doit être supérieure à 0.';
    }

    // Vérifier que la catégorie existe dans la base
    if ($categorieValide !== false && $categorieValide <= 0) {
        $erreurs[] = 'Veuillez sélectionner une catégorie valide.';
    }

    // 6. Vérifier que le titre n'existe pas déjà
    try {
        if ($titre !== '') {
            $requete = $bdd->prepare(
                'SELECT COUNT(*) FROM jeu WHERE titre = :titre'
            );
            $requete->execute(['titre' => $titre]);

            if ($requete->fetchColumn() > 0) {
                $erreurs[] = 'Un jeu portant ce titre existe déjà.';
            }
        }

        // Vérifier l'existence de la catégorie
        if ($categorieValide !== false && $categorieValide > 0) {
            $requete = $bdd->prepare(
                'SELECT COUNT(*) FROM categorie WHERE id_categorie = :id'
            );
            $requete->execute(['id' => $categorieValide]);

            if ($requete->fetchColumn() == 0) {
                $erreurs[] = 'La catégorie sélectionnée n’existe pas.';
            }
        }

        // Enregistrer le jeu seulement s'il n'y a aucune erreur
        if (empty($erreurs)) {
            $requete = $bdd->prepare("
                INSERT INTO jeu
                    (titre, joueurs_min, joueurs_max, duree, id_categorie, id_membre)
                VALUES
                    (:titre, :joueurs_min, :joueurs_max, :duree, :id_categorie, :id_membre)
            ");

            $requete->execute([
                'titre' => $titre,
                'joueurs_min' => $minValide,
                'joueurs_max' => $maxValide,
                'duree' => $dureeValide,
                'id_categorie' => $categorieValide,
                'id_membre' => $_SESSION['id_membre']
            ]);

            $message = 'Le jeu a été ajouté avec succès.';

            // Vider les champs après l'ajout
            $titre = '';
            $joueurs_min = '';
            $joueurs_max = '';
            $duree = '';
            $id_categorie = '';
        }

    } catch (PDOException $e) {
        die('Erreur : ' . $e->getMessage());
    }
}

// Récupérer les catégories
try {
    $requete = $bdd->prepare("
        SELECT id_categorie, nom_categorie
        FROM categorie
        ORDER BY nom_categorie ASC
    ");
    $requete->execute();
    $categories = $requete->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}

include('includes/header.php');
?>

<main>
    <h1>Ajouter un jeu</h1>

    <?php if (!empty($erreurs)) { ?>
        <div>
            <p>Le jeu n'a pas été enregistré :</p>
            <ul>
                <?php foreach ($erreurs as $erreur) { ?>
                    <li>
                        <?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <?php if ($message !== '') { ?>
        <p>
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php } ?>

    <form action="ajout-jeu.php" method="POST">

        <div>
            <label for="titre">Titre du jeu :</label>
            <input
                type="text"
                id="titre"
                name="titre"
                maxlength="100"
                value="<?= htmlspecialchars($titre, ENT_QUOTES, 'UTF-8') ?>"
                required
            >
        </div>

        <div>
            <label for="joueurs_min">Nombre minimum de joueurs :</label>
            <input
                type="number"
                id="joueurs_min"
                name="joueurs_min"
                min="1"
                step="1"
                value="<?= htmlspecialchars((string) $joueurs_min, ENT_QUOTES, 'UTF-8') ?>"
                required
            >
        </div>

        <div>
            <label for="joueurs_max">Nombre maximum de joueurs :</label>
            <input
                type="number"
                id="joueurs_max"
                name="joueurs_max"
                min="1"
                step="1"
                value="<?= htmlspecialchars((string) $joueurs_max, ENT_QUOTES, 'UTF-8') ?>"
                required
            >
        </div>

        <div>
            <label for="duree">Durée d'une partie (en minutes) :</label>
            <input
                type="number"
                id="duree"
                name="duree"
                min="1"
                step="1"
                value="<?= htmlspecialchars((string) $duree, ENT_QUOTES, 'UTF-8') ?>"
                required
            >
        </div>

        <div>
            <label for="id_categorie">Catégorie :</label>
            <select id="id_categorie" name="id_categorie" required>
                <option value="">-- Choisir une catégorie --</option>

                <?php foreach ($categories as $categorie) { ?>
                    <option
                        value="<?= (int) $categorie['id_categorie'] ?>"
                        <?= (string) $id_categorie === (string) $categorie['id_categorie'] ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($categorie['nom_categorie'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <button type="submit">Ajouter le jeu</button>
    </form>
</main>

<?php include('includes/footer.php'); ?>
