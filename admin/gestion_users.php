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
    if (isset($_POST['envoi_mail'])) {
        if (!empty($_POST['destinataire'])) {
            $user = sql("SELECT * FROM users WHERE email=:email", array(
                'email' => $_POST['destinataire']
            ));
            if ($user->rowCount() > 0) {
                // génération du mail
                $infosuser = $user->fetch();
                $destinataire = $infosuser['email'];
                $prenom = $infosuser['prenom'];
                if (!empty($_POST['id_mail']) && $_POST['id_mail'] > 0 && !empty($_POST['contenu'])) {
                    $id_mail = $_POST['id_mail']; //mail d'envoi du lien
                    $contenu = $_POST['contenu'];
                    $sujet = $_POST['title'];
                    $expiration = time() + 60 * 60 * 24 * 10;
                    $token = str_repeat(uniqid(), 3);
                    sql("UPDATE users SET token=:token, expiration=:expiration WHERE id=:id", array(
                        'token' => $token,
                        'expiration' => $expiration,
                        'id' => $infosuser['id']
                    ));
                    if ($id_mail > 0) {
                        sql("UPDATE mails SET title=:title, contenu=:contenu WHERE id=:id_mail", array(
                            'title' => $sujet,
                            'contenu' => $contenu,
                            'id_mail' => $id_mail
                        ));
                    } else {
                        sql("INSERT INTO mails (title, contenu) VALUES (:title, :contenu)", array(
                            'title' => $sujet,
                            'contenu' => $contenu
                        ));
                    }

                    $lien = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . URL . 'index.php?email=' . $infosuser['email'] . '&token=' . $token;
                    $contenu = str_replace('%prenom%', $prenom, $contenu);
                    $contenu = str_replace('%lien%', $lien, $contenu);
                    //envoiMail($destinataire, $nom, $sujet, $contenu);
                    sql("INSERT INTO histo_mails (id_user ,title, contenu,  date) VALUES (:id_user, :title, :contenu, :date)", array(
                        'title' => $sujet,
                        'contenu' => $contenu,
                        'id_user' => $infosuser['id'],
                        'date' => date("Y-m-d H:i:s")
                    ));
                }
            }

            add_flash("Le mail a été envoyé", 'info');
            echo ($contenu);
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
                <button type="button" data-bs-toggle="modal" data-bs-target="#modal_mail" name="prepa_mail" class="btn btn-outline-primary" data-index="<?php echo $user['email'] ?>">
                    <i class="far fa-envelope"></i>
                </button>
                <a href="?action=delete&id=<?php echo $user['id']  ?>" class="btn btn-outline-danger confirm">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        <?php endwhile ?>
    <?php else : ?>
        <div class="mt-4 alert alert-warning">Il n'y a pas encore d'utilisateur</div>
    <?php endif ?>
    <hr class="my-3">
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
    <div class="modal fade" tabindex="-1" id="modal_mail">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sélection du mail à envoyer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>
                        <label for="id_mail" class="form-label">Email prédéfini </label>
                        <select class="form-select" name="id_mail" id="id_mail">
                            <option value="0">Choisir</option>
                            <?php $mails = sql("SELECT * FROM mails ORDER BY id "); ?>
                            <?php if ($mails->rowCount() > 0) : ?>
                                <?php while ($mail = $mails->fetch()) : ?>
                                    <option value="<?php echo $mail['id'] ?>"><?php echo $mail['title'] ?></option>
                                <?php endwhile ?>
                            <?php endif ?>
                        </select>
                    <p><span class="fst-italic">Ou email à créer</span></p>
                    <label for="title" class="form-label">Sujet</label>
                    <input type="text" class="form-control mb-3" name="title" id="title">
                    <label for="contenu" class="form-label">Contenu</label>
                    <textarea class="form-control" name="contenu" id="contenu" rows="10"></textarea>
                    </p>
                    <p>
                        <label for="destinataire">Envoyer à : </label>
                        <input type="text" class="form-control mb-3" name="destinataire" id="destinataire" readonly>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="envoi_mail" class="btn btn-secondary">Envoyer</button>
                </div>
            </div>
        </div>
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
            $('button[name=prepa_mail]').click(function() {
                $('#destinataire').val($(this).attr('data-index'));
            });
        </script>

        <!-- script page -->
        <script src="<?php echo URL ?>js/mail.js"></script>

        <?php
        require_once('../includes/footer.php');
