<p class="center_align" id="p_activation_desactivation"></p>
<form id="form_activation_desactivation">
    <div class="form-group">
        <label for="user_actif_inactif_input">Souhaitez-vous réellement <?= str_replace('0','Activer',str_replace('1','Désactiver',$utilisateur['statut'])).' '.$utilisateur['prenom'];?> ?</label>
        <input type="hidden" id="user_actif_inactif_input" value="<?= $utilisateur['user_id'];?>" /><br />
        <button type="submit" id="btn_activer_desactiver" class="btn btn-info btn-sm"><i class="fa fa-check"></i> Valider</button>
        <a href="<?= URL.'parametres/utilisateurs/details?id='.$utilisateur['user_id'];?>" class="btn btn-secondary btn-sm"><i class="fa fa-chevron-circle-left"></i> Retourner</a>
    </div>
</form>