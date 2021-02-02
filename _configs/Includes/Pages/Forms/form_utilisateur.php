<div id="div_utilisateur">
    <br />
    <p class="center_align" id="p_utilisateur"></p>
    <form id="form_utilisateur">
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="code_ogd_input">Organisme</label>
                    <select class="custom-select custom-select-sm" id="code_ogd_input" aria-label="Organisme">
                        <option value="">Organisme</option>
                        <?php
                        foreach ($ogds as $ogd) {
                            ?>
                            <option value="<?= $ogd['code'];?>"<?php if(isset($utilisateur) && $utilisateur['code_ogd'] == $ogd['code']){echo 'selected';} ?>><?= $ogd['libelle'];?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <small id="code_ogd_small"></small>
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
                <div class="form-group">
                    <label for="code_statut_input">Statut</label>
                    <select class="custom-select custom-select-sm" id="code_statut_input" aria-label="Statut">
                        <option value="">Statut</option>
                        <option value="-1" <?php if(isset($utilisateur) && $utilisateur['statut'] == 0){echo 'selected';} ?>>Désactivé</option>
                        <option value="1" <?php if(isset($utilisateur) && $utilisateur['statut'] == 1){echo 'selected';} ?>>Activé</option>
                    </select>
                    <small id="code_statut_small"></small>
                </div>
                <div class="form-group">
                    <input type="hidden" id="user_id_input" value="<?php if(isset($utilisateur)){echo $utilisateur['user_id'];} ?>" />
                    <button type="submit" id="button_utilisateur" class="btn btn-info btn-block btn-sm"><i class="fa fa-save"></i> Enregistrer</button>
                </div>
            </div>
        </div>
    </form><br />
</div>