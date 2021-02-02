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
            ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-adjust"></i> Organismes</li>
                </ol>
            </nav>
            <div class="col">
                <?php
                require_once "../../../Classes/OGD.php";
                $OGD = new OGD();
                if(empty($user['code_ogd'])) {
                    $organismes = $OGD->lister();
                    $nb_organismes = count($organismes);
                    if($nb_organismes != 0) {
                        ?>
                        <div class="container">
                            <div class="row">
                                <?php
                                foreach ($organismes as $organisme) {
                                    ?>
                                    <div class="col-sm-4"><a href="<?= URL.'ogd/details?code='.$organisme['code'];?>" class="btn btn-outline-primary btn-sm btn-block"><i class="fa fa-adjust"></i><br /><b><?= $organisme['libelle'];?></b></a></div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                }else {

                }
                ?>
            </div>
            <?php
            echo '<script type="application/javascript" src="'.JS.'n1.js"></script>';
        }
    }
}
?>
