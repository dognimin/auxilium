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
                echo '<script>window.location.href="'.URL.'ogd/"</script>';
            }else {
                $ogd = $OGD->trouver($user['code_ogd']);
                if(!$ogd) {
                    echo '<script>window.location.href="'.URL.'ogd/"</script>';
                }else {
                    require_once "../../../Classes/FACTURES.php";
                    $FACTURES = new FACTURES();
                    $etablissements = $FACTURES->lister_etablissement_sante();
                    $statuts = $FACTURES->lister_factures_statut();
                    ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'ogd/';?>"><i class="fa fa-adjust"></i> <?= $ogd['libelle'];?></a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'ogd/factures';?>"><i class="fa fa-file-medical-alt"></i> Factures</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-search"></i> Recherche</li>
                        </ol>
                    </nav>
                    <div class="col">
                        <?php
                        include "../Forms/form_recherche_factures.php";
                        ?>
                    </div>
                    <?php
                    echo '<script type="application/javascript" src="'.JS.'n2.js"></script>';
                    echo '<script type="application/javascript" src="'.JS.'ogd_factures_page.js"></script>';
                }
            }
        }
    }
}
?>
