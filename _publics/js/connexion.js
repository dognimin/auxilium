jQuery(function () {

    AOS.init();
    $("#username_input").keyup(function () {
        let username = $(this).val().trim();
        if(username.length > 1) {
            $("#username_input").removeClass('is-valid').removeClass('is-invalid');
            $("#username_small").removeClass('valid-feedback').removeClass('invalid-feedback').html('');
        }
    });
    $("#password_input").keyup(function () {
        let password = $(this).val().trim();
        if(password.length > 1) {
            $("#password_input").removeClass('is-valid').removeClass('is-invalid');
            $("#password_small").removeClass('valid-feedback').removeClass('invalid-feedback').html('');
        }
    });
    $("#form_connexion").submit(function () {
        let username = $("#username_input").val().trim(),
            password = $("#password_input").val().trim();
        if(username && password) {
            $("#username_input").addClass('is-valid');
            $("#username_small").addClass('valid-feedback');
            $("#password_input").addClass('is-valid');
            $("#password_small").addClass('valid-feedback');

            $("#button_connexion").prop('disabled', true).removeClass('btn-primary').addClass('btn-warning').html('<img alt="" width="100" src="_publics/images/loading_white.gif" />');

            $.ajax({
                url: '_configs/Includes/Submits/submit_connexion.php',
                type: 'POST',
                data: {
                    'username': username,
                    'password': password
                },
                dataType: 'json',
                success: function (data) {
                    $("#button_connexion").prop('disabled', false).removeClass('btn-warning').addClass('btn-info').html('<i class="fa fa-check-circle"></i> Connexion');
                    if(data['status'] === true) {
                        $("#div_connexion").hide();
                        $("#p_login_results").removeClass('text-danger').addClass('text-success').html(data['message']);
                        setTimeout(function(){
                            window.location.href="";
                        }, 5000);
                    }else {
                        $("#p_login_results").removeClass('text-success').addClass('text-danger').html(data['message']);
                    }
                }
            });
        }else if (!username && !password) {
            $("#username_input").focus().addClass('is-invalid').addClass('invalid-feedback').html('Veuillez saisir SVP votre nom d\'utilisateur ou votre adresse Email');

            $("#password_input").addClass('is-invalid');
            $("#password_small").addClass('invalid-feedback').html('Veuillez saisir SVP votre mot de passe');
        } else if (!username && password) {
            $("#username_input").focus().addClass('is-invalid').addClass('invalid-feedback').html('Veuillez saisir SVP votre nom d\'utilisateur ou votre adresse Email');
        } else {
            $("#password_input").focus().addClass('is-invalid').addClass('invalid-feedback').html('Veuillez saisir SVP votre mot de passe');
        }
        return false;
    });

    $("#a_mot_de_passe_oublie").click(function () {
        $("#p_login_results").hide();
        $("#div_connexion").hide();
        $("#div_mot_de_passe_oublie").slideToggle();
        return false;
    });
    $("#a_connexion").click(function () {
        $("#p_login_results").hide();
        $("#div_mot_de_passe_oublie").hide();
        $("#div_connexion").slideToggle();
        return false;
    });
    $("#form_mot_de_passe_oublie").submit(function () {
        let email = $("#email_input").val().trim();
        if(email) {
            $("#p_login_results").hide();
            $("#button_mot_de_passe_oublie").prop('disabled', true).removeClass('btn-primary').addClass('btn-warning').html('<img width="100" src="_publics/images/loading_white.gif" />');
            $.ajax({
                url: '_configs/Includes/Submits/submit_email.php',
                type: 'POST',
                data: {
                    'email': email
                },
                dataType: 'json',
                success: function (data) {
                    $("#p_login_results").show();
                    $("#button_mot_de_passe_oublie").prop('disabled', false).removeClass('btn-warning').addClass('btn-info').html('<i class="fa fa-envelope"></i> Envoyer');
                    if(data['status'] === true) {
                        $("#form_mot_de_passe_oublie").hide();
                        $("#p_login_results").removeClass('text-danger').addClass('text-success').html(data['message']);
                    }else {
                        $("#p_login_results").removeClass('text-success').addClass('text-danger').html(data['message']);
                    }
                }
            });




        }else {
            $("#email_input").focus().addClass('is-invalid').addClass('invalid-feedback').html('Veuillez saisir SVP votre adresse Email afin de de recevoir vos identifiants de connexion.');
        }
        return false;
    });


    /**
     * Enregistrement d'un nouvel utilisateur
     */

    $("#num_telephone_input").inputFilter(function(value) {
        return /^\d*$/.test(value);    // Allow digits only, using a RegExp
    });

    $("#email_input").keyup(function () {
        let email = $(this).val().trim().toLowerCase(),
            username = $("#username_input").val().trim().toLowerCase();

        $("#email_input").removeClass('is(invalid').addClass('is(valid');
        $("#email_small").html('');
    }).blur(function () {
        let email = $(this).val().trim().toLowerCase(),
            username = $("#username_input").val().trim().toLowerCase();
        if(email) {
            $("#button_utilisateur").prop('disabled', true).removeClass('btn-primary').addClass('btn-warning').html('<img width="100" src="_publics/images/loading_white.gif" />');
            $.ajax({
                url: '_configs/Includes/Searches/search_username.php',
                type: 'POST',
                data: {
                    'email': email
                },
                dataType: 'json',
                success: function (data) {
                    $("#button_utilisateur").prop('disabled', false).removeClass('btn-warning').addClass('btn-primary').html('<i class="fa fa-save"></i> Enregistrer');

                    if(data['success'] === true) {
                        $("#email_input").removeClass('is-invalid').addClass('is-valid');
                        $("#email_small").html('');
                        if(!username) {
                            $('#username_input').val(data['username']);
                        }
                    }else {
                        $("#"+data['subject']+"_input").focus().val('').addClass('is-invalid').addClass('text-danger').html(data['message']);
                    }
                }
            });
        }
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
            $("#button_utilisateur").prop('disabled', true).removeClass('btn-primary').addClass('btn-warning').html('<img width="100" src="_publics/images/loading_white.gif" />');
            $.ajax({
                url: '_configs/Includes/Submits/Parametres/Utilisateurs/submit_utilisateur.php',
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

    /**
     * Fin de l'enregistrement de l'utilisateur
     */
});