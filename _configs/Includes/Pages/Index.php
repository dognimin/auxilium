<?php
require_once "../../Classes/UTILISATEURS.php";
if(!isset($_SESSION['auxilium_user_id'])){
    echo '<script>window.location.href="'.URL.'connexion"</script>';
}
else {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);

    if(!$user['user_id']) {
        session_destroy();
        echo '<script>window.location.href="'.URL.'"</script>';
    }
    else {
        if($user['mot_de_passe_statut'] == 0) {
            echo '<script>window.location.href="'.URL.'maj-mot-de-passe"</script>';
        }else {
            require_once '../Menu.php';
            ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-home"></i></li>
                </ol>
            </nav>
            <?php
            if(empty($user['code_ogd'])) {
                ?>
               <div class="container">
                   <div class="row justify-content-md-center">

                       <div class="col-md-3">
                           <a href="<?= URL.'ogd/';?>" class="btn btn-block btn-outline-primary"><i class="fa fa-adjust"></i><br /> OGD</a>
                       </div>
                       <div class="col-md-3">
                           <a href="<?= URL.'statistiques/';?>" class="btn btn-block btn-outline-primary"><i class="fa fa-chart-bar"></i><br /> Statistiques</a>
                       </div>
                       <div class="col-md-3">
                               <a href="<?= URL.'parametres/';?>" class="btn btn-block btn-outline-danger"><i class="fa fa-cogs"></i><br /> Paramètres</a>
                       </div>
                   </div>
               </div>
                <?php
            }else {
                echo '<script>window.location.href="'.URL.'ogd/"</script>';
            }

            echo '<script type="application/javascript" src="'.NODE_MODULES.'jqueryui/jquery-ui.js"></script>';
            echo '<script type="application/javascript" src="'.JS.'n0.js"></script>';
        }
    }

}


?>