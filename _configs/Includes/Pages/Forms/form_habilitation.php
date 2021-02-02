<p class="center_align" id="p_results_habilitations"></p>
<form id="form_habilitation">
    <div class="form-group">
        <table class="table table-hover table-sm">
            <thead class="bg-danger text-white">
            <tr>
                <th colspan="2">ENTITES</th>
                <th width="5"><i class="fa fa-eye"></i></th>
                <th width="5"><i class="fa fa-edit"></i></th>
            </tr>
            </thead>
            <tr class="bg-light">
                <td width="100" colspan="2"><b>Chargements</b></td>
                <td width="5"><input type="checkbox" id="chargements_affichage_input" aria-label="CHARGEMENTS_AFFICHAGE" value="CHARGEMENTS_AFFICHAGE;" <?php if(in_array('CHARGEMENTS_AFFICHAGE',$utilisateur_modules)){echo 'checked';} ?> /></td>
                <td width="5"><input type="checkbox" id="chargements_edition_input" aria-label="CHARGEMENTS_EDITION" value="CHARGEMENTS_EDITION;" <?php if(in_array('CHARGEMENTS_EDITION',$utilisateur_modules)){echo 'checked';} ?> /></td>
            </tr>
            <tr>
                <td></td>
                <td>Imports</td>
                <td width="5"><input type="checkbox" id="chargements_imports_affichage_input" aria-label="CHARGEMENTS_IMPORTS_AFFICHAGE" value="CHARGEMENTS_IMPORTS_AFFICHAGE;" <?php if(in_array('CHARGEMENTS_IMPORTS_AFFICHAGE',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
                <td width="5"><input type="checkbox" id="chargements_imports_edition_input" aria-label="CHARGEMENTS_IMPORTS_EDITION" value="CHARGEMENTS_IMPORTS_EDITION;" <?php if(in_array('CHARGEMENTS_IMPORTS_EDITION',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
            </tr>
            <tr>
                <td></td>
                <td>Exports</td>
                <td width="5"><input type="checkbox" id="chargements_exports_affichage_input" aria-label="CHARGEMENTS_EXPORTS_AFFICHAGE" value="CHARGEMENTS_EXPORTS_AFFICHAGE;" <?php if(in_array('CHARGEMENTS_EXPORTS_AFFICHAGE',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
                <td width="5"><input type="checkbox" id="chargements_exports_edition_input" aria-label="CHARGEMENTS_EXPORTS_EDITION" value="CHARGEMENTS_EXPORTS_EDITION;" <?php if(in_array('CHARGEMENTS_EXPORTS_EDITION',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
            </tr>




            <tr class="bg-light">
                <td width="100" colspan="2"><b>Populations</b></td>
                <td width="5"><input type="checkbox" id="populations_affichage_input" aria-label="POPULATIONS_AFFICHAGE" value="POPULATIONS_AFFICHAGE;" <?php if(in_array('POPULATIONS_AFFICHAGE',$utilisateur_modules)){echo 'checked';} ?> /></td>
                <td width="5"><input type="checkbox" id="populations_edition_input" aria-label="POPULATIONS_EDITION" value="POPULATIONS_EDITION;" <?php if(in_array('POPULATIONS_EDITION',$utilisateur_modules)){echo 'checked';} ?> /></td>
            </tr>



            <tr class="bg-light">
                <td width="100" colspan="2"><b>Factures</b></td>
                <td width="5"><input type="checkbox" id="factures_affichage_input" aria-label="FACTURES_AFFICHAGE" value="FACTURES_AFFICHAGE;" <?php if(in_array('FACTURES_AFFICHAGE',$utilisateur_modules)){echo 'checked';} ?> /></td>
                <td width="5"><input type="checkbox" id="factures_edition_input" aria-label="FACTURES_EDITION" value="FACTURES_EDITION;" <?php if(in_array('FACTURES_EDITION',$utilisateur_modules)){echo 'checked';} ?> /></td>
            </tr>
            <tr>
                <td></td>
                <td>Recherche</td>
                <td width="5"><input type="checkbox" id="factures_recherche_affichage_input" aria-label="FACTURES_RECHERCHE_AFFICHAGE" value="FACTURES_RECHERCHE_AFFICHAGE;" <?php if(in_array('FACTURES_RECHERCHE_AFFICHAGE',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
                <td width="5"><input type="checkbox" id="factures_recherche_edition_input" aria-label="FACTURES_RECHERCHE_EDITION" value="FACTURES_RECHERCHE_EDITION;" <?php if(in_array('FACTURES_RECHERCHE_EDITION',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
            </tr>
            <tr>
                <td></td>
                <td>Vérification</td>
                <td width="5"><input type="checkbox" id="factures_verification_affichage_input" aria-label="FACTURES_VERIFICATION_AFFICHAGE" value="FACTURES_VERIFICATION_AFFICHAGE;" <?php if(in_array('FACTURES_VERIFICATION_AFFICHAGE',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
                <td width="5"><input type="checkbox" id="factures_verification_edition_input" aria-label="FACTURES_VERIFICATION_EDITION" value="FACTURES_VERIFICATION_EDITION;" <?php if(in_array('FACTURES_VERIFICATION_EDITION',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
            </tr>
            <tr>
                <td></td>
                <td>Liquidation</td>
                <td width="5"><input type="checkbox" id="factures_liquidation_affichage_input" aria-label="FACTURES_LIQUIDATION_AFFICHAGE" value="FACTURES_LIQUIDATION_AFFICHAGE;" <?php if(in_array('PARAMETRES_SCRIPTS_EDITION',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
                <td width="5"><input type="checkbox" id="factures_liquidation_edition_input" aria-label="FACTURES_LIQUIDATION_EDITION" value="FACTURES_LIQUIDATION_EDITION;" <?php if(in_array('PARAMETRES_SCRIPTS_EDITION',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
            </tr>
            <tr>
                <td></td>
                <td>Détails de facture</td>
                <td width="5"><input type="checkbox" id="factures_details_affichage_input" aria-label="FACTURES_DETAILS_AFFICHAGE" value="FACTURES_DETAILS_AFFICHAGE;" <?php if(in_array('FACTURES_DETAILS_AFFICHAGE',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
                <td width="5"><input type="checkbox" id="factures_details_edition_input" aria-label="FACTURES_DETAILS_EDITION" value="FACTURES_DETAILS_EDITION;" <?php if(in_array('FACTURES_DETAILS_EDITION',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
            </tr>



            <tr class="bg-light">
                <td width="100" colspan="2"><b>Statistiques</b></td>
                <td width="5"><input type="checkbox" id="statistiques_affichage_input" aria-label="STATISTIQUES_AFFICHAGE" value="STATISTIQUES_AFFICHAGE;" <?php if(in_array('STATISTIQUES_AFFICHAGE',$utilisateur_modules)){echo 'checked';} ?> /></td>
                <td width="5"><input type="checkbox" id="statistiques_edition_input" aria-label="STATISTIQUES_EDITION" value="STATISTIQUES_EDITION;" disabled <?php if(in_array('STATISTIQUES_EDITION',$utilisateur_modules)){echo 'checked';} ?> /></td>
            </tr>



            <tr class="bg-light">
                <td width="100" colspan="2"><b>Paramètres</b></td>
                <td width="5"><input type="checkbox" id="parametres_affichage_input" aria-label="PARAMETRES_AFFICHAGE" value="PARAMETRES_AFFICHAGE;" <?php if(in_array('PARAMETRES_AFFICHAGE',$utilisateur_modules)){echo 'checked';} ?> /></td>
                <td width="5"><input type="checkbox" id="parametres_edition_input" aria-label="PARAMETRES_EDITION" value="PARAMETRES_EDITION;" <?php if(in_array('PARAMETRES_EDITION',$utilisateur_modules)){echo 'checked';} ?> /></td>
            </tr>
            <tr>
                <td></td>
                <td>Utilisateurs</td>
                <td width="5"><input type="checkbox" id="parametres_utilisateurs_affichage_input" aria-label="PARAMETRES_UTILISATEURS_AFFICHAGE" value="PARAMETRES_UTILISATEURS_AFFICHAGE;" <?php if(in_array('PARAMETRES_UTILISATEURS_AFFICHAGE',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
                <td width="5"><input type="checkbox" id="parametres_utilisateurs_edition_input" aria-label="PARAMETRES_UTILISATEURS_EDITION" value="PARAMETRES_UTILISATEURS_EDITION;" <?php if(in_array('PARAMETRES_UTILISATEURS_EDITION',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
            </tr>
            <tr>
                <td></td>
                <td>Référentiels</td>
                <td width="5"><input type="checkbox" id="parametres_referentiels_affichage_input" aria-label="PARAMETRES_REFERENTIELS_AFFICHAGE" value="PARAMETRES_REFERENTIELS_AFFICHAGE;" <?php if(in_array('PARAMETRES_REFERENTIELS_AFFICHAGE',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
                <td width="5"><input type="checkbox" id="parametres_referentiels_edition_input" aria-label="PARAMETRES_REFERENTIELS_EDITION" value="PARAMETRES_REFERENTIELS_EDITION;" <?php if(in_array('PARAMETRES_REFERENTIELS_EDITION',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
            </tr>
            <tr>
                <td></td>
                <td>Scripts</td>
                <td width="5"><input type="checkbox" id="parametres_scripts_affichage_input" aria-label="PARAMETRES_SCRIPTS_AFFICHAGE" value="PARAMETRES_SCRIPTS_AFFICHAGE;" <?php if(in_array('PARAMETRES_SCRIPTS_AFFICHAGE',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
                <td width="5"><input type="checkbox" id="parametres_scripts_edition_input" aria-label="PARAMETRES_SCRIPTS_EDITION" value="PARAMETRES_SCRIPTS_EDITION;" <?php if(in_array('PARAMETRES_SCRIPTS_EDITION',$utilisateur_sous_modules)){echo 'checked';} ?> /></td>
            </tr>
        </table>
    </div>
    <div class="form-group">
        <p class="right_align">
            <input type="hidden" id="user_id_input" value="<?= $utilisateur['user_id'];?>" />
            <button type="submit" id="button_habilitations" class="btn btn-info btn-sm"><i class="fa fa-check"></i> Valider</button>
        </p>
    </div>
</form>