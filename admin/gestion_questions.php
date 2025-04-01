<?php

require_once('../includes/init.php');

// Redirection si qq tente d'utiliser les url sans en avoir le droit
if (!isAdmin()) {
    header('location:' . URL . 'index.php');
    exit();
}

if (isset($_GET['questionnaire']) && is_numeric($_GET['questionnaire'])) {
    $questionnaires = sql("SELECT * FROM questionnaires WHERE id = :id", array(
        'id' => $_GET['questionnaire']
    ));
    $questionnaire = $questionnaires->fetch();

    $questions = sql("SELECT ROW_NUMBER() OVER (ORDER BY id) AS num_ligne, id, libelle, id_questionnaire FROM questions  WHERE id_questionnaire = :id_questionnaire",  array(
        'id_questionnaire' => $questionnaire['id']
    ));
}

// Suppression ($_Get)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($_GET['destination']) && $_GET['destination'] == 'question' && !empty($_GET['id']) && is_numeric($_GET['id'])) {
    sql("DELETE FROM questions WHERE id=:id", array(
        'id' => $_GET['id']
    ));
    add_flash('La question a bien été supprimée', 'warning');
    header('location:' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Suppression ($_Get)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && !empty($_GET['destination']) && $_GET['destination'] == 'proposition' && !empty($_GET['id']) && is_numeric($_GET['id'])) {
    sql("DELETE FROM propositions WHERE id=:id", array(
        'id' => $_GET['id']
    ));
    add_flash('La proposition a bien été supprimée', 'warning');
    header('location:' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Traitement des formulaires

if (!empty($_POST)) {
    // Formulaire d'upload soumis
    if (isset($_POST['upload'])) {
        // Vérifie si le fichier a été uploadé sans erreur.
        if (isset($_FILES["doc"]) && $_FILES["doc"]["error"] == 0) {
            $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png", "pdf" => "application/pdf");
            $filename = $_FILES["doc"]["name"];
            $filetype = $_FILES["doc"]["type"];
            $filesize = $_FILES["doc"]["size"];

            // Vérifie l'extension du fichier
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if (!array_key_exists($ext, $allowed)) die("Erreur : Veuillez sélectionner un format de fichier valide.");

            // Vérifie la taille du fichier - 5Mo maximum
            $maxsize = 5 * 1024 * 1024;
            if ($filesize > $maxsize) die("Error: La taille du fichier est supérieure à la limite autorisée.");

            // Vérifie le type MIME du fichier
            if (in_array($filetype, $allowed)) {
                // Vérifie si le fichier existe avant de le télécharger.
                // if(file_exists("upload/" . $_FILES["doc"]["name"])){
                //     echo $_FILES["doc"]["name"] . " existe déjà.";
                // } else{
                
                move_uploaded_file($_FILES["doc"]["tmp_name"], UPLOAD_PATH . $_FILES["doc"]["name"]);
                sql("UPDATE questionnaires SET support=:support WHERE id=:id_quest", array(
                    'id_quest' => $_POST['id_questionnaire'],
                    'support' => $_FILES["doc"]["name"]
                ));
                echo "Votre fichier a été téléchargé avec succès.";
                add_flash('Votre fichier a été téléchargé avec succès ', 'success');
                header('location:' . $_SERVER['REQUEST_URI']);
                exit();
                //} 
            } else {
                echo "Error: Il y a eu un problème de téléchargement de votre fichier. Veuillez réessayer.";
                add_flash('Error: Il y a eu un problème de téléchargement de votre fichier. Veuillez réessayer.', 'warning');
                header('location:' . $_SERVER['REQUEST_URI']);
            }
        } else {
            echo "Error: " . $_FILES["doc"]["error"];
        }
    }
    //suppression du support
    if (isset($_POST['suppr'])) {
        if (!empty($_POST['id_questionnaire'])) {
            sql("UPDATE questionnaires SET support=:support WHERE id=:id_quest", array(
                'id_quest' => $_POST['id_questionnaire'],
                'support' => ""
            ));    
            $nomfichier = $_POST['support'];
            if (!empty($nomfichier) && file_exists(UPLOAD_PATH . $nomfichier)) {
                unlink(UPLOAD_PATH . $nomfichier);
            }
            add_flash('Le support de formation a bien été supprimé ', 'warning');
            header('location:' . $_SERVER['REQUEST_URI']);
            exit();
        } 
    }


    // Formulaire d'ajout soumis
    if (isset($_POST['add'])) {
        if (!empty(trim($_POST['nv_question']))) {
            sql("INSERT INTO questions VALUES(NULL, :libelle, :id_questionnaire)", array(
                'libelle' => $_POST['nv_question'],
                'id_questionnaire' => $_POST['id_questionnaire'],
            ));
            add_flash('La question ' . $_POST['nv_question'] . ' a été ajoutée', 'warning');
            header('location:' . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            add_flash('La question ne doit pas être vide', 'danger');
        }
    }
    // Formulaire d'update soumis
    if (isset($_POST['update'])) {
        if (!empty(trim($_POST['question']))) {
            sql("UPDATE questions SET libelle=:nvlibelle WHERE id=:id_question", array(
                'nvlibelle' => $_POST['question'],
                'id_question' => $_POST['id_question']
            ));
            add_flash('La question a été mise à jour', 'warning');
            header('location:' . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            add_flash('La question ne doit pas être vide', 'danger');
        }
    }

    // Proposition - Formulaire d'ajout soumis
    // if (isset($_POST['add_propo'])) {
    //     if (!empty(trim($_POST['nv_proposition']))) {
    //         sql("INSERT INTO propositions VALUES(NULL, :id_question, :libelle)", array(
    //             'id_question' => $_POST['la_question'],
    //             'libelle' => $_POST['nv_proposition']
    //         ));
    //         add_flash('La proposition ' . $_POST['nv_proposition'] . ' a été ajoutée', 'warning');
    //         exit();
    //     } else {
    //         add_flash('La proposition ne doit pas être vide', 'danger');
    //     }
    // }
    //Proposition - Formulaire d'update soumis
    if (isset($_POST['update_propo'])) {
        if (!empty(trim($_POST['proposition']))) {
            sql("UPDATE propositions SET libelle=:nvlibelle WHERE id=:id_proposition", array(
                'nvlibelle' => $_POST['proposition'],
                'id_proposition' => $_POST['id_proposition']
            ));
            add_flash('La proposition a été mis à jour', 'warning');
            header('location:' . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            add_flash('La proposition ne doit pas être vide', 'danger');
        }
    }
}

$title = "Gestion des questions";
$subtitle = "Admin";
require_once('../includes/header.php');
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <h2><?php echo $questionnaire['libelle'] ?></h2>
    </div>
    <div class="col-md-6">
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_questionnaire" value="<?php echo $questionnaire['id'] ?>">
            <input type="hidden" name="support" value="<?php echo $questionnaire['support'] ?>">

            <?php if (!empty($questionnaire['support'])) : ?>
                <div class="fw-bold text-warning mb-2">Support de formation : </div>
                <div class="row">
                    <div class="col">
                        <a href="<?php echo UPLOAD_PATH . $questionnaire['support'] ?>" aria-describedby="gestion" class="support"><?php echo $questionnaire['support'] ?></a>
                    </div>
                    <div class="col-auto">
                        <a class="btn btn-outline-warning" href="<?php echo UPLOAD_PATH . $questionnaire['support'] ?>" data-bs-placement="bottom" title="Télécharger"><i class="fas fa-file-download"></i></a>
                        <button type="submit" name="suppr" class="btn btn-outline-danger" data-bs-placement="bottom" title="Supprimer"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            <?php else : ?>
                <div class="fw-bold text-warning mb-2">Télécharger le support de formation</div>
                <div class="input-group mb-3">
                    <input type="file" name="doc" class="form-control" accept="image/*,application/pdf" aria-describedby="submit">
                    <button class="btn btn-secondary" type="submit" id="upload" name="upload">Télécharger</button>

                    <div>
                        <span><?php echo $questionnaire['support'] ?></span>

                    </div>
                <?php endif ?>
        </form>
    </div>
</div>
<hr class="my-3">
<div class="row">
    <div class="col-md-6">
        <?php if ($questions->rowCount() > 0) : ?>
            <h4>Liste des questions</h4>
            <?php while ($question = $questions->fetch()) : ?>
                <form method="post" class="row mb-3">
                    <input type="hidden" name="id_question" value="<?php echo $question['id'] ?>">
                    <div class="col-md-9 mb-3">
                        <div class="input-group">
                            <span class="input-group-text" id="<?php echo $question['num_ligne'] ?>"><?php echo $question['num_ligne'] ?></span><input type="text" name="question" data-id="<?php echo $question['id'] ?>" class="form-control" value="<?php echo $question['libelle'] ?>" aria-describedby="<?php echo $question['num_ligne'] ?>">
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button type="submit" name="update" class="btn btn-outline-success">
                            <i class="fas fa-check"></i>
                        </button>
                        <a href="?action=delete&destination=question&id=<?php echo $question['id'] ?>" class="btn btn-outline-danger confirm">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </form>
            <?php endwhile ?>
        <?php else : ?>
            <div class="mt-4 alert alert-warning">Il n'y a pas encore de question</div>
        <?php endif ?>
        <hr class="my-3">
        <form method="post" class="row">
            <input type="hidden" name="id_questionnaire" value="<?php echo $questionnaire['id'] ?>">
            <div class="col-md-9 mb-3">
                <input type="text" id="nv_question" name="nv_question" class="form-control" placeholder="question à ajouter">
            </div>
            <div class="col-md-3 mb-3">
                <button type="submit" name="add" class="btn btn-secondary">Ajouter</button>
            </div>
        </form>
    </div>
    <div class="col-md-6">
        <div id="zone_propo" style="display:none">
            <h2>Propositions <span id="num_ligne"></span></h2>
            <div class="row">
                <div id="les_propositions">
                </div>
            </div>
            <hr class="my-3">
            <form method="post" class="row">
                <input type="hidden" name="la_question" id="la_question" value="">
                <div class="col-md-9 mb-3">
                    <input type="text" id="nv_proposition" name="nv_proposition" class="form-control" placeholder="proposition à ajouter">
                </div>
                <div class="col-md-3 mb-3">
                    <button type="submit" name="add_propo" id="add_propo" class="btn btn-secondary">Ajouter</button>
                </div>
            </form>
            <div class="col mt-3">
                <label for="commentaire" class="form-label">Commentaire réponse</label>
                <textarea class="form-control" id="commentaire" name="commentaire" rows="5"></textarea>
            </div>
            <div class="col mt-3 text-end">
                <button type="btn" name="update" id="update" class="btn btn-outline-success">
                    <i class="fas fa-check"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="modal_suppr">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p>Supprimer le support ?</p>
            </div>
            <div class="modal-footer">
                <form name="delete">
                    <input type="hidden" name="id_questionnaire" value="<?php echo $questionnaire['id'] ?>">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- script page -->
<script src="<?php echo URL ?>js/question.js"></script>

<?php
require_once('../includes/footer.php');
