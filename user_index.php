<?php

require_once('includes/init.php');

// Redirection si qq tente d'utiliser les url sans en avoir le droit
if (!isConnected()) {
    header('location:' . URL . 'index.php');
    exit();
}

$questionnaires = sql("SELECT * FROM questionnaires ORDER BY libelle");

$requete = "SELECT DISTINCT questionnaires.*
FROM questionnaires , questions, reponses_utilisateur
WHERE questionnaires.id = questions.id_questionnaire
AND questions.id = reponses_utilisateur.id_question 
AND reponses_utilisateur.id_utilisateur=" . $_SESSION['user']['id'] . " ORDER BY questionnaires.libelle ASC";

$requete_nb = "SELECT DISTINCT  questions.id, questions.id_questionnaire FROM questions";

$questionnaires_repondus = sql($requete);

$title = "Accueil";
require_once('includes/header.php');

?>
<div class="row" id="questionnaires">
    <h3>Vos questionnaires</h3>
    <div class="col-md-6">
    </div>
    <div class="col-md-3 text-center">
        <h5>Scores</h5>
    </div>
    <?php if ($questionnaires_repondus->rowCount() > 0) : ?>
        <?php while ($questionnnaire_repondu = $questionnaires_repondus->fetch()) :?>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h5><?php echo $questionnnaire_repondu['libelle'] ?></h5>
                    <input type="text" name="id_questionnaire<?php echo $questionnnaire_repondu['id'] ?>" id="id_questionnaire<?php echo $questionnnaire_repondu['id'] ?>" value="<?php echo $questionnnaire_repondu['id'] ?>" hidden>
                </div>
                <?php 
                $sqlscores = "SELECT questions.id_questionnaire, reponses_utilisateur.date, SUM(propositions.reponse) as score
                FROM reponses_utilisateur 
                INNER JOIN propositions ON reponses_utilisateur.id_reponse = propositions.id LEFT JOIN questions ON propositions.id_question = questions.ID
                WHERE reponses_utilisateur.id_utilisateur = " . $_SESSION['user']['id'] . 
                " AND questions.id_questionnaire = " . $questionnnaire_repondu['id'] . 
                " GROUP BY reponses_utilisateur.date, questions.id_questionnaire ORDER BY reponses_utilisateur.date";

                $scores = sql($sqlscores);
                $sqlTotal = "SELECT COUNT(id) as total FROM questions WHERE questions.id_questionnaire = " . $questionnnaire_repondu['id'] . "";
                $total = sql($sqlTotal)->fetch(); ?>
                <?php  while ($score = $scores->fetch()) : ?>
                    <div class="col-md-1 text-center">
                    <h5><a href="user_questionnaire.php?questionnaire=<?php echo $questionnnaire_repondu['id'] . "&amp;date=" . $score['date'] ."&".  uniqid() .  uniqid() ?>" class="align-self-end mt-3 text-decoration-none" id="lien_questionnaire" data-bs-toggle="tooltip"  data-bs-html="true" data-bs-placement="bottom" title="<?php $date = date_create($score['date']); echo date_format($date, 'd/m/Y H:i'); ?> - Voir le détail"><?php echo $score['score'] ?>/<?php echo $total['total'] ?></a></h5>
                    </div>
                <?php endwhile ?>
            </div>
        <?php endwhile ?>
        <hr>
    <?php else : ?>
        <div class="alert alert-info">Pas de questionnaire dans cette catégorie</div>
    <?php endif ?>
</div>

<?php
require_once('includes/footer.php');
