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
            $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png", "pdf");
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
                move_uploaded_file($_FILES["doc"]["tmp_name"], "upload/" . $_FILES["doc"]["name"]);
                echo "Votre fichier a été téléchargé avec succès.";
                //} 
            } else {
                echo "Error: Il y a eu un problème de téléchargement de votre fichier. Veuillez réessayer.";
            }
        } else {
            echo "Error: " . $_FILES["doc"]["error"];
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
    <div class="col-lg-9 col-md-8 col-sm-6">
        <h2><?php echo $questionnaire['libelle'] ?></h2>
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6 text-end">
        <form action="upload" method="post" enctype="multipart/form-data">
            <span class="fw-bold text-warning">Télécharger le support de formation</span><br>
            <input type="file" name="support" class="form-control text-end mt-1"
                data-key="support_<?php echo $questionnaire['id'] ?>" +
                accept=".png, .jpg, .jpeg, .pdf">
            <input type="submit" name="submit" value="Upload">
            <div class='align-items-center mySpinner' id="spinner" style="display:none"><strong
                    class='text-success'>Chargement en cours...</strong>
                <div class='spinner-border text-success ms-auto' role='status' aria-hidden='true'></div>
            </div>
            <div class="d-grid gap-2 d-md-flex "><button class="btn btn-outline-warning"
                    id="download_support" onclick="fileDownload(this.id)" style="display:none"><i
                        class="fas fa-file-download"></i></button><button class="btn btn-outline-danger"
                    id="delete_support" data-bs-toggle="modal" data-bs-placement="bottom" title="Supprimer"
                    data-bs-target="#ModalDelete" onclick="fileDelete(this.id)" style="display:none;"><i
                        class="fas fa-trash"></i></button><button class="btn btn-dt-vl" id="valide_support"
                    data-bs-toggle="tooltip" data-bs-placement="bottom" title="Document validé"
                    style="display:none;"><i class="fas fa-check-square"></i></button></div>
        </form>
    </div>
</div>
<hr class="my-3">
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


<!-- script page -->
<script src="<?php echo URL ?>js/question.js"></script>

<?php
require_once('../includes/footer.php');
