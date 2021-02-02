<p class="center_align" id="p_results_refuser_facture"></p>
<form id="form_refuser_facture">
    <div class="row">
        <div class="col">
            <?php
            $id_ligne_rejet = 0;
            foreach ($actes as $ligne_acte) {
                if(strlen($ligne_acte['code_acte']) == 7) {
                    $acte_trouve = $ACTESMEDICAUX->trouver($ligne_acte['code_acte']);
                }else {
                    $acte_trouve = $MEDICAMENTS->trouver($ligne_acte['code_acte']);
                }
                ?>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="code_acte_rejet_<?= $id_ligne_rejet;?>_input">Code</label>
                        <input type="text" class="form-control form-control-sm" id="code_acte_rejet_<?= $id_ligne_rejet;?>_input" aria-label="Code de l'acte" maxlength="7" value="<?= $ligne_acte['code_acte'];?>" autocomplete="off" placeholder="______________________________________" readonly />
                    </div>
                    <div class="col-sm-9">
                        <label for="libelle_acte_rejet_<?= $id_ligne_rejet;?>_input">Libellé</label>
                        <input type="text" class="form-control form-control-sm" id="libelle_acte_rejet_<?= $id_ligne_rejet;?>_input" aria-label="Libellé de l'acte" autocomplete="off" value="<?= $acte_trouve['libelle'];?>" placeholder="__________________________________________________________________________________________________________________" readonly />
                    </div>
                    <div class="col-sm-12">
                        <label for="code_motif_rejet_<?= $id_ligne_rejet;?>_input">Motif</label>
                        <select class="custom-select custom-select-sm code_motif_rejet_input" id="code_motif_rejet_<?= $id_ligne_rejet;?>_input" aria-label="Motif rejet">
                            <option value="">Sélectionner le motif de rejet</option>
                            <?php
                            foreach ($motifs as $motif) {
                                ?>
                                <option value="<?= $motif['code']; ?>"><?= $motif['libelle']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php
                $id_ligne_rejet++;
            }
            ?>
        </div>
    </div><hr />
    <div class="form-group row">
        <div class="col-sm-12">
            <label for="date_debut_input">Souhaitez-vous vraiment rejeter cette facture ?</label><br />
            <button type="submit" id="btn_refuser_facture" class="btn btn-success btn-sm"><i class="fa fa-check-circle"></i> Valider</button>
            <input type="hidden" id="occurrences_input" value="<?= $nb_actes;?>" />
            <input type="hidden" id="facture_refusee_infos" value="<?= $facture['num_facture'].'_R';?>" />
            <a href="<?= URL.'ogd/facture?num='.$facture['num_facture'];?>" class="btn btn-secondary btn-sm"><i class="fa fa-chevron-circle-left"></i> Retourner</a>
        </div>
    </div>
</form>