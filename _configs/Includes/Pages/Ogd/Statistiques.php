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
                echo '<script>window.location.href="'.URL.'ogd/"</script>';
            }else {
                $ogd = $OGD->trouver($user['code_ogd']);
                if(!$ogd) {
                    echo '<script>window.location.href="'.URL.'ogd/"</script>';
                }else {
                    require_once "../../../Classes/STATISTIQUES.php";
                    $STATISTIQUES = new STATISTIQUES();
                    $nombre_populations = $STATISTIQUES->trouver_effectif_populations($ogd['code']);
                    $nombre_factures = $STATISTIQUES->trouver_effectif_factures($ogd['code']);
                    $montant_factures = $STATISTIQUES->trouver_montant_factures($ogd['code']);
                    $effectif_types_factures = $STATISTIQUES->lister_effectif_types_factures($ogd['code']);
                    $montant_types_factures = $STATISTIQUES->lister_montant_types_factures($ogd['code']);
                    ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'ogd/';?>"><i class="fa fa-adjust"></i> <?= $ogd['libelle'];?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-chart-bar"></i> Statistiques</li>
                        </ol>
                    </nav>
                    <div class="col">

                        <div class="row">
                            <div class="col-sm-2">
                                <div class="div_data_box">
                                    <h6>Population globale</h6>
                                    <p><?= number_format($nombre_populations['effectif'],'0','',' ');?></p>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="div_data_box" id="div_top_datas"></div>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-sm btn-outline-dark btn_data btn-danger text-white" id="btn_top_etablissements">TOP Etablissements</button>
                                    <button type="button" class="btn btn-sm btn-outline-dark btn_data" id="btn_top_actes_medicaux">TOP Actes médicaux</button>
                                    <button type="button" class="btn btn-sm btn-outline-dark btn_data" id="btn_top_medicaments">TOP Médicaments</button>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="div_data_box">
                                    <h6>Factures</h6>
                                    <p><?= number_format($nombre_factures['effectif'],'0','',' ');?></p>
                                    <ul>
                                        <?php
                                        foreach ($effectif_types_factures as $effectif_types_facture) {
                                            echo '<li class="text-warning"><b class="center_align">'.number_format($effectif_types_facture['effectif'],'0','',' ').'</b><br /><small class="text-white right_align">'.$effectif_types_facture['libelle'].'</small></li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="div_data_box">
                                    <h6>Montant (F CFA)</h6>
                                    <p><?= number_format($montant_factures['montant'],'0','',' ');?></p>
                                    <ul>
                                        <?php
                                        foreach ($montant_types_factures as $montant_types_facture) {
                                            echo '<li class="text-warning"><b class="center_align">'.number_format($montant_types_facture['montant'],'0','',' ').'</b><br /><small class="text-white right_align">'.$montant_types_facture['libelle'].'</small></li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    echo '<script type="application/javascript" src="'.JS.'n2.js"></script>';
                    echo '<script type="application/javascript" src="'.JS.'parametres_ogd_statistiques.js"></script>';
                }
            }
        }
    }
}
?>
