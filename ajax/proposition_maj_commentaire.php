<?php

require_once('../includes/init.php');

if (isset($_POST)) {

    if ($_POST['id'] > 0 && is_numeric($_POST['id']) && $_POST['commentaire']!="") {

        sql("UPDATE propositions SET commentaire=:commentaire WHERE id=:id", array(
            'id' => $_POST['id'],
            'commentaire' => $_POST['commentaire']
        ));

    }
}