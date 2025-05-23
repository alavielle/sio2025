<?php

require_once('../includes/init.php');
//require_once('../includes/mailer.php');

// Redirection si qq tente d'utiliser les url sans en avoir le droit
if (!isAdmin()) {
    header('location:' . URL . 'index.php');
    exit();
}

$requete = "select users.* , groupes.libelle
from users left join groupes on groupes.id = users.id_groupe";

$params = array();
// Tenir compte d'un éventuel filtre sur la catégorie
if (isset($_GET['groupe']) && is_numeric($_GET['groupe'])) {
    $requete .= ' WHERE groupes.id = :id';
    $params['id'] = $_GET['groupe'];
}

$requete .= " order by users.prenom";
$users = sql($requete, $params);

$title = "Fiche d'appel";
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
            size: A4 portrait;
            max-width: 19cm;
            margin: 2cm 1.3cm 2cm 1.3cm;
        }

        body {
            max-width: 21cm;
            margin: 2cm 1.3cm 2cm 1.3cm;
            font-size: 14px;
        }
    </style>
</head>

<body class="mx-auto">
    <div class="row text-center mb-3">
        <h3>Fiche d'appel</h3>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <h5>Formation</h5>
        </div>
        <div class="col-md-3">
            <h5> </h5>
        </div>
        <div class="col-auto text-end">
            <h5>Date : <?php echo date("d/m/Y") ?></h5>
        </div>
    </div>
    <div class="row " id="toPDF">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Signature</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users->rowCount() > 0) :
                        while ($user = $users->fetch()) : ?>
                            <tr height="60">
                                <td><?= $user['nom'] ?></td>
                                <td><?= $user['prenom'] ?></td>
                                <td></td>
                            </tr>
                        <?php endwhile ?>
                    <?php else : ?>
                        <div class="mt-4 alert alert-warning">Il n'y a pas encore d'utilisateur</div>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>

</body>