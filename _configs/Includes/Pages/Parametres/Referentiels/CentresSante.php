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
            require_once "../../../../Classes/ETABLISSEMENTSANTE.php";
            require_once '../../../Menu.php';

            $ETABLISSEMENTSANTE = new ETABLISSEMENTSANTE();
            $secteurs_activite = $ETABLISSEMENTSANTE->lister_secteurs_activite();
            $villes = $ETABLISSEMENTSANTE->lister_villes();
            ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="<?= URL.'parametres/';?>"><i class="fa fa-cogs"></i> Paramètres</a></li>
                    <li class="breadcrumb-item"><a href="<?= URL.'parametres/referentiels/';?>"><i class="fa fa-list-alt"></i>  Référentiels</a></li>
                    <li class="breadcrumb-item"><a href="<?= URL.'parametres/referentiels/generaux';?>"><i class="fa fa-list-alt"></i>  Référentiels généraux</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Centres de santé</li>
                </ol>
            </nav>
            <div class="col">
                <?php include "../../Forms/form_recherche_centres_sante.php";?>
            </div>
            <?php
            echo '<script type="application/javascript" src="'.JS.'n2.js"></script>';
            echo '<script type="application/javascript" src="'.JS.'js_parametres_referentiels_generaux_chargements_page.js"></script>';
        }
    }
}
?>