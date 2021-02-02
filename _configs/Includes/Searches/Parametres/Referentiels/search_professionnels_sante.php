<?php
require_once '../../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(!empty($utilisateur['user_id'])) {
        $parametres = array(
            'code' => $_POST['code'],
            'nom' => $_POST['nom'],
            'specialite' => $_POST['specialite'],
            'ville' => $_POST['ville']
        );

        $log = $UTILISATEURS->ajouter_log_piste_audit(CLIENT_ADRESSE_IP,'RECHERCHE',json_encode($parametres),$utilisateur['user_id']);
        if($log['success'] == true) {
            require_once '../../../../Classes/PROFESSIONNELSANTE.php';
            $PROFESSIONNELSANTE = new PROFESSIONNELSANTE();
            $professionnels = $PROFESSIONNELSANTE->moteur_recherche($parametres['code'],$parametres['nom'],$parametres['specialite'],$parametres['ville']);
            $nb_professionnels = count($professionnels);
            if($nb_professionnels == 0) {
                echo '<p class="alert alert-info center_align">AUCUN PROFESSIONNEL DE SANTE TROUVE</p>';
            }else {
                ?>
                <table class="table table-bordered table-hover table-secondary table-sm">
                    <thead class="bg-dark text-white">
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
                        <th width="5"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $ligne = 1;
                    foreach ($professionnels as $professionnel) {
                        $specialite_medicale = $PROFESSIONNELSANTE->trouver_specialite_medicale($professionnel['code_specialite']);
                        ?>
                        <tr>
                            <td><?= $ligne;?></td>
                            <td><b><?= $professionnel['ps_code'];?></b></td>
                            <td><?= $professionnel['ps_civilite'];?></td>
                            <td><?= $professionnel['ps_nom'].' '.$professionnel['ps_prenom'];?></td>
                            <td><?= $specialite_medicale['libelle'];?></td>
                            <td><?= $professionnel['ps_adresse_postale'];?></td>
                            <td><?= $professionnel['ps_ville'];?></td>
                            <td><?= $professionnel['ps_telephone'];?></td>
                            <td><?= $professionnel['ps_email'];?></td>
                            <td><a href="<?= URL.'parametres/referentiels/professionnel-sante?code='.$professionnel['ps_code'];?>" class="badge badge-info"><i class="fa fa-eye"></i></a></td>
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