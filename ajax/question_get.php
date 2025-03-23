<?php

require_once('../includes/init.php');

if (isset($_POST)) {

    if ($_POST['id_questionnaire'] > 0 && is_numeric($_POST['id_questionnaire'])) {

        $questions = sql("SELECT questions.* FROM questions 
                where id_questionnaire = :id_questionnaire ORDER BY id", array(
            'id_questionnaire' =>  $_POST['id_questionnaire']
        ));

        $question = $questions->fetchAll();

        if (!empty($question)) {
            if ($_POST['num_question'] >= 0 && is_numeric($_POST['num_question'])) {
                $nb_question = count($question);
                $num_question = $_POST['num_question'];
                if ($num_question < $nb_question) {
                    $la_question = $question[$num_question];

                    // $date = date("Y-m-d H:i:s");
                    // $search_date = sql(
                    //     "SELECT reponses_utilisateur.* FROM reponses_utilisateur 
                    //         where date = :date  && id_question = :id_question && id_utilisateur = :id_utilisateur",
                    //     array(
                    //         'date' =>  $date,
                    //         'id_question' => $la_question['id'],
                    //         'id_utilisateur' => $_SESSION['user']['id']
                    //     )
                    // );
                    // $deja_repondu = $search_date->fetchAll();

                    $reponses = sql("SELECT reponses_utilisateur.id_question FROM reponses_utilisateur 
                            where id_question = :id_question && id_utilisateur = :id_utilisateur",
                        array(
                            'id_question' => $la_question['id'],
                            'id_utilisateur' => $_SESSION['user']['id']
                        )
                    );
                    // $nb = $reponses->rowCount();

                    // if (!empty($deja_repondu)) {
                    //     $message = "Vous avez déjà répondu à ce questionnaire aujourd'hui !";
                    //     $la_question['erreur'] = $message;
                    // } else {
                    //     if($nb==3){
                    //        $message = "Vous avez atteind la limite de participation à ce questionnaire !";
                    //        $la_question['erreur'] = $message;
                    //     } else {
                            $propositions = sql("SELECT * FROM propositions WHERE id_question = :id_question", array(
                                'id_question' => $la_question['id']
                            ));
                            $propos = $propositions->fetchAll();
                            $la_question['propositions'] = json_encode($propos);
                            $la_question['num_question'] = $num_question + 1;
                    //     }
                    // }
                } else {
                    $la_question = (object)[];
                }
            }
        }
        echo json_encode($la_question);
    }
}
