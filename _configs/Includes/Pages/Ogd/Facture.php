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
            if(isset($_POST['code_ogd']) || $user['code_ogd']) {
                require_once "../../../Classes/OGD.php";
                $OGD = new OGD();
                if(isset($_POST['code_ogd'])) {
                    $ogd = $OGD->trouver($_POST['code_ogd']);
                }elseif($user['code_ogd']) {
                    $ogd = $OGD->trouver($user['code_ogd']);
                }
                if($ogd) {
                    if(isset($_POST['num_facture']) && !empty($_POST['num_facture'])) {
                        $tableau_edition = array('C','L','R');
                        require_once '../../Menu.php';
                        require_once "../../../Classes/PROFESSIONNELSANTE.php";
                        require_once "../../../Classes/ETABLISSEMENTSANTE.php";
                        require_once "../../../Classes/ACTESMEDICAUX.php";
                        require_once "../../../Classes/PATHOLOGIES.php";
                        require_once "../../../Classes/MEDICAMENTS.php";
                        require_once "../../../Classes/FACTURES.php";
                        require_once "../../../Classes/REJETS.php";
                        $PROFESSIONNELSANTE = new PROFESSIONNELSANTE();
                        $ETABLISSEMENTSANTE = new ETABLISSEMENTSANTE();
                        $ACTESMEDICAUX = new ACTESMEDICAUX();
                        $PATHOLOGIES = new PATHOLOGIES();
                        $MEDICAMENTS = new MEDICAMENTS();
                        $FACTURES = new FACTURES();
                        $REJETS = new REJETS();
                        $facture = $FACTURES->trouver($ogd['code'],$_POST['num_facture']);
                        if($facture) {
                            $actes = $FACTURES->lister_facture_lignes_actes($facture['num_facture']);
                            $nb_actes = count($actes);
                            $ets = $ETABLISSEMENTSANTE->trouver($facture['ets_code']);
                            $ps_dossier = $FACTURES->trouver_professionnel_sante($facture['ps_code_ds'],$facture['date_facture']);
                            $specialite_dossier = $PROFESSIONNELSANTE->trouver_specialite_medicale($ps_dossier['code_specialite']);
                            $pathologie = $PATHOLOGIES->trouver($facture['code_pathologie_ds']);
                            $statut = $FACTURES->trouver_statut($facture['statut']);
                            $motifs = $REJETS->lister();
                            ?>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                                    <?php
                                    if(isset($_POST['code_ogd'])) {
                                        ?>
                                        <li class="breadcrumb-item"><a href="<?= URL.'ogd/';?>"><i class="fa fa-adjust"></i> Organismes</a></li>
                                        <?php
                                    }
                                    ?>
                                    <li class="breadcrumb-item"><a href="<?= URL.'ogd/details?code='.$ogd['code'];?>"><i class="fa fa-adjust"></i> <?= $ogd['libelle'];?></a></li>
                                    <li class="breadcrumb-item"><a href="<?= URL.'ogd/factures';?>"><i class="fa fa-file-medical-alt"></i> Factures</a></li>
                                    <li class="breadcrumb-item"><a href="<?= URL.'ogd/factures-liquidation'?>"><i class="fa fa-flask"></i> Liquidation</a></li>
                                    <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-notes-medical"></i> Facture n° <?= $facture['num_facture'];?></li>
                                </ol>
                            </nav>
                            <?php
                            if($facture['statut'] == 'N') {
                                ?>
                                <div class="col"><p class="center_align alert alert-info">Cette facture ne peut être affichée ici car n'a subit aucune vérification. Prière vérifier que la facture physique existe.</p></div>
                                <?php
                            }else {
                                ?>
                                <div class="col">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="card border-info">
                                                <div class="card-header bg-info text-white">
                                                    Identification de la feuille de soins
                                                </div>
                                                <div class="card-body">
                                                    <table class="facture_table">
                                                        <tr>
                                                            <td width="120">Statut facture</td>
                                                            <td class="data_table"><b><?= $facture['statut'].': '.$statut['nom'];?></b></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Type facture</td>
                                                            <td class="data_table"><b><?= $facture['type_facture'];?></b></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Date des soins</td>
                                                            <td class="text-success data_table"><b><?= date('d/m/Y',strtotime($facture['date_facture']));?></b></td>
                                                        </tr>
                                                        <tr>
                                                            <td>N° Facture</td>
                                                            <td class="data_table"><b class="text-success"><?= $facture['num_facture'];?></b></td>
                                                        </tr>
                                                        <tr>
                                                            <td>N° Dossier</td>
                                                            <td class="data_table"><b><a href="<?= URL.'ogd/facture?num='.$facture['num_ds'];?>" target="_blank"><?= $facture['num_ds'];?></a></b></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Code ETS</td>
                                                            <td class="data_table"><b><?= $facture['ets_code'];?></b></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Raison sociale ETS</td>
                                                            <td class="data_table"><b><?= $ets['raison_sociale'];?></b></td>
                                                        </tr>
                                                    </table><br /><br />
                                                    <table class="facture_table">
                                                        <tr>
                                                            <td width="120">N° sécu</td>
                                                            <td class="data_table"><b><?= $facture['num_secu'];?></b></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Nom & prénom(s)</td>
                                                            <td class="data_table"><b><?= $facture['nom'].' '.$facture['prenom'];?></b></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Date de naissance</td>
                                                            <td class="data_table"><b><?= date('d/m/Y',strtotime($facture['date_naissance']));?></b></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="card border-secondary">
                                                <div class="card-header bg-secondary text-white">Professionnel de santé</div>
                                                <div class="card-body">
                                                    <table class="facture_table">
                                                        <tr>
                                                            <td>Type</td>
                                                            <td>Code</td>
                                                            <td>Nom & prénom(s)</td>
                                                            <td>Spécialité</td>
                                                        </tr>
                                                        <tr class="bg-light">
                                                            <td>Dossier</td>
                                                            <td class="data_table"><b><?= $facture['ps_code_ds'];?></b></td>
                                                            <td class="data_table"><b><?= $ps_dossier['nom'].' '.$ps_dossier['prenom'];?></b></td>
                                                            <td class="data_table"><b><?= $specialite_dossier['libelle'];?></b></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div><br />
                                            <div class="card border-secondary">
                                                <div class="card-header bg-secondary text-white">Pathologie du dossier</div>
                                                <div class="card-body">
                                                    <table class="facture_table">
                                                        <tr>
                                                            <td>Code</td>
                                                            <td>Libellé</td>
                                                        </tr>
                                                        <tr class="bg-light">
                                                            <td class="data_table"><b><?= $facture['code_pathologie_ds'];?></b></td>
                                                            <td class="data_table"><b><?= $pathologie['libelle'];?></b></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div><br />
                                            <div class="card border-secondary">
                                                <div class="card-header bg-secondary text-white">Prestations</div>
                                                <div class="card-body">
                                                    <?php
                                                    $montant_depense = 0;
                                                    $base_remboursement = 0;
                                                    $taux_remboursement_ro = 70;
                                                    $montant_remboursement_ro = 0;
                                                    foreach ($actes as $acte) {
                                                        $ps_acte = $FACTURES->trouver_professionnel_sante($acte['code_executant'],$facture['date_facture']);
                                                        $specialite_acte = $PROFESSIONNELSANTE->trouver_specialite_medicale($ps_acte['code_specialite']);
                                                        if(strlen($acte['code_acte']) == 7) {
                                                            $acte_trouve = $ACTESMEDICAUX->trouver($acte['code_acte']);
                                                        }else {
                                                            $acte_trouve = $MEDICAMENTS->trouver($acte['code_acte']);
                                                        }

                                                        $montant_depense = $montant_depense + intval($acte['montant_depense']);
                                                        $base_remboursement = $base_remboursement + intval($acte['base_remboursement']);
                                                        $montant_remboursement_ro = $montant_remboursement_ro + intval($acte['montant_remboursement_ro']);
                                                        ?>
                                                        <div class="div_acte">
                                                            <table class="facture_table">
                                                                <tr>
                                                                    <td width="100">Code</td>
                                                                    <td>Libellé</td>
                                                                    <td width="100">Date début</td>
                                                                    <td width="100">Date fin</td>
                                                                    <td width="30">Prix.U</td>
                                                                    <td width="30">Qté</td>
                                                                </tr>
                                                                <tr class="bg-light">
                                                                    <td class="data_table"><b><?= $acte['code_acte'];?></b></td>
                                                                    <td class="data_table"><b><?= $acte_trouve['libelle'];?></b></td>
                                                                    <td class="data_table <?php if(strtotime($acte['date_debut_soins']) < strtotime($facture['date_facture'])) {echo 'text-danger';} ?>"><b><?php if($acte['date_debut_soins']) {echo date('d/m/Y',strtotime($acte['date_debut_soins']));} ?></b></td>
                                                                    <td class="data_table"><b><?php if($acte['date_fin_soins']) {echo date('d/m/Y',strtotime($acte['date_debut_soins']));} ?></b></td>
                                                                    <td class="right_align data_table"><b><?= intval($acte['prix_unitaire']);?></b></td>
                                                                    <td class="right_align data_table"><b><?= $acte['quantite_acte'];?></b></td>
                                                                </tr>
                                                            </table>
                                                            <table class="facture_table">
                                                                <tr>
                                                                    <td>Code exécutant</td>
                                                                    <td>Nom & prénom(s) exécutant</td>
                                                                    <td>Spécialité exécutant</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="data_table"><b><?= $acte['code_executant'];?></b></td>
                                                                    <td class="data_table"><b><?= $ps_acte['nom'].' '.$ps_acte['nom'];?></b></td>
                                                                    <td class="data_table"><b><?= $specialite_acte['libelle'];?></b></td>
                                                                </tr>
                                                            </table>
                                                            <table class="facture_table">
                                                                <tr>
                                                                    <td>Montant dépsense</td>
                                                                    <td class="right_align data_table"><b><?= intval($acte['montant_depense']);?></b></td>
                                                                    <td>Base remboursement</td>
                                                                    <td class="right_align data_table"><b><?= intval($acte['base_remboursement']);?></b></td>
                                                                    <td>Taux remboursement</td>
                                                                    <td class="right_align data_table"><b><?= intval($acte['taux_remboursement_ro']).'%';?></b></td>
                                                                    <td>Montant remboursement</td>
                                                                    <td class="right_align data_table"><b><?= intval($acte['montant_remboursement_ro']);?></b></td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                    <table class="facture_table bg-dark text-white">
                                                        <tr>
                                                            <td>Montant dépsense</td>
                                                            <td class="right_align data_table"><b><?= intval($montant_depense);?></b></td>
                                                            <td>Base remboursement</td>
                                                            <td class="right_align data_table"><b><?= intval($base_remboursement);?></b></td>
                                                            <td>Taux remboursement</td>
                                                            <td class="right_align data_table"><b><?= intval($taux_remboursement_ro).'%';?></b></td>
                                                            <td>Montant remboursement</td>
                                                            <td class="right_align data_table"><b><?= intval($montant_remboursement_ro);?></b></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div><br />
                                            <div>
                                                <p class="right_align">
                                                    <?php
                                                    if (in_array($facture['statut'],$tableau_edition)) {
                                                        ?>
                                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#validerModal" <?php if($facture['statut'] == 'L') {echo 'hidden';} ?>><i class="fa fa-check-circle"></i> Valider</button>
                                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#refuserModal" <?php if($facture['statut'] == 'R') {echo 'hidden';} ?>><i class="fa fa-times-circle"></i> Rejeter</button>
                                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editerModal"><i class="fa fa-edit"></i> Editer</button>
                                                        <?php
                                                    }
                                                    ?>
                                                </p>
                                                <div class="modal fade" id="validerModal" tabindex="-1" aria-labelledby="validerModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="validerModalLabel">Validation de la facture n°<?= $facture['num_facture'];?></h5>
                                                            </div>
                                                            <div class="modal-body">
                                                                <?php include "../Forms/form_valider_facture.php";?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="refuserModal" tabindex="-1" aria-labelledby="refuserModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="refuserModalLabel">Rejet de la facture n°<?= $facture['num_facture'];?></h5>
                                                            </div>
                                                            <div class="modal-body">
                                                                <?php include "../Forms/form_refuser_facture.php";?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="editerModal" tabindex="-1" aria-labelledby="editerModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editerModalLabel">Edition de la facture n°<?= $facture['num_facture'];?></h5>
                                                            </div>
                                                            <div class="modal-body">
                                                                <?php include "../Forms/form_editer_facture.php";?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            echo '<script type="application/javascript" src="'.JS.'n2.js"></script>';
                            echo '<script type="application/javascript" src="'.JS.'ogd_factures_page.js"></script>';
                        }else {
                            echo '<script>window.location.href="'.URL.'ogd/factures-liquidation"</script>';
                        }
                    }else {
                        echo '<script>window.location.href="'.URL.'ogd/factures-liquidation"</script>';
                    }
                }else {
                    echo '<script>window.location.href="'.URL.'ogd/"</script>';
                }
            }else {
                echo '<script>window.location.href="'.URL.'ogd/"</script>';
            }
        }
    }
}
?>
<script>
    $('.dataTable').DataTable();
    $('#validerModal').modal({
        show: false,
        backdrop: 'static'
    });
    $('#refuserModal').modal({
        show: false,
        backdrop: 'static'
    });
    $('#editerModal').modal({
        show: false,
        backdrop: 'static'
    });

</script>
