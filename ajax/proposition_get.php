<?php

require_once('../includes/init.php');

if (isset($_POST)) {

    if ($_POST['id_question'] > 0 && is_numeric($_POST['id_question'])) {

        $propositions = sql("SELECT ROW_NUMBER() OVER (ORDER BY id) AS num_ligne, id, id_question, libelle, reponse, commentaire FROM propositions 
                where id_question = :id_question ORDER BY id", array(
            'id_question' =>  $_POST['id_question']
        ));

        $proposition = $propositions->fetchAll();
        echo json_encode($proposition);
    }
}
