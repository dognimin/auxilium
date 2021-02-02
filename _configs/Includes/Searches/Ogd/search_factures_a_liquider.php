<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(!empty($utilisateur['user_id'])) {
        $parametres = array(
            'norme' => $_POST['norme'],
            'num_fichier' => $_POST['num_fichier'],
            'num_facture' => $_POST['num_facture'],
            'code_ets' => $_POST['code_ets'],
            'statut' => $_POST['statut']
        );
        $log = $UTILISATEURS->ajouter_log_piste_audit(CLIENT_ADRESSE_IP,'RECHERCHE',json_encode($parametres),$utilisateur['user_id']);
        if($log['success'] == true) {
            require_once '../../../Classes/FACTURES.php';
            require_once '../../../Classes/ASSURES.php';
            $FACTURES = new FACTURES();
            $ASSURES = new ASSURES();
            $factures = $FACTURES->lister_factures($utilisateur['code_ogd'],$parametres['norme'],$parametres['num_fichier'], $parametres['num_facture'], $parametres['code_ets'],$parametres['statut']);
            $nb_factures = count($factures);
            if($nb_factures != 0) {
                $etablissements = $FACTURES->lister_factures_ets($utilisateur['code_ogd'],$parametres['norme'],$parametres['num_fichier'], $parametres['num_facture'], $parametres['code_ets'],$parametres['statut']);
                $nb_etablissements = count($etablissements);
                if($nb_etablissements != 0) {
                    ?>
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <h5 type="button" class="alert alert-dark center_align">
                                    <span class="badge badge-light"><?= number_format($nb_factures,'0','',' ');?></span>
                                    <br />Factures
                                </h5>
                            </div>
                            <div class="col">
                                <h5 type="button" class="alert alert-dark center_align">
                                    <span class="badge badge-light"><?= number_format($nb_etablissements,'0','',' ');?></span>
                                    <br />Etablissements
                                </h5>
                            </div>
                            <div class="col-sm-12">
                                <table id="facture_table" class="table table-sm table-bordered table-hover" data-toggle="table">
                                    <thead class="bg-info">
                                    <tr>
                                        <th width="5">N°</th>
                                        <th width="100">CODE ETS</th>
                                        <th width="100">DATE SOINS</th>
                                        <th width="100">N° DOSSIER</th>
                                        <th width="100">N° FACTURE</th>
                                        <th width="100">N° SECU</th>
                                        <th>NOM & PRENOM(S)</th>
                                        <th width="5"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $ligne = 1;
                                    foreach ($factures as $facture) {
                                        $assure = $ASSURES->trouver($facture['num_secu']);
                                        ?>
                                        <tr>
                                            <td class="right_align"><?= $ligne;?></td>
                                            <td><?= $facture['ets_code'];?></td>
                                            <td class="center_align"><?= date('d/m/Y',strtotime($facture['date_facture']));?></td>
                                            <?php
                                            if($facture['num_facture'] == $facture['num_ds']) {
                                                echo '<td class="right_align"><b>'.$facture['num_ds'].'</b></td>';
                                            }else {
                                                echo '<td class="right_align">'.$facture['num_ds'].'</td>';
                                            }
                                            ?>
                                            <td class="right_align"><b><?= $facture['num_facture'];?></b></td>

                                            <td><?= $facture['num_secu'];?></td>
                                            <td><?= $assure['nom'].' '.$assure['prenom'];?></td>
                                            <td><a href="<?= URL.'ogd/facture?num='.$facture['num_facture'];?>" class="badge badge-info"><i class="fa fa-eye"></i></a></td>
                                        </tr>
                                        <?php
                                        $ligne++;
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    <?php
                }else {
                    echo '<p class="text-info display-4" align="center">Aucun résultat ne correspond à votre recherche.</p>';
                }
            }else {
                echo '<p class="text-info display-4" align="center">Aucun résultat ne correspond à votre recherche.</p>';
            }
        }else {
            echo '<p class="alert alert-info center_align"></p>';
        }
    }else {
        echo '<script type="application/javascript">window.location.href="'.URL.'"</script>';
    }
}else {
    echo '<script type="application/javascript">window.location.href="'.URL.'"</script>';
}
?>
