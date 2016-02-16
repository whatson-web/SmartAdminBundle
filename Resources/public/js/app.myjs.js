/**
 * Quitter sans enregistrer :
 */

var formChanged = false;

function formChange(){

    if(!$('form', $('#content')).length) {

        return;
    }

    $('input, textarea, select').change(function() {

        if (!$(this).data('notformchanged')) {

            formChanged = true;
        }
    });

}

formChange();

$('form', $('#content')).on('submit', function() {

    formChanged = false;

});

window.onbeforeunload = confirmExit;

function confirmExit() {

    if(formChanged) {
        return "Etes-vous sûr de vouloir quitter sans enregistrer ? ";
    }
}

/**
 * TEST JSON
 * @param str
 * @returns {boolean}
 * @constructor
 */
var IsJsonString = function (str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}


/**
 * Select2 : Avec chargement ajax
 *
 */
var select2ajx = function (element) {

    $(element).each(function(index, el){

        $(el).select2({
            ajax: {
                url: $(el).data('path'),
                dataType: 'json',
                data: function (term) {
                    return {
                        term: term
                    }
                },
                processResults: function (data) {

                    var myResults = [];

                    $.each(data, function (index, item) {
                        myResults.push({
                            'id': item.id,
                            'text': item.name
                        });
                    });
                    return {
                        results: myResults
                    };
                }
            }
        });

    });

}

select2ajx($('.select2ajx'));


/**
 * Ajout d'un élément au formulaire
 * @param container
 * @param linkAdd
 * @param nameInput
 */
var addEl = function (container, linkAdd, nameInput) {

    // On récupère la balise <div> en question qui contient l'attribut « data-prototype » qui nous intéresse.
    var $container = container;

    // Lien d'ajout
    var $addLink = linkAdd;

    // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
    $addLink.click(function(e) {
        addContact($container);
        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        return false;
    });

    // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
    var index = $container.find(':input').length;

    // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un (cas d'une nouvelle annonce par exemple).

    if (index == 0) {

    } else {
        // Pour chaque catégorie déjà existante, on ajoute un lien de suppression
        $container.children().each(function() {
            addDeleteLink($(this));
        });
    }


    // La fonction qui ajoute un formulaire Categorie
    function addContact($container) {
        // Dans le contenu de l'attribut « data-prototype », on remplace :
        // - le texte "__name__label__" qu'il contient par le label du champ
        // - le texte "__name__" qu'il contient par le numéro du champ
        var $prototype = $($container.attr('data-prototype').replace(/__name__label__/g, nameInput + (index+1))
            .replace(/__name__/g, index));

        // On ajoute au prototype un lien pour pouvoir supprimer la catégorie
        addDeleteLink($prototype);

        // On ajoute le prototype modifié à la fin de la balise <div>
        $container.append($prototype);

        // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
        index++;
    }

    // La fonction qui ajoute un lien de suppression d'une catégorie
    function addDeleteLink($prototype) {

        // Création du lien
        $deleteLink = $('.btn-remove, .btn-delete', $prototype);

        // Ajout du listener sur le clic du lien
        $deleteLink.click(function(e) {
            $prototype.remove();
            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
            return false;
        });
    }




}