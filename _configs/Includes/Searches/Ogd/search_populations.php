<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(!empty($utilisateur['user_id'])) {
        $parametres = array(
            'num_secu' => $_POST['num_secu'],
            'nom' => $_POST['nom'],
            'date_naissance' => $_POST['date_naissance']
        );
        if($parametres['date_naissance']) {
            $date_naissance = date('Y-m-d',strtotime(str_replace('/','-',$parametres['date_naissance'])));
        }else {
            $date_naissance = null;
        }
        $log = $UTILISATEURS->ajouter_log_piste_audit(CLIENT_ADRESSE_IP,'RECHERCHE',json_encode($parametres),$utilisateur['user_id']);
        if($log['success'] == true) {
            require_once '../../../Classes/ASSURES.php';
            $ASSURES = new ASSURES();
            $assures = $ASSURES->moteur_recherche($utilisateur['code_ogd'],$parametres['num_secu'], $parametres['nom'],$date_naissance);
            $nb_assures = count($assures);
            if($nb_assures == 0) {
                echo '<p class="alert alert-info center_align">Aucun assure correspondant a votre recherche n\'a ete trouve</p>';
            }else {
                ?>
                <table class="table table-bordered table-hover table-secondary table-sm">
                    <thead class="bg-dark text-white">
                    <tr>
                        <th width="5">N°</th>
                        <th width="50">N° SECU</th>
                        <th>CIVILITE</th>
                        <th>SEXE</th>
                        <th>NOM</th>
                        <th>NOM PATRONYMIQUE</th>
                        <th>PRENOM(S)</th>
                        <th width="150">DATE DE NAISSANCE</th>
                        <th width="5"></th>
                    </tr>
                    <tbody>
                    <?php
                    $ligne = 1;
                    foreach ($assures as $assure) {
                        ?>
                        <tr>
                            <td class="right_align"><?= $ligne;?></td>
                            <td><b><?= $assure['num_secu'];?></b></td>
                            <td><?= $assure['code_civilite'];?></td>
                            <td><?= $assure['code_sexe'];?></td>
                            <td><?= $assure['nom'];?></td>
                            <td><?= $assure['nom_patronymique'];?></td>
                            <td><?= $assure['prenom'];?></td>
                            <td class="center_align"><?= date('d/m/Y',strtotime($assure['date_naissance']));?></td>
                            <td><a href="<?= URL.'ogd/assure?num-secu='.$assure['num_secu'];?>" class="badge badge-info"><i class="fa fa-eye"></i></a></td>
                        </tr>
                        <?php
                        $ligne++;
                    }
                    ?>
                    </tbody>
                    </thead>
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