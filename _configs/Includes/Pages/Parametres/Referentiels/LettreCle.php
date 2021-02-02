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
            if($_POST['code_lc']) {
                $code_lc = $_POST['code_lc'];
                require_once "../../../../Classes/LETTRESCLES.php";
                require_once '../../../Menu.php';

                $LETTRESCLES = new LETTRESCLES();
                $lc = $LETTRESCLES->trouver($code_lc);
                if($lc) {
                    ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'parametres/';?>"><i class="fa fa-cogs"></i> Paramètres</a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'parametres/referentiels/';?>"><i class="fa fa-list-alt"></i>  Référentiels</a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'parametres/referentiels/generaux';?>"><i class="fa fa-list-alt"></i>  Référentiels généraux</a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'parametres/referentiels/lettres-cles';?>">Lettres clés</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= $lc['libelle'];?></li>
                        </ol>
                    </nav>
                    <div class="col">
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="alert-primary">
                                    <div class="panel-body">
                                        <table class="table table-hover table-sm">
                                            <tr>
                                                <td width="150">Code</td>
                                                <td><b><?= $lc['code'];?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Libellé</td>
                                                <td><b><?= $lc['libelle'];?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Date effet</td>
                                                <td><b><?= date('d/m/Y',strtotime($lc['date_debut']));?></b></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-7">&nbsp;</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <?php
                                $lettres_cles = $LETTRESCLES->lister_historique($lc['code']);
                                $nb_lettres_cles = count($lettres_cles);
                                if($nb_lettres_cles == 0) {
                                    echo '<p class="alert alert-info center_align">AUCUN HISTORIQUE ENCORE DISPONIBLE</p>';
                                }else {
                                    ?>
                                    <table class="table table-bordered table-hover table-dark">
                                        <thead class="bg-info">
                                        <tr>
                                            <th width="5">N°</th>
                                            <th width="100">CODE</th>
                                            <th>LIBELLE</th>
                                            <th width="100">DATE DEBUT</th>
                                            <th width="100">DATE FIN</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $ligne = 1;
                                        foreach ($lettres_cles as $lettre_cle) {

                                            ?>
                                            <tr class="<?php if($lettre_cle['date_fin']==''){echo 'alert-success';}?>">
                                                <td><?= $ligne;?></td>
                                                <td><b><?= $lettre_cle['code'];?></b></td>
                                                <td><?= $lettre_cle['libelle'];?></td>
                                                <td><?= date('d/m/Y',strtotime($lettre_cle['date_debut']));?></td>
                                                <td><?php if(!$lettre_cle['date_fin']){echo '';}else{echo date('d/m/Y',strtotime($lettre_cle['validite_date_fin']));}?></td>
                                            </tr>
                                            <?php
                                            $ligne++;
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                    <?php
                                }
                                $bdd = null;
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    echo '<script type="application/javascript" src="'.JS.'n2.js"></script>';
                    echo '<script type="application/javascript" src="'.JS.'js_parametres_referentiels_generaux_chargements_page.js"></script>';

                }else {
                    echo '<script>window.location.href="'.URL.'parametres/referentiels/professionnels-sante"</script>';
                }
            }else {
                echo '<script>window.location.href="'.URL.'parametres/referentiels/professionnels-sante"</script>';
            }
        }
    }
}
?>