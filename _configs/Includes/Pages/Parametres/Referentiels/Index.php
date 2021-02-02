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
                    <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-list-alt"></i> Référentiels</li>
                </ol>
            </nav>
            <div class="container">
                <div class="row">
                    <div class="col-sm-4"><a href="<?= URL.'parametres/referentiels/generaux';?>" class="btn btn-outline-danger btn-sm btn-block"><i class="fa fa-list-alt"></i><br /><b>Référentiels</b></a></div>
                    <div class="col-sm-4"><a href="<?= URL.'parametres/referentiels/tables-de-valeur';?>" class="btn btn-outline-danger btn-sm btn-block"><i class="fa fa-list-alt"></i><br /><b>Tables de valeur</b></a></div>
                </div>
            </div>
            <?php
            echo '<script type="application/javascript" src="'.JS.'n2.js"></script>';
        }
    }
}
?>