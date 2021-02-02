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
                    echo '<script>window.location.href="'.URL.'"</script>';
                }
                else {
                    ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-adjust"></i> <?= $ogd['libelle'];?></li>
                        </ol>
                    </nav>
                    <div class="col">
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <a href="<?= URL.'ogd/chargements';?>" class="btn btn-outline-primary btn-block btn-sm"><i class="fa fa-exchange-alt"></i><br /> Chargements</a>
                                </div>
                                <div class="col">
                                    <a href="<?= URL.'ogd/populations';?>" class="btn btn-outline-primary btn-block btn-sm"><i class="fa fa-user-shield"></i><br /> Populations</a>
                                </div>
                                <div class="col">
                                    <a href="<?= URL.'ogd/factures';?>" class="btn btn-outline-primary btn-block btn-sm"><i class="fa fa-file-medical-alt"></i><br /> Factures</a>
                                </div>
                                <div class="col">
                                    <a href="<?= URL.'ogd/statistiques';?>" class="btn btn-outline-dark btn-block btn-sm"><i class="fa fa-chart-bar"></i><br /> Statistiques</a>
                                </div>
                                <div class="col">
                                    <a href="<?= URL.'parametres/';?>" class="btn btn-outline-danger btn-block btn-sm"><i class="fa fa-cogs"></i><br /> Parametres</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                echo '<script type="application/javascript" src="'.JS.'n1.js"></script>';
            }
        }
    }
}
?>
