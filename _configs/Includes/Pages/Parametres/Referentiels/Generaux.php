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
                    <li class="breadcrumb-item"><a href="<?= URL.'parametres/referentiels/';?>"><i class="fa fa-list-alt"></i> Référentiels</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-list-alt"></i> Référentiels généraux</li>
                </ol>
            </nav>
            <div class="col">
                <div class="row">
                    <div class="col"><a href="<?= URL.'parametres/referentiels/centres-sante';?>" class="btn btn-danger btn-sm btn-block">Centres de santé</a></div>
                    <div class="col"><a href="<?= URL.'parametres/referentiels/professionnels-sante';?>" class="btn btn-danger btn-sm btn-block">Professionnels de santé</a></div>
                    <div class="col"><a href="<?= URL.'parametres/referentiels/lettres-cles';?>" class="btn btn-danger btn-sm btn-block">Lettres clés</a></div>
                    <div class="col"><a href="<?= URL.'parametres/referentiels/actes-medicaux';?>" class="btn btn-danger btn-sm btn-block" title="Nomenclature Générale les Actes de Médecine et de Biologie de Côte d'Ivoire">Actes médicaux</a></div>
                </div><br />
                <div class="row">
                    <div class="col"><a href="<?= URL.'parametres/referentiels/forfaits-hospitaliers';?>" class="btn btn-danger btn-sm btn-block">Forfaits Hospitaliers</a></div>
                    <div class="col"><a href="<?= URL.'parametres/referentiels/medicaments';?>" class="btn btn-danger btn-sm btn-block">Médicaments</a></div>
                    <div class="col"><a href="<?= URL.'parametres/referentiels/pathologies';?>" class="btn btn-danger btn-sm btn-block">Pathologies</a></div>
                    <div class="col"><a href="<?= URL.'parametres/referentiels/generaux-chargements';?>" class="btn btn-outline-danger btn-sm btn-block"><i class="fa fa-upload"></i> Chargements</a></div>
                </div>
            </div>
            <?php
            echo '<script type="application/javascript" src="'.JS.'n2.js"></script>';
        }
    }
}
?>