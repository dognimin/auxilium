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
            if(isset($_POST['code_ogd'])) {
                require_once "../../../Classes/OGD.php";
                $OGD = new OGD();
                $ogd = $OGD->trouver($_POST['code_ogd']);
                if($ogd) {
                    require_once '../../Menu.php';
                    ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'ogd/';?>"><i class="fa fa-adjust"></i> Organismes</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= $ogd['libelle'];?></li>
                        </ol>
                    </nav>
                    <div class="col">
                        <?php
                        require_once "../../../Classes/OGD.php";
                        $OGD = new OGD();
                        if(empty($user['code_ogd'])) {
                            ?>
                            <div class="container">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <a href="<?= URL.'ogd/chargements?code-ogd='.$ogd['code'];?>" class="btn btn-outline-primary btn-block btn-sm"><i class="fa fa-upload"></i><br /> Chargements</a>
                                    </div>
                                    <div class="col-sm-3">
                                        <a href="<?= URL.'ogd/populations?code-ogd='.$ogd['code'];?>" class="btn btn-outline-primary btn-block btn-sm"><i class="fa fa-user-shield"></i><br /> Populations</a>
                                    </div>
                                    <div class="col-sm-3">
                                        <a href="<?= URL.'ogd/factures?code-ogd='.$ogd['code'];?>" class="btn btn-outline-primary btn-block btn-sm"><i class="fa fa-file-medical-alt"></i><br /> Factures</a>
                                    </div>
                                    <div class="col-sm-3">
                                        <a href="<?= URL.'ogd/statistiques?code-ogd='.$ogd['code'];?>" class="btn btn-outline-primary btn-block btn-sm"><i class="fa fa-chart-bar"></i><br /> Statistiques</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }else {
                            echo '<script>window.location.href="'.URL.'ogd/"</script>';
                        }
                        ?>
                    </div>
                    <?php
                    echo '<script type="application/javascript" src="'.JS.'n2.js"></script>';
                    echo '<script type="application/javascript" src="'.JS.'parametres_ogd_page.js"></script>';
                }else {
                    echo '<script>window.location.href="'.URL.'ogd/"</script>';
                }
            }else {
                echo '<script>window.location.href="'.URL.'ogd/"</script>';
            }
        }
    }
}
?>
<script>
    $('.dataTable').DataTable();
</script>
