jQuery(function () {
    $("#form_recherche_factures_a_verifier").submit(function () {
        var num_deca         = $("#num_deca_select").val(),
            code_ets         = $("#code_ets_select").val();
        if(num_deca || code_ets) {
            $("#afficher_resultats_div").html('<p class="center_align"><img src="../_publics/images/loading_blue.gif" width="100" /></p>');
            $("#btn_rechercher").prop('disabled',true)
                .removeClass('btn-primary')
                .addClass('btn-warning')
                .html('...');
            $.ajax({
                url: '../_configs/Includes/Searches/Ogd/search_factures_a_verifier.php',
                type: 'POST',
                data: {
                    'num_deca': num_deca,
                    'code_ets': code_ets
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

    $("#form_recherche_factures_a_liquider").submit(function () {
        var num_deca         = $("#num_deca_select").val(),
            num_facture      = $("#num_facture_input").val(),
            code_ets         = $("#code_ets_select").val();
        if(num_deca || code_ets || num_facture) {
            $("#afficher_resultats_div").html('<p class="center_align"><img src="../_publics/images/loading_blue.gif" width="100" /></p>');
            $("#btn_rechercher").prop('disabled',true)
                .removeClass('btn-primary')
                .addClass('btn-warning')
                .html('...');
            $.ajax({
                url: '../_configs/Includes/Searches/Ogd/search_factures_a_liquider.php',
                type: 'POST',
                data: {
                    'num_deca': num_deca,
                    'code_ets': code_ets,
                    'num_facture': num_facture
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