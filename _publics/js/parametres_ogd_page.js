jQuery(function () {
    $("#form_ogd").submit(function () {
        var date_debut = $("#date_debut_ogd_input").val().trim(),
            date_fin = $("#date_fin_ogd_input").val().trim(),
            num_centre = $("#num_centre_ogd_input").val().trim(),
            regime = $("#regime_ogd_input").val().trim(),
            caisse = $("#caisse_ogd_input").val().trim(),
            code_ogd = $("#code_ogd_input").val().trim(),
            libelle = $("#libelle_ogd_input").val().trim().toUpperCase();
        if(date_debut && num_centre && regime && caisse && code_ogd && libelle) {
            $("#button_enregistrer").prop('disabled', true);
            $("#button_enregistrer").removeClass('btn-primary');
            $("#button_enregistrer").addClass('btn-warning');
            $("#button_enregistrer").html('<img width="100" src="../../_publics/images/loading_white.gif" />');
            $.ajax({
                url: '../../_configs/Includes/Submits/Parametres/Ogd/submit_ogd.php',
                type: 'POST',
                data: {
                    'date_debut': date_debut,
                    'date_fin': date_fin,
                    'num_centre': num_centre,
                    'regime': regime,
                    'caisse': caisse,
                    'code_ogd': code_ogd,
                    'libelle': libelle
                },
                dataType: 'json',
                success: function (data) {
                    $("#button_enregistrer").prop('disabled', false);
                    $("#button_enregistrer").removeClass('btn-warning');
                    $("#button_enregistrer").addClass('btn-primary');
                    $("#button_enregistrer").html('<i class="fa fa-save"></i> Enregistrer');
                    if(data['success'] === true) {
                        $("#form_ogd").hide();
                        $("#p_resultats").removeClass('alert alert-danger');
                        $("#p_resultats").addClass('alert alert-success');
                        $("#p_resultats").html('Enregistrement effectu?? avec succ??s.');
                        setTimeout(function () {
                            window.location.href="details.php?code="+code_ogd;
                        },3000);
                    }else {
                        $("#p_resultats").removeClass('alert alert-success');
                        $("#p_resultats").addClass('alert alert-danger');
                        $("#p_resultats").html(data['message']);
                    }
                }
            });

        }
        return false;
    });

    $(".num_input").inputFilter(function(value) {
        return /^\d*$/.test(value);    // Allow digits only, using a RegExp
    });
    $( ".datepicker" ).datepicker();
});