jQuery(function () {
    $("#form_referentiels_chargement").submit(function (e) {
        $("#div_resultats").fadeOut().html('');
        e.preventDefault();
        let formData = new FormData(this);
        $("#btn_charger").prop('disabled',true)
            .removeClass('btn-danger')
            .addClass('btn-warning')
            .html('<i>Chargement...</i>');
        $.ajax({
            url: "../../_configs/Includes/Submits/Parametres/Referentiels/submit_referentiels_chargement.php",
            type: "POST",
            data: formData,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                $(".progress").show();
            },
            uploadProgress: function (event,position,total,percentComplete) {
                $(".progress-bar").width(percentComplete+'%');
                $(".sr-only").html(percentComplete+'%');
            },
            success: function (data) {
                $("#btn_charger").prop('disabled',false)
                    .removeClass('btn-warning')
                    .addClass('btn-danger')
                    .html('<i class="fa fa-upload"></i> Charger');
                $(".progress").hide();
                if(data['success'] === true) {
                    $("#div_resultats").fadeIn().html('<div class="alert alert-success alert-dismissible fade show" role="alert">\n' +data['message'] +
                        '  <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
                        '    <span aria-hidden="true">&times;</span>\n' +
                        '  </button>\n' +
                        '</div>');
                }else {
                    $("#div_resultats").fadeIn().html('<div class="alert alert-danger alert-dismissible fade show" role="alert">\n' +data['message'] +
                        '  <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
                        '    <span aria-hidden="true">&times;</span>\n' +
                        '  </button>\n' +
                        '</div>');
                }

            }
        });
        return false;
    });

    $("#form_recherche_centres_sante").submit(function () {
        let code    = $("#code_input").val().trim(),
            nom     = $("#nom_input").val().trim(),
            secteur = $("#secteur_input").val().trim(),
            ville   = $("#ville_input").val().trim();
        if(code || nom || secteur || ville) {
            $("#btn_rechercher")
                .prop('disabled',true)
                .removeClass('btn-primary')
                .addClass('btn-warning')
                .html('...');
            $("#div_resultats_recherche").html('<p class="center_align"><img src="../../_publics/images/loading_white.gif" /></p>');
            $.ajax({
                url: '../../_configs/Includes/Searches/Parametres/Referentiels/search_centres_sante.php',
                type: 'POST',
                data: {
                    'code': code,
                    'nom': nom,
                    'secteur': secteur,
                    'ville': ville
                },
                success: function (data){
                    $("#btn_rechercher")
                        .prop('disabled',false)
                        .removeClass('btn-warning')
                        .addClass('btn-primary')
                        .html('<i class="fa fa-search"></i>');
                    $("#div_resultats_recherche").html(data);
                }
            });
        }else {
            $("#div_resultats_recherche").html('<p class="alert alert-warning center_align">Veuillez renseigner au moins un champ avant d\'effectuer une recherche.</p>');
        }
        return false;
    });
    $("#form_recherche_professionnels_sante").submit(function () {
        let code        = $("#code_input").val().trim(),
            nom         = $("#nom_input").val().trim(),
            specialite  = $("#specialite_input").val().trim(),
            ville       = $("#ville_input").val().trim();
        if(code || nom || specialite || ville) {
            $("#btn_rechercher")
                .prop('disabled',true)
                .removeClass('btn-primary')
                .addClass('btn-warning')
                .html('...');
            $("#div_resultats_recherche").html('<p class="center_align"><img src="../../_publics/images/loading_white.gif" /></p>');
            $.ajax({
                url: '../../_configs/Includes/Searches/Parametres/Referentiels/search_professionnels_sante.php',
                type: 'POST',
                data: {
                    'code': code,
                    'nom': nom,
                    'specialite': specialite,
                    'ville': ville
                },
                success: function (data){
                    $("#btn_rechercher")
                        .prop('disabled',false)
                        .removeClass('btn-warning')
                        .addClass('btn-primary')
                        .html('<i class="fa fa-search"></i>');
                    $("#div_resultats_recherche").html(data);
                }
            });
        }else {
            $("#div_resultats_recherche").html('<p class="alert alert-warning center_align">Veuillez renseigner au moins un champ avant d\'effectuer une recherche.</p>');
        }
        return false;
    });
    $("#form_recherche_lettre_cle").submit(function () {
        let code        = $("#code_input").val().trim(),
            nom         = $("#nom_input").val().trim();
        if(code || nom) {
            $("#btn_rechercher")
                .prop('disabled',true)
                .removeClass('btn-primary')
                .addClass('btn-warning')
                .html('...');
            $("#div_resultats_recherche").html('<p class="center_align"><img src="../../_publics/images/loading_white.gif" /></p>');
            $.ajax({
                url: '../../_configs/Includes/Searches/Parametres/Referentiels/search_lettres_cles.php',
                type: 'POST',
                data: {
                    'code': code,
                    'nom': nom
                },
                success: function (data){
                    $("#btn_rechercher")
                        .prop('disabled',false)
                        .removeClass('btn-warning')
                        .addClass('btn-primary')
                        .html('<i class="fa fa-search"></i>');
                    $("#div_resultats_recherche").html(data);
                }
            });
        }else {
            $("#div_resultats_recherche").html('<p class="alert alert-warning center_align">Veuillez renseigner au moins un champ avant d\'effectuer une recherche.</p>');
        }
        return false;
    });
    $("#form_recherche_acte_medical").submit(function () {
        let code        = $("#code_input").val().trim(),
            nom         = $("#nom_input").val().trim(),
            code_lettre = $("#code_lettre_cle_input").val().trim();
        if(code || nom || code_lettre) {
            $("#btn_rechercher")
                .prop('disabled',true)
                .removeClass('btn-primary')
                .addClass('btn-warning')
                .html('...');
            $("#div_resultats_recherche").html('<p class="center_align"><img src="../../_publics/images/loading_white.gif" /></p>');
            $.ajax({
                url: '../../_configs/Includes/Searches/Parametres/Referentiels/search_actes_medicaux.php',
                type: 'POST',
                data: {
                    'code': code,
                    'nom': nom,
                    'code_lettre': code_lettre
                },
                success: function (data){
                    $("#btn_rechercher")
                        .prop('disabled',false)
                        .removeClass('btn-warning')
                        .addClass('btn-primary')
                        .html('<i class="fa fa-search"></i>');
                    $("#div_resultats_recherche").html(data);
                }
            });
        }else {
            $("#div_resultats_recherche").html('<p class="alert alert-warning center_align">Veuillez renseigner au moins un champ avant d\'effectuer une recherche.</p>');
        }
        return false;
    });
});