<?php
session_start();
include('utils/utils.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if ($email === '' || $mot_de_passe === '') {
        $message = 'Email et/ou mot de passe incorrect';
    } else {
        try {
            $requete = $bdd->prepare(
                'SELECT id_membre, prenom, nom, email, mot_de_passe
                 FROM membre
                 WHERE email = :email'
            );

            $requete->execute([
                'email' => $email
            ]);

            $membre = $requete->fetch(PDO::FETCH_ASSOC);

            if (
                $membre &&
                password_verify(
                    $mot_de_passe,
                    $membre['mot_de_passe']
                )
            ) {
                session_regenerate_id(true);

                $_SESSION['id_membre'] = $membre['id_membre'];
                $_SESSION['prenom'] = $membre['prenom'];
                $_SESSION['nom'] = $membre['nom'];
                $_SESSION['email'] = $membre['email'];

                header('Location: index.php');
                exit;
            } else {
                $message = 'Email et/ou mot de passe incorrect';
            }

        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
}

include('includes/header.php');
?>

<main>
    <h1>Connexion</h1>

    <?php if ($message !== '') { ?>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php } ?>

    <form action="connexion.php" method="POST">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>

        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" id="mot_de_passe"
               name="mot_de_passe" required>

        <button type="submit">Se connecter</button>
    </form>
</main>

<?php include('includes/footer.php'); ?>
