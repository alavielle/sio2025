<?php

require_once('../includes/init.php');
//require_once('../includes/mailer.php');

// Redirection si qq tente d'utiliser les url sans en avoir le droit
if (!isAdmin()) {
    header('location:' . URL . 'index.php');
    exit();
}

// Gestion de l'affichage '
if (isset($_GET['id'])) {
    $selectUsers = sql('SELECT * FROM users WHERE id=:id', array(
        'id' => $_GET['id']
    ));
    $selectUser = $selectUsers->fetch();
} else {
    $selectUsers = sql('SELECT * FROM users WHERE id=:id', array(
        'id' => $_SESSION['user']['id']
    ));
    $selectUser = $selectUsers->fetch();
}

// Gestion de la suppression
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    if ($_GET['id'] == $_SESSION['user']['id']) {
        add_flash("Vous êtes administrateur, vous ne pouvez supprimer votre compte", 'danger');
    } else {
        sql('DELETE FROM users WHERE id=:id', array(
            'id' => $_GET['id']
        ));
        add_flash("le compte a été supprimé", 'info');
        header('location:' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Traitement des formulaires

if (!empty($_POST)) {
    // Formulaire d'ajout soumis
    if (isset($_POST['add'])) {
        if (!empty(trim($_POST['nv_email']))) {
            if (!filter_var($_POST['nv_email'], FILTER_VALIDATE_EMAIL)) {
                add_flash('Adresse email invalide', 'danger');
            }
            $mdp = getUserByEmail($_POST['nv_email']);
            if ($mdp) {
                add_flash('L\'email choisi est indisponible, merci d\'en choisir un autre', 'warning');
            } else {
                if (isset($_POST['nv_droits'])) {
                    $nv_droits = 1;
                } else {
                    $nv_droits = 0;
                }
                sql("INSERT INTO users VALUES(NULL,  :email, :nom, :prenom, :password, :droits, :id_groupe, NULL, NULL)", array(
                    'email' => $_POST['nv_email'],
                    'nom' => $_POST['nv_nom'],
                    'prenom' => $_POST['nv_prenom'],
                    'password' => password_hash(uniqid(), PASSWORD_DEFAULT),
                    'droits' => $nv_droits,
                    'id_groupe' => $_POST['nv_groupe'],
                ));
                add_flash("L'utilisateur a bien été ajoutée", 'warning');
                header('location:' . $_SERVER['PHP_SELF']);
                exit();
            }
        } else {
            add_flash("L'email ne doit pas être vide", 'danger');
        }
    }

    // Formulaire d'update soumis
    if (isset($_POST['update'])) {
        if (!empty($_POST['email'])) {
            if (isset($_POST['droits'])) {
                $droits = 1;
            } else {
                $droits = 0;
            }
            sql("UPDATE users SET email=:email, nom=:nom, prenom=:prenom, droits=:droits,id_groupe=:id_groupe WHERE id=:id", array(
                'email' => $_POST['email'],
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
                'droits' => $droits,
                'id_groupe' => $_POST['id_groupe'],
                'id' => $_POST['id']
            ));
            add_flash('L\'utilisateur a été mis à jour', 'warning');
            header('location:' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            add_flash('L\'email ne peut pas être vide', 'danger');
        }
    }

    
    // Formulaire de mail soumis
    if (isset($_POST['mail'])) {
        if (!empty($_POST['email'])) {
            $user = sql("SELECT * FROM users WHERE email=:email", array(
                'email' => $_POST['email']
            ));
            if ($user->rowCount() > 0) {
                // génération du mail
                $infosuser = $user->fetch();
                $destinataire = $infosuser['email'];
                $nom = $infosuser['prenom']." ".$infosuser['nom'];
                $delai = 10; // en jours
                $expiration = time() + 60 * 60 * 24 * $delai;
                $token = str_repeat(uniqid(), 3);
                sql("UPDATE users SET token=:token, expiration=:expiration WHERE id=:id", array(
                    'token' => $token,
                    'expiration' => $expiration,
                    'id' => $infosuser['id']
                ));
                $sujet = "Accès au questionnaire d'évaluation";
                $lien = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . URL . 'index.php?email=' . $infosuser['email'] . '&token=' . $token;
                $message = '<p>Bonjour ' . $nom . ',<br><br>Voici le lien à suivre pour compléter votre questionnaire d\'évaluation...<br> 
                Ce lien est valide '. $delai. ' jours<br>
                <a href="' . $lien . '">' . $lien . '</a><br><br>
                A bientôt pour tester vos connaissances !</p>';
                //envoiMail($destinataire, $nom, $sujet, $message);
            }
            
            add_flash("Le mail a été envoyé", 'info');
            echo($message);
            //header('location:' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            add_flash('L\'email ne peut pas être envoyé', 'danger');
        }
    }
}


$groupes = sql("select groupes.* from groupes order by groupes.libelle");

$users = sql("select users.* , groupes.libelle
from users
left join groupes on groupes.id = users.id_groupe order by users.prenom");

$title = "Gestion des utilisateurs";
$subtitle = "Admin";
require_once('../includes/header.php');
?>


<div class="row ">
    <h1>Utilisateurs</h1>
    <hr class="my-3">
    <div class="col-md-2 mb-3">
        Email
    </div>
    <div class="col-md-2 mb-3">
        Nom
    </div>
    <div class="col-md-2 mb-3">
        Prénom
    </div>
    <div class="col-md-2 mb-3">
        Droits
    </div>
    <div class="col-md-2 mb-3">
        Groupe
    </div>
    <div class="col-md-2 mb-3">
        Actions
    </div>
</div>
<?php if ($users->rowCount() > 0) :
    while ($user = $users->fetch()) : ?>
        <form method="post" class="row mb-3">
            <input type="hidden" name="id" value="<?php echo $user['id'] ?>">
            <div class="col-md-2 mb-3">
                <input type="text" name="email" class="form-control" value="<?php echo $user['email'] ?>">
            </div>
            <div class="col-md-2 mb-3">
                <input type="text" name="nom" class="form-control" value="<?php echo $user['nom'] ?>">
            </div>
            <div class="col-md-2 mb-3">
                <input type="text" name="prenom" class="form-control" value="<?php echo $user['prenom'] ?>">
            </div>
            <div class="col-md-2 mb-3 mt-1">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="droits" <?php if ($user['droits'] == 1) echo 'checked';
                                                                                    if ($selectUser['id']  == $user['id']) echo ' disabled' ?>>
                    <label class="form-check-label <?php if ($user['droits'] == 0) echo 'text-muted' ?>" data-label="label_droits">Administrateur </label>
                </div>

            </div>
            <div class="col-md-2 mb-3">
                <select class="form-select" name="id_groupe">
                    <option value="0">Choisir</option>
                    <?php if ($user['id_groupe'] > 0) : ?>
                        <option selected value="<?php echo $user['id_groupe'] ?>"><?php echo $user['libelle'] ?></option>
                    <?php endif ?>
                    <?php $groupes = sql("SELECT * FROM groupes WHERE id != " . $user['id_groupe'] . " ORDER BY libelle "); ?>
                    <?php if ($groupes->rowCount() > 0) : ?>
                        <?php while ($groupe = $groupes->fetch()) : ?>
                            <option value="<?php echo $groupe['id'] ?>"><?php echo $groupe['libelle'] ?></option>
                        <?php endwhile ?>
                    <?php endif ?>
                </select>
            </div>
            <div class="col-md-auto mb-3">
                <button type="submit" name="update" class="btn btn-outline-success">
                    <i class="fa fa-check"></i>
                </button>
                <button type="submit" name="mail" class="btn btn-outline-primary">
                    <i class="far fa-envelope"></i>
                </button>
                <a href="?action=delete&id=<?php echo $user['id']  ?>" class="btn btn-outline-danger confirm">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </form>
    <?php endwhile ?>
<?php else : ?>
    <div class="mt-4 alert alert-warning">Il n'y a pas encore d'utilisateur</div>
<?php endif ?>
<hr class="my-3">
<form method="post" class="row">
    <div class="col-md-2 mb-3">
        <input type="text" id="nv_email" name="nv_email" class="form-control" placeholder="email">
    </div>
    <div class="col-md-2 mb-3">
        <input type="text" id="nv_nom" name="nv_nom" class="form-control" placeholder="Nom">
    </div>
    <div class="col-md-2 mb-3">
        <input type="text" id="nv_prenom" name="nv_prenom" class="form-control" placeholder="Prénom">
    </div>
    <div class="col-md-2 mt-1 mb-3">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="nv_droits" id="droits">
            <label class="form-check-label" data-label="label_droits" for="droits">Administrateur</label>
        </div>
    </div>
    <div class="col-md-2 mb-3">
        <select class="form-select" name="nv_groupe">
            <option value="0">Choisir</option>
            <?php $groupes = sql("SELECT * FROM groupes ORDER BY libelle "); ?>
            <?php if ($groupes->rowCount() > 0) : ?>
                <?php while ($groupe = $groupes->fetch()) : ?>
                    <option value="<?php echo $groupe['id'] ?>"><?php echo $groupe['libelle'] ?></option>
                <?php endwhile ?>
            <?php endif ?>
        </select>
    </div>
    <div class="col-md-2 mb-3">
        <button type="submit" name="add" class="btn btn-secondary">Ajouter</button>
    </div>
</form>

<script>
    $('input[name="droits"]').change(function(e) {
        if ($(this).is(":checked")) {
            $(this).next('label').removeClass('text-muted');
        } else {
            console.log($(this).next('span'));
            $(this).next('label').addClass('text-muted');
        }
    });
</script>

<?php
require_once('../includes/footer.php');
