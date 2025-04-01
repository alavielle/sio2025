document.addEventListener("DOMContentLoaded", function () {

  var myChart;
  $('#questionnaire').on('change', function () {

    let ctx = document.getElementById('chartQuest');
    var id_questionnaire = $('#questionnaire').val();
    if (myChart != undefined) {
      myChart.destroy();
    }


    $.ajax({
      url: "ajax/stat_user.php",
      type: "POST",
      dataType: "json",
      data: {
        id_questionnaire: id_questionnaire
      },
    })
      .done(function (datas) {

        if ($.isEmptyObject(datas)) {
          $("#les_scores").html("Pas encore de scores pour ce questionnaire");
        } else {
          myChart = new Chart(ctx, {
            type: 'line',
            data: {
              labels: datas.map(row => row.date),
              datasets: [
                {
                  label: "Scores",
                  data: datas.map(row => row.score),
                  fill: true,
                  borderColor: 'rgb(75, 192, 192)',
                  backgroundColor: 'rgb(75, 192, 192, 0.3)',
                  tension: 0.1
                }
              ]
            },
            options: {
              scales: {
                y: {
                  beginAtZero: true
                }
              },
              plugins: {
                title: {
                  display: true,
                  text: 'Questionnaire ' + id_questionnaire
                }
              }
            }
          });

        }
      })
      .fail(function (error) {
        console.log(error);
      });
  })

  AfficherMyChart(1);

  $('#stat_quest').change(function () {
    AfficherMyChart($(this).val());
  });

  function AfficherMyChart(id_questionnaire) {
    let ctx = document.getElementById('chartQuest');

    if (myChart != undefined) {
      myChart.destroy();
    }

    $.ajax({
      url: "../ajax/stat_admin.php",
      type: "POST",
      dataType: "json",
      data: {
        id_questionnaire: id_questionnaire
      },
    })
      .done(function (datas) {
        console.log(datas);
        if ($.isEmptyObject(datas)) {
          $("#les_scores").html("Pas encore de scores pour ce questionnaire");
        } else {
          myChart = new Chart(ctx, {
            type: 'bar',
            data: {
              labels: datas.map(row => row.row_num),
              datasets: [
                {
                  label: "Scores",
                  data: datas.map(row => row.score),
                  fill: true,
                  borderColor: 'rgb(75, 192, 192)',
                  backgroundColor: 'rgb(75, 192, 192, 0.3)',
                  tension: 0.1
                }
              ]
            },
            options: {
              scales: {
                y: {
                  beginAtZero: true
                }
              },
              plugins: {
                title: {
                  display: true,
                  text: 'Questionnaire ' + id_questionnaire
                }
              }
            }
          });

        }
      })
      .fail(function (error) {
        console.log(error);
      });

  }

});