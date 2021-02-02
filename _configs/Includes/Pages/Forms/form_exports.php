<form id="form_exports" enctype="multipart/form-data">
    <div class="form-row">
        <div class="form-group col-sm-3">
            <label for="type_ref_input">Type de téléchargement</label>
            <select class="custom-select custom-select-sm" aria-label="Type de référentiel" id="type_ref_input" name="type_ref_input" required>
                <option value="">Sélectionnez le type de chargement</option>
                <option value="DECLIQ">DECLIQ</option>
                <option value="DECPAI">DECPAI</option>
                <option value="REJETSO">REJETS ORGANISME</option>
                <option value="REJETSC">REJETS CNAM</option>
                <option value="TDM_EDT_009E">PAIEMENTS CHEQUES</option>
                <option value="BPAIEMENTS">BORDEREAUX DE PAIEMENTS</option>
            </select>
        </div>
        <div class="col">
            <label for="num_fichier_input">Fichier</label>
            <select class="custom-select custom-select-sm" aria-label="num_fichier_input" id="num_fichier_input" disabled required>
                <option value="">Sélectionnez un fichier</option>
            </select>
        </div>
        <div class="form-group col-sm-2">
            <label for="btn_charger">&nbsp;</label>
            <button type="submit" class="btn btn-block btn-danger btn-sm" id="btn_charger"><i class="fa fa-download"></i> Télécharger</button>
        </div>
    </div>

</form>
<div class="progress">
    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
</div>
<div class="center_align" id="div_resultats"></div>