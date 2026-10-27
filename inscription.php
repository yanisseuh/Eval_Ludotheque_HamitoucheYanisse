
<?php
session_start();
include('utils/utils.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération des données du formulaire
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $confirmation = $_POST['confirmation'] ?? '';

    // Vérification des champs obligatoires
    if (
        empty($prenom) ||
        empty($nom) ||
        empty($email) ||
        $mot_de_passe === '' ||
        $confirmation === ''
    ) {
        $message = 'Erreur : tous les champs sont obligatoires.';

    // Vérification du format de l'email
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Erreur : le format de l\'email est invalide.';

    // Vérification de la longueur du mot de passe
    } elseif (strlen($mot_de_passe) < 12) {
        $message = 'Erreur : le mot de passe doit contenir au moins 12 caractères.';

    // Vérification de la confirmation du mot de passe
    } elseif ($mot_de_passe !== $confirmation) {
        $message = 'Erreur : les mots de passe ne sont pas identiques.';

    } else {
        try {
            // Vérifier si l'email existe déjà
            $requete = $bdd->prepare(
            'SELECT id_membre FROM membre WHERE email = :email'
            );

            $requete->execute([
                'email' => $email
            ]);

            if ($requete->fetch()) {
                $message = 'Erreur : cet email est déjà utilisé.';
            } else {
                // Sécuriser le mot de passe
                $mot_de_passe_hash = password_hash(
                    $mot_de_passe,
                    PASSWORD_DEFAULT
                );

                // Enregistrer le membre en base de données
                $requete = $bdd->prepare(
                    'INSERT INTO membre (prenom, nom, email, mot_de_passe)
                     VALUES (:prenom, :nom, :email, :mot_de_passe)'
                );

                $requete->execute([
                    'prenom' => $prenom,
                    'nom' => $nom,
                    'email' => $email,
                    'mot_de_passe' => $mot_de_passe_hash
                ]);

                $message = 'Inscription réussie ! Votre compte a été créé.';
            }

        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
}

include('includes/header.php');
?>

<main>
    <h1>Inscription</h1>

    <?php if ($message !== '') { ?>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php } ?>

    <form action="inscription.php" method="POST">
        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required>

        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>

        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" id="mot_de_passe"
               name="mot_de_passe" minlength="12" required>

        <label for="confirmation">Confirmer le mot de passe :</label>
        <input type="password" id="confirmation"
               name="confirmation" minlength="12" required>

        <button type="submit">S'inscrire</button>
    </form>
</main>

<?php
include('includes/footer.php');
?>
