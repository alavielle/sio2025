<?php

require_once('../includes/init.php');

if (isset($_POST)) {

    if ($_POST['id_mail'] > 0 && is_numeric($_POST['id_mail'])) {

        $mails = sql("SELECT * FROM mails  
                where id = :id_mail", array(
            'id_mail' =>  $_POST['id_mail']
        ));

        $mail = $mails->fetchAll();
        echo json_encode($mail);
    }
}
