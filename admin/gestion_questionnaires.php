<?php

require_once('../includes/init.php');

// Redirection si qq tente d'utiliser les url sans en avoir le droit
if (!isAdmin()) {
    header('location:' . URL . 'index.php');
    exit();
}

// Suppression ($_Get)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($_GET['id']) && is_numeric($_GET['id'])) {
    sql("DELETE FROM questionnaires WHERE id=:id", array(
        'id' => $_GET['id']
    ));
    add_flash('Le questionnaire a bien été supprimé', 'warning');
    header('location:' . $_SERVER['PHP_SELF']);
    exit();
}


// Traitement des formulaires

if (!empty($_POST)) {
    // Formulaire d'ajout soumis
    if (isset($_POST['add'])) {
        if (!empty(trim($_POST['nv_questionnaire']))) {
            sql("INSERT INTO questionnaires VALUES(NULL, :libelle, 0, '', :uuid)", array(
                'libelle' => $_POST['nv_questionnaire'],
                'uuid' => uniqid().uniqid()
            ));
            add_flash('Le questionnaire ' . $_POST['nv_questionnaire'] . ' a été ajouté', 'warning');
            header('location:' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            add_flash('Le questionnaire ne doit pas être vide', 'danger');
        }
    }
    // Formulaire d'update soumis
    if (isset($_POST['update'])) {
        if (!empty(trim($_POST['questionnaire']))) {
            sql("UPDATE questionnaires SET libelle=:nvlibelle, id_categorie=:id_categorie WHERE id=:id_questionnaire", array(
                'id_categorie' => $_POST['categorie'],
                'nvlibelle' => $_POST['questionnaire'],
                'id_questionnaire' => $_POST['id_questionnaire']
            ));
            add_flash('Le questionnaire a été mis à jour', 'warning');
            header('location:' . $_SERVER['PHP_SELF']);
            exit();
        } else {
            add_flash('Le questionnaire ne doit pas être vide', 'danger');
        }
    }
}


$questionnaires = sql("SELECT questionnaires.id, questionnaires.libelle, questionnaires.id_categorie, categories.libelle as categorie FROM questionnaires 
LEFT join categories ON questionnaires.id_categorie = categories.id
ORDER BY libelle");
$categories = sql("SELECT * FROM categories ORDER BY libelle");


$title = "Gestion des questionnaires";
$subtitle = "Admin";
require_once('../includes/header.php');
?>
<div class="container">
    <div class="row justify-content-center">
        <h3>Questionnaires</h3>
        <hr class="mb-3">
        <?php if ($questionnaires->rowCount() > 0) : ?>
            <?php while ($questionnaire = $questionnaires->fetch()) : ?>
                <form method="post" class="row mt-2">
                    <input type="hidden" name="id_questionnaire" value="<?php echo $questionnaire['id'] ?>">
                    <div class="col-md-5 mb-3">
                        <input type="text" name="questionnaire" class="form-control" value="<?php echo $questionnaire['libelle'] ?>">
                    </div>
                    <div class="col-md-5 mb-3">
                        <select class="form-select" name="categorie">
                            <option value="0">Choisir</option>
                            <?php if ($questionnaire['id_categorie'] > 0) : ?>
                                <option selected value="<?php echo $questionnaire['id_categorie'] ?>"><?php echo $questionnaire['categorie'] ?></option>
                            <?php endif ?>
                            <?php $categories = sql("SELECT * FROM categories WHERE id != " . $questionnaire['id_categorie'] . " ORDER BY libelle " ); ?>
                            <?php if ($categories->rowCount() > 0) : ?>
                                <?php while ($categorie = $categories->fetch()) : ?>
                                    <option value="<?php echo $categorie['id'] ?>"><?php echo $categorie['libelle'] ?></option>
                                <?php endwhile ?>
                            <?php endif ?>
                        </select>
                    </div>
                    <div class="col-lg-2 mb-3">
                        <button type="submit" name="update" class="btn btn-outline-success">
                            <i class="fas fa-check"></i>
                        </button>
                        <a href="<?php echo URL ?>admin/gestion_questions.php?questionnaire=<?php echo $questionnaire['id'] ?>" class="btn btn-outline-primary">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="?action=delete&id=<?php echo $questionnaire['id'] ?>" class="btn btn-outline-danger confirm">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </form>
            <?php endwhile ?>
        <?php else : ?>
            <div class="mt-4 alert alert-warning">Il n'y a pas encore de questionnaire</div>
        <?php endif ?>
        <hr class="my-3">
        <form method="post" class="row">
            <div class="col-md-5 mb-3">
                <input type="text" id="nv_questionnaire" name="nv_questionnaire" class="form-control" placeholder="Questionnaire à ajouter">
            </div>
            <div class="col-md-4 mb-3">
                <button type="submit" name="add" class="btn btn-secondary">Ajouter</button>
            </div>
        </form>
    </div>
</div>



<?php
require_once('../includes/footer.php');
