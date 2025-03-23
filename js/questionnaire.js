// Prévisu de l'image à la sélection du fichier
document.addEventListener("DOMContentLoaded", function () {

  let id_questionnaire = $("#id_questionnaire").val();
  let num_question = $("#num_question").val();
  console.log( num_question);
  let texte_reponse = "";
  let bonne_reponse = 0;

  if (num_question == 0) {
    getQuestion(id_questionnaire, 0);
  }

  $("#valider").on("click", function (e) {
    e.preventDefault();
    id_question = $("#id_question").val();
    id_reponse = $("input[name='proposition']:checked").val();
    num_question = $("#num_question").val();
    console.log(id_question, id_reponse, num_question);
    $.ajax({
      url: "ajax/reponse_save.php",
      type: "POST",
      dataType: "json",
      data: {
        id_question: id_question,
        id_reponse: id_reponse, 
        num_question: num_question
      },
    })
      .done(function (datas) {
        console.log(datas);
        $("input[name='proposition']").prop("disabled", true);
        $("#proposition" + bonne_reponse).prop("checked", true);
        if (id_reponse == bonne_reponse) {
          $("#label" + bonne_reponse).css("color", "green");
          $('#info_retour').html("Bonne réponse !");
        } else {
          //$("#label"+id_reponse).css("color","red");
          $("#label" + bonne_reponse).css("color", "green");
          $('#info_retour').html("La bonne réponse était la proposition :" + $("#label" + bonne_reponse).html());
        }
        $("#commentaire").html(texte_reponse);
        $("#zone_comm").show();
        $("#valider").hide();
      })
      .fail(function (error) {
        console.log(error);
      });
  });

  $("#suivant").on("click", function (e) {
    e.preventDefault();
    num_question = $("#num_question").val();
    getQuestion(id_questionnaire, num_question);
  });

  // fonction de remplissage de la div tbody
  function getQuestion(
    id_questionnaire, num_question
  ) {
    $.ajax({
      url: "ajax/question_get.php",
      type: "POST",
      dataType: "json",
      data: {
        id_questionnaire: id_questionnaire,
        num_question: num_question
      },
    })
      .done(function (datas) {
        if ($.isEmptyObject(datas)) {
          $("#form").hide()
          $("#fin_questions").show()
          $("#fin_questions").html("Vous êtes arrivé à la fin de ce questionnaire ! <br>Merci de votre participation");
        } else {
          if (isObject(datas)) {
            if (datas.erreur != undefined) {
              $("#form").hide()
              $("#fin_questions").show()
              $("#fin_questions").html(datas.erreur);
            } else {
              $("#id_question").val(datas.id);
              $("#num_question").val(datas.num_question);
              $("#libelle_question").html(datas.libelle);
              $("#titre").html(datas.titre);
              var propositions = JSON.parse(datas.propositions);
              var les_propositions = "";
              $("#les_propositions").html("");
              $.each(propositions, function (index, obj) {
                $.each(obj, function (key, value) {
                  if(obj['reponse']==1){
                    bonne_reponse=obj["id"];
                    texte_reponse = obj["commentaire"];
                  }
                  les_propositions = `
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="proposition" id="proposition${obj["id"]}" value="${obj["id"]}">
                                    <label class="form-check-label" for="proposition${obj["id"]}" id="label${obj["id"]}">
                                    ${obj["libelle"]}
                                    </label>
                                </div>`;
                });
                $("#les_propositions").append(les_propositions);
              });
              $("#zone_comm").hide();
              $("#valider").show();            
            }
          }
        }
      })
      .fail(function (error) {
        console.log(error);
      });
  }

  function isObject(val) {
    return val instanceof Object;
  }

});
