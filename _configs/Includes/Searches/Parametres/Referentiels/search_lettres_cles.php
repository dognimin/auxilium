<?php
require_once '../../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(!empty($utilisateur['user_id'])) {
        $parametres = array(
            'code' => $_POST['code'],
            'nom' => $_POST['nom']
        );

        $log = $UTILISATEURS->ajouter_log_piste_audit(CLIENT_ADRESSE_IP,'RECHERCHE',json_encode($parametres),$utilisateur['user_id']);
        if($log['success'] == true) {
            require_once '../../../../Classes/LETTRESCLES.php';
            $LETTRESCLES = new LETTRESCLES();
            $lettres_cles = $LETTRESCLES->moteur_recherche($parametres['code'],$parametres['nom']);
            $nb_lettres_cles = count($lettres_cles);
            if($nb_lettres_cles == 0) {
                echo '<p class="alert alert-info center_align">AUCUNE LETTRE CLE TROUVEE</p>';
            }else {
                ?>
                <table class="table table-bordered table-hover table-secondary table-sm">
                    <thead class="bg-dark text-white">
                    <tr>
                        <th width="5">N°</th>
                        <th width="100">CODE</th>
                        <th>LIBELLE</th>
                        <th>TARIF</th>
                        <th>DATE EFFET</th>
                        <th width="5"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $ligne = 1;
                    foreach ($lettres_cles as $lettre_cle) {
                        ?>
                        <tr>
                            <td><?= $ligne;?></td>
                            <td><b><?= $lettre_cle['code'];?></b></td>
                            <td><?= $lettre_cle['libelle'];?></td>
                            <td><?= $lettre_cle['prix_unitaire'];?></td>
                            <td><?= date('d/m/Y',strtotime($lettre_cle['date_debut']));?></td>
                            <td><a href="<?= URL.'parametres/referentiels/lettre-cle?code='.$lettre_cle['code'];?>" class="badge badge-info"><i class="fa fa-eye"></i></a></td>
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