jQuery(function () {
    $("#form_imports").submit(function (e) {
        $("#div_resultats").fadeOut().html('');
        e.preventDefault();
        let formData = new FormData(this);
        $("#btn_charger").prop('disabled',true)
            .removeClass('btn-danger')
            .addClass('btn-warning')
            .html('<i>Chargement...</i>');
        $.ajax({
            url: "../_configs/Includes/Submits/Ogd/Imports/submit_chargement.php",
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

    $("#form_exports").submit(function () {
        let type_ref    = $("#type_ref_input").val().trim(),
            num_fichier = $("#num_fichier_input").val().trim();

        if(type_ref && num_fichier) {
            $("#btn_charger").prop('disabled',true)
                .removeClass('btn-danger')
                .addClass('btn-warning')
                .html('<i>Traitement...</i>');
            $.ajax({
                url: "../_configs/Includes/Submits/Ogd/Exports/submit_chargement.php",
                type: "POST",
                data: {
                    'type_ref': type_ref,
                    'num_fichier': num_fichier
                },
                dataType: 'json',
                success: function (data) {
                    $("#btn_charger").prop('disabled',false)
                        .removeClass('btn-warning')
                        .addClass('btn-danger')
                        .html('<i class="fa fa-download"></i> Télécharger');
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
        }
        return false;
    });


    $("#type_ref_input").change(function () {
        let type_fichier = $(this).val();
        if(type_fichier && (type_fichier === 'DECLIQ' || type_fichier === 'DECPAI' || type_fichier === 'REJETSO' || type_fichier === 'REJETSC' || type_fichier === 'TDM_EDT_009E' || type_fichier === 'BPAIEMENTS')) {
            $("#btn_charger").prop('disabled',true)
                .removeClass('btn-danger')
                .addClass('btn-warning')
                .html('<i>Recherche...</i>');
            $("#num_fichier_input").prop('disabled',false)
                .empty();
            $.ajax({
                url: '../_configs/Includes/Searches/Ogd/search_fichiers_a_telecharger.php',
                type: 'post',
                data: {
                    'type_fichier': type_fichier
                },
                dataType: 'json',
                success: function(json) {
                    $("#btn_charger").prop('disabled',false)
                        .removeClass('btn-warning')
                        .addClass('btn-danger')
                        .html('<i class="fa fa-upload"></i> Télécharger');
                    if(type_fichier === 'DECLIQ') {
                        $("#num_fichier_input").append('<option value="">Sélectionnez le DECA</option>');
                    }else if (type_fichier === 'DECPAI' || type_fichier === 'REJETSC') {
                        $("#num_fichier_input").append('<option value="">Sélectionnez le DECRET</option>');
                    }else if (type_fichier === 'TDM_EDT_009E') {
                        $("#num_fichier_input").append('<option value="">Sélectionnez le DECPAI</option>');
                    }else if (type_fichier === 'BPAIEMENTS') {
                        $("#num_fichier_input").append('<option value="">Sélectionnez le DECPAI</option>');
                    }else if (type_fichier === 'REJETSO') {
                        $("#num_fichier_input").append('<option value="">Sélectionnez le DECLIQ</option>');
                    }else {
                        $("#num_fichier_input").prop('disabled',true)
                            .append('<option value="">Sélectionnez un fichier</option>');
                    }

                    $.each(json, function(index, value) {
                        $("#num_fichier_input").append('<option value="'+ index +'">'+ value +'</option>');
                    });
                }
            });
        }
    });


});