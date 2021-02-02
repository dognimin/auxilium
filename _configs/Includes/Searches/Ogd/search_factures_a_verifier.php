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
                                <p align="center"><button type="button" id="btn_confirmer_verification" class="btn btn-success btn-sm">Confirmer la vérification</button></p>
                                <p align="center" id="resultats_p"></p>
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
                                            <td>
                                                <div class="form-check">
                                                    <input aria-label="" class="form-check-input facture_verifiee" type="checkbox" value="<?= $facture['num_facture'];?>" id="facture_<?= $facture['num_facture'];?>">
                                                </div>
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
                    echo '<p class="text-info display-4" align="center">Aucune facture en attente de vérification. <br /><b><a href="'.URL.'ogd/imports">Cliquez ici</a></b> pour en charger de nouvelles</p>';
                }
            }else {
                echo '<p class="text-info display-4" align="center">Aucune facture en attente de vérification. <br /><b><a href="'.URL.'ogd/imports">Cliquez ici</a></b> pour en charger de nouvelles</p>';
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
<script>
    jQuery(function () {
        $(".facture_verifiee").click(function () {
            $("#resultats_p").hide();
            var id = this.id,
                tab = id.split('_'),
                num_facture = tab[1];
            if ($("#"+id).is(':checked')) {
                $("#"+id).closest("tr").addClass("bg-secondary");
            }else {
                $("#"+id).closest("tr").removeClass("bg-secondary");
            }
        });


        $("#btn_confirmer_verification").click(function () {
            $("#resultats_p").hide();
            let num_factures = [],
                num_deca = '<?= $parametres['norme'];?>',
                code_ets = '<?= $parametres['code_ets'];?>',
                statut = '<?= $parametres['statut'];?>';

            $('input.facture_verifiee:checkbox:checked').each(function () {
                num_factures.push($(this).val());
            });
            if(num_factures.length === 0) {
                $("#resultats_p").show()
                    .removeClass('text-success')
                    .addClass('text-danger')
                    .html('<b>Veuillez sélectionner au moins une facture.</b>');

            }else {
                $("#btn_confirmer_verification").prop('disabled',true)
                    .removeClass('btn-success')
                    .addClass('btn-warning')
                    .html('traitement en cours...');

                $.ajax({
                    url: '../_configs/Includes/Submits/Ogd/Factures/submit_factures_validees.php',
                    type: 'post',
                    data: {
                        'num_factures': num_factures,
                        'num_deca': num_deca,
                        'code_ets': code_ets,
                        'statut': statut
                    },
                    dataType: 'json',
                    success: function (data) {
                        if(data['success'] === true) {
                            $("#facture_table").hide();
                            $("#btn_confirmer_verification").hide();

                            $("#resultats_p").show()
                                .removeClass('text-danger')
                                .addClass('text-success')
                                .html('<b>'+num_factures.length+' facture(s) vérifiée(s) avec succès</b>');

                            setTimeout(function () {
                                display_ogd_factures_a_verifier_ou_liquider(num_deca,'','',code_ets,statut);
                            }, 3000);
                        }else {
                            $("#btn_confirmer_verification").prop('disabled',false)
                                .removeClass('btn-warning')
                                .addClass('btn-success')
                                .html('Confirmer la vérification');

                            $("#resultats_p").show()
                                .removeClass('text-success')
                                .addClass('text-danger')
                                .html(data['message']);
                        }
                    }
                });
            }
            return false;
        });
    });
</script>
