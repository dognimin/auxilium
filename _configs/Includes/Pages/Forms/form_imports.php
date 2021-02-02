<form id="form_imports" enctype="multipart/form-data">
    <div class="form-row">
        <div class="form-group col">
            <label for="type_ref_input">Type de chargement</label>
            <select class="custom-select custom-select-sm" aria-label="Type de référentiel" id="type_ref_input" name="type_ref_input">
                <option value="">Sélectionnez le type de chargement</option>
                <option value="SITFAMILLE">Populations</option>
                <option value="DECA">DECA</option>
                <option value="DECRET">DECRET</option>
            </select>
        </div>
        <div class="form-group col">
            <label for="fichier_input">Fichier</label>
            <div class="custom-file mb-3">
                <input type="file" class="custom-file-input" id="fichier_input" name="fichier_input" required>
                <label class="custom-file-label" for="validatedCustomFile">Choisir le fichier...</label>
            </div>
        </div>
        <div class="form-group col-sm-2">
            <label for="btn_charger">&nbsp;</label>
            <button type="submit" class="btn btn-block btn-danger btn-sm" id="btn_charger"><i class="fa fa-upload"></i> Charger</button>
        </div>
    </div>

</form>
<div class="progress">
    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
</div>
<div class="center_align" id="div_resultats"></div>