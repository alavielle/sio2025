<?php

require_once('../includes/init.php');

if (isset($_POST)) {

    if ($_POST['id_question'] > 0 && is_numeric($_POST['id_question'])) {

        $propositions = sql("INSERT INTO propositions VALUES(NULL, :id_question, :libelle, 0, '')", array(
            'id_question' => $_POST['id_question'],
            'libelle' => $_POST['libelle']
        ));

    }
}
