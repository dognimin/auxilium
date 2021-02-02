<form id="form_recherche_factures">
    <div class="form-group row">
        <div class="col-sm-1">
            <label for="date_debut_input">Date début</label>
            <input type="text" aria-label="date_debut_input" class="form-control form-control-sm datepicker" maxlength="10" id="date_debut_input" placeholder="___________________________________" autocomplete="off" value="<?= date('d/m/Y',strtotime('-1 WEEK',time()));?>" readonly />
        </div>
        <div class="col-sm-1">
            <label for="date_fin_input">Date fin</label>
            <input type="text" aria-label="date_fin_input" class="form-control form-control-sm datepicker" maxlength="10" id="date_fin_input" placeholder="___________________________________" autocomplete="off" value="<?= date('d/m/Y',time());?>" readonly />
        </div>
        <div class="col-sm-2">
            <label for="num_facture_input">N° facture</label>
            <input type="text" aria-label="num_facture_input" class="form-control form-control-sm" maxlength="13" id="num_facture_input" placeholder="___________________________________" autocomplete="off" value="" />
        </div>
        <div class="col">
            <label for="code_etablissement_input">Centre de santé</label>
            <select class="custom-select custom-select-sm" aria-label="code_etablissement_input" id="code_etablissement_input">
                <option value="">Sélectionnez un établissement</option>
                <?php
                foreach ($etablissements as $etablissement) {
                    echo '<option value="'.$etablissement['code'].'">'.$etablissement['raison_sociale'].'</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-sm-3">
            <label for="code_statut_input">Statut de facture</label>
            <select class="custom-select custom-select-sm" aria-label="code_statut_input" id="code_statut_input">
                <option value="">Sélectionnez un statut</option>
                <?php
                foreach ($statuts as $statut) {
                    echo '<option value="'.$statut['code'].'">'.$statut['libelle'].'</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-sm-1">
            <label for="btn_rechercher">&nbsp;</label>
            <button type="submit" id="btn_rechercher" class="btn btn-primary btn-sm btn-block"><b class="fa fa-search"></b></button>
        </div>
    </div>
</form>
<div id="div_resultats_recherche"></div>