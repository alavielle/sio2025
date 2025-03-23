
document.addEventListener("DOMContentLoaded", function () {

  function fileUpload(key) {
    //var key = upload_id.replace("upload", "");
    var file = $("input[name='" + key + "']")[0].files[0];
    var uuid = $("input[name='" + key + "']").attr('data-key');
    if (file) {
        $("#spinner" + key).addClass('d-flex').css("display", "block");
        $('input[type="file"]').attr('disabled', true)
        uploadToS3(file, key, uuid, false);
    };
}

  $("input[name='question']").on("click", function (e) {
    e.preventDefault();
    id_question = $(this).attr('data-id');
    num_ligne = $(this).attr('aria-describedby');
    $("#num_ligne").html("pour la question n°" + num_ligne);
    $("#la_question").val(id_question);
    $('#zone_propo').show();
    getProposition(id_question);
  });

  $('#add_propo').on('click', function (e) {
    e.preventDefault();
    nv_proposition = $('#nv_proposition').val();
    id_question = $("#la_question").val();
    addProposition(id_question, nv_proposition);
    getProposition(id_question);
    $('#nv_proposition').val("");
  })

  $("body").on("click", "input[name='reponse']", function () {
    let id=0;
    if ($(this).prop("checked")) {
      id = $(this).val();
    }
    $('input[name=reponse]').each(function () {
      $(this).prop("checked", false);
    });
    $(this).prop("checked", true);
    id_question = $("#la_question").val();
    addReponse(id,id_question);
  })

  $('#update').on('click', function () {
    commentaire = $('#commentaire').val();
    if (commentaire !="") {
      let id = 0;
      id = $("input:checked").val();
      if (id == 0) {
        alert("Il faut sélectionner une réponse parmi les propositions")
      } else {
        majReponse(id, commentaire);
        message='<div class="alert alert-success" role="alert">Le commentaire a bien été mis à jour</div>'
        $('#flash').html(message);
      }
    }
  })

  // fonction de remplissage de la div proposition
  function getProposition(
    id_question
  ) {
    $.ajax({
      url: "../ajax/proposition_get.php",
      type: "POST",
      dataType: "json",
      data: {
        id_question: id_question
      },
    })
      .done(function (datas) {
        $('#commentaire').val("");
        if ($.isEmptyObject(datas)) {
          $("#les_propositions").html("Pas encore de proposition pour cette question");
        } else {
          var propositions = datas;
          var les_propositions = "";
          $("#les_propositions").html('<div class="col-lg-8 text-end mb-3">Réponse</div>');
          $.each(propositions, function (index, obj) {
            if (obj['reponse'] == 1) {
              coche_reponse = "checked";
              $('#commentaire').val(obj['commentaire']);
            } else {
              coche_reponse = "";
            }
            les_propositions = `
                    <form method="post" class="row mb-3">
                    <input type="hidden" name="id_question" value="${obj["id_question"]}">
                    <input type="hidden" name="id_proposition" value="${obj["id"]}">
                    <div class="col-lg-8 mb-3">
                        <div class="input-group">
                            <span class="input-group-text" id="${obj["num_ligne"]}">${obj["num_ligne"]}</span>
                            <input type="text" name="proposition" class="form-control" value="${obj["libelle"]}" aria-describedby="${obj["num_ligne"]}">
                            <input type="checkbox" class="form-check-input ms-3 pt-2" name="reponse" value="${obj["id"]}" ${coche_reponse}>
                        </div>
                    </div>
                    <div class="col-lg-3 mb-3">
                        <button type="submit" name="update_propo" class="btn btn-outline-success">
                            <i class="fas fa-check"></i>
                        </button>
                        <a href="?action=delete&destination=proposition&id=${obj["id"]}" class="btn btn-outline-danger confirm">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                    </form>`;
            $("#les_propositions").append(les_propositions);
          });
        }
      })
      .fail(function (error) {
        console.log(error);
      });
  }

  function addProposition(
    id_question, libelle
  ) {
    $.ajax({
      url: "../ajax/proposition_add.php",
      type: "POST",
      dataType: "json",
      data: {
        id_question: id_question,
        libelle: libelle
      },
    })
      .done(function (datas) {

      })
      .fail(function (error) {
        console.log(error);
      });
  }

  function addReponse(
    id,id_question
  ) {
    $.ajax({
      url: "../ajax/proposition_maj_reponse.php",
      type: "POST",
      dataType: "json",
      data: {
        id: id,
        id_question:id_question
      },
    })
      .done(function (datas) {

      })
      .fail(function (error) {
        console.log(error);
      });
  }

  function majReponse(
    id, commentaire
  ) {
    $.ajax({
      url: "../ajax/proposition_maj_commentaire.php",
      type: "POST",
      dataType: "json",
      data: {
        id: id,
        commentaire: commentaire
      },
    })
      .done(function (datas) {

      })
      .fail(function (error) {
        console.log(error);
      });
  }


});
