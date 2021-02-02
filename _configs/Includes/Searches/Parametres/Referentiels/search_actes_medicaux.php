<?php
require_once '../../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(!empty($utilisateur['user_id'])) {
        $parametres = array(
            'code' => $_POST['code'],
            'nom' => $_POST['nom'],
            'code_lettre' => $_POST['code_lettre']
        );

        $log = $UTILISATEURS->ajouter_log_piste_audit(CLIENT_ADRESSE_IP,'RECHERCHE',json_encode($parametres),$utilisateur['user_id']);
        if($log['success'] == true) {
            require_once '../../../../Classes/ACTESMEDICAUX.php';
            require_once '../../../../Classes/LETTRESCLES.php';
            $ACTESMEDICAUX = new ACTESMEDICAUX();
            $LETTRESCLES = new LETTRESCLES();
            $actes = $ACTESMEDICAUX->moteur_recherche('NGAP',$parametres['code'],$parametres['nom'],$parametres['code_lettre']);
            $nb_actes = count($actes);
            if($nb_actes == 0) {
                echo '<p class="alert alert-info center_align">AUCUN ACTE CLE TROUVE</p>';
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
                    foreach ($actes as $acte) {
                        $lettre_cle = $LETTRESCLES->trouver($acte['lettre_cle']);
                        $tarif = intval($acte['coefficient'] * $lettre_cle['prix_unitaire']);
                        ?>
                        <tr>
                            <td><?= $ligne;?></td>
                            <td><b><?= $acte['code'];?></b></td>
                            <td><?= $acte['libelle'];?></td>
                            <td><?= $tarif;?></td>
                            <td><?= date('d/m/Y',strtotime($acte['date_debut']));?></td>
                            <td><a href="<?= URL.'parametres/referentiels/acte-medical?code='.$acte['code'];?>" class="badge badge-info"><i class="fa fa-eye"></i></a></td>
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