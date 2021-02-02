<div id="div_utilisateur">
    <br />
    <form id="form_utilisateur">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <select class="custom-select custom-select-sm" id="">
                        <option value="">Organisme</option>
                        <?php
                        $ogds = $OGD->lister();
                        foreach ($ogds as $ogd) {
                            echo '<option value="'.$ogd['code'].'">'.$ogd['libelle'].'</option>';
                        }
                        ?>
                    </select>
                    <small id="email_small"></small>
                </div>
                <div class="form-group">
                    <label for="email_input">Email <exp class="text-danger">*</exp></label>
                    <input type="email" class="form-control form-control-sm" id="email_input" value="<?php if(isset($utilisateur)){echo $utilisateur['email'];} ?>" aria-describedby="Email" maxlength="100" autocomplete="off" required placeholder="______________________________________________________________________" />
                    <small id="email_small"></small>
                </div>
                <div class="form-group">
                    <label for="username_input">Nom d'utilisateur <exp class="text-danger">*</exp></label>
                    <input type="text" class="form-control form-control-sm" id="username_input" value="<?php if(isset($utilisateur)){echo $utilisateur['pseudo'];} ?>" aria-describedby="Nom d'utilisateur" maxlength="50" autocomplete="off" required placeholder="______________________________________________________________________" />
                    <small id="username_small"></small>
                </div>
                <div class="form-group">
                    <label for="firstname_input">Prénom(s) <exp class="text-danger">*</exp></label>
                    <input type="text" class="form-control form-control-sm" id="firstname_input" value="<?php if(isset($utilisateur)){echo $utilisateur['prenom'];} ?>" aria-describedby="Prénom(s)" maxlength="50" autocomplete="off" required placeholder="______________________________________________________________________" />
                    <small id="firstname_small"></small>
                </div>
                <div class="form-group">
                    <label for="lastname_input">Nom <exp class="text-danger">*</exp></label>
                    <input type="text" class="form-control form-control-sm" id="lastname_input" value="<?php if(isset($utilisateur)){echo $utilisateur['nom'];} ?>" aria-describedby="Nom" maxlength="30" autocomplete="off" required placeholder="______________________________________________________________________" />
                    <small id="lastname_small"></small>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="direction_input">Direction</label>
                    <input type="text" class="form-control form-control-sm" id="direction_input" value="<?php if(isset($utilisateur)){echo $utilisateur['direction'];} ?>" aria-describedby="Direction" maxlength="100" autocomplete="off" placeholder="______________________________________________________________________" />
                    <small id="direction_small"></small>
                </div>
                <div class="form-group">
                    <label for="service_input">Service</label>
                    <input type="text" class="form-control form-control-sm" id="service_input" value="<?php if(isset($utilisateur)){echo $utilisateur['service'];} ?>" aria-describedby="service" maxlength="100" autocomplete="off" placeholder="______________________________________________________________________" />
                    <small id="service_small"></small>
                </div>
                <div class="form-group">
                    <label for="fonction_input">Fonction</label>
                    <input type="text" class="form-control form-control-sm" id="fonction_input" value="<?php if(isset($utilisateur)){echo $utilisateur['fonction'];} ?>" aria-describedby="Fonction" maxlength="100" autocomplete="off" placeholder="______________________________________________________________________" />
                    <small id="fonction_small"></small>
                </div>
                <div class="form-group">
                    <label for="num_telephone_input">N° Téléphone <exp class="text-danger">*</exp></label>
                    <input type="text" class="form-control form-control-sm" id="num_telephone_input" value="<?php if(isset($utilisateur)){echo $utilisateur['num_telephone'];} ?>" aria-describedby="N° Téléphone" maxlength="10" autocomplete="off" required placeholder="______________________________________________________________________" />
                    <small id="num_telephone_small"></small>
                </div>
                <input type="hidden" id="user_id_input" value="" />
                <button type="submit" id="button_utilisateur" class="btn btn-info btn-block btn-sm"><i class="fa fa-save"></i> Enregistrer</button>
            </div>
        </div>
    </form><br />
</div>