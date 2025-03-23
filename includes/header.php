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

    <!-- css principal -->
    <link rel="stylesheet" href="<?php echo URL ?>css/style.css">


</head>

<body>
    <header>

        <nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-dark">
            <div class="container-fluid">

                <a class="navbar-brand" href="<?php echo URL . 'accueil.php' ?>"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav me-auto mb-2 mb-md-0">

                        <li class="nav-item">
                            <a class="nav-link <?php if ($title == "Accueil") echo 'active'; ?>" aria-current="page" href="<?php echo URL . 'accueil.php' ?>"><i class="fas fa-home"></i>
                                <?php if (isConnected()) : ?><?php echo $_SESSION['user']['prenom'] ?><?php endif; ?></a>
                        </li>

                        <?php if (!isConnected()) : ?>
                            <li class="nav-item">
                                <a class="nav-link <?php if ($title == "Inscription") echo 'active'; ?>" href="<?php echo URL ?>inscription.php">Inscription</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link <?php if ($title == "Connexion") echo 'active'; ?>" href="<?php echo URL ?>index.php">Connexion</a>
                            </li>
                        <?php endif; ?>

                        <?php if (isAdmin()): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="sousmenuSupervision" role='button' data-bs-toggle="dropdown">Supervision</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?php echo URL ?>admin/supervision_users.php">Par utilisateur</a></li>
                                    <li><a class="dropdown-item" href="<?php echo URL ?>admin/supervision_groupes.php">Par groupes</a></li>
                                    <li><a class="dropdown-item" href="<?php echo URL ?>admin/supervision_questionnaires.php">Par questionnaire</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="sousmenu" role='button' data-bs-toggle="dropdown">Back Office</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?php echo URL ?>admin/gestion_users.php">Gestion des utilisateurs</a></li>
                                    <li><a class="dropdown-item" href="<?php echo URL ?>admin/gestion_groupes.php">Gestion des groupes</a></li>
                                    <li><a class="dropdown-item" href="<?php echo URL ?>admin/gestion_categories.php">Gestion des categories</a></li>
                                    <li><a class="dropdown-item" href="<?php echo URL ?>admin/gestion_questionnaires.php">Gestion des questionnaires</a></li>
                                </ul>
                            </li>

                        <?php endif; ?>

                        <?php if (isConnected()) : ?>
                            <?php if (isSelfCreated() | isAdmin()) : ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="sousmenu" role='button' data-bs-toggle="dropdown">Mon compte </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="<?php echo URL ?>user_index.php">Mes questionnaires</a></li>
                                        <li><a class="dropdown-item" href="<?php echo URL ?>profil.php">Mon profil</a></li>
                                    </ul>
                                </li>
                            <?php else : ?>
                                <li class="nav-item">
                                <li><a class="nav-link" href="<?php echo URL ?>user_index.php">Mes questionnaires</a></li>
                                </li>
                            <?php endif ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo URL ?>index.php?action=logout"><i class="fas fa-power-off"></i></a>
                            </li>
                        <?php endif; ?>

                    </ul>
                </div>
            </div>
        </nav>


    </header>
    <main class="container-xxl my-5">
        <div id="flash"></div>
        <?php if (!empty(show_flash())) : ?>
            <div class="row justify-content-center">
                <div class="col">
                    <?php echo show_flash('reset'); ?>
                </div>
            </div>
        <?php endif; ?>