<?php


function sql(string $requete, array $params = array()): PDOStatement{

    global $pdo;
    $statement = $pdo->prepare($requete);
    if (!empty($params)) {
        foreach ($params as $key => $value) {
            $statement->bindValue(
                $key,
                htmlspecialchars($value),
                PDO::PARAM_STR
            );
        }
    }
    $statement->execute();
    return $statement;
}

// Fonctions utilisateur
function isConnected(){
    return isset($_SESSION['user']);
}

function isSelfCreated(){
    return (isConnected() && $_SESSION['user']['self_created'] > 0);
}

function isAdmin(){
    return (isConnected() && $_SESSION['user']['droits'] > 0);
}

function getUserById(string $id){
    $requete = sql("SELECT * FROM users WHERE id=:id", array(
        'id' => $id
    ));
    if ($requete->rowCount() > 0) {
        return $requete->fetch();
    } else {
        return false;
    }
}

function getUserByEmail(string $email){
    $requete = sql("SELECT * FROM users WHERE email=:email", array(
        'email' => $email
    ));
    if ($requete->rowCount() > 0) {
        return $requete->fetch();
    } else {
        return false;
    }
}

// fonction des messages
function add_flash(string $message, string $classe){
    if (!isset($_SESSION['messages'][$classe])) {
        $_SESSION['messages'][$classe] = array();
    }
    $_SESSION['messages'][$classe][] = $message;
}

function show_flash($option = null){
    $messages = '';
    if (isset($_SESSION['messages'])) {
        foreach (array_keys($_SESSION['messages']) as $keyname) {
            $messages .= '<div class="alert alert-' . $keyname . '">' . implode('<br>', $_SESSION['messages'][$keyname]) . '</div>'; // implode est équivalent au split
        }
    }
    if ($option == 'reset') {
        unset($_SESSION['messages']); // Je détruis les messages pour ne les afficher qu'1 seule fois
    }
    return $messages;
}


// fonction de chargement des docs
function upLoadDoc($numDoc){
    $nomfichier = '';
    $doc = "doc" . $numDoc;
    $data_doc = "data_doc" . $numDoc;
    $nom_original = "nom_original" . $numDoc;
    $chemin = $_SERVER['DOCUMENT_ROOT'] . URL . 'upload/';

    // 1er cas $_FILES est dispo
    if (!empty($_FILES[$doc]['name'])) {
        $nomfichier =  time() . uniqid() .'.'. pathinfo($_FILES[$doc]['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES[$doc]['tmp_name'], $chemin . $nomfichier);
    } 
    // 2ème cas, on utilise la memoire car $_FILES est perdu
    elseif (!empty($_POST[$data_doc])) {
        $nomfichier =  time() . uniqid() .'.'. pathinfo($_POST[$nom_original], PATHINFO_EXTENSION);
        list(, $data) = explode(',', $_POST[$data_doc]); // On récupère la variable $data qui est issue du tableau explode
        // ecriture du fichier 
        file_put_contents($chemin . $nomfichier, base64_decode($data));
    }
    return $nomfichier;
}

// fonction de suppression des photos
function suppPhotos($nomfichier){
    $chemin = $_SERVER['DOCUMENT_ROOT'] . URL . 'upload/';
    if (!empty($nomfichier) && file_exists($chemin . $nomfichier)) {
        unlink($chemin . $nomfichier);
    }
}