<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(!empty($utilisateur['user_id'])) {
        $parametres = array(
            'date_debut' => date('Y-m-d',strtotime(str_replace('/','-',$_POST['date_debut']))),
            'date_fin' => date('Y-m-d',strtotime(str_replace('/','-',$_POST['date_fin']))),
            'num_facture' => trim($_POST['num_facture']),
            'code_ets' => trim($_POST['code_ets']),
            'statut' => trim($_POST['code_statut'])
        );
        $log = $UTILISATEURS->ajouter_log_piste_audit(CLIENT_ADRESSE_IP,'RECHERCHE',json_encode($parametres),$utilisateur['user_id']);
        if($log['success'] == true) {
            require_once '../../../Classes/FACTURES.php';
            require_once '../../../Classes/ASSURES.php';
            $FACTURES = new FACTURES();
            $ASSURES = new ASSURES();
            $factures = $FACTURES->moteur_recherche($parametres['date_debut'],$parametres['date_fin'], $parametres['num_facture'], $parametres['code_ets'],$parametres['statut']);
            $nb_factures = count($factures);
            if($nb_factures != 0) {
                $etablissements = $FACTURES->lister_factures_ets($utilisateur['code_ogd'],NULL,NULL, $parametres['num_facture'], $parametres['code_ets'],$parametres['statut']);
                $nb_etablissements = count($etablissements);
                if($nb_etablissements != 0) {
                    ?>
                    <div class="col">
                        <div class="row">
                            <div class="col">
                               <p class="right_align">
                                   <button type="button" class="btn btn-success btn-sm"><i class="fa fa-download"></i> Exporter</button>
                                   <button type="button" class="btn btn-secondary btn-sm"><i class="fa fa-print"></i> Imprimer</button>
                               </p>
                            </div>
                            <div class="col-sm-12">
                                <table id="facture_table" class="table table-sm table-bordered table-hover" data-toggle="table">
                                    <thead class="bg-info">
                                    <tr>
                                        <th width="5">N°</th>
                                        <th width="100">DATE SOINS</th>
                                        <th width="100">N° DOSSIER</th>
                                        <th width="100">N° FACTURE</th>
                                        <th width="100">CODE ETS</th>
                                        <th>RAISON SOCIALE</th>
                                        <th width="100">N° SECU</th>
                                        <th>NOM & PRENOM(S)</th>
                                        <th width="5"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $ligne = 1;
                                    foreach ($factures as $facture) {
                                        $ets = $FACTURES->trouver_etablissement_sante($facture['ets_code'],$facture['facture_date']);
                                        ?>
                                        <tr>
                                            <td class="right_align"><?= $ligne;?></td>
                                            <td class="center_align"><?= date('d/m/Y',strtotime($facture['facture_date']));?></td>
                                            <?php
                                            if($facture['facture_num'] == $facture['num_ds']) {
                                                echo '<td class="right_align"><b>'.$facture['num_ds'].'</b></td>';
                                            }else {
                                                echo '<td class="right_align">'.$facture['num_ds'].'</td>';
                                            }
                                            ?>
                                            <td class="right_align"><b><?= $facture['facture_num'];?></b></td>
                                            <td><?= $facture['ets_code'];?></td>
                                            <td><b><?= $ets['raison_sociale'];?></b></td>
                                            <td><?= $facture['num_secu'];?></td>
                                            <td><?= $facture['nom'].' '.$facture['prenom'];?></td>
                                            <td>
                                                <?php
                                                if($facture['facture_statut'] != 'N') {
                                                    ?><a href="<?= URL.'ogd/facture?num='.$facture['facture_num'];?>" class="badge badge-info"><i class="fa fa-eye"></i></a><?php
                                                }else {
                                                    ?><button type="button" class="badge badge-secondary" disabled title="La facture n°<?= $facture['facture_num'];?> n'a pas été vérifiée."><i class="fa fa-eye-slash"></i></button><?php
                                                }
                                                ?>
                                            </td>
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
