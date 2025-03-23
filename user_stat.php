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
    <h1>Statistiques</h1>

    <canvas id="chartQuest"></canvas>
</div>

<!-- script page -->
 
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?php echo URL ?>js/charts.js"></script>

<?php
require_once('includes/footer.php');
