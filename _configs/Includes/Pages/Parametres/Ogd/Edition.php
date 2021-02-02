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
            require_once '../../../Menu.php';
            ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="<?= URL.'parametres/';?>"><i class="fa fa-cogs"></i> Paramètres</a></li>
                    <li class="breadcrumb-item"><a href="<?= URL.'parametres/ogd/';?>"><i class="fa fa-adjust"></i> Organismes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edition</li>
                </ol>
            </nav>
            <div class="col">
                <?php
                require_once "../../../../Classes/OGD.php";
                $OGD = new OGD();
                if(empty($user['code_ogd'])) {
                    ?>
                    <div class="row">
                        <div class="col-sm-4">
                            <?php include "../../Forms/form_ogd.php";?>
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
        }
    }
}
?>
<script>
    $('.dataTable').DataTable();
</script>
