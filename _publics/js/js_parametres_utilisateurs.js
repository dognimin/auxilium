jQuery(function () {
    $("#btn_maj_mot_de_passe").click(function () {
        $("#div_mot_de_passe").slideDown();
        $("#div_edition").hide();
        $("#div_habilitation").hide();
        return false;
    });
    $("#btn_edition").click(function () {
        $("#div_edition").slideDown();
        $("#div_mot_de_passe").hide();
        $("#div_habilitation").hide();
        return false;
    });
    $("#btn_habilitation").click(function () {
        $("#div_habilitation").slideDown();
        $("#div_mot_de_passe").hide();
        $("#div_edition").hide();
        return false;
    });

    $("#form_utilisateur").submit(function () {
        let code_ogd        = $("#code_ogd_input").val().trim(),
            code_statut     = $("#code_statut_input").val().trim(),
            user_id         = $("#user_id_input").val().trim(),
            email           = $("#email_input").val().trim().toLowerCase(),
            username        = $("#username_input").val().trim().toLowerCase(),
            firstname       = $("#firstname_input").val().trim().toUpperCase(),
            lastname        = $("#lastname_input").val().trim().toUpperCase(),
            direction       = $("#direction_input").val().trim().toUpperCase(),
            service         = $("#service_input").val().trim().toUpperCase(),
            fonction        = $("#fonction_input").val().trim().toUpperCase(),
            num_telephone   = $("#num_telephone_input").val().trim();

        if(code_statut && email && username && firstname && lastname && num_telephone) {
            $("#button_utilisateur")
                .prop('disabled', true)
                .removeClass('btn-primary')
                .addClass('btn-warning')
                .html('<img width="100" src="../../_publics/images/loading_white.gif" />');
            $.ajax({
                url: '../../_configs/Includes/Submits/Parametres/Utilisateurs/submit_utilisateur.php',
                type: 'POST',
                data: {
                    'code_ogd': code_ogd,
                    'code_statut': code_statut,
                    'user_id': user_id,
                    'email': email,
                    'username': username,
                    'firstname': firstname,
                    'lastname': lastname,
                    'direction': direction,
                    'service': service,
                    'fonction': fonction,
                    'num_telephone': num_telephone
                },
                dataType: 'json',
                success: function (data) {
                    $("#button_utilisateur")
                        .prop('disabled', false)
                        .removeClass('btn-warning')
                        .addClass('btn-primary')
                        .html('<i class="fa fa-save"></i> Enregistrer');
                    if(data['success'] === true) {
                        $("#form_utilisateur").hide();
                        $("#p_utilisateur").removeClass('text-danger')
                            .addClass('text-success')
                            .html(data['message']);
                        setTimeout(function(){
                            window.location.href="";
                        }, 3000);
                    }else {
                        $("#p_utilisateur").removeClass('text-success')
                            .addClass('text-danger')
                            .html(data['message']);
                    }
                }
            });
        }
        return false;
    });

    $("#nouveau_mot_de_passe_input").keyup(function () {
        let mot_de_passe = $(this).val().trim();
        $("#confirmer_nouveau_mot_de_passe_input").val('');
        passwordChecker(mot_de_passe);
    });

    $("#confirmer_nouveau_mot_de_passe_input").keyup(function () {
        let mot_de_passe = $(this).val().trim(),
            actuel_mot_de_passe = $("#nouveau_mot_de_passe_input").val().trim();
        if(mot_de_passe !== actuel_mot_de_passe) {
            $("#confirmer_nouveau_mot_de_passe_small").addClass('text-danger')
                .html('<i class="fa fa-dot-circle"></i> Les 2 mots de passe doivent être identiques.');
        }else {
            $("#confirmer_nouveau_mot_de_passe_small").html('');
        }
    });

    $("#form_mot_de_passe").submit(function () {
        let actuel_mot_de_passe = $("#actuel_mot_de_passe_input").val().trim(),
            nouveau_mot_de_passe = $("#nouveau_mot_de_passe_input").val().trim(),
            confirmer_nouveau_mot_de_passe = $("#confirmer_nouveau_mot_de_passe_input").val().trim();


        if (!actuel_mot_de_passe) {
            $("#actuel_mot_de_passe_input").focus()
                .removeClass('is-valid')
                .addClass('is-invalid');
            $("#actuel_mot_de_passe_small").removeClass('valid-feedback')
                .addClass('invalid-feedback')
                .html('Veuillez saisir SVP votre actuel mot de passe.');
        }else {
            if (actuel_mot_de_passe === nouveau_mot_de_passe) {
                $("#nouveau_mot_de_passe_input").focus()
                    .removeClass('is-valid')
                    .addClass('is-invalid');
                $("#nouveau_mot_de_passe_small").removeClass('valid-feedback')
                    .addClass('invalid-feedback')
                    .html('L\'actuel mot de passe et ne nouveau ne doivent pas être identiques.');
            }else {
                if (nouveau_mot_de_passe.length < 8) {
                    $("#nouveau_mot_de_passe_input").focus()
                        .removeClass('is-valid')
                        .addClass('is-invalid');
                    $("#nouveau_mot_de_passe_small").removeClass('valid-feedback')
                        .addClass('invalid-feedback')
                        .html('Le nouveau mot de passe doit contenir huit (8) caractères au mois.');
                }else {
                    if(nouveau_mot_de_passe !== confirmer_nouveau_mot_de_passe) {
                        $("#nouveau_mot_de_passe_input").removeClass('is-valid')
                            .addClass('is-invalid');
                        $("#nouveau_mot_de_passe_small").removeClass('valid-feedback')
                            .addClass('invalid-feedback')
                            .html('Les deux (2) nouveaux mots de passe doivent être identiques.');

                        $("#confirmer_nouveau_mot_de_passe_input").removeClass('is-valid')
                            .addClass('is-invalid');
                        $("#confirmer_nouveau_mot_de_passe_small").removeClass('valid-feedback')
                            .addClass('invalid-feedback')
                            .html('Les deux (2) nouveaux mots de passe doivent être identiques.');
                    }else {
                        $("#actuel_mot_de_passe_small").removeClass('is-invalid');
                        $("#actuel_mot_de_passe_input").removeClass('is-invalid')
                            .addClass('is-valid');

                        $("#nouveau_mot_de_passe_small").removeClass('is-invalid');
                        $("#nouveau_mot_de_passe_input").removeClass('is-invalid')
                            .addClass('is-valid');


                        $("#confirmer_nouveau_mot_de_passe_small").removeClass('is-invalid');
                        $("#confirmer_nouveau_mot_de_passe_input").removeClass('is-invalid')
                            .addClass('is-valid');

                        $("#button_mot_de_passe").prop('disabled', true)
                            .removeClass('btn-primary')
                            .addClass('btn-warning')
                            .html('<img alt="Chargement..." width="100" src="_publics/images/loading_white.gif" />');

                        $.ajax({
                            url: '../../_configs/Includes/Submits/Parametres/Utilisateurs/submit_maj_mot_de_passe.php',
                            type: 'POST',
                            data: {
                                'actuel_mot_de_passe': actuel_mot_de_passe,
                                'nouveau_mot_de_passe': nouveau_mot_de_passe,
                                'confirmer_nouveau_mot_de_passe': confirmer_nouveau_mot_de_passe
                            },
                            dataType: 'json',
                            success: function (data) {
                                $("#button_mot_de_passe").prop('disabled', false)
                                    .removeClass('btn-warning')
                                    .addClass('btn-info')
                                    .html('<i class="fa fa-check"></i> Valider');

                                if(data['success'] === true) {
                                    $("#form_mot_de_passe").hide();
                                    $("#p_results").removeClass('text-danger')
                                        .addClass('text-success')
                                        .html(data['message']);
                                    setTimeout(function(){
                                        window.location.href="";
                                    }, 5000);
                                }else {
                                    $("#p_results").removeClass('text-success')
                                        .addClass('text-danger')
                                        .html(data['message']);
                                }
                            }
                        });


                    }
                }
            }
        }
        return false;
    });

    $("#form_habilitation").submit(function () {
        let chargements_affichage,
            chargements_edition,
            chargements_imports_affichage,
            chargements_imports_edition,
            chargements_exports_affichage,
            chargements_exports_edition,
            populations_affichage,
            populations_edition,
            factures_affichage,
            factures_edition,
            factures_recherche_affichage,
            factures_recherche_edition,
            factures_verification_affichage,
            factures_verification_edition,
            factures_liquidation_affichage,
            factures_liquidation_edition,
            factures_details_affichage,
            factures_details_edition,
            statistiques_affichage,
            statistiques_edition,
            parametres_affichage,
            parametres_edition,
            parametres_utilisateurs_affichage,
            parametres_utilisateurs_edition,
            parametres_referentiels_affichage,
            parametres_referentiels_edition,
            parametres_scripts_affichage,
            parametres_scripts_edition,
            user_id = $("#user_id_input").val().trim();
        if($("#chargements_affichage_input").is(":checked")) {chargements_affichage = $("#chargements_affichage_input").val();}else{chargements_affichage = '';}
        if($("#chargements_edition_input").is(":checked")) {chargements_edition = $("#chargements_edition_input").val();}else{chargements_edition = '';}

        if($("#chargements_imports_affichage_input").is(":checked")) {chargements_imports_affichage = $("#chargements_imports_affichage_input").val();}else{chargements_imports_affichage = '';}
        if($("#chargements_imports_edition_input").is(":checked")) {chargements_imports_edition = $("#chargements_imports_edition_input").val();}else{chargements_imports_edition = '';}

        if($("#chargements_exports_affichage_input").is(":checked")) {chargements_exports_affichage = $("#chargements_exports_affichage_input").val();}else{chargements_exports_affichage = '';}
        if($("#chargements_exports_edition_input").is(":checked")) {chargements_exports_edition = $("#chargements_exports_edition_input").val();}else{chargements_exports_edition = '';}

        if($("#populations_affichage_input").is(":checked")) {populations_affichage = $("#populations_affichage_input").val();}else{populations_affichage = '';}
        if($("#populations_edition_input").is(":checked")) {populations_edition = $("#populations_edition_input").val();}else{populations_edition = '';}

        if($("#factures_affichage_input").is(":checked")) {factures_affichage = $("#factures_affichage_input").val();}else{factures_affichage = '';}
        if($("#factures_edition_input").is(":checked")) {factures_edition = $("#factures_edition_input").val();}else{factures_edition = '';}

        if($("#factures_recherche_affichage_input").is(":checked")) {factures_recherche_affichage = $("#factures_recherche_affichage_input").val();}else{factures_recherche_affichage = '';}
        if($("#factures_recherche_edition_input").is(":checked")) {factures_recherche_edition = $("#factures_recherche_edition_input").val();}else{factures_recherche_edition = '';}

        if($("#factures_verification_affichage_input").is(":checked")) {factures_verification_affichage = $("#factures_verification_affichage_input").val();}else{factures_verification_affichage = '';}
        if($("#factures_verification_edition_input").is(":checked")) {factures_verification_edition = $("#factures_verification_edition_input").val();}else{factures_verification_edition = '';}

        if($("#factures_liquidation_affichage_input").is(":checked")) {factures_liquidation_affichage = $("#factures_liquidation_affichage_input").val();}else{factures_liquidation_affichage = '';}
        if($("#factures_liquidation_edition_input").is(":checked")) {factures_liquidation_edition = $("#factures_liquidation_edition_input").val();}else{factures_liquidation_edition = '';}

        if($("#factures_details_affichage_input").is(":checked")) {factures_details_affichage = $("#factures_details_affichage_input").val();}else{factures_details_affichage = '';}
        if($("#factures_details_edition_input").is(":checked")) {factures_details_edition = $("#factures_details_edition_input").val();}else{factures_details_edition = '';}

        if($("#statistiques_affichage_input").is(":checked")) {statistiques_affichage = $("#statistiques_affichage_input").val();}else{statistiques_affichage = '';}
        if($("#statistiques_edition_input").is(":checked")) {statistiques_edition = $("#statistiques_edition_input").val();}else{statistiques_edition = '';}

        if($("#parametres_affichage_input").is(":checked")) {parametres_affichage = $("#parametres_affichage_input").val();}else{parametres_affichage = '';}
        if($("#parametres_edition_input").is(":checked")) {parametres_edition = $("#parametres_edition_input").val();}else{parametres_edition = '';}

        if($("#parametres_utilisateurs_affichage_input").is(":checked")) {parametres_utilisateurs_affichage = $("#parametres_utilisateurs_affichage_input").val();}else{parametres_utilisateurs_affichage = '';}
        if($("#parametres_utilisateurs_edition_input").is(":checked")) {parametres_utilisateurs_edition = $("#parametres_utilisateurs_edition_input").val();}else{parametres_utilisateurs_edition = '';}

        if($("#parametres_referentiels_affichage_input").is(":checked")) {parametres_referentiels_affichage = $("#parametres_referentiels_affichage_input").val();}else{parametres_referentiels_affichage = '';}
        if($("#parametres_referentiels_edition_input").is(":checked")) {parametres_referentiels_edition = $("#parametres_referentiels_edition_input").val();}else{parametres_referentiels_edition = '';}

        if($("#parametres_scripts_affichage_input").is(":checked")) {parametres_scripts_affichage = $("#parametres_scripts_affichage_input").val();}else{parametres_scripts_affichage = '';}
        if($("#parametres_scripts_edition_input").is(":checked")) {parametres_scripts_edition = $("#parametres_scripts_edition_input").val();}else{parametres_scripts_edition = '';}

        let modules = chargements_affichage+chargements_edition
            +populations_affichage+populations_edition
            +factures_affichage+factures_edition
            +statistiques_affichage+statistiques_edition
            +parametres_affichage+parametres_edition,

            sous_modules = chargements_imports_affichage+chargements_imports_edition+chargements_exports_affichage+chargements_exports_edition
                +factures_recherche_affichage+factures_recherche_edition+factures_verification_affichage+factures_verification_edition+factures_liquidation_affichage+factures_liquidation_edition+factures_details_affichage+factures_details_edition
                +parametres_utilisateurs_affichage+parametres_utilisateurs_edition+parametres_referentiels_affichage+parametres_referentiels_edition+parametres_scripts_affichage+parametres_scripts_edition;


        if(modules && sous_modules && user_id) {
            $("#button_habilitations").prop('disabled',true)
                .removeClass('btn-info')
                .addClass('btn-warning')
                .html('Traiement...');
            $.ajax({
                url: '../../_configs/Includes/Submits/Parametres/Utilisateurs/submit_habilitations.php',
                type: 'POST',
                data: {
                    'modules': modules,
                    'sous_modules': sous_modules,
                    'user_id': user_id
                },
                dataType: 'json',
                success: function (data) {
                    if(data['success'] === true) {
                        $("#button_habilitations").prop('disabled',false)
                            .removeClass('btn-warning')
                            .addClass('btn-info')
                            .html('<i class="fa fa-check"></i> Valider');
                        $("#form_habilitation").hide();
                        $("#p_results_habilitations").removeClass('text-danger')
                            .addClass('text-success')
                            .html(data['message']);
                        setTimeout(function(){
                            window.location.reload();
                        }, 2000);
                    }else {
                        $("#p_results_habilitations").removeClass('text-success')
                            .addClass('text-danger')
                            .html(data['message']);
                    }
                }
            });
        }

        return false;
    });

    $("#form_activation_desactivation").submit(function () {
        let user_id = $("#user_actif_inactif_input").val().trim();
        if(user_id) {
            $("#btn_activer_desactiver").prop('disabled',true)
                .removeClass('btn-info')
                .addClass('btn-warning')
                .html('Traiement...');
            $.ajax({
                url: '../../_configs/Includes/Submits/Parametres/Utilisateurs/submit_activation_desactivation.php',
                type: 'POST',
                data: {
                    'user_id': user_id
                },
                dataType: 'json',
                success: function (data) {
                    if(data['success'] === true) {
                        $("#btn_activer_desactiver").prop('disabled',false)
                            .removeClass('btn-warning')
                            .addClass('btn-info')
                            .html('<i class="fa fa-check"></i> Valider');
                        $("#form_activation_desactivation").hide();
                        $("#p_activation_desactivation").removeClass('text-danger')
                            .addClass('text-success')
                            .html(data['message']);
                        setTimeout(function(){
                            window.location.reload();
                        }, 2000);
                    }else {
                        $("#p_activation_desactivation").removeClass('text-success')
                            .addClass('text-danger')
                            .html(data['message']);
                    }
                }
            });
        }
        return false;
    });

    $("#form_reinitialisation_mot_de_passe").submit(function () {
        let user_id = $("#user_password_reset_input").val().trim();
        if(user_id) {
            $("#btn_reset_password").prop('disabled',true)
                .removeClass('btn-info')
                .addClass('btn-warning')
                .html('Traiement...');
            $.ajax({
                url: '../../_configs/Includes/Submits/Parametres/Utilisateurs/submit_reinitialisation_mot_de_passe.php',
                type: 'POST',
                data: {
                    'user_id': user_id
                },
                dataType: 'json',
                success: function (data) {
                    if(data['success'] === true) {
                        $("#btn_reset_password").prop('disabled',false)
                            .removeClass('btn-warning')
                            .addClass('btn-info')
                            .html('<i class="fa fa-check"></i> Valider');
                        $("#form_reinitialisation_mot_de_passe").hide();
                        $("#p_reinitialisation_mot_de_passe").removeClass('text-danger')
                            .addClass('text-success')
                            .html(data['message']);
                        setTimeout(function(){
                            window.location.reload();
                        }, 2000);
                    }else {
                        $("#p_reinitialisation_mot_de_passe").removeClass('text-success')
                            .addClass('text-danger')
                            .html(data['message']);
                    }
                }
            });
        }
        return false;
    });
});