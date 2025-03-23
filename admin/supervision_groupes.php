<?php

require_once('../includes/init.php');

// Redirection si qq tente d'utiliser les url sans en avoir le droit
if (!isAdmin()) {
    header('location:' . URL . 'index.php');
    exit();
}


$groupes = sql("SELECT * FROM groupes ORDER BY libelle");

$requete = "SELECT questionnaires.id as id_questionnaire, questionnaires.libelle as libelle_questionnaire
FROM questionnaires";

$requeteliste = "SELECT DISTINCT users.prenom, users.nom, users.id, users.id_groupe, questionnaires.id as id_questionnaire, questionnaires.libelle as libelle_questionnaire, groupes.id as groupe, groupes.libelle as libelle_groupe
     FROM users
     inner join groupes on users.id_groupe = groupes.id
     inner join reponses_utilisateur on reponses_utilisateur.id_utilisateur = users.id
     inner join questions on questions.id = reponses_utilisateur.id_question
     inner join questionnaires on questionnaires.id = questions.id_questionnaire";

$requetescore = "SELECT DISTINCT propositions.libelle as proposition, propositions.commentaire
    FROM propositions
    WHERE propositions.reponse = 1";





$title = "Scores par Groupes";
require_once('../includes/header.php');
?>


<div class="row" id="questionnaires">
    <h2>Réponses par groupes</h2>

    <?php if ($groupes->rowCount() > 0) : ?>
        <?php while ($groupe = $groupes->fetch()) : ?>
            <h3 class="mt-3"><?php echo $groupe['libelle']; ?></h3>
            <div class="col-md-9 order-1 order-md-0">
                <?php $questionnaires = sql($requete); ?>
                <?php if ($questionnaires->rowCount() > 0) : ?>
                    <?php while ($questionnnaire = $questionnaires->fetch()) : ?>
                        <div class="accordion" id="accordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <?php echo $questionnnaire['libelle_questionnaire']; ?>
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <?php $liste = $requeteliste . " WHERE questionnaires.id = "  . $questionnnaire['id_questionnaire'] . " and users.id_groupe = " . $groupe['id'] . " group by questionnaires.id, users.id"; ?>
                                            <?php $utilisateurs = sql($liste); ?>
                                            <?php if ($utilisateurs->rowCount() > 0) : ?>
                                                <?php while ($utilisateur = $utilisateurs->fetch()) : ?>
                                                    <div class="col-md-3">
                                                        <?php echo $utilisateur['prenom'] ?>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <?php
                                                        $sqlscores = "SELECT questions.id_questionnaire, reponses_utilisateur.date, SUM(propositions.reponse) as score
                FROM reponses_utilisateur 
                INNER JOIN propositions ON reponses_utilisateur.id_reponse = propositions.id LEFT JOIN questions ON propositions.id_question = questions.ID
                WHERE reponses_utilisateur.id_utilisateur = " . $utilisateur['id'] .
                                                            " AND questions.id_questionnaire = " . $questionnnaire['id_questionnaire'] .
                                                            " GROUP BY reponses_utilisateur.date, questions.id_questionnaire ORDER BY reponses_utilisateur.date DESC";

                                                        $scores = sql($sqlscores);
                                                        $sqlTotal = "SELECT COUNT(id) as total FROM questions WHERE questions.id_questionnaire = " . $questionnnaire['id_questionnaire'] . "";
                                                        $total = sql($sqlTotal)->fetch(); ?>
                                                        <div class="row">
                                                            <?php while ($score = $scores->fetch()) : ?>
                                                                <div class="col-md-1 text-center">
                                                                    <h5><a href="../user_questionnaire.php?questionnaire=<?php echo $questionnnaire['id_questionnaire'] . "&amp;id_user=" . $utilisateur['id'] . "&amp;date=" . $score['date'] . "&" .  uniqid() .  uniqid() ?>
                                                        " class="align-self-end mt-3 text-decoration-none" id="lien_questionnaire" data-bs-toggle="tooltip"  data-bs-html="true" data-bs-placement="bottom" title="<?php $date = date_create($score['date']); echo date_format($date, 'd/m/Y H:i'); ?> - Voir le détail"><?php echo $score['score'] ?>/<?php echo $total['total'] ?></a> </h5>

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

        <?php endwhile ?>
    <?php else : ?>
        <div class="alert alert-light">Pas de groupes</div>
    <?php endif ?>

    <?php
    require_once('../includes/footer.php');
