<?php

require_once('includes/init.php');

if (!isConnected()) {
    header('location:' . URL . 'index.php');
    exit();
}

// Gestion de la suppression
if (isset($_GET['action']) && $_GET['action'] == 'delete') {

    sql('DELETE FROM users WHERE id=:id', array('id' => $_SESSION['user']['id']));
    add_flash("Votre compte a été supprimé.", 'info');
    header('location:' . URL . 'index.php?action=logout');
    exit();
}

if (!empty($_POST)) {

    // Formulaire données personnelles
    if (isset($_POST['update_perso'])) {

        $errors = 0;

        if (empty($_POST['nom'])) {
            $errors++;
            add_flash('Le nom ne peut pas etre vide', 'danger');
        }

        if (empty($_POST['prenom'])) {
            $errors++;
            add_flash('Le prénom ne peut pas etre vide', 'danger');
        }

        if (empty($_POST['email'])) {
            $errors++;
            add_flash("L'email ne peut pas etre vide", 'danger');
        } else {
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors++;
                add_flash("L'adresse mail est invalide", 'danger');
            }
            $user = getUserByEmail($_POST['email']);
            if ($user && $user['id'] != $_SESSION['user']['id']) {
                $errors++;
                add_flash('Cet email est indisponible', 'danger');
            }
        }

        if ($errors == 0) {
            sql("UPDATE users SET nom=:nom, prenom=:prenom, email=:email WHERE id=:id", array(
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
                'email' => $_POST['email'],
                'id' => $_SESSION['user']['id']
            ));
            $_SESSION['user']['nom']  = $_POST['nom'];
            $_SESSION['user']['prenom']  = $_POST['prenom'];
            $_SESSION['user']['email']  = $_POST['email'];
            add_flash('Vos informations ont été mises à jour', 'success');
        }
    }

    // Formulaire mot de passe
    if (isset($_POST['update_password'])) {

        $errors = 0;


        if (empty($_POST['password'])) {
            $errors++;
            add_flash('Merci de saisir votre mot de passe actuel', 'danger');
        } else {
            if (!password_verify($_POST['password'], $_SESSION['user']['password'])) {
                $errors++;
                add_flash('Mot de passe incorrect', 'danger');
            }
        }

        if (empty($_POST['newpassword'])) {
            $errors++;
            add_flash('Merci de saisir un nouveau mot de passe', 'danger');
        } else {
            $pattern = '#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[\S]{8,20}$#';
            if (!preg_match($pattern, $_POST['newpassword'])) {
                $errors++;
                add_flash('Le nouveau mot de passe doit être composé de 8 à 20 caractères comprenant au moins une minuscule, une majuscule et un chiffre', 'danger');
            }
        }


        if (empty($_POST['confirmation'])) {
            $errors++;
            add_flash('Merci de confirmer votre mot de passe', 'danger');
        } else {
            if (!empty($_POST['newpassword']) && ($_POST['confirmation'] !== $_POST['newpassword'])) {
                $errors++;
                add_flash('La confirmation ne concorde pas avec le mot de passe', 'danger');
            }
        }

        if ($errors == 0) {

            $password_crypte = password_hash($_POST['newpassword'], PASSWORD_DEFAULT);

            sql("UPDATE users SET password=:password  WHERE id=:id", array(
                'password' => $password_crypte,
                'id' => $_SESSION['user']['id']
            ));
            $_SESSION['user']['password']  =  $password_crypte;
            add_flash('Votre mot de passe a été mis à jour', 'success');
        }
    }
}


$title = 'Profil';
require_once('includes/header.php');
?>
<div class="row">
    <div class="col-md-6 mb-3">
        <h2>Modifier mes infos personnelles</h2>
        <hr class="mb-3">
        <form method="post">

            <div class="mb-3">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" class="form-control" value="<?php echo $_SESSION['user']['nom'] ?>">
            </div>

            <div class="mb-3">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" class="form-control" value="<?php echo $_SESSION['user']['prenom'] ?>">
            </div>

            <div class="mb-3">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" class="form-control" value="<?php echo $_SESSION['user']['email'] ?>">
            </div>
            <div class="col-auto text-end">
                <button class="btn btn-secondary " name="update_perso">Mettre à jour</button>
            </div>
        </form>

    </div>
    <div class="col-md-6 mb-3">
        <h2>Modifier mon mot de passe</h2>
        <hr class="mb-3">

        <form method="post">

            <div class="mb-3">
                <label for="password">Mot de passe actuel</label>
                <input type="password" id="password" name="password" class="form-control">
            </div>
            <div class="mb-3">
                <label for="newpassword">Nouveau mot de passe</label>
                <input type="password" id="newpassword" name="newpassword" class="form-control">
            </div>
            <div class="mb-3">
                <label for="confirmation">Confirmation</label>
                <input type="password" id="confirmation" name="confirmation" class="form-control">
            </div>
            <div class="col-auto text-end">
                <button class="btn btn-secondary" name="update_password">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col">
        <hr class="mb-3">
        <a href="?action=delete" class="btn btn-danger confirm" data-message="Etes-vous sûr(e) de vouloir  supprimer votre compte ? Cette action est irreversible et supprimera toutes vos données personnelles."><i class="fas fa-exclamation-triangle"></i> Supprimer mon compte</a>
    </div>
</div>

<?php


require_once('includes/footer.php');
