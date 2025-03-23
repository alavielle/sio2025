<?php

require_once('includes/init.php');

// Redirection si qq tente d'utiliser les url sans en avoir le droit
if (!isConnected()) {
    header('location:' . URL . 'index.php');
    exit();
}


if (isset($_GET['questionnaire']) && is_numeric($_GET['questionnaire']) && isset($_GET['date'])) {
    if (isset($_GET['id_user']) && is_numeric($_GET['id_user'])){
        $id_user = $_GET['id_user'];
        $theUser = getUserById($id_user)['prenom'];
    } else {
        $id_user = $_SESSION['user']['id'];
        $theUser = "";
    }
    if (DateTime::createFromFormat('Y-m-d H:i:s', $_GET['date']) !== false) {
        $getdate = date_create($_GET['date']);
        $date = date_format($getdate, "d/m/Y H:i:s");

        $questionnaires = sql("SELECT * FROM questionnaires WHERE id = :id", array(
            'id' => $_GET['questionnaire']
        ));
        $questionnaire = $questionnaires->fetch();

        $user_requete = "SELECT DISTINCT questionnaires.libelle as questionnaire, questions.id, questions.libelle as question, propositions.libelle as proposition, propositions.reponse
    FROM questionnaires , questions, propositions, reponses_utilisateur
    WHERE questionnaires.id = " . $_GET['questionnaire']
            . " AND questionnaires.id = questions.id_questionnaire
    AND questions.id = reponses_utilisateur.id_question 
    AND reponses_utilisateur.id_reponse = propositions.id
    AND reponses_utilisateur.date = '" . $_GET['date'] . "' 
    AND reponses_utilisateur.id_utilisateur=" . $id_user;

        $user_requete .= " ORDER BY questions.id ASC";
        $user_questionnaires = sql($user_requete);

        $requete = "SELECT DISTINCT propositions.libelle as proposition, propositions.commentaire
    FROM propositions
    WHERE propositions.reponse = 1";
    }
}

if (!empty($questionnaire)) {
    $title = $questionnaire['libelle'];
} else {
    $title = "";
}
require_once('includes/header.php');

?>
<div class="row" id="questionnaires">
    <div class="col-md-9">
        <h1 class="text-center mb-5"><?php echo $title ?></h1>
    </div>
    <div class="col-md-3 pt-3 text-end user_color">
        <h5><?php echo $theUser ?></h5><h6><?php echo $date ?></h6>
    </div>
    <div class="row">
        <div class="col-md-6">
            <h4 class="user_color">Vos réponses</h4>
        </div>
        <div class="col-md-6">
            <h4 class="text-success ms-md-5">Ce qu'il fallait répondre</h4>
        </div>
    </div>
    <?php if ($user_questionnaires->rowCount() > 0) : ?>
        <?php while ($user_questionnaire = $user_questionnaires->fetch()) : ?>
            <hr>
                <?php $requete_question = $requete . " AND propositions.id_question = " . $user_questionnaire['id'];
                $bonnes_reponses = sql($requete_question);
                $bonne_reponse = $bonnes_reponses->fetch();
                ?>
            <div class="col-md-6">
                <h5 class="ms-3 "><?php echo $user_questionnaire['question'] ?></h5>
                <p class="ms-5 user_color"><i class="far fa-hand-point-right"></i> <?php echo $user_questionnaire['proposition'] ?>
                <?php if($user_questionnaire['reponse']==1) : ?><span class="text-white"><i class="far fa-window-minimize"></i></span><i class="fas fa-check"></i><?php endif ?>
                </p>
            </div>
            <div class="col-md-6">
                <p class="ms-5 text-success"><i class="far fa-hand-point-right"></i> <?php echo $bonne_reponse['proposition'] ?></p>
                <p class="ms-5"><?php echo $bonne_reponse['commentaire'] ?></p>
            </div>
        <?php endwhile ?>
    <?php else : ?>
        <div class="alert alert-info">Pas de bonne réponse enregistrée</div>
    <?php endif ?>
</div>
</div>

<?php
require_once('includes/footer.php');
