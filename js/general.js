// Prévisu de l'image à la sélection du fichier
document.addEventListener("DOMContentLoaded", function () {


  // Afficher ou masquer le password
  $(".toggle-password").on("click", function () {
    $(this).toggleClass("fa-eye fa-eye-slash");
    let input = $(this).attr("data-index");
    if ($('#'+input).attr("type") == "password") {
      $('#'+input).attr("type", "text");
    } else {
      $('#'+input).attr("type", "password");
    }
  });

  if (document.getElementById("preview")) {
    document
      .getElementById("header")
      .addEventListener("change", function (event) {
        let fichier = event.target.files[0];
        let ext = ["image/jpeg", "image/png"];
        if (ext.includes(fichier.type)) {
          let reader = new FileReader();
          reader.readAsDataURL(fichier);
          reader.onload = (e) => {
            document
              .querySelector("#preview img")
              .setAttribute("src", e.target.result);
            // Pour les articles
            if (document.getElementById('nom_original')) {
              // memoriser les infos du fichier image
              document.getElementById('nom_original').setAttribute('value', fichier.name);
              document.getElementById('data_img').setAttribute('value', e.target.result);
            }
          };
        }
      });
  }

  if (document.querySelectorAll('a.confirm')) {
    let confirmations = document.querySelectorAll('a.confirm');
    for (let i = 0; i < confirmations.length; i++) {
      let message = (confirmations[i].dataset.message) ? confirmations[i].dataset.message : 'Etes vous sûr(e) de vouloir supprimer cet élément ?';
      confirmations[i].onclick = () => {
        return (window.confirm(message));
      }
    }
  }

  if (document.getElementById('preview')) {
    document.addEventListener('dragover', (e) => {
      e.stopPropagation();
      e.preventDefault();
      document.getElementById('preview').style.border = '4px dashed blue';
    });
    document.addEventListener('dragleave', (e) => {
      e.stopPropagation();
      e.preventDefault();
      document.getElementById('preview').style.border = '';
    });
    document.addEventListener('drop', (e) => {
      e.stopPropagation();
      e.preventDefault();
      document.getElementById('preview').style.border = '';
    });
    // On dépose le fichier uniquement qd on est en survol sur la zone
    document.getElementById('preview').addEventListener('drop', (e) => {
      document.getElementById('preview').style.border = '';
      let fichier = e.dataTransfer.files;
      // Alimentation de l'input de type file avec cette info
      document.getElementById('header').files = fichier;
      let event = new Event('change'); // On simule un evenement avec un trigger
      document.getElementById('header').dispatchEvent(event);
    })
  }

});
