<form id="form_recherche_population">
    <div class="form-group row">
        <div class="col-sm-2">
            <label for="num_secu_input">N° sécu</label>
            <input type="text" aria-label="num_secu_input" class="form-control form-control-sm" maxlength="13" id="num_secu_input" placeholder="___________________________________" autocomplete="off" value="" />
        </div>
        <div class="col">
            <label for="nom_input">Nom & Prénom(s)</label>
            <input type="text" aria-label="nom_input" class="form-control form-control-sm" maxlength="100" id="nom_input" placeholder="____________________________________________________________________________________________________________________________________________" autocomplete="off" value="" />
        </div>
        <div class="col-sm-2">
            <label for="date_naissance_input">Date de naissance</label>
            <input type="text" aria-label="date_naissance_input" class="form-control form-control-sm datepicker" maxlength="100" id="date_naissance_input" placeholder="____________________________" autocomplete="off" value="" />
        </div>
        <div class="col-sm-1">
            <label for="btn_rechercher">&nbsp;</label>
            <button type="submit" id="btn_rechercher" class="btn btn-primary btn-sm btn-block"><b class="fa fa-search"></b></button>
        </div>
    </div>
</form>
<div id="div_resultats_recherche"></div>