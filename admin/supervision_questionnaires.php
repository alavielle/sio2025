<?php

require_once('../includes/init.php');

// Redirection si qq tente d'utiliser les url sans en avoir le droit
if (!isAdmin()) {
    header('location:' . URL . 'index.php');
    exit();
}


$categories = sql("SELECT * FROM categories ORDER BY libelle");

$requete = "SELECT q.id as id_quest, q.libelle, c.libelle as categorie
FROM questionnaires q
LEFT JOIN categories c ON c.id = q.id_categorie";
$params = array();

$requeteliste = "SELECT DISTINCT users.prenom, users.nom, users.id, questionnaires.libelle
     FROM users
     inner join reponses_utilisateur on reponses_utilisateur.id_utilisateur = users.id
     inner join questions on questions.id = reponses_utilisateur.id_question
     inner join questionnaires on questionnaires.id = questions.id_questionnaire";

$requetescore = "SELECT DISTINCT propositions.libelle as proposition, propositions.commentaire
    FROM propositions
    WHERE propositions.reponse = 1";

// Tenir compte d'un éventuel filtre sur la catégorie
if (isset($_GET['categorie']) && is_numeric($_GET['categorie'])) {
    $requete .= ' WHERE q.id_categorie = :id';
    $params['id'] = $_GET['categorie'];
}

$requete .= " GROUP BY q.id ORDER BY q.libelle ASC";
$questionnaires = sql($requete, $params);


$title = "Scores par Questionnaires";
require_once('../includes/header.php');
?>


<div class="row" id="questionnaires">
    <h1>Réponses par questionnaires</h1>
    <div class="col-md-9 order-1 order-md-0">
        <?php if ($questionnaires->rowCount() > 0) : ?>
            <?php while ($questionnnaire = $questionnaires->fetch()) : ?>
                <div class="accordion" id="accordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <?php echo $questionnnaire['libelle']; ?>
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <div class="row">
                                    <?php ?>
                                    <?php $liste = $requeteliste . " WHERE questionnaires.id = "  . $questionnnaire['id_quest'] . " group by questionnaires.id, users.id"; ?>
                                    <?php $utilisateurs = sql($liste); ?>
                                    <?php if ($utilisateurs->rowCount() > 0) : ?>
                                        <?php while ($utilisateur = $utilisateurs->fetch()) : ?>
                                            <div class="col-md-3 col-sm-2">
                                                <?php echo $utilisateur['prenom'] ?>
                                            </div>
                                            <div class="col-md-9 col-sm-10">
                                                <?php
                                                $sqlscores = "SELECT questions.id_questionnaire, reponses_utilisateur.date, SUM(propositions.reponse) as score
                FROM reponses_utilisateur 
                INNER JOIN propositions ON reponses_utilisateur.id_reponse = propositions.id LEFT JOIN questions ON propositions.id_question = questions.ID
                WHERE reponses_utilisateur.id_utilisateur = " . $utilisateur['id'] .
                                                    " AND questions.id_questionnaire = " . $questionnnaire['id_quest'] .
                                                    " GROUP BY reponses_utilisateur.date, questions.id_questionnaire ORDER BY reponses_utilisateur.date DESC";

                                                $scores = sql($sqlscores);
                                                $sqlTotal = "SELECT COUNT(id) as total FROM questions WHERE questions.id_questionnaire = " . $questionnnaire['id_quest'] . "";
                                                $total = sql($sqlTotal)->fetch(); ?>
                                                <div class="row">
                                                <?php while ($score = $scores->fetch()) : ?>
                                                    <div class="col-md-1 col-sm-3 text-center">
                                                        <h5><a href="../user_questionnaire.php?questionnaire=<?php echo $questionnnaire['id_quest'] . "&amp;id_user=" . $utilisateur['id'] . "&amp;date=" . $score['date'] . "&" .  uniqid() .  uniqid() ?>
                                                        " class="align-self-end mt-3 text-decoration-none" id="lien_questionnaire" data-bs-toggle="tooltip"  data-bs-html="true" data-bs-placement="bottom" title="<?php $date = date_create($score['date']); echo date_format($date, 'd/m/Y H:i'); ?> - Voir le détail"><?php echo $score['score'] ?>/<?php echo $total['total'] ?></a></h5>
                                                        
                                                    </div>
                                                <?php endwhile ?>
                                                </div>
                                            </div>
                                        <?php endwhile ?>
                                    <?php else : ?>
                                        <div class="alert alert-light">Pas de réponses à ce questionnaire</div>
                                    <?php endif ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile ?>

        <?php else : ?>
            <div class="alert alert-light">Pas de questionnaire dans cette catégorie</div>
        <?php endif ?>
    </div>

    <?php if ($categories->rowCount() > 0) : ?>
        <aside class="col-md-3 order-0 order-md-1 border-start border-dark">
            <div class="list-group mb-2">
                <a href="<?php echo $_SERVER['PHP_SELF'] ?>" class="list-group-item list-group-item-action <?php if (!isset($_GET['categorie'])) echo 'active' ?>">Tous</a>
                <?php while ($categorie = $categories->fetch()) : ?>
                    <a href="?categorie=<?php echo $categorie['id'] ?>" class="list-group-item liste-group-item-action <?php if (isset($_GET['categorie']) && $_GET['categorie'] == $categorie['id']) echo 'active' ?>"><?php echo $categorie['libelle'] ?></a>
                <?php endwhile ?>
            </div>
        </aside>
    <?php endif ?>


    <?php
    require_once('../includes/footer.php');
