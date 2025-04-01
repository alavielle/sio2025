<?php

require_once('includes/init.php');

// Si je suis connecté et que je tente de rentrer l'url de la page d'inscription, je suis redirigé vers ma page profil
if (isConnected()) {
    // avant la fonction header, aucun echo, aucune balise html
    header('location:' . URL . 'profil.php');
    exit(); // Stoppe le script php
}
// Traitement du formulaire
if (!empty($_POST)) { // formulaire soumis
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
        add_flash('Merci de saisir votre adresse mail', 'danger');
    } else {
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors++;
            add_flash('Adresse email invalide', 'danger');
        }
        $mdp = getUserByEmail($_POST['email']);
        if ($mdp) {  // la valeur booléenne de qqchose de rempli est vrai
            $errors++;
            add_flash('L\'email choisi est indisponible, merci d\'en choisir un autre', 'warning');
        }
    }

    if (empty($_POST['password'])) {
        $errors++;
        add_flash('Merci de saisir un mot de passe', 'danger');
    } else {
        $pattern = '#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[\S]{8,20}$#';
        if (!preg_match($pattern, $_POST['password'])) {
            $errors++;
            add_flash('Le mot de passe doit être composé de 8 à 20 caractères, comprenant au moins une majuscule, une minuscule et un chiffre', 'danger');
        }
    }

    if (empty($_POST['confirmation'])) {
        $errors++;
        add_flash('Merci de confirmer votre mot de passe', 'danger');
    } else {
        if (!empty($_POST['password']) && ($_POST['confirmation'] !== $_POST['password'])) {
            $errors++;
            add_flash('La confirmation ne concorde pas avec le mot de passe', 'danger');
        }
    }

    // A ce stade, si $error vaut tjs 0, tout est OK 
    if ($errors == 0) {
        $today = date('Y-m-d H:i:s');
        sql("INSERT INTO users VALUES (NULL, :nom, :prenom; :password, :email, :date, 0, NULL, NULL)", array(
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'email' => $_POST['email'], 
            'date' => $today
        ));
        add_flash('Inscription réussie, vous pouvez vous connecter', 'success');
        header('location:' . URL . 'index.php');
        exit();
    }
}


$title = "Inscription";
require_once('includes/header.php');
?>
<div class="row justify-content-center">
    <div class="col-md-8 col-xl-4 border border-dark p-5 rounded">
        <h3>Inscription</h3>
        <hr class="mb-3">
        <form method="post">
            <div class="mb-3">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" class="form-control" value="">
            </div>
            <div class="mb-3">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" class="form-control" value="">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" id="email" name="email" class="form-control" value="">
            </div>
            <div class="form-group position-relative mb-3">
                <label for="password">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" >
                <span toggle="password" class="fa fa-fw fa-eye field-icon toggle-password position-absolute top-50 end-0 pe-4 pt-1" data-index="password"></span>
            </div>
            <div class="form-group position-relative mb-3">
                <label for="password">Confirmation du mot de passe</label>
                <input type="password" class="form-control" id="confirmation" name="confirmation">
                <span toggle="password" class="fa fa-fw fa-eye field-icon toggle-password position-absolute top-50 end-0 pe-4 pt-1" data-index="confirmation"></span>
            </div>
            <div class="mb-3 text-end">
                <button type="submit" class="btn btn-secondary">S'inscrire</button>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col text-center mt-4">
        <p>Déjà inscrit ? Vous pouvez vous connecter en <a href="<?php echo URL ?>index.php">cliquant ici </a></p>
    </div>
</div>

<?php
require_once('includes/footer.php');
