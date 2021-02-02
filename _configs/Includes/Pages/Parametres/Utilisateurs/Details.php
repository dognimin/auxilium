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
            $user_modules = preg_split('/;/', $user['modules'], -1, PREG_SPLIT_NO_EMPTY);
            $user_sous_modules = preg_split('/;/', $user['sous_modules'], -1, PREG_SPLIT_NO_EMPTY);
            if(!in_array('PARAMETRES_AFFICHAGE',$user_modules)) {
                echo '<script>window.location.href="'.URL.'"</script>';
            }else {
                if(isset($_POST['id_user'])) {
                    $utilisateur = $UTILISATEURS->trouver($_POST['id_user'],NULL,NULL);
                    if($utilisateur) {
                        $utilisateur_modules = preg_split('/;/', $utilisateur['modules'], -1, PREG_SPLIT_NO_EMPTY);
                        $utilisateur_sous_modules = preg_split('/;/', $utilisateur['sous_modules'], -1, PREG_SPLIT_NO_EMPTY);
                        require_once '../../../Menu.php';
                        require_once "../../../../Classes/OGD.php";
                        $OGD = new OGD();
                        $ogds = $OGD->lister();
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
                                    if(in_array('PARAMETRES_EDITION',$user_modules) && in_array('PARAMETRES_UTILISATEURS_EDITION',$user_sous_modules)) {
                                        ?>
                                        <button type="button" class="btn btn-danger btn-sm"  data-toggle="modal" data-target="#resetPasswordModal"><i class="fa fa-lock"></i> Réinitialisation du mot de passe</button>
                                        <button type="button" class="btn btn-<?= str_replace('0','success',str_replace('1','danger',$utilisateur['statut']));?> btn-sm"  data-toggle="modal" data-target="#disableModal"><?= str_replace('0','<i class="fa fa-check"></i> Activation',str_replace('1','<i class="fa fa-times"></i> Désactivation',$utilisateur['statut']));?></button>
                                        <?php
                                    }
                                }
                                if(in_array('PARAMETRES_EDITION',$user_modules) && in_array('PARAMETRES_UTILISATEURS_EDITION',$user_sous_modules)){
                                    ?>
                                    <button type="button" id="btn_edition" class="btn btn-danger btn-sm"><i class="fa fa-edit"></i> Edition</button>
                                    <button type="button" id="btn_habilitation" class="btn btn-danger btn-sm"><i class="fa fa-list-alt"></i> Gestion des habilitations</button>
                                    <?php
                                }
                                ?>
                            </div><br />
                            <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="resetPasswordModalLabel">Réinitialisation du mot de passe</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            </button>
                                        </div>
                                        <div class="modal-body"><?php include "../../Forms/form_reinitialisation_mot_de_passe.php";?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="disableModal" tabindex="-1" aria-labelledby="disableModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="disableModalLabel"><?= str_replace('0','Activation',str_replace('1','Désactivation',$utilisateur['statut']));?> de l'utilisateur</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            </button>
                                        </div>
                                        <div class="modal-body"><?php include "../../Forms/form_activation_desactivation.php";?></div>
                                    </div>
                                </div>
                            </div>

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
}
?>
<script>
    $('.dataTable').DataTable();

    $('#resetPasswordModal').modal({
        show: false,
        backdrop: 'static'
    });
    $('#disableModal').modal({
        show: false,
        backdrop: 'static'
    });
</script>
