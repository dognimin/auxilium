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
            require_once "../../../../Classes/OGD.php";
            $OGD = new OGD();
            $ogds = $OGD->lister();
            ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="<?= URL.'parametres/';?>"><i class="fa fa-cogs"></i> Paramètres</a></li>
                    <li class="breadcrumb-item"><a href="<?= URL.'parametres/utilisateurs/';?>"><i class="fa fa-users"></i> Utilisateurs</a></li>
                    <?php
                    if(isset($_POST['id_user'])) {
                        $utilisateur = $UTILISATEURS->trouver(trim($_POST['id_user']),NULL,NULL);
                        if(!empty($utilisateur['user_id'])) {
                            ?>
                            <li class="breadcrumb-item"><a href="<?= URL.'parametres/utilisateurs/details.php?id='.$utilisateur['user_id'];?>"><i class="fa fa-user"></i> <?= $utilisateur['prenom'].' '.$utilisateur['nom'];?></a></li>
                            <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-edit"></i> Edition</li>
                            <?php
                        }else {
                            echo '<script>window.location.href="'.URL.'parametres/utilisateurs/"</script>';
                        }
                    }else {
                        ?>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-edit"></i> Edition</li>
                        <?php
                    }
                    ?>
                </ol>
            </nav>
            <div class="col"><div class="row">
                    <div class="col">
                        <?php include "../../Forms/form_utilisateur.php";?>
                    </div>
                    <div class="col"></div>
                </div></div>
            <?php
            echo '<script type="application/javascript" src="'.JS.'n2.js"></script>';
        }
    }
}
?>
<script>
    $('.dataTable').DataTable();
</script>
