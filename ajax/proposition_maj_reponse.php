<?php

require_once('../includes/init.php');

if (isset($_POST)) {

    if ($_POST['id'] > 0 && is_numeric($_POST['id']) && $_POST['id_question'] > 0 && is_numeric($_POST['id_question'])) {

        sql("UPDATE propositions SET reponse=1 WHERE id=:id", array(
            'id' => $_POST['id']
        ));

        sql("UPDATE propositions SET reponse=0 WHERE id!=:id AND id_question=:id_question", array(
            'id' => $_POST['id'],
            'id_question' => $_POST['id_question'],
        ));
    }
}
