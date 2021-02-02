<?php
require_once '../../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(!empty($utilisateur['user_id'])) {
        $parametres = array(
            'code' => $_POST['code'],
            'nom' => $_POST['nom'],
            'secteur' => $_POST['secteur'],
            'ville' => $_POST['ville']
        );

        $log = $UTILISATEURS->ajouter_log_piste_audit(CLIENT_ADRESSE_IP,'RECHERCHE',json_encode($parametres),$utilisateur['user_id']);
        if($log['success'] == true) {
            require_once '../../../../Classes/ETABLISSEMENTSANTE.php';
            $ETABLISSEMENTSANTE = new ETABLISSEMENTSANTE();
            $etablissements = $ETABLISSEMENTSANTE->moteur_recherche($parametres['code'],$parametres['nom'],$parametres['secteur'],$parametres['ville']);
            $nb_etablissements = count($etablissements);
            if($nb_etablissements == 0) {
                echo '<p class="alert alert-info center_align">AUCUN ETABLISSEMENT ENCORE ENREGISTRE</p>';
            }else {
                ?>
                <table class="table table-bordered table-hover table-secondary table-sm">
                    <thead class="bg-dark text-white">
                    <tr>
                        <th width="5">N°</th>
                        <th align="50">CODE</th>
                        <th>RAISON SOCIALE</th>
                        <th>SECTEUR D'ACTIVITE</th>
                        <th>DEPARTEMENT</th>
                        <th width="5"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $ligne = 1;
                    foreach ($etablissements as $etablissement) {
                        //$secteur = $EtablissementsSante->trouver_secteur_activite($centre['ets_honoraire_code']);
                        ?>
                        <tr>
                            <td align="right"><?= $ligne;?></td>
                            <td><b><?= $etablissement['ets_code'];?></b></td>
                            <td><?= $etablissement['ets_raison_sociale'];?></td>
                            <td><?='' //$secteur['libelle'];?></td>
                            <td><?= $etablissement['ets_nom_bureau_distributeur'];?></td>
                            <td><a href="<?= URL.'parametres/referentiels/centre-sante?code='.$etablissement['ets_code'];?>" class="badge badge-info"><i class="fa fa-eye"></i></a></td>
                        </tr>
                        <?php
                        $ligne++;
                    }
                    ?>
                    </tbody>
                </table>
                <?php
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