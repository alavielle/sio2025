<?php

require_once('includes/init.php');

// Gestion de la deconnexion
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    unset($_SESSION['user']);
    add_flash('Vous vous etes déconnecté', 'success');
    header('location:' . URL . 'index.php');
    exit();
}

if (isset($_GET['email']) && isset($_GET['token'])) {
    $user = sql("SELECT * FROM users WHERE email=:email AND token=:token AND expiration >=:expiration", array(
        'email' => $_GET['email'],
        'token' => $_GET['token'],
        'expiration' => time()
    ));
    if ($user->rowCount() > 0) {
        $infosuser = $user->fetch();
        $_SESSION['user'] = $infosuser;
        header('location:' . URL . 'accueil.php');
        exit();
    } else {
        add_flash('Le lien a expiré', 'danger');
        header('location:' . URL . 'index.php');
        exit();
    }
}


if (isConnected()) {
    header('location:' . URL . 'accueil.php');
    exit();
}

// Traitement de la connexion
if (!empty($_POST)) {
    $errors = 0;
    if (empty($_POST['email'])) {
        $errors++;
        add_flash('Merci de saisir votre email', 'danger');
    }
    if (empty($_POST['password'])) {
        $errors++;
        add_flash('Merci de saisir votre mot de passe', 'danger');
    }
    if ($errors == 0) {
        $user = getuserByEmail($_POST['email']);
        if ($user) {
            if (password_verify($_POST['password'], $user['password'])) {
                $_SESSION['user'] = $user;
                add_flash('Connexion réussie', 'success');
                header('location:' . URL . 'accueil.php');
                exit();
            } else {
                add_flash('Erreur sur les identifiants', 'danger');
            }
        } else {
            add_flash('Erreur sur les identifiants', 'danger');
        }
    }
}

$title = "Connexion";
require_once('includes/header.php');
?>
<div class="row justify-content-center">
    <div class="col-md-8 col-xl-4 border border-dark p-5 rounded">
        <h1>Connexion</h1>
        <hr class="mb-3">
        <form method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" id="email" name="email" class="form-control" value="<?php echo $_POST['email'] ?? '' ?>">
            </div>
            <div class="form-group position-relative mb-3">
                <label for="password">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password">
                <span toggle="password" class="fa fa-fw fa-eye field-icon toggle-password position-absolute top-50 end-0 pe-4 pt-1" data-index="password"></span>
            </div>
            <div>
                <a href="<?php echo URL ?>reinitmdp.php">Oubli du mot de passe</a>
            </div>
            <div class="mb-3 text-end">
                <button type="submit" class="btn btn-secondary">Se connecter</button>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <div class="col text-center mt-4">
        <p>Pas encore de compte ? Vous pouvez en créer un en <a href="<?php echo URL ?>inscription.php">cliquant ici </a></p>
    </div>
</div>




<?php
require_once('includes/footer.php');
