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
                    $type = $_POST['type'];
                    require_once '../../Menu.php';
                    ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'ogd/';?>"><i class="fa fa-adjust"></i> Organismes</a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'ogd/details?code='.$ogd['code'];?>"><i class="fa fa-adjust"></i> <?= $ogd['libelle'];?></a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'ogd/chargements?code='.$ogd['code'];?>"><i class="fa fa-upload"></i> Chargements</a></li>
                            <?php
                            if($type) {
                                ?>
                                <li class="breadcrumb-item"><a href="<?= URL.'ogd/imports?code-ogd='.$ogd['code'];?>"><i class="fa fa-upload"></i> Imports</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?= $type;?></li>
                                <?php
                            }else {
                                ?>
                                <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-upload"></i> Imports</li>
                                <?php
                            }
                            ?>
                        </ol>
                    </nav>
                    <div class="col">
                        <?php
                        require_once "../../../Classes/OGD.php";
                        $OGD = new OGD();
                        if(empty($user['code_ogd'])) {
                            ?>
                            <div class="container">
                                <?php
                                if($type) {
                                    if($type == 'populations') {
                                        echo $type;
                                    }elseif ($type == 'deca') {
                                        echo $type;
                                    }elseif ($type == 'decret') {
                                        echo $type;
                                    }else {
                                        echo '<script>window.location.href="'.URL.'ogd/imports?code-ogd='.$ogd['code'].'"</script>';
                                    }
                                }else {
                                    ?>
                                    <div class="row justify-content-center">
                                        <div class="col-sm-3">
                                            <a href="<?= URL.'ogd/imports?code-ogd='.$ogd['code'].'&type=populations';?>" class="btn btn-outline-primary btn-block btn-sm"><i class="fa fa-users"></i><br /> POPULATIONS</a>
                                        </div>
                                        <div class="col-sm-3">
                                            <a href="<?= URL.'ogd/imports?code-ogd='.$ogd['code'].'&type=deca';?>" class="btn btn-outline-primary btn-block btn-sm"><i class="fa fa-file-code"></i><br /> DECA</a>
                                        </div>
                                        <div class="col-sm-3">
                                            <a href="<?= URL.'ogd/imports?code-ogd='.$ogd['code'].'&type=decret';?>" class="btn btn-outline-info btn-block btn-sm"><i class="fa fa-file-code"></i><br /> DECRET</a>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
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
                    echo '<script>window.location.href="'.URL.'parametres/ogd/"</script>';
                }
            }else {
                echo '<script>window.location.href="'.URL.'parametres/ogd/"</script>';
            }
        }
    }
}
?>
<script>
    $('.dataTable').DataTable();
</script>
