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
            require_once '../../Menu.php';
            require_once "../../../Classes/OGD.php";
            $OGD = new OGD();
            if(!$user['code_ogd']) {
                echo '<script>window.location.href="'.URL.'"</script>';
            }else {
                $ogd = $OGD->trouver($user['code_ogd']);
                if(!$ogd) {
                    echo '<script>window.location.href="'.URL.'ogd/chargements"</script>';
                }else {
                    require_once "../../../Classes/LOGS.php";
                    $LOGS = new LOGS();
                    ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'ogd/';?>"><i class="fa fa-adjust"></i> <?= $ogd['libelle'];?></a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'ogd/chargements';?>"><i class="fa fa-exchange-alt"></i> Chargements</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-upload"></i> Imports</li>
                        </ol>
                    </nav>
                    <div class="col">
                        <?php
                        if($user['code_ogd']) {
                            include "../Forms/form_imports.php";
                            $fichiers = $LOGS->lister_fichiers($user['code_ogd'],NULL,'IMP',NULL,NULL);
                            $nb_fichiers = count($fichiers);
                            if($nb_fichiers != 0) {
                                ?>
                                <div id="div_resultats_recherche">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-hover table-sm dataTable">
                                            <thead class="bg-dark text-white">
                                            <tr>
                                                <th width="5">N°</th>
                                                <th width="150">NORME</th>
                                                <th width="80">N° FICHIER</th>
                                                <th width="130">DATE EMISSION</th>
                                                <th>NOM</th>
                                                <th width="80">OCCURRENCES</th>
                                                <th width="130">DATE CREATION</th>
                                                <th width="5"></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $ligne = 1;
                                            foreach ($fichiers as $fichier) {
                                                ?>
                                                <tr>
                                                    <td class="right_align"><?= $ligne;?></td>
                                                    <td><?= $fichier['norme'];?></td>
                                                    <td class="right_align"><b><?= $fichier['num_fichier'];?></b></td>
                                                    <td class="center_align"><?= date('d/m/Y',strtotime($fichier['fichier_date']));?></td>
                                                    <td><?= $fichier['fichier_nom'];?></td>
                                                    <td class="right_align"><?= number_format($fichier['occurrences_fichier'],'0','',' ');?></td>
                                                    <td class="center_align"><?= date('d/m/Y H:i',strtotime($fichier['date_reg']));?></td>
                                                    <td><a href="<?= URL.'IMPORTS/CHARGEMENTS/'.$fichier['norme'].'/'.$user['code_ogd'].'/'.$fichier['fichier_nom'];?>" target="_blank" class="badge badge-success" download="<?= $fichier['fichier_nom'];?>"><i class="fa fa-download"></i></a></td>
                                                </tr>
                                                <?php
                                                $ligne++;
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php
                            }else {

                            }
                        }else {
                            echo '<script>window.location.href="'.URL.'ogd/"</script>';
                        }
                        ?>
                    </div>
                    <?php
                    echo '<script type="application/javascript" src="'.JS.'n2.js"></script>';
                    echo '<script type="application/javascript" src="'.JS.'parametres_ogd_chargements_page.js"></script>';
                }
            }
        }
    }
}
?>
<script>
    $('.dataTable').DataTable();
</script>
