<?php
require_once "../../../Classes/UTILISATEURS.php";
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
            if(isset($_POST['code_ogd']) || $user['code_ogd']) {
                require_once "../../../Classes/OGD.php";
                $OGD = new OGD();
                if(isset($_POST['code_ogd'])) {
                    $ogd = $OGD->trouver($_POST['code_ogd']);
                }elseif($user['code_ogd']) {
                    $ogd = $OGD->trouver($user['code_ogd']);
                }
                if(isset($_POST['num_secu'])) {
                    require_once "../../../Classes/ASSURES.php";
                    $ASSURES = new ASSURES();
                    $assure = $ASSURES->trouver($_POST['num_secu']);
                    if($assure) {
                        $collectivite = $ASSURES->trouver_collectivite($assure['num_secu']);
                        $coordonnees = $ASSURES->lister_coordonnees($assure['num_secu']);
                        if($ogd) {
                            require_once '../../Menu.php';
                            ?>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                                    <?php
                                    if(isset($_POST['code_ogd'])) {
                                        ?>
                                        <li class="breadcrumb-item"><a href="<?= URL.'ogd/';?>"><i class="fa fa-adjust"></i> Organismes</a></li>
                                        <?php
                                    }
                                    ?>
                                    <li class="breadcrumb-item"><a href="<?= URL.'ogd/details?code='.$ogd['code'];?>"><i class="fa fa-adjust"></i> <?= $ogd['libelle'];?></a></li>
                                    <li class="breadcrumb-item"><a href="<?= URL.'ogd/populations'.$ogd['code'];?>"><i class="fa fa-user-shield"></i> Populations</a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-user"></i> <?= $assure['nom'].' '.$assure['prenom'];?></li>
                                </ol>
                            </nav>
                            <div class="col">
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="col border border-primary">
                                            <p class="row bg-primary text-white"><b>IDENTIFICATION</b></p>
                                            <div class="row">
                                                <div class="col-sm-4">N° Sécu</div>
                                                <div class="col-sm-8"><b class="text-danger"><?= $assure['num_secu'];?></b></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">Clé Sécu</div>
                                                <div class="col-sm-8"><b><?= $assure['cle_secu'];?></b></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">Civilité</div>
                                                <div class="col-sm-8"><b><?= $assure['civilite_code'];?></b></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">Sexe</div>
                                                <div class="col-sm-8"><b><?= $assure['genre_code'];?></b></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">Nom</div>
                                                <div class="col-sm-8"><b><?= $assure['nom'];?></b></div>
                                            </div>
                                            <?php
                                            if($assure['genre_code'] == 'F' && $assure['civilite_code'] == 'MME') {
                                                ?>
                                                <div class="row">
                                                    <div class="col-sm-4">Nom Patronymique</div>
                                                    <div class="col-sm-8"><b><?= $assure['nom_patronymique'];?></b></div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <div class="row">
                                                <div class="col-sm-4">Prénom(s)</div>
                                                <div class="col-sm-8"><b><?= $assure['prenom'];?></b></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">Date de naissance</div>
                                                <div class="col-sm-8"><b><?= date("d/m/Y",strtotime($assure['date_naissance']));?></b></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">Situation familiale</div>
                                                <div class="col-sm-8"><b><?= $assure['situation_familiale'];?></b></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">Lieu de naissance</div>
                                                <div class="col-sm-8"><b><?= $assure['naissance_lieu'].' ('.$assure['naissance_pays'].')';?></b></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">Lieu de résidence</div>
                                                <div class="col-sm-8"><b><?= $assure['adresse_1'].' | '.$assure['adresse_2'].' ('.$assure['adresse_nom_acheminement'].')';?></b></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">Qualité civile</div>
                                                <div class="col-sm-8"><b><?= $assure['qualite_code'];?></b></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">CSP</div>
                                                <div class="col-sm-8"><b><?= $assure['categorie_professionnelle'];?></b></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">Collectivité</div>
                                                <div class="col-sm-8"><b><?= $collectivite['collectivite_code'];?></b></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4">Coordonnées</div>
                                                <div class="col-sm-8"><?php foreach ($coordonnees as $coordonnee) {
                                                            echo $coordonnee['type'].': <b>'.$coordonnee['valeur'].'</b><br />';
                                                } ?></div>
                                            </div>
                                        </div>
                                        <table class="table table-sm">
                                            <tr>
                                                <td>Etat</td>
                                                <td><b class="text-success"><?php if($assure["type_mouvement"]=='MDF'){echo 'Mis a jour';}elseif($assure["type_mouvement"]=='CRE'){echo 'NOUVEAU';}else{echo 'SUPPRIME';} if($assure["date_edit"]==NULL){echo ' <i>le '. date("d/m/Y",strtotime($assure["date_reg"])).'</i>'; }else{echo ' <i>le '. date("d/m/Y",strtotime($assure["date_edit"])).'</i>';} ?></b></td>
                                            </tr>
                                            <tr>
                                                <td>N° Fichier de transmission</td>
                                                <td><b class="text-success"><?=$assure["num_fichier"];?></b></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col">
                                        <div class="col border border-success">
                                            <p class="row bg-success text-white"><b>CONSOMMATIONS</b></p>
                                            <?php
                                            require_once "../../../Classes/FACTURES.php";
                                            $FACTURES = new FACTURES();
                                            $factures = $FACTURES->lister_consommation($user['code_ogd'],$assure['num_secu']);
                                            $nb_factures = count($factures);
                                            if($nb_factures != 0) {
                                                ?>
                                                <table class="table table-bordered table-striped table-hover table-sm">
                                                    <thead class="bg-secondary">
                                                    <tr>
                                                        <th width="5">N°</th>
                                                        <th>DATE SOINS</th>
                                                        <th>N° FACTURE</th>
                                                        <th>ETABLISSEMENT</th>
                                                        <th>MONTANT DEPENSE</th>
                                                        <th>PART CMU</th>
                                                        <th width="5"></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $ligne = 1;
                                                    foreach ($factures as $facture) {
                                                        ?>
                                                        <tr>
                                                            <td><?= $ligne;?></td>
                                                            <td><?= date('d/m/Y',strtotime($facture['date_soins']));?></td>
                                                            <td><?= $facture['num_facture'];?></td>
                                                            <td><?= $facture['code_ets'];?></td>
                                                            <td class="right_align"><?= $facture['montant_depense'];?></td>
                                                            <td class="right_align"><?= $facture['montant_remboursement'];?></td>
                                                            <td><a href="" class="badge badge-info" target="_blank"><i class="fa fa-eye"></i></a></td>
                                                        </tr>
                                                        <?php
                                                        $ligne++;
                                                    }
                                                    ?>
                                                    </tbody>
                                                </table>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            echo '<script type="application/javascript" src="'.JS.'n2.js"></script>';
                            echo '<script type="application/javascript" src="'.JS.'ogd_populations_page.js"></script>';
                        }else {
                            echo '<script>window.location.href="'.URL.'ogd/"</script>';
                        }
                    }else {
                        echo '<script>window.location.href="'.URL.'ogd/populations"</script>';
                    }
                }else {
                    echo '<script>window.location.href="'.URL.'ogd/populations"</script>';
                }

            }else {
                echo '<script>window.location.href="'.URL.'ogd/"</script>';
            }
        }
    }
}
?>
