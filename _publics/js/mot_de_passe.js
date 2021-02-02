jQuery(function () {

    $("#nouveau_mot_de_passe_input").keyup(function () {
        var mot_de_passe = $(this).val().trim();
        $("#confirmer_nouveau_mot_de_passe_input").val('');
        passwordChecker(mot_de_passe);
    });

    $("#confirmer_nouveau_mot_de_passe_input").keyup(function () {
        var mot_de_passe = $(this).val().trim(),
            actuel_mot_de_passe = $("#nouveau_mot_de_passe_input").val().trim();
        if(mot_de_passe != actuel_mot_de_passe) {
            $("#confirmer_nouveau_mot_de_passe_small").addClass('text-danger');
            $("#confirmer_nouveau_mot_de_passe_small").html('<i class="fa fa-dot-circle"></i> Les 2 mots de passe doivent être identiques.');
        }else {
            $("#confirmer_nouveau_mot_de_passe_small").html('');
        }
    });


    $("#form_mot_de_passe").submit(function () {
        var actuel_mot_de_passe = $("#actuel_mot_de_passe_input").val().trim(),
            nouveau_mot_de_passe = $("#nouveau_mot_de_passe_input").val().trim(),
            confirmer_nouveau_mot_de_passe = $("#confirmer_nouveau_mot_de_passe_input").val().trim();


        if (!actuel_mot_de_passe) {
            $("#actuel_mot_de_passe_input").focus();
            $("#actuel_mot_de_passe_input").removeClass('is-valid');
            $("#actuel_mot_de_passe_input").addClass('is-invalid');
            $("#actuel_mot_de_passe_small").removeClass('valid-feedback');
            $("#actuel_mot_de_passe_small").addClass('invalid-feedback');
            $("#actuel_mot_de_passe_small").html('Veuillez saisir SVP votre actuel mot de passe.');
        }else {
            if (actuel_mot_de_passe == nouveau_mot_de_passe) {
                $("#nouveau_mot_de_passe_input").focus();
                $("#nouveau_mot_de_passe_input").removeClass('is-valid');
                $("#nouveau_mot_de_passe_input").addClass('is-invalid');
                $("#nouveau_mot_de_passe_small").removeClass('valid-feedback');
                $("#nouveau_mot_de_passe_small").addClass('invalid-feedback');
                $("#nouveau_mot_de_passe_small").html('L\'actuel mot de passe et ne nouveau ne doivent pas être identiques.');
            }else {
                if (nouveau_mot_de_passe.length < 8) {
                    $("#nouveau_mot_de_passe_input").focus();
                    $("#nouveau_mot_de_passe_input").removeClass('is-valid');
                    $("#nouveau_mot_de_passe_input").addClass('is-invalid');
                    $("#nouveau_mot_de_passe_small").removeClass('valid-feedback');
                    $("#nouveau_mot_de_passe_small").addClass('invalid-feedback');
                    $("#nouveau_mot_de_passe_small").html('Le nouveau mot de passe doit contenir huit (8) caractères au mois.');
                }else {
                    if(nouveau_mot_de_passe != confirmer_nouveau_mot_de_passe) {
                        $("#nouveau_mot_de_passe_input").removeClass('is-valid');
                        $("#nouveau_mot_de_passe_input").addClass('is-invalid');
                        $("#nouveau_mot_de_passe_small").removeClass('valid-feedback');
                        $("#nouveau_mot_de_passe_small").addClass('invalid-feedback');
                        $("#nouveau_mot_de_passe_small").html('Les deux (2) nouveaux mots de passe doivent être identiques.');

                        $("#confirmer_nouveau_mot_de_passe_input").removeClass('is-valid');
                        $("#confirmer_nouveau_mot_de_passe_input").addClass('is-invalid');
                        $("#confirmer_nouveau_mot_de_passe_small").removeClass('valid-feedback');
                        $("#confirmer_nouveau_mot_de_passe_small").addClass('invalid-feedback');
                        $("#confirmer_nouveau_mot_de_passe_small").html('Les deux (2) nouveaux mots de passe doivent être identiques.');
                    }else {
                        $("#actuel_mot_de_passe_input").removeClass('is-invalid');
                        $("#actuel_mot_de_passe_small").removeClass('is-invalid');
                        $("#actuel_mot_de_passe_input").addClass('is-valid');
                        $("#nouveau_mot_de_passe_input").removeClass('is-invalid');
                        $("#nouveau_mot_de_passe_small").removeClass('is-invalid');
                        $("#nouveau_mot_de_passe_input").addClass('is-valid');
                        $("#confirmer_nouveau_mot_de_passe_input").removeClass('is-invalid');
                        $("#confirmer_nouveau_mot_de_passe_small").removeClass('is-invalid');
                        $("#confirmer_nouveau_mot_de_passe_input").addClass('is-valid');


                        $("#button_mot_de_passe").prop('disabled', true);
                        $("#button_mot_de_passe").removeClass('btn-primary');
                        $("#button_mot_de_passe").addClass('btn-warning');
                        $("#button_mot_de_passe").html('<img width="100" src="_publics/images/loading_white.gif" />');

                        $.ajax({
                            url: '_configs/Includes/Submits/submit_maj_mot_de_passe.php',
                            type: 'POST',
                            data: {
                                'actuel_mot_de_passe': actuel_mot_de_passe,
                                'nouveau_mot_de_passe': nouveau_mot_de_passe,
                                'confirmer_nouveau_mot_de_passe': confirmer_nouveau_mot_de_passe
                            },
                            dataType: 'json',
                            success: function (data) {
                                $("#button_mot_de_passe").prop('disabled', false);
                                $("#button_mot_de_passe").removeClass('btn-warning');
                                $("#button_mot_de_passe").addClass('btn-info');
                                $("#button_mot_de_passe").html('<i class="fa fa-check"></i> Valider');

                                if(data['status'] === true) {
                                    $("#form_mot_de_passe").hide();
                                    $("#p_results").removeClass('text-danger');
                                    $("#p_results").addClass('text-success');
                                    $("#p_results").html(data['message']);
                                    setTimeout(function(){
                                        window.location.href="";
                                    }, 5000);
                                }else {
                                    $("#p_results").removeClass('text-success');
                                    $("#p_results").addClass('text-danger');
                                    $("#p_results").html(data['message']);
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