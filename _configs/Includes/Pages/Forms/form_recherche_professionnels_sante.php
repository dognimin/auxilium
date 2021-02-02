<form id="form_recherche_professionnels_sante">
    <div class="row">
        <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm" id="code_input" placeholder="Code" autocomplete="off" />
        </div>
        <div class="col-sm-4">
            <input type="text" class="form-control form-control-sm" id="nom_input" placeholder="Nom & Prénom(s)" autocomplete="off" />
        </div>
        <div class="col">
            <select class="form-control form-control-sm" id="specialite_input">
                <option value="">Sépcialité</option>
                <?php
                foreach ($specialites as $specialite) {
                    echo '<option value="'.$specialite['code'].'">'.$specialite['libelle'].'</option>';
                }
                ?>
            </select>
        </div>
        <div class="col">
            <select class="form-control form-control-sm" id="ville_input">
                <option value="">Ville</option>
                <?php
                foreach ($villes as $ville) {
                    echo '<option value="'.$ville['ville'].'">'.$ville['ville'].'</option>';
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