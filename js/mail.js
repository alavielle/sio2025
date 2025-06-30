document.addEventListener("DOMContentLoaded", function () {

  $('#id_mail').on('change', function () {

    id_mail=$(this).val();

    $.ajax({
      url: "../ajax/prepa_mail.php",
      type: "POST",
      dataType: "json",
      data: {
        id_mail: id_mail
      },
    })
      .done(function (datas) {
        console.log(datas);
        if ($.isEmptyObject(datas)) {
          $('#title').val("");
          $('#contenu').val("");
        } else {
          $('#title').val(datas[0].title);
          contenu=datas[0].contenu;
         // contenu=contenu.replace(/<br\s*[\/]?>/gi, "");
          $('#contenu').val(contenu);
        }
      })
      .fail(function (error) {
        console.log(error);
      });
  })


});