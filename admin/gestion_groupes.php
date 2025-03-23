<?php

require_once('../includes/init.php');

// Redirection si qq tente d'utiliser les url sans en avoir le droit
if (!isAdmin()) {
    header('location:' . URL . 'index.php');
    exit();
}

// Suppression ($_Get)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($_GET['id']) && is_numeric($_GET['id'])) {
    sql("DELETE FROM groupes WHERE id=:id", array(
        'id' => $_GET['id']
    ));
    add_flash('La catégorie a bien été supprimée', 'warning');
    header('location:' . $_SERVER['PHP_SELF']);
    exit();
}


// Traitement des formulaires

if (!empty($_POST)) {
    // Formulaire d'ajout soumis
    if (isset($_POST['add'])) {
        if (!empty(trim($_POST['nv_groupe']))) {
            sql("INSERT INTO groupes VALUES(NULL, :libelle)", array(
                'libelle' => $_POST['nv_groupe']
            ));
            add_flash('La catégorie ' . $_POST['nv_groupe'] . ' a été ajoutée', 'warning');
            header('location:' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            add_flash('La catégorie ne doit pas être vide', 'danger');
        }
    }
    // Formulaire d'update soumis
    if (isset($_POST['update'])) {
        if (!empty(trim($_POST['libelle']))) {
            sql("UPDATE groupes SET libelle=:libelle WHERE id=:id", array(
                'libelle' => $_POST['libelle'],
                'id' => $_POST['id']
            ));
            add_flash('La catégorie a été mise à jour', 'warning');
            header('location:' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            add_flash('La catégorie ne doit pas être vide', 'danger');
        }
    }
}


$groupes = sql("SELECT * FROM groupes ORDER BY libelle");


$title = "Gestion des catégories";
$subtitle = "Admin";
require_once('../includes/header.php');
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <h1>Groupes</h1>
            <hr class="my-3">
            <?php if ($groupes->rowCount() > 0) : ?>
                <?php while ($groupe = $groupes->fetch()) : ?>
                    <form method="post" class="row mb-3">
                        <input type="hidden" name="id" value="<?php echo $groupe['id'] ?>">
                        <div class="col-md-5 mb-3">
                            <input type="text" name="libelle" class="form-control" value="<?php echo $groupe['libelle'] ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <button type="submit" name="update" class="btn btn-outline-success">
                            <i class="fas fa-check"></i>
                            </button>
                            <a href="?action=delete&id=<?php echo $groupe['id'] ?>" class="btn btn-outline-danger confirm">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </form>
                <?php endwhile ?>
            <?php else : ?>
                <div class="mt-4 alert alert-warning">Il n'y a pas encore de groupe</div>
            <?php endif ?>
            <hr class="my-3">
            <form method="post" class="row">
                <div class="col-md-5 mb-3">
                    <input type="text" id="nv_groupe" name="nv_groupe" class="form-control" placeholder="Groupe à ajouter">
                </div>
                <div class="col-md-4 mb-3">
                    <button type="submit" name="add" class="btn btn-secondary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>



<?php
require_once('../includes/footer.php');
