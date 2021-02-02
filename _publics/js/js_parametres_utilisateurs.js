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
});