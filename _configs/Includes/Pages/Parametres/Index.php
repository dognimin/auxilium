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
                    <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-cogs"></i> Paramètres</li>
                </ol>
            </nav>
            <div class="container">
                <div class="row justify-content-md-center">
                    <?php
                    if(!$user['code_ogd']) {
                        ?>
                        <div class="col-md-3">
                            <a href="<?= URL.'parametres/ogd/';?>" class="btn btn-block btn-outline-danger"><i class="fa fa-adjust"></i><br /> Organismes</a>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="col-md-3">
                        <a href="<?= URL.'parametres/utilisateurs/';?>" class="btn btn-block btn-outline-danger"><i class="fa fa-users"></i><br /> Utilisateurs</a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= URL.'parametres/referentiels/';?>" class="btn btn-block btn-outline-danger"><i class="fa fa-list-alt"></i><br /> Référentiels</a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?= URL.'parametres/scripts/';?>" class="btn btn-block btn-outline-danger"><i class="fa fa-code"></i><br /> Scripts</a>
                    </div>
                </div>
            </div>
            <?php
            echo '<script type="application/javascript" src="'.JS.'n1.js"></script>';
        }
    }
}
?>