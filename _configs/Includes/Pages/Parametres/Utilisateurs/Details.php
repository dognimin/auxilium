<?php
require_once "../../../../Classes/UTILISATEURS.php";
if(!isset($_SESSION['auxilium_user_id'])){
    echo '<script>window.location.href="'.URL.'connexion"</script>';
}else {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(empty($user['user_id'])) {
        session_destroy();
        echo '<script>window.location.href="'.URL.'"</script>';
    }else {
        if($user['mot_de_passe_statut'] == 0) {
            echo '<script>window.location.href="'.URL.'maj-mot-de-passe"</script>';
        }else {
            if(isset($_POST['id_user'])) {
                $utilisateur = $UTILISATEURS->trouver($_POST['id_user'],NULL,NULL);
                if($utilisateur) {
                    require_once '../../../Menu.php';
                    require_once "../../../../Classes/OGD.php";
                    $OGD = new OGD();
                    ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'parametres/';?>"><i class="fa fa-cogs"></i> Paramètres</a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'parametres/utilisateurs/';?>"><i class="fa fa-users"></i> Utilisateurs</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= $utilisateur['nom'].' '.$utilisateur['prenom'];?></li>
                        </ol>
                    </nav>
                    <div class="col">
                        <div class="right_align">
                            <?php
                            if($user['user_id'] == $utilisateur['user_id']) {
                                ?><button type="button" id="btn_maj_mot_de_passe" class="btn btn-danger btn-sm"><i class="fa fa-lock"></i> mise à jour du mot de passe</button><?php
                            }else {
                                ?>
                                <button type="button" class="btn btn-danger btn-sm"><i class="fa fa-lock"></i> Réinitialisation du mot de passe</button>
                                <button type="button" class="btn btn-danger btn-sm"><i class="fa fa-times"></i> Désactivation</button>
                                <?php
                            }
                            ?>
                            <button type="button" id="btn_edition" class="btn btn-danger btn-sm"><i class="fa fa-edit"></i> Edition</button>
                            <button type="button" id="btn_habilitation" class="btn btn-danger btn-sm"><i class="fa fa-list-alt"></i> Gestion des habilitations</button>
                        </div><br />
                        <div class="row">
                            <div class="col-sm-5">
                                <table class="table table-bordered table-sm table-hover">
                                    <tr>
                                        <td width="200" class="<?php if($utilisateur['statut'] == 0){echo 'border-danger';}else {echo 'border-success';}?>">Statut</td>
                                        <td class="<?php if($utilisateur['statut'] == 0){echo 'text-danger border-danger';}else {echo 'text-success border-success';}?>"><b><?= str_replace('0','Désactivé',str_replace('1','Activé',$utilisateur['statut']));?></b></td>
                                    </tr>
                                    <tr>
                                        <td>Organisme</td>
                                        <td><b><?= $utilisateur['code_ogd'];?></b></td>
                                    </tr>
                                    <tr>
                                        <td>Pseudo</td>
                                        <td><b><?= $utilisateur['pseudo'];?></b></td>
                                    </tr>
                                    <tr>
                                        <td>Prénom(s)</td>
                                        <td><b><?= $utilisateur['prenom'];?></b></td>
                                    </tr>
                                    <tr>
                                        <td>Nom</td>
                                        <td><b><?= $utilisateur['nom'];?></b></td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td><b><a href="mailto:<?= $utilisateur['email']; ?>"><?= $utilisateur['email'];?></a></b></td>
                                    </tr>
                                    <tr>
                                        <td>N° Téléphone</td>
                                        <td><b><?= $utilisateur['num_telephone'];?></b></td>
                                    </tr>
                                    <tr>
                                        <td>Direction</td>
                                        <td><b><?= $utilisateur['direction'];?></b></td>
                                    </tr>
                                    <tr>
                                        <td>Service</td>
                                        <td><b><?= $utilisateur['service'];?></b></td>
                                    </tr>
                                    <tr>
                                        <td>Fonction</td>
                                        <td><b><?= $utilisateur['fonction'];?></b></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col" id="div_utilisateur_profil">
                                <div id="div_mot_de_passe">
                                    <div class="row">
                                        <div class="col"><?php include "../../Forms/form_mot_de_passe.php";?></div>
                                        <div class="col"></div>
                                    </div>
                                </div>
                                <div id="div_edition">
                                    <div class="row">
                                        <div class="col"><?php include "../../Forms/form_utilisateur.php";?></div>
                                    </div>
                                </div>
                                <div id="div_habilitation">
                                    <div class="row">
                                        <div class="col"><?php include "../../Forms/form_habilitation.php";?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    echo '<script type="application/javascript" src="'.JS.'n2.js"></script>';
                    echo '<script type="application/javascript" src="'.JS.'js_parametres_utilisateurs.js"></script>';
                }else {
                    echo '<script>window.location.href="'.URL.'parametres/utilisateurs/"</script>';
                }
            }else {
                echo '<script>window.location.href="'.URL.'parametres/utilisateurs/"</script>';
            }
        }
    }
}
?>
<script>
    $('.dataTable').DataTable();
</script>
