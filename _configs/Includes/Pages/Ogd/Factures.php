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
                    ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'ogd/';?>"><i class="fa fa-adjust"></i> <?= $ogd['libelle'];?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-file-medical-alt"></i> Factures</li>
                        </ol>
                    </nav>
                    <div class="col">
                        <div class="container">
                            <?php
                            if($user['code_ogd']) {
                                $lien = '';
                            }else {
                                $lien = '?code-ogd='.$ogd['code'];
                            }
                            ?>
                            <div class="row justify-content-center">
                                <div class="col-sm-3">
                                    <a href="<?= URL.'ogd/factures-recherche'.$lien;?>" class="btn btn-outline-primary btn-block btn-sm"><i class="fa fa-search"></i><br /> RECHERCHE</a>
                                </div>
                                <div class="col-sm-3">
                                    <a href="<?= URL.'ogd/factures-verification'.$lien;?>" class="btn btn-outline-primary btn-block btn-sm"><i class="fa fa-check-square"></i><br /> VERIFICATION</a>
                                </div>
                                <div class="col-sm-3">
                                    <a href="<?= URL.'ogd/factures-liquidation'.$lien;?>" class="btn btn-outline-primary btn-block btn-sm"><i class="fa fa-flask"></i><br /> LIQUIDATION</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    echo '<script type="application/javascript" src="'.JS.'n2.js"></script>';
                    echo '<script type="application/javascript" src="'.JS.'parametres_ogd_page.js"></script>';
                }
            }
        }
    }
}
?>
<script>
    $('.dataTable').DataTable();
</script>
