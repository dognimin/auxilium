jQuery(function () {
    let path_image_deep_1 = '../_publics/images/loading_blue.gif';
    $("#num_fichier_input").change(function () {

        let norme = $("#norme_input").val(),
            num_fichier = $("#num_fichier_input").val(),
            code_statut = $("#code_statut_input").val();

        display_ogd_factures_a_verifier_ou_liquider(norme,num_fichier,'','',code_statut);

        $("#code_ets_input").empty();
        $.ajax({
            url: '../_configs/Includes/Searches/Ogd/search_fichiers_ets.php',
            type: 'post',
            data: {
                'norme': norme,
                'num_fichier': num_fichier,
                'statut': code_statut
            },
            dataType: 'json',
            success: function(json) {
                $("#code_ets_input").append('<option value="">Sélectionnez le centre de santé</option>');
                $.each(json, function(index, value) {
                    $("#code_ets_input").append('<option value="'+ index +'">'+ value +'</option>');
                });
            }
        });
    });

    $("#code_ets_input").change(function () {
        let norme = $("#norme_input").val(),
            num_fichier = $("#num_fichier_input").val(),
            code_statut = $("#code_statut_input").val(),
            code_ets = $("#code_ets_input").val();
        display_ogd_factures_a_verifier_ou_liquider(norme,num_fichier,'',code_ets,code_statut);
    });

    $("#form_recherche_factures_a_liquider").submit(function () {
        let num_fichier      = $("#num_fichier_input").val(),
            num_facture      = $("#num_facture_input").val(),
            code_ets         = $("#code_ets_input").val(),
            code_statut      = $("#code_statut_input").val();
        if(num_fichier || code_ets || num_facture) {
            $("#div_resultats_recherche").html('<p class="center_align"><img alt="Chjargement..." src="'+path_image_deep_1+'" width="100" /></p>');
            $("#btn_rechercher").prop('disabled',true)
                .removeClass('btn-primary')
                .addClass('btn-warning')
                .html('...');
            $.ajax({
                url: '../_configs/Includes/Searches/Ogd/search_factures_a_liquider.php',
                type: 'POST',
                data: {
                    'norme': 'DECA',
                    'num_fichier': num_fichier,
                    'num_facture': num_facture,
                    'code_ets': code_ets,
                    'statut': code_statut
                },
                success: function (dataRecherche) {
                    $("#btn_rechercher").prop('disabled',false)
                        .removeClass('btn-warning')
                        .addClass('btn-primary')
                        .html('<b class="fa fa-search"></b>');
                    $("#div_resultats_recherche").html(dataRecherche);
                }
            });
        }
        return false;
    });

    $("#form_recherche_factures").submit(function () {
        let date_debut  = $("#date_debut_input").val(),
            date_fin    = $("#date_fin_input").val(),
            num_facture = $("#num_facture_input").val(),
            code_ets    = $("#code_etablissement_input").val(),
            code_statut = $("#code_statut_input").val();
        if(date_debut && date_fin) {
            $("#div_resultats_recherche").html('<p class="center_align"><img alt="Chargement..." src="'+path_image_deep_1+'" width="100" /></p>');
            $("#btn_rechercher").prop('disabled',true)
                .removeClass('btn-primary')
                .addClass('btn-warning')
                .html('...');

            $.ajax({
                url: '../_configs/Includes/Searches/Ogd/search_factures.php',
                type: 'POST',
                data: {
                    'date_debut': date_debut,
                    'date_fin': date_fin,
                    'num_facture': num_facture,
                    'code_ets': code_ets,
                    'code_statut': code_statut
                },
                success: function (data) {
                    $("#btn_rechercher").prop('disabled',false)
                        .removeClass('btn-warning')
                        .addClass('btn-primary')
                        .html('<b class="fa fa-search"></b>');
                    $("#div_resultats_recherche").html(data);
                }
            });
        }
        return false;
    });

    $("#form_valider_facture").submit(function () {
        let facture_valide_infos    = $("#facture_valide_infos").val(),
            tableau                 = facture_valide_infos.split('_'),
            num_facture             = tableau[0],
            statut                  = tableau[1];

        if(num_facture && statut) {
            $("#p_results_valider_facture").show();
            $("#p_valider_facture").hide();
            $("#btn_valider_facture").prop('disabled',true)
                .removeClass('btn-primary')
                .addClass('btn-warning')
                .html('<i>Traitement...</i>');
            $.ajax({
                url: '../_configs/Includes/Submits/Ogd/Factures/submit_facture_validee.php',
                type: 'POST',
                data: {
                    'num_facture': num_facture,
                    'statut': statut
                },
                dataType: 'json',
                success: function (data) {

                    $("#btn_valider_facture").prop('disabled',false)
                        .removeClass('btn-warning')
                        .addClass('btn-success')
                        .html('<i class="fa fa-check-circle"></i> Valider');
                    if(data['success'] === true) {
                        $("#form_valider_facture").hide();
                        $("#p_results_valider_facture").removeClass('text-danger')
                            .addClass('text-success')
                            .html(data['message']);
                        setTimeout(function(){
                            window.location.href="facture?num="+num_facture;
                        }, 3000);
                    }else {
                        $("#p_results_valider_facture").removeClass('text-success')
                            .addClass('text-danger')
                            .html(data['message']);
                    }
                }
            });
        }
        return false;
    });
    
    $("#form_refuser_facture").submit(function () {
        let facture_refusee_infos   = $("#facture_refusee_infos").val(),
            tableau                 = facture_refusee_infos.split('_'),
            num_facture             = tableau[0],
            statut                  = tableau[1],
            occurrences             = $("#occurrences_input").val().trim();

        for(let i = 0; i < occurrences; i++) {
            let code_acte   = $('#code_acte_rejet_'+i+'_input').val().trim(),
                motif_rejet = $('#code_motif_rejet_'+i+'_input').val().trim();

            if(num_facture && code_acte && motif_rejet && statut) {
                $("#p_results_refuser_facture").show();
                $("#btn_refuser_facture").prop('disabled',true)
                    .removeClass('btn-primary')
                    .addClass('btn-warning')
                    .html('<i>Traitement...</i>');
                $.ajax({
                    url: '../_configs/Includes/Submits/Ogd/Factures/submit_facture_refusee.php',
                    type: 'POST',
                    data: {
                        'num_facture': num_facture,
                        'code_acte': code_acte,
                        'motif_rejet': motif_rejet,
                        'statut': statut
                    },
                    dataType: 'json',
                    success: function (data) {
                        $("#btn_refuser_facture").prop('disabled',false)
                            .removeClass('btn-warning')
                            .addClass('btn-success')
                            .html('<i class="fa fa-check-circle"></i> Valider');
                        if(data['success'] === true) {
                            $("#form_refuser_facture").hide();
                            $("#p_results_refuser_facture").removeClass('text-danger')
                                .addClass('text-success')
                                .html(data['message']);
                            setTimeout(function(){
                                window.location.href="facture?num="+num_facture;
                            }, 3000);
                        }else {
                            $("#p_results_refuser_facture").removeClass('text-success')
                                .addClass('text-danger')
                                .html(data['message']);
                        }
                    }
                });
            }
        }
        return false;
    });

    $("#form_editer_facture").submit(function () {
        let num_facture     = $("#facture_editee_infos").val().trim(),
            code_ps         = $ ("#code_ps_input").val().trim(),
            code_specialite = $("#code_specialite_input").val().trim(),
            code_pathologie = $("#code_pathologie_input").val().trim(),
            nb_actes = $(".code_acte_input").length,
            actes_trouves,
            actes_valides;
        if(num_facture && code_ps && code_specialite && code_pathologie) {
            $("#code_ps_input").focus().removeClass('is-invalid').addClass('is-valid');
            $("#code_specialite_input").focus().removeClass('is-invalid').addClass('is-valid');
            $("#code_pathologie_input").focus().removeClass('is-invalid').addClass('is-valid');
            if(nb_actes) {
                for (let n= 0; n < nb_actes; n++) {
                    let code_acte       = $("#code_acte_"+n+"_input").val().trim(),
                        date_debut      = $("#acte_date_debut_"+n+"_input").val().trim(),
                        date_fin        = $("#acte_date_fin_"+n+"_input").val().trim(),
                        prix_unitaire   = $("#acte_prix_unitiaire_"+n+"_input").val().trim(),
                        quantite        = $("#acte_quantite_"+n+"_input").val().trim();
                    if(code_acte && date_debut && date_fin && quantite) {
                        $("#btn_editer_facture").prop('disabled',true)
                            .removeClass('btn-success')
                            .addClass('btn-warning')
                            .html('<i>Traitement...</i>');
                        $.ajax({
                            url: '',
                            type: 'POST',
                            data: {
                                'num_facture': num_facture,
                                'code_ps': code_ps,
                                'code_specialite': code_specialite,
                                'code_pathologie': code_pathologie,
                                'code_acte': code_acte,
                                'date_debut': date_debut,
                                'date_fin': date_fin,
                                'prix_unitaire': prix_unitaire,
                                'quantite': quantite
                            },
                            dataType: 'json',
                            success: function (data) {
                                $("#btn_editer_facture").prop('disabled',false)
                                    .removeClass('btn-warning')
                                    .addClass('btn-success')
                                    .html('<i class="fa fa-check-circle"></i> Valider');
                                if(data['success'] === true) {
                                    $("#form_editer_facture").hide();
                                    $("#p_results_editer_facture").removeClass('text-danger')
                                        .addClass('text-success')
                                        .html(data['message']);
                                    setTimeout(function(){
                                        window.location.href="facture?num="+num_facture;
                                    }, 3000);
                                }else {
                                    $("#p_results_editer_facture").removeClass('text-success')
                                        .addClass('text-danger')
                                        .html(data['message']);
                                }
                            }
                        });
                    }
                }
            }
        }else {
            if(!num_facture) {

            }
            if(!code_ps) {
                $("#code_ps_input").focus()
                    .removeClass('is-valid')
                    .addClass('is-invalid');
                $("#code_ps_small").removeClass('valid-feedback')
                    .addClass('invalid-feedback')
                    .html('Veuillez renseigner le code du PS.');
            }
            if(!code_specialite) {
                $("#code_specialite_input").focus()
                    .removeClass('is-valid')
                    .addClass('is-invalid');
                $("#code_specialite_small").removeClass('valid-feedback')
                    .addClass('invalid-feedback')
                    .html('La spécialité du Ps n\'est pas renseigné. Veuillez vous référer aux référentiels');
            }
            if(!code_pathologie) {
                $("#code_pathologie_input").focus()
                    .removeClass('is-valid')
                    .addClass('is-invalid');
                $("#code_pathologie_small").removeClass('valid-feedback')
                    .addClass('invalid-feedback')
                    .html('Veuillez renseigner le code de la pathologie.');
            }
        }
        return false;
    });

    $("#date_soins_input").change(function () {
        $("#code_ps_input").val('');
            $("#nom_prenoms_ps_input").val('');
            $("#code_specialite_input").val('');
            $("#code_pathologie_input").val('');
            $("#libelle_pathologie_input").val('');
            $(".code_acte_input").val('');
            $(".libelle_acte_input").val('');
            $(".acte_date_debut_input").val('');
            $(".acte_date_fin_input").val('');
            $(".acte_prix_unitiaire").val('0');
            $(".acte_quantite_input").val('1');
    });
    
    $("#code_ps_input").keyup(function () {
        let code_ps         = $(this).val(),
            num_facture     = $("#facture_editee_infos").val().trim();
        $("#nom_prenoms_ps_input").val('');
        $("#code_specialite_input").val('');
        if(code_ps.length === 9) {

        }
    }).blur(function () {
        let code_ps = $(this).val();
        if(code_ps.length !== 9) {
            $("#code_ps_input").val('');
        }
    });

    $(".acte_quantite_input").change(function () {
        let acte_id = this.id,
            tab = acte_id.split('_'),
            id = tab[2],
            prix_unitiaire = $("#acte_prix_unitiaire_"+id+"_input").val(),
            quantite = $("#acte_quantite_"+id+"_input").val();
        $("#acte_prix_total_"+id+"_input").val(quantite * prix_unitiaire);
    });

    $(".acte_prix_unitiaire").keyup(function () {
        let acte_id = this.id,
            tab = acte_id.split('_'),
            id = tab[3],
            quantite = $("#acte_quantite_"+id+"_input").val(),
            prix_unitiaire = $("#acte_prix_unitiaire_"+id+"_input").val(),
            prix_total = 0;
        if(!prix_unitiaire) {
            $("#"+prix_unitiaire.id).val(0);
        }else {
            prix_total = prix_unitiaire * quantite;
        }
        $("#acte_prix_total_"+id+"_input").val(prix_total);

    });

    $( ".datepicker" ).datepicker({
        changeMonth: true,
        changeYear: true
    });
});