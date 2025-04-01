<?php

require_once('includes/init.php');

// Redirection si qq tente d'utiliser les url sans en avoir le droit
if (!isConnected()) {
    header('location:' . URL . 'index.php');
    exit();
}

if (isset($_GET['questionnaire']) && is_numeric($_GET['questionnaire'])) {
    $questionnaires = sql("SELECT * FROM questionnaires WHERE id = :id", array(
        'id' => $_GET['questionnaire']
    ));
    $questionnaire = $questionnaires->fetch();
}

if (!empty($questionnaire)) {
    $title = "Questionnaire " . $questionnaire['id'];
} else {
    $title = "";
}
require_once('includes/header.php');


?>
<div class="row" id="question">
    <div class="col-md-9">
        <?php if (!empty($questionnaire)) : ?>
            <h3><?php echo $questionnaire['libelle'] ?></h3>
            <hr>
            <div class="col-md-9 order-1 order-md-0">
                <form method="post" class="ms-5" id="form">
                    <h3 class="mb-3" id="libelle_question"></h3>
                    <input type="text" name="id_questionnaire" id="id_questionnaire" value="<?php echo $questionnaire['id'] ?>" hidden>
                    <input type="text" name="id_question" id="id_question" value="" hidden>
                    <input type="text" name="num_question" id="num_question" value="0" hidden>
                    <div id="les_propositions"></div>
                    <div class="alert alert-info" style="display:none">Pas encore de propositions pour cette question</div>
                    <div class="mb-3 text-end">
                        <button type="submit" class="btn btn-secondary" id="valider">Valider</button>
                    </div>
                    <div id="zone_comm" style="display:none">
                        <div id="info_retour" class="my-3"></div>
                        <div class="card mb-3">
                            <div class="card-body" id="commentaire">
                            </div>
                        </div>
                        <div class="mb-3 text-end">
                            <button class="btn btn-secondary" id="suivant">Suivant</button>
                        </div>
                    </div>
                </form>
                <div class="alert alert-info" id="fin_questions" style="display:none">Pas encore de questions pour ce questionnaire</div>
            </div>
        <?php else : ?>
            <div class="alert alert-info">Ce questionnaire n'existe pas</div>
        <?php endif ?>
    </div>
</div>

<!-- script page -->
<script src="<?php echo URL ?>js/questionnaire.js"></script>

<?php
require_once('includes/footer.php');
