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
            if($_POST['code_ps']) {
                $code_ps = $_POST['code_ps'];
                require_once "../../../../Classes/PROFESSIONNELSANTE.php";
                require_once '../../../Menu.php';

                $PROFESSIONNELSANTE = new PROFESSIONNELSANTE();
                $ps = $PROFESSIONNELSANTE->trouver($code_ps);
                if($ps) {
                    $specialite_medicale = $PROFESSIONNELSANTE->trouver_specialite_medicale($ps['code_specialite']);
                    ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'parametres/';?>"><i class="fa fa-cogs"></i> Paramètres</a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'parametres/referentiels/';?>"><i class="fa fa-list-alt"></i>  Référentiels</a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'parametres/referentiels/generaux';?>"><i class="fa fa-list-alt"></i>  Référentiels généraux</a></li>
                            <li class="breadcrumb-item"><a href="<?= URL.'parametres/referentiels/professionnels-sante';?>">Professionnels de santé</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= $ps['nom'].' '.$ps['prenom'];?></li>
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
                                                <td><b><?= $ps['code'];?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Nom</td>
                                                <td><b><?= $ps['nom'];?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Pr&eacute;noms</td>
                                                <td><b><?= $ps['prenom'];?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Adresse</td>
                                                <td><b><?= $ps['adresse_postale'];?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Spécialité</td>
                                                <td><b><?= $specialite_medicale['libelle'];?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Ville</td>
                                                <td><b><?= $ps['ville'];?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Téléphone</td>
                                                <td><b><?= $ps['telephone'];?></b></td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td><b><a href="mailto:<?= $ps['email']; ?>"><?= $ps['email'];?></a></b></td>
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
                                $profs = $PROFESSIONNELSANTE->lister_historique($ps['code']);
                                $nb_ps = count($profs);
                                if($nb_ps == 0) {
                                    echo '<p class="alert alert-info center_align">AUCUN HISTORIQUE ENCORE DISPONIBLE</p>';
                                }else {
                                    ?>
                                    <table class="table table-bordered table-hover table-dark">
                                        <thead class="bg-info">
                                        <tr>
                                            <th width="5">N°</th>
                                            <th width="100">CODE</th>
                                            <th>CIVLITE</th>
                                            <th>NOM & PRENOM(S)</th>
                                            <th>SPECIALITE</th>
                                            <th>ADRESSE</th>
                                            <th>VILLE</th>
                                            <th>TELEPHONE</th>
                                            <th>EMAIL</th>
                                            <th width="100">DATE DEBUT</th>
                                            <th width="100">DATE FIN</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $ligne = 1;
                                        foreach ($profs as $prof) {
                                            $specialite_medicale = $PROFESSIONNELSANTE->trouver_specialite_medicale($prof['code_specialite']);

                                            ?>
                                            <tr class="<?php if($prof['validite_date_fin']==''){echo 'alert-success';}?>">
                                                <td><?= $ligne;?></td>
                                                <td><b><?= $prof['ps_code'];?></b></td>
                                                <td><?= $prof['ps_civilite'];?></td>
                                                <td><?= $prof['ps_nom'].' '.$prof['ps_prenom'];?></td>
                                                <td><?= $specialite_medicale['libelle'];?></td>
                                                <td><?= $prof['ps_adresse_postale'];?></td>
                                                <td><?= $prof['ps_ville'];?></td>
                                                <td><?= $prof['ps_telephone'];?></td>
                                                <td><?= $prof['ps_email'];?></td>
                                                <td><?= date('d-m-Y',strtotime($prof['validite_date_debut']));?></td>
                                                <td><?php if($prof['validite_date_fin']==''){echo '';}else{echo date('d-m-Y',strtotime($prof['validite_date_fin']));}?></td>
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