$(document).ready(function() {
    // Au clique de btnPopup
  $('.btnPopup').on('click', function(e) {

    // On récupère l'URL passé en href (ex:/produit/popup/1 pour l'id #1)
    var URL = $(this).attr('href');

    // À partir de l'élément sélectionné (.btnPopup) , on obtient la valeur de sont attribut "target"
    // Qui est utilisé pour déterminer la div modal
    var target_modal = $(e.currentTarget).data('target');

    // Contient la modal trouvé dans le DOM
    var modal = $(target_modal);
    
    // Trouve la div qui a l'id demandé dans la modal
    var newContent = $(target_modal + ' #modal-content');
    
    //On s'occupe de charger le contenu venant de l'URL dans l'id de la modal choisi plus haut
            $(newContent).load(URL);
        // On affiche la modal une fois les informations changées
            $(modal).modal('show');
        
    // On return false pour prévenir Bootstrap's JS 3.1.1
    // De lancer une erreur 'preventDefault' à cause que nous avons overridé l'utilisation des ancres.
    return false;
  });
  $(".productQuickView").on("hidden.bs.modal", function(){
      $("#modal-content").html(""); // Lorsque la modal est fermée , le contenu est supprimé
  });
});