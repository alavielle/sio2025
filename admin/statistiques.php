<?php

require_once('../includes/init.php');

// Redirection si qq tente d'utiliser les url sans en avoir le droit
if (!isAdmin()) {
    header('location:' . URL . 'index.php');
    exit();
}







$title = "Statistiques";
$subtitle = "Admin";
require_once('../includes/header.php');
?>

<div class="row">
</div>

<!-- Utilisation du pluggin Chrts.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<?php
require_once('../includes/footer.php');
