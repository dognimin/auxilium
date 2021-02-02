<?php
require_once "../../Classes/UTILISATEURS.php";
if(!isset($_SESSION['auxilium_user_id'])){
    echo '<script>window.location.href="'.URL.'connexion"</script>';
}else {
    $UTILISATEURS = new UTILISATEURS();
    $user = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(empty($user['user_id'])) {
        session_destroy();
        echo '<script>window.location.href="'.URL.'"</script>';
    }else {
        if($user['mot_de_passe_statut'] != 0) {
            echo '<script>window.location.href="'.URL.'"</script>';
        }else {
            require_once '../Menu.php';
            ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i> Accueil</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-lock"></i> Mise à jour du mot de passe</li>
                </ol>
            </nav>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-3">
                        <?php include "Forms/form_mot_de_passe.php";?>
                        <script type="application/javascript" src="<?= JS.'mot_de_passe.js';?>"></script>
                    </div>
                    <div class="col">
                        <div id="mot_de_passe_div"></div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
}
?>