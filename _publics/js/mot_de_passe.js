jQuery(function () {

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
                            url: '_configs/Includes/Submits/Parametres/Utilisateurs/submit_maj_mot_de_passe.php',
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

                                if(data['status'] === true) {
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
});