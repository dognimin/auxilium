<p class="center_align" id="p_results_valider_facture"></p>
<form id="form_valider_facture">
    <div class="form-group row">
        <div class="col-sm-12">
            <label for="date_debut_input">Souhaitez-vous vraiment valider cette facture ?</label><br />
            <button type="submit" id="btn_valider_facture" class="btn btn-success btn-sm"><i class="fa fa-check-circle"></i> Valider</button>
            <input type="hidden" id="facture_valide_infos" value="<?= $facture['num_facture'].'_L';?>" />
            <a href="<?= URL.'ogd/facture?num='.$facture['num_facture'];?>" class="btn btn-secondary btn-sm"><i class="fa fa-chevron-circle-left"></i> Retourner</a>
        </div>
    </div>
</form>