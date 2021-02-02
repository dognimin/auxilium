
/*
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
});
document.onkeydown = function(e) {
    if(event.keyCode == 123) {
        return false;
    }
    if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
        return false;
    }
    if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
        return false;
    }
    if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
        return false;
    }
    if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
        return false;
    }
}
*/
function getUrlVars() {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}
function deconnexion(niveau) {
    if(niveau == 0) {
        var url = '';
    }else if(niveau == 1) {
        var url = '../';
    }else if(niveau == 2) {
        var url = '../../';
    }
    $.ajax({
        url: url+'_configs/Includes/Submits/submit_deconnexion.php',
        dataType: 'json',
        success: function (data) {
            if(data['success'] === true) {
                window.location.href="";
            }
        }
    });
}
function passwordChecker(mot_de_passe) {

    var longueur = mot_de_passe.length,
        format = mot_de_passe.match(/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/),
        i = 0;
    if(longueur > 0) {
        $("#nouveau_mot_de_passe_small").addClass('text-danger');
        if(!mot_de_passe.match(/[a-z]/)) {
            $("#nouveau_mot_de_passe_small").html('<i class="fa fa-dot-circle"></i> Le mot de passe doit contenir au moins une lettre minuscule.');
        }else {
            if(!mot_de_passe.match(/[A-Z]/)) {
                $("#nouveau_mot_de_passe_small").html('<i class="fa fa-dot-circle"></i> Le mot de passe doit contenir au moins une lettre majuscule.');
            }else {
                if(!mot_de_passe.match(/[0-9]/)) {
                    $("#nouveau_mot_de_passe_small").html('<i class="fa fa-dot-circle"></i> Le mot de passe doit contenir au moins un chiffre.');
                }else {
                    if(!format) {
                        $("#nouveau_mot_de_passe_small").html('<i class="fa fa-dot-circle"></i> Le mot de passe doit contenir au moins un caractère spécial.');
                    }else {
                        if(longueur < 8) {
                            $("#nouveau_mot_de_passe_small").html('<i class="fa fa-dot-circle"></i> Le mot de passe doit contenir au moins 8 caractères.');
                        }else {
                            $("#nouveau_mot_de_passe_small").html('');
                        }
                    }
                }
            }
        }
    }else {
        $("#nouveau_mot_de_passe_small").removeClass('alert alert-dark');
        $("#nouveau_mot_de_passe_small").html('');
    }
}






function display_index_page() {
    $("#div_display_index_page").html('<p align="center"><img src="_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '_configs/Includes/Pages/Index.php',
        success: function (data) {
            $("#div_display_index_page").html(data);
        }
    })
}
function display_login_page() {
    $("#div_display_login_page").html('<p align="center"><img src="_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '_configs/Includes/Pages/Connexion.php',
        success: function (data) {
            $("#div_display_login_page").html(data);
        }
    })
}

function display_mot_de_passe_page() {
    $("#div_display_mot_de_passe_page").html('<p align="center"><img src="_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '_configs/Includes/Pages/MajMotDePasse.php',
        success: function (data) {
            $("#div_display_mot_de_passe_page").html(data);
        }
    })
}
function display_manuel_page() {
    $("#div_display_manuel_page").html('<p align="center"><img src="_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '_configs/Includes/Pages/Manuel.php',
        success: function (data) {
            $("#div_display_manuel_page").html(data);
        }
    })
}

function display_ogd_index_page() {
    $("#div_display_ogd_index_page").html('<p align="center"><img src="../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../_configs/Includes/Pages/Ogd/Index.php',
        success: function (data) {
            $("#div_display_ogd_index_page").html(data);
        }
    })
}
function display_ogd_details_page(code_ogd) {
    $("#div_display_ogd_details_page").html('<p align="center"><img src="../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../_configs/Includes/Pages/Ogd/Details.php',
        type: 'POST',
        data: {
            'code_ogd': code_ogd
        },
        success: function (data) {
            $("#div_display_ogd_details_page").html(data);
        }
    })
}
function display_ogd_chargements_page(code_ogd) {
    $("#div_display_ogd_chargements_page").html('<p align="center"><img src="../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../_configs/Includes/Pages/Ogd/Chargements.php',
        type: 'POST',
        data: {
            'code_ogd': code_ogd
        },
        success: function (data) {
            $("#div_display_ogd_chargements_page").html(data);
        }
    })
}
function display_ogd_imports_page(code_ogd) {
    $("#div_display_ogd_imports_page").html('<p align="center"><img src="../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../_configs/Includes/Pages/Ogd/Imports.php',
        type: 'POST',
        data: {
            'code_ogd': code_ogd
        },
        success: function (data) {
            $("#div_display_ogd_imports_page").html(data);
        }
    })
}
function display_ogd_exports_page(code_ogd) {
    $("#div_display_ogd_exports_page").html('<p align="center"><img src="../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../_configs/Includes/Pages/Ogd/Exports.php',
        type: 'POST',
        data: {
            'code_ogd': code_ogd
        },
        success: function (data) {
            $("#div_display_ogd_exports_page").html(data);
        }
    })
}
function display_ogd_populations_page(code_ogd) {
    $("#div_display_ogd_populations_page").html('<p align="center"><img src="../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../_configs/Includes/Pages/Ogd/Populations.php',
        type: 'POST',
        data: {
            'code_ogd': code_ogd
        },
        success: function (data) {
            $("#div_display_ogd_populations_page").html(data);
        }
    })
}
function display_ogd_assure_page(code_ogd, num_secu) {
    $("#div_display_ogd_assure_page").html('<p align="center"><img src="../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../_configs/Includes/Pages/Ogd/Assure.php',
        type: 'POST',
        data: {
            'code_ogd': code_ogd,
            'num_secu': num_secu
        },
        success: function (data) {
            $("#div_display_ogd_assure_page").html(data);
        }
    })
}
function display_ogd_factures_page(code_ogd) {
    $("#div_display_ogd_factures_page").html('<p align="center"><img src="../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../_configs/Includes/Pages/Ogd/Factures.php',
        type: 'POST',
        data: {
            'code_ogd': code_ogd
        },
        success: function (data) {
            $("#div_display_ogd_factures_page").html(data);
        }
    })
}
function display_ogd_factures_verification_page(code_ogd) {
    $("#div_display_ogd_factures_verification_page").html('<p align="center"><img src="../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../_configs/Includes/Pages/Ogd/FacturesVerification.php',
        type: 'POST',
        data: {
            'code_ogd': code_ogd
        },
        success: function (data) {
            $("#div_display_ogd_factures_verification_page").html(data);
        }
    })
}
function display_ogd_factures_a_verifier_ou_liquider(norme,num_fichier,num_facture,code_ets,statut) {
    $("#div_resultats_recherche").html('<p align="center"><img src="../_publics/images/loading_white.gif" width="100" /></p>');
    if(statut === 'N') {
        $.ajax({
            url: '../_configs/Includes/Searches/Ogd/search_factures_a_verifier.php',
            type: 'POST',
            data: {
                'norme': norme,
                'num_fichier': num_fichier,
                'num_facture': num_facture,
                'code_ets': code_ets,
                'statut': statut
            },
            success: function (data) {
                $("#div_resultats_recherche").html(data);
            }
        });
    }
    if(statut === 'C') {
        $.ajax({
            url: '../_configs/Includes/Searches/Ogd/search_factures_a_liquider.php',
            type: 'POST',
            data: {
                'norme': norme,
                'num_fichier': num_fichier,
                'num_facture': num_facture,
                'code_ets': code_ets,
                'statut': statut
            },
            success: function (data) {
                $("#div_resultats_recherche").html(data);
            }
        });
    }
}
function display_ogd_facture_page(num_facture) {
    $("#div_display_ogd_facture_page").html('<p align="center"><img src="../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../_configs/Includes/Pages/Ogd/Facture.php',
        type: 'POST',
        data: {
            'num_facture': num_facture
        },
        success: function (data) {
            $("#div_display_ogd_facture_page").html(data);
        }
    })
}
function display_ogd_factures_liquidation_page(code_ogd) {
    $("#div_display_ogd_factures_liquidation_page").html('<p align="center"><img src="../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../_configs/Includes/Pages/Ogd/FacturesLiquidation.php',
        type: 'POST',
        data: {
            'code_ogd': code_ogd
        },
        success: function (data) {
            $("#div_display_ogd_factures_liquidation_page").html(data);
        }
    })
}
function display_ogd_factures_recherche_page(code_ogd) {
    $("#div_display_ogd_factures_recherche_page").html('<p align="center"><img src="../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../_configs/Includes/Pages/Ogd/FacturesRecherche.php',
        type: 'POST',
        data: {
            'code_ogd': code_ogd
        },
        success: function (data) {
            $("#div_display_ogd_factures_recherche_page").html(data);
        }
    })
}
function display_ogd_statistiques_page(code_ogd) {
    $("#div_display_ogd_statistiques_page").html('<p align="center"><img src="../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../_configs/Includes/Pages/Ogd/Statistiques.php',
        type: 'POST',
        data: {
            'code_ogd': code_ogd
        },
        success: function (data) {
            $("#div_display_ogd_statistiques_page").html(data);
        }
    })
}

function display_statistiques_index_page() {
    $("#div_display_statistiques_index_page").html('<p align="center"><img src="../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../_configs/Includes/Pages/Statistiques/Index.php',
        success: function (data) {
            $("#div_display_statistiques_index_page").html(data);
        }
    })
}
function display_parametres_index_page() {
    $("#div_display_parametres_index_page").html('<p align="center"><img src="../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../_configs/Includes/Pages/Parametres/Index.php',
        success: function (data) {
            $("#div_display_parametres_index_page").html(data);
        }
    })
}
function display_parametres_ogd_index_page() {
    $("#div_display_parametres_ogd_index_page").html('<p align="center"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Ogd/Index.php',
        success: function (data) {
            $("#div_display_parametres_ogd_index_page").html(data);
        }
    })
}
function display_parametres_ogd_details_page(code_ogd) {
    $("#div_display_parametres_ogd_details_page").html('<p align="center"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Ogd/Details.php',
        type: 'POST',
        data: {
            'code_ogd': code_ogd
        },
        success: function (data) {
            $("#div_display_parametres_ogd_details_page").html(data);
        }
    })
}
function display_parametres_ogd_edition_page() {
    $("#div_display_parametres_ogd_edition_page").html('<p align="center"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Ogd/Edition.php',
        success: function (data) {
            $("#div_display_parametres_ogd_edition_page").html(data);
        }
    })
}
function display_parametres_utilisateurs_index_page() {
    $("#div_display_parametres_utilisateurs_index_page").html('<p align="center"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Utilisateurs/Index.php',
        success: function (data) {
            $("#div_display_parametres_utilisateurs_index_page").html(data);
        }
    })
}
function display_parametres_utilisateurs_details_page(id_user) {
    $("#div_display_parametres_utilisateurs_details_page").html('<p align="center"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Utilisateurs/Details.php',
        type: 'POST',
        data: {
            'id_user': id_user
        },
        success: function (data) {
            $("#div_display_parametres_utilisateurs_details_page").html(data);
        }
    })
}
function display_parametres_utilisateurs_edition_page(id_user) {
    $("#div_display_parametres_utilisateurs_edition_page").html('<p align="center"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Utilisateurs/Edition.php',
        type: 'POST',
        data: {
            'id_user': id_user
        },
        success: function (data) {
            $("#div_display_parametres_utilisateurs_edition_page").html(data);
        }
    });
}
function display_parametres_referentiels_index_page() {
    $("#div_display_parametres_referentiels_index_page").html('<p align="center"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Referentiels/Index.php',
        success: function (data) {
            $("#div_display_parametres_referentiels_index_page").html(data);
        }
    })
}
function display_parametres_referentiels_generaux_page() {
    $("#div_display_parametres_referentiels_generaux_page").html('<p align="center"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Referentiels/Generaux.php',
        success: function (data) {
            $("#div_display_parametres_referentiels_generaux_page").html(data);
        }
    })
}
function display_parametres_referentiels_generaux_chargements_page() {
    $("#div_display_parametres_referentiels_generaux_chargements_page").html('<p align="center"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Referentiels/GenerauxChargements.php',
        success: function (data) {
            $("#div_display_parametres_referentiels_generaux_chargements_page").html(data);
        }
    })
}
function display_parametres_referentiels_centres_sante_page() {
    $("#div_display_parametres_referentiels_centres_sante_page").html('<p class="center_align"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Referentiels/CentresSante.php',
        success: function (data) {
            $("#div_display_parametres_referentiels_centres_sante_page").html(data);
        }
    })
}
function display_parametres_referentiels_centre_sante_page(code_ets) {
    $("#div_display_parametres_referentiels_centre_sante_page").html('<p class="center_align"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Referentiels/CentreSante.php',
        type: 'POST',
        data: {
            'code_ets': code_ets
        },
        success: function (data) {
            $("#div_display_parametres_referentiels_centre_sante_page").html(data);
        }
    })
}

function display_parametres_referentiels_professionnels_sante_page() {
    $("#div_display_parametres_referentiels_professionnels_sante_page").html('<p class="center_align"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Referentiels/ProfessionnelsSante.php',
        success: function (data) {
            $("#div_display_parametres_referentiels_professionnels_sante_page").html(data);
        }
    })
}
function display_parametres_referentiels_professionnel_sante_page(code_ps) {
    $("#div_display_parametres_referentiels_professionnel_sante_page").html('<p class="center_align"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Referentiels/ProfessionnelSante.php',
        type: 'POST',
        data: {
            'code_ps': code_ps
        },
        success: function (data) {
            $("#div_display_parametres_referentiels_professionnel_sante_page").html(data);
        }
    })
}
function display_parametres_referentiels_lettres_cles_page() {
    $("#div_display_parametres_referentiels_lettres_cles_page").html('<p class="center_align"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Referentiels/LettresCles.php',
        success: function (data) {
            $("#div_display_parametres_referentiels_lettres_cles_page").html(data);
        }
    })
}
function display_parametres_referentiels_lettre_cle_page(code_lc) {
    $("#div_display_parametres_referentiels_lettre_cle_page").html('<p class="center_align"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Referentiels/LettreCle.php',
        type: 'POST',
        data: {
            'code_lc': code_lc
        },
        success: function (data) {
            $("#div_display_parametres_referentiels_lettre_cle_page").html(data);
        }
    })
}

function display_parametres_referentiels_actes_medicaux_page() {
    $("#div_display_parametres_referentiels_actes_medicaux_page").html('<p class="center_align"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Referentiels/ActesMedicaux.php',
        success: function (data) {
            $("#div_display_parametres_referentiels_actes_medicaux_page").html(data);
        }
    })
}
function display_parametres_referentiels_acte_medical_page(code_acte) {
    $("#div_display_parametres_referentiels_acte_medical_page").html('<p class="center_align"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Referentiels/ActeMedical.php',
        type: 'POST',
        data: {
            'code_acte': code_acte
        },
        success: function (data) {
            $("#div_display_parametres_referentiels_acte_medical_page").html(data);
        }
    })
}
function display_parametres_referentiels_tables_de_valeur_page() {
    $("#div_display_parametres_referentiels_tables_de_valeur_page").html('<p align="center"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Referentiels/TablesDeValeur.php',
        success: function (data) {
            $("#div_display_parametres_referentiels_tables_de_valeur_page").html(data);
        }
    })
}

function display_parametres_scripts_index_page() {
    $("#div_display_parametres_scripts_index_page").html('<p align="center"><img src="../../_publics/images/loading_white.gif" /></p>');
    $.ajax({
        url: '../../_configs/Includes/Pages/Parametres/Scripts/Index.php',
        success: function (data) {
            $("#div_display_parametres_scripts_index_page").html(data);
        }
    })
}

























$.fn.inputFilter = function(inputFilter) {
    return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
        if (inputFilter(this.value)) {
            this.oldValue = this.value;
            this.oldSelectionStart = this.selectionStart;
            this.oldSelectionEnd = this.selectionEnd;
        } else if (this.hasOwnProperty("oldValue")) {
            this.value = this.oldValue;
            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
        } else {
            this.value = "";
        }
    });
};