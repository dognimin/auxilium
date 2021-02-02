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
            if($_POST['code_ets']) {
                $code_ets = $_POST['code_ets'];
                require_once "../../../../Classes/ETABLISSEMENTSANTE.php";
                require_once '../../../Menu.php';

                $ETABLISSEMENTSANTE = new ETABLISSEMENTSANTE();
                $ets = $ETABLISSEMENTSANTE->trouver($code_ets);
                if($ets) {
                    ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'parametres/';?>"><i class="fa fa-cogs"></i> Paramètres</a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'parametres/referentiels/';?>"><i class="fa fa-list-alt"></i>  Référentiels</a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'parametres/referentiels/generaux';?>"><i class="fa fa-list-alt"></i>  Référentiels généraux</a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'parametres/referentiels/centres-sante';?>">Centres de santé</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= $ets['raison_sociale'];?></li>
                        </ol>
                    </nav>
                    <div class="col">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="alert alert-primary">
                                    <div class="panel-heading">
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-sm">
                                            <tr>
                                                <td width="150">Code</td>
                                                <td><b><?= $ets['code'];?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Raison sociale</td>
                                                <td><b><?= $ets['raison_sociale'];?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Secteur d'activité</td>
                                                <td><b><?= ''//$secteur['libelle'];?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Ville</td>
                                                <td><b><?= $ets['ville'];?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Téléphone</td>
                                                <td><b><?= $ets['ets_num_telephone'];?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td><b><?= $ets['ets_email'];?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Date début</td>
                                                <td><b><?= date('d/m/Y',strtotime($ets['date_debut_validite']));?></b></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <?php
                                $centres = $ETABLISSEMENTSANTE->lister_historique($ets['code']);
                                $nb_centre = count($centres);
                                if($nb_centre == 0) {
                                    echo '<p align="center" class="alert alert-info">AUCUN HISTORIQUE DISPONIBLE POUR LE CENTRE: '.$ets['raison_sociale'].'</p>';
                                }else {
                                    ?>
                                    <table class="table table-bordered table-hover datatable">
                                        <thead class="bg-secondary">
                                        <tr>
                                            <th width="5">N°</th>
                                            <th>CODE</th>
                                            <th>RAISON SOCIALE</th>
                                            <th>SECTEUR D'ACTIVITE</th>
                                            <th>DEPARTEMENT</th>
                                            <th width="100">DATE DEBUT</th>
                                            <th width="100">DATE FIN</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $ligne = 1;
                                        foreach ($centres as $centre) {
                                            //$sect = $EtablissementsSante->trouver_secteur_activite($centre['ets_honoraire_code']);
                                            ?>
                                            <tr>
                                                <td align="right"><?= $ligne;?></td>
                                                <td><b><?= $centre['ets_code'];?></b></td>
                                                <td><?= $centre['ets_raison_sociale'];?></td>
                                                <td><?= ''//$sect['libelle'];?></td>
                                                <td><?= $centre['ets_nom_bureau_distributeur'];?></td>
                                                <td><?= date('d/m/Y',strtotime($centre['validite_date_debut']));?></td>
                                                <td><?php if($centre['ets_date_fin']==''){echo '';}else{echo date('d/m/Y',strtotime($centre['ets_date_fin']));}?></td>
                                            </tr>
                                            <?php
                                            $ligne++;
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                    <?php
                                }

                                $bdd = null;
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    echo '<script type="application/javascript" src="'.JS.'n2.js"></script>';
                    echo '<script type="application/javascript" src="'.JS.'js_parametres_referentiels_generaux_chargements_page.js"></script>';

                }else {
                    echo '<script>window.location.href="'.URL.'parametres/referentiels/centres-sante"</script>';
                }
            }else {
                echo '<script>window.location.href="'.URL.'parametres/referentiels/centres-sante"</script>';
            }
        }
    }
}
?>