<?php

require_once('includes/init.php');

// Redirection si qq tente d'utiliser les url sans en avoir le droit
if (!isAdmin()) {
    header('location:' . URL . 'index.php');
    exit();
}

$categories = sql("SELECT * FROM categories ORDER BY libelle");

$requete = "SELECT q.*, c.libelle as categorie
FROM questionnaires q
LEFT JOIN categories c ON c.id = q.id_categorie";
$params = array();


// Tenir compte d'un éventuel filtre sur la catégorie
if (isset($_GET['categorie']) && is_numeric($_GET['categorie'])) {
    $requete .= ' WHERE q.id_categorie = :id';
    $params['id'] = $_GET['categorie'];
}

$requete .= " GROUP BY q.id ORDER BY q.libelle ASC";
$questionnaires = sql($requete, $params);

$title = "Accueil";
require_once('includes/header.php');

?>
<div class="row" id="questionnaires">
        <h1>Questionnaires proposés</h1>
    <div class="col-md-9 order-1 order-md-0">
        <div class="col-md-9 order-1 order-md-0">
            <?php if ($questionnaires->rowCount() > 0) : ?>
                <?php while ($questionnnaire = $questionnaires->fetch()) : ?>
                    <hr>
                    <h5><a href="questionnaire.php?questionnaire=<?php echo $questionnnaire['id']."&".  uniqid().  uniqid()?>" class="align-self-end mt-3 text-decoration-none" id="lien_questionnaire"><?php echo $questionnnaire['libelle'] ?></a></h5>
                    <input type="text" name="id_questionnaire<?php echo $questionnnaire['id'] ?>" id="id_questionnaire<?php echo $questionnnaire['id'] ?>" value="<?php echo $questionnnaire['id'] ?>" hidden>
                    <p class="ms-5 fst-italic"><?php echo $questionnnaire['categorie'] ?></p>
                <?php endwhile ?>

            <?php else : ?>
                <div class="alert alert-info">Pas de questionnaire dans cette catégorie</div>
            <?php endif ?>
        </div>
    </div>
    <?php if ($categories->rowCount() > 0) : ?>
        <aside class="col-md-3 order-0 order-md-1 border-start border-dark">
            <div class="list-group mb-2">
                <a href="<?php echo URL ?>" class="list-group-item list-group-item-action <?php if (!isset($_GET['categorie'])) echo 'active' ?>">Tous</a>
                <?php while ($categorie = $categories->fetch()) : ?>
                    <a href="?categorie=<?php echo $categorie['id'] ?>" class="list-group-item liste-group-item-action <?php if (isset($_GET['categorie']) && $_GET['categorie'] == $categorie['id']) echo 'active' ?>"><?php echo $categorie['libelle'] ?></a>
                <?php endwhile ?>
            </div>
        </aside>
    <?php endif ?>
</div>

<?php
require_once('includes/footer.php');
