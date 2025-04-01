<?php

require_once('includes/init.php');

// Redirection si qq tente d'utiliser les url sans en avoir le droit
if (!isConnected()) {
    header('location:' . URL . 'index.php');
    exit();
}

$questionnaires = sql("SELECT * FROM questionnaires ORDER BY libelle");

$title = "Accueil";
require_once('includes/header.php');

?>
<div class="row" >
    <h3>Statistiques</h3>

    <div class="col-md-3 my-3">
        <select class="form-select" name="questionnaire" id="questionnaire">
            <option value="0">Choisir</option>
            <?php if ($questionnaires->rowCount() > 0) : ?>
                <?php while ($questionnaire = $questionnaires->fetch()) : ?>
                    <option value="<?php echo $questionnaire['id'] ?>"><?php echo $questionnaire['libelle'] ?></option>
                <?php endwhile ?>
            <?php endif ?>
        </select>
    </div>
    <div class="col-8 mx-auto">
        <canvas id="chartQuest"></canvas>
    </div>
</div>

<!-- script page -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?php echo URL ?>js/charts.js"></script>

<?php
require_once('includes/footer.php');
