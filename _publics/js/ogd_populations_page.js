jQuery(function () {
    $("#form_recherche_population").submit(function () {
        var num_secu         = $("#num_secu_input").val(),
            nom             = $("#nom_input").val(),
            date_naissance   = $("#date_naissance_input").val();
        if(num_secu || nom || date_naissance) {
            $("#afficher_resultats_div").html('<p class="center_align"><img src="../_publics/images/loading_blue.gif" width="100" /></p>');
            $("#btn_rechercher").prop('disabled',true)
                .removeClass('btn-primary')
                .addClass('btn-warning')
                .html('...');
            $.ajax({
                url: '../_configs/Includes/Searches/Ogd/search_populations.php',
                type: 'POST',
                data: {
                    'num_secu': num_secu,
                    'nom': nom,
                    'date_naissance': date_naissance
                },
                success: function (dataRecherche) {
                    $("#btn_rechercher").prop('disabled',false)
                        .removeClass('btn-warning')
                        .addClass('btn-primary')
                        .html('<b class="fa fa-search"></b>');
                    $("#afficher_resultats_div").html(dataRecherche);
                }
            });
        }
        return false;
    });

    $( ".datepicker" ).datepicker({
        changeMonth: true,
        changeYear: true
    });
});