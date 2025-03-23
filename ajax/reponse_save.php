<?php

require_once('../includes/init.php');
if (isset($_POST)) {
    $d = array();
    if ($_POST['id_question'] > 0 && is_numeric($_POST['id_question']) && $_POST['id_reponse'] > 0 && is_numeric($_POST['id_reponse'])) {

        $id = $_SESSION['user']['id'];
        if($_POST['num_question']==1){
            $_SESSION['user']['dateTime'] =  date("Y-m-d H:i:s");
        }

        if ($id > 0) {
            sql("INSERT INTO reponses_utilisateur VALUES (NULL, :id_utilisateur, :id_question, :id_reponse, :date)", array(
                'id_utilisateur' => $id,
                'id_question' => $_POST['id_question'],
                'id_reponse' => $_POST['id_reponse'],
                'date' => $_SESSION['user']['dateTime']
            ));
            $d["retour"]="ok";
        }
    }
}
 
echo json_encode($d);