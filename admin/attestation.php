<?php

require_once('../includes/init.php');
//require_once('../includes/mailer.php');

// Redirection si qq tente d'utiliser les url sans en avoir le droit
if (!isAdmin()) {
    header('location:' . URL . 'index.php');
    exit();
}


$requete = "select distinct users.* , groupes.libelle
from users left join groupes on groupes.id = users.id_groupe";

$params = array();
// Tenir compte d'un éventuel filtre sur le groupe ou le questionnaire
if (isset($_GET['groupe']) && is_numeric($_GET['groupe']) && !isset($_GET['questionnaire'])) {
    $requete .= ' WHERE groupes.id = :id_groupe';
    $params['id_groupe'] = $_GET['groupe'];
}
if (isset($_GET['questionnaire']) && is_numeric($_GET['questionnaire']) && !isset($_GET['groupe'])) {
    $requete .= ' inner join reponses_utilisateur on reponses_utilisateur.id_utilisateur = users.id
        inner join questions on questions.id = reponses_utilisateur.id_question
        WHERE questions.id_questionnaire = :id_quest';
    $params['id_quest'] = $_GET['questionnaire'];
}
if (isset($_GET['groupe']) && isset($_GET['questionnaire'])) {
    if (is_numeric($_GET['questionnaire']) && is_numeric($_GET['groupe'])) {
        $requete .= ' inner join reponses_utilisateur on reponses_utilisateur.id_utilisateur = users.id
        inner join questions on questions.id = reponses_utilisateur.id_question
        WHERE questions.id_questionnaire = :id_quest AND groupes.id = :id_groupe';
        $params['id_quest'] = $_GET['questionnaire'];
        $params['id_groupe'] = $_GET['groupe'];
    }
}

$users = sql($requete, $params);


$title = "Attestation";
$subtitle = "Admin";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes questionnaires </title>

    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>

    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        @page {
            height: 29.7cm;
            max-width: 21cm;
            margin: 0cm 1.3cm 0cm 1.3cm;
        }

        .page {
            height: 29.7cm;
            max-width: 21cm;
            margin: 0cm 1.3cm 0cm 1.3cm;
            font-size: 14px;
        }
    </style>
</head>

<body class="mx-auto">
    <?php if ($users->rowCount() > 0) :
        while ($user = $users->fetch()) : ?>
            <div class="page">
                <div class="row border text-center" style="height:200px">
                    <h3 class="align-self-center">Attestation d'évaluation </h3>
                </div>
                <div class="row text-center mb-3">
                    
                </div>


                <p id="date">
                    Paris, le <?php echo date("d/m/Y") ?>
                </p>

                <div id="msg">
                    <p id="obj">
                        Objet : attstation d'évaluation de <?php echo $user['prenom'] . " " . $user['nom'] ?>
                    </p>

                    <p>
                    </p>

                    <p id="sig">
                        AF COTE, Starquest
                    </p>
                </div>
            </div>
        <?php endwhile ?>
    <?php else : ?>
        <div class="mt-4 alert alert-warning">Il n'y a pas encore d'utilisateur</div>
    <?php endif ?>
</body>