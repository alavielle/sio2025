<?php

require_once('../includes/init.php');

if (isset($_POST)) {

    if ($_POST['id_questionnaire'] > 0 && is_numeric($_POST['id_questionnaire'])) {

        $sqlscores = sql("SELECT ROW_NUMBER() OVER (ORDER BY questions.id) AS row_num, questions.id_questionnaire, questions.id, SUM(propositions.reponse) as score
                FROM reponses_utilisateur 
                INNER JOIN propositions ON reponses_utilisateur.id_reponse = propositions.id LEFT JOIN questions ON propositions.id_question = questions.ID
                WHERE reponses_utilisateur.id_utilisateur = :id_user AND questions.id_questionnaire = :id_questionnaire  GROUP BY reponses_utilisateur.id_question ORDER BY row_num", array(
                    'id_questionnaire' =>  $_POST['id_questionnaire'],
                    'id_user' =>  $_SESSION['user']['id']
                ));

                $scores = $sqlscores->fetchAll();

        if (!empty($scores)) {

        echo json_encode($scores);
        }
    }
}
