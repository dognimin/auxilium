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
                            <li class="breadcrumb-item"><a href="<?= URL.'ogd/details?code='.$ogd['code'];?>"><i class="fa fa-adjust"></i> <?= $ogd['libelle'];?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-chart-bar"></i> Statistiques</li>
                        </ol>
                    </nav>
                    <div class="col">
                        <?php
                        require_once "../../../Classes/OGD.php";
                        $OGD = new OGD();
                        if(empty($user['code_ogd'])) {
                            ?>
                            <div class="container">

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
