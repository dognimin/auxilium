<p class="center_align" id="p_results_editer_facture"></p>
<form id="form_editer_facture">
    <div class="row">
        <div class="col">
            <p class="center_align bg-primary text-white font-weight-bold">Facture</p>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="date_soins_input">Date des soins</label>
                    <input type="text" class="form-control form-control-sm datepicker" id="date_soins_input" aria-label="Date soins" maxlength="10" value="<?= date('d/m/Y',strtotime($facture['date_facture']));?>" autocomplete="off" placeholder="______________________________________" />
                    <small id="date_soins_small"></small>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <p class="center_align bg-primary text-white font-weight-bold">Professionnel de santé</p>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="code_ps_input">Code</label>
                    <input type="text" class="form-control form-control-sm" id="code_ps_input" aria-label="Code du PS" maxlength="9" autocomplete="off" placeholder="______________________________________" />
                    <small id="code_ps_small"></small>
                </div>
                <div class="col">
                    <label for="nom_prenoms_ps_input">Nom & Prénom(s)</label>
                    <input type="text" class="form-control form-control-sm" id="nom_prenoms_ps_input" aria-label="Nom & Prénom(s) du PS" autocomplete="off" placeholder="__________________________________________________________________________________________________________________" readonly />
                    <small id="code_ps_small"></small>
                </div>
                <div class="col-sm-12">
                    <label for="code_specialite_input">Spécialité</label>
                    <select class="custom-select custom-select-sm" id="code_specialite_input" aria-label="Spécialité médicale du PS" disabled>
                        <option value="">Spécialité du professionnel</option>
                    </select>
                    <small id="code_specialite_small"></small>
                </div>
            </div>
        </div>
    </div><hr />
    <div class="row">
        <div class="col">
            <p class="center_align bg-primary text-white font-weight-bold">Pathologie</p>
            <div class="form-group row">
                <div class="col-sm-3">
                    <label for="code_pathologie_input">Code</label>
                    <input type="text" class="form-control form-control-sm" id="code_pathologie_input" aria-label="Code de la pathologie" maxlength="3" value="<?= $facture['code_pathologie_ds'];?>" autocomplete="off" placeholder="______________________________________" />
                    <small id="code_pathologie_small"></small>
                </div>
                <div class="col">
                    <label for="libelle_pathologie_input">Libellé</label>
                    <input type="text" class="form-control form-control-sm" id="libelle_pathologie_input" aria-label="Libellé de la pathologie" value="<?= $pathologie['libelle'];?>" autocomplete="off" placeholder="__________________________________________________________________________________________________________________" readonly />
                    <small id="code_pathologie_small"></small>
                </div>
            </div>
        </div>
    </div><hr />
    <div class="row">
        <div class="col">
            <p class="center_align bg-primary text-white font-weight-bold">Lignes d'actes</p>
            <?php
            $id_ligne = 0;
            foreach ($actes as $ligne_acte) {
                if(strlen($ligne_acte['code_acte']) == 7) {
                    $acte_trouve = $ACTESMEDICAUX->trouver($ligne_acte['code_acte']);
                }else {
                    $acte_trouve = $MEDICAMENTS->trouver($ligne_acte['code_acte']);
                }
                ?>
                <div class="form-group row">
                    <div class="col-sm-3">
                        <label for="code_acte_input">Code</label>
                        <input type="text" class="form-control form-control-sm code_acte_input" id="code_acte_<?= $id_ligne;?>_input" aria-label="Code de l'acte" maxlength="7" value="<?= $ligne_acte['code_acte'];?>" autocomplete="off" placeholder="______________________________________" />
                        <small id="code_ps_small"></small>
                    </div>
                    <div class="col-sm-9">
                        <label for="libelle_acte_input">Libellé</label>
                        <input type="text" class="form-control form-control-sm libelle_acte_input" id="libelle_acte_<?= $id_ligne;?>_input" aria-label="Libellé de l'acte" autocomplete="off" value="<?= $acte_trouve['libelle'];?>" placeholder="__________________________________________________________________________________________________________________" readonly />
                        <small id="code_ps_small"></small>
                    </div>
                    <div class="col-sm-3">
                        <label for="acte_date_debut_input">Date début</label>
                        <input type="text" maxlength="10" autocomplete="off" value="<?= date('d/m/Y',strtotime($ligne_acte['date_debut_soins']));?>" class="form-control form-control-sm datepicker acte_date_debut_input" id="acte_date_debut_<?= $id_ligne;?>_input" aria-describedby="acte_date_debut" placeholder="Date début" readonly />
                    </div>
                    <div class="col-sm-3">
                        <label for="acte_date_fin_input">Date fin</label>
                        <input type="text" maxlength="10" autocomplete="off" value="<?= date('d/m/Y',strtotime($ligne_acte['date_fin_soins']));?>" class="form-control form-control-sm datepicker acte_date_fin_input" id="acte_date_fin_<?= $id_ligne;?>_input" aria-describedby="acte_date_fin" placeholder="Date fin" readonly />
                    </div>
                    <div class="col-sm-2">
                        <label for="acte_prix_unitiaire_input">Prix unitaire</label>
                        <input type="text" autocomplete="off" value="<?= intval($ligne_acte['prix_unitaire']);?>" class="form-control form-control-sm acte_prix_unitiaire" id="acte_prix_unitiaire_<?= $id_ligne;?>_input" aria-describedby="acte_prix_unitiaire" placeholder="0" style="text-align: right" />
                    </div>
                    <div class="col-sm-2">
                        <label for="acte_quantite_input">Quantité</label>
                        <select class="form-control form-control-sm acte_quantite_input" id="acte_quantite_<?= $id_ligne;?>_input" style="text-align: right">
                            <?php
                            for($q = 1; $q <= 100; $q++) {
                                ?>
                                <option value="<?= $q; ?>" <?php if($q == $ligne_acte['quantite_acte']){echo 'selected';} ?>><?= $q; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label for="acte_prix_total_input">Prix total</label>
                        <input type="text" autocomplete="off" value="<?= intval($ligne_acte['prix_unitaire']) * $ligne_acte['quantite_acte'];?>" class="form-control form-control-sm" id="acte_prix_total_<?= $id_ligne;?>_input" aria-describedby="acte_prix_total" placeholder="0" style="text-align: right" disabled />
                    </div>
                </div>
                <?php
                $id_ligne++;
            }
            ?>
        </div>
    </div><hr />
    <div class="form-group row">
        <div class="col-sm-12 right_align">
            <button type="submit" id="btn_editer_facture" class="btn btn-success btn-sm"><i class="fa fa-check-circle"></i> Valider</button>
            <input type="hidden" id="facture_editee_infos" value="<?= $facture['num_facture'];?>" />
            <a href="<?= URL.'ogd/facture?num='.$facture['num_facture'];?>" class="btn btn-secondary btn-sm"><i class="fa fa-chevron-circle-left"></i> Retourner</a>
        </div>
    </div>
</form>