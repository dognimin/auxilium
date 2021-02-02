<p class="center_align" id="p_resultats"></p>
<form id="form_ogd">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label for="date_debut_ogd_input">Date début</label>
                <input type="text" class="form-control form-control-sm datepicker" id="date_debut_ogd_input" aria-describedby="Date début" maxlength="10" autocomplete="off" value="<?= date('d/m/Y',time());?>" readonly placeholder="______________________________________________________________________" />
                <small id="date_debut_ogd_small"></small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label for="date_fin_ogd_input">Date fin</label>
                <input type="text" class="form-control form-control-sm datepicker" id="date_fin_ogd_input" aria-describedby="Date fin" maxlength="10" autocomplete="off" readonly placeholder="______________________________________________________________________" />
                <small id="date_debut_ogd_small"></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="form-group">
                <label for="num_centre_ogd_input">N° centre</label>
                <input type="text" class="form-control form-control-sm num_input" id="num_centre_ogd_input" aria-describedby="N° Centre" maxlength="3" autocomplete="off" placeholder="______________________________________________________________________" />
                <small id="num_centre_ogd_small"></small>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="regime_ogd_input">Régime</label>
                <input type="text" class="form-control form-control-sm num_input" id="regime_ogd_input" aria-describedby="Régime OGD" maxlength="3" autocomplete="off" placeholder="______________________________________________________________________" />
                <small id="regime_ogd_small"></small>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="caisse_ogd_input">Caisse</label>
                <input type="text" class="form-control form-control-sm num_input" id="caisse_ogd_input" aria-describedby="Caisse OGD" maxlength="3" autocomplete="off" placeholder="______________________________________________________________________" />
                <small id="caisse_ogd_small"></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label for="code_ogd_input">Code</label>
                <input type="text" class="form-control form-control-sm num_input" id="code_ogd_input" aria-describedby="Code OGD" maxlength="8" autocomplete="off" placeholder="______________________________________________________________________" />
                <small id="code_ogd_small"></small>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="libelle_ogd_input">Libellé</label>
                <input type="text" class="form-control form-control-sm" id="libelle_ogd_input" aria-describedby="Libellé OGD" maxlength="30" autocomplete="off" placeholder="______________________________________________________________________" />
                <small id="libelle_ogd_small"></small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <button type="submit" id="button_enregistrer" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Enregistrer</button>
            <a href="<?php if(!isset($_POST['code'])){echo URL.'parametres/ogd/';} ?>" class="btn btn-secondary btn-sm"><i class="fa fa-chevron-left"></i> Retourner</a>
        </div>
    </div>
</form>