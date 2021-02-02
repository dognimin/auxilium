<form id="form_recherche_factures_a_liquider">
    <div class="form-group row">
        <div class="col-sm-4">
            <label for="num_deca_select">DECA</label>
            <select class="custom-select custom-select-sm" aria-label="num_deca_select" id="num_deca_select">
                <option value="">Sélectionnez un DECA</option>
            </select>
        </div>
        <div class="col">
            <label for="code_ets_select">Centre de santé</label>
            <select class="custom-select custom-select-sm" aria-label="code_ets_select" id="code_ets_select">
                <option value="">Sélectionnez un centre de santé</option>
            </select>
        </div>
        <div class="col-sm-2">
            <label for="num_facture_input">N° facture</label>
            <input type="text" aria-label="num_facture_input" class="form-control form-control-sm" maxlength="13" id="num_facture_input" placeholder="___________________________________" autocomplete="off" value="" />
        </div>
        <div class="col-sm-1">
            <label for="btn_rechercher">&nbsp;</label>
            <button type="submit" id="btn_rechercher" class="btn btn-primary btn-sm btn-block"><b class="fa fa-search"></b></button>
        </div>
    </div>
</form>
<div id="afficher_resultats_div"></div>