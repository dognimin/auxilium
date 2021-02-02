<form id="form_recherche_acte_medical">
    <div class="row">
        <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" id="code_input" placeholder="Code" autocomplete="off" />
        </div>
        <div class="col">
            <input type="text" class="form-control form-control-sm" id="nom_input" placeholder="Libellé" autocomplete="off" />
        </div>
        <div class="col-sm-4">
            <select class="form-control form-control-sm" id="code_lettre_cle_input">
                <option value="">Lettre clé</option>
                <?php

                foreach ($lettres_cles as $lettre_cle) {
                    echo '<option value="'.$lettre_cle['code'].'">'.$lettre_cle['libelle'].'</option>';
                }
                ?>
            </select>
        </div>
        <div class="col col-sm-1">
            <button type="submit" id="btn_rechercher" class="btn btn-primary btn-sm btn-block"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
<div id="div_resultats_recherche"></div>