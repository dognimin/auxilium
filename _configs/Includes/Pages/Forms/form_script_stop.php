<p class="center_align" id="p_results_arret_script"></p>
<form id="form_script_stop">
    <div class="form-group row">
        <div class="col-sm-12">
            <label for="date_debut_input">Souhaitez-vous vraiment arrêter ce script ?</label><br />
            <button type="submit" id="btn_arret_script" class="btn btn-danger btn-sm"><i class="fa fa-stop"></i> Arrêter</button>
            <input type="hidden" id="script_id_input" value="" />
            <a href="<?= URL.'parametres/scripts/';?>" class="btn btn-secondary btn-sm"><i class="fa fa-chevron-circle-left"></i> Retourner</a>
        </div>
    </div>
</form>