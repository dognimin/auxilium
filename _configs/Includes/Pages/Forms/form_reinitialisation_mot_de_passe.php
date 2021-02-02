<p class="center_align" id="p_reinitialisation_mot_de_passe"></p>
<form id="form_reinitialisation_mot_de_passe">
    <div class="form-group">
        <label for="user_password_reset_input">Souhaitez-vous réellement réinitialiser le mot de passe de <?= $utilisateur['prenom'];?> ?</label>
        <input type="hidden" id="user_password_reset_input" value="<?= $utilisateur['user_id'];?>" /><br />
        <button type="submit" id="btn_reset_password" class="btn btn-info btn-sm"><i class="fa fa-check"></i> Valider</button>
        <a href="<?= URL.'parametres/utilisateurs/details?id='.$utilisateur['user_id'];?>" class="btn btn-secondary btn-sm"><i class="fa fa-chevron-circle-left"></i> Retourner</a>
    </div>
</form>