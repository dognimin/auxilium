<form id="form_recherche_centres_sante">
    <div class="row">
        <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" id="code_input" maxlength="9" placeholder="Code" autocomplete="off" />
        </div>
        <div class="col">
            <input type="text" class="form-control form-control-sm" id="nom_input" placeholder="Raison sociale" autocomplete="off" />
        </div>
        <div class="col-sm-2">
            <select class="form-control form-control-sm" id="secteur_input">
                <option value="">Secteur d'activite</option>
                <?php
                foreach ($secteurs_activite as $secteur_activite) {
                    echo '<option value="'.$secteur_activite['code'].'">'.$secteur_activite['libelle'].'</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-sm-2">
            <select class="form-control form-control-sm" id="ville_input">
                <option value="">Ville</option>
                <?php

                foreach ($villes as $ville) {
                    echo '<option value="'.$ville['ville'].'">'.$ville['ville'].'</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-sm-1">
            <button type="submit" id="btn_rechercher" class="btn btn-primary btn-sm btn-block"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
<div id="div_resultats_recherche"></div>