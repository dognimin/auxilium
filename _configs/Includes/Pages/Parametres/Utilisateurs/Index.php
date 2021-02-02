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
            require_once '../../../Menu.php';
            ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="<?= URL.'parametres/';?>"><i class="fa fa-cogs"></i> Paramètres</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-users"></i> Utilisateurs</li>
                </ol>
            </nav>
            <div class="col">
                <p class="right_align btn_p_danger">
                    <a href="<?= URL.'parametres/utilisateurs/edition';?>" class="btn btn-danger btn-sm"><i class="fa fa-plus-circle"></i> Nouvel utilisateur</a>
                </p>
                <?php
                if(empty($user['code_ogd'])) {
                    $code_ogd = null;
                }else {
                    $code_ogd = $user['code_ogd'];
                }

                $utilisateurs = $UTILISATEURS->lister($code_ogd);
                $nb_utilisateurs = count($utilisateurs);
                if($nb_utilisateurs != 0) {
                    ?>
                    <table class="table table-bordered table-sm table-hover table-striped dataTable">
                        <thead class="bg-secondary">
                        <tr>
                            <th width="5">N°</th>
                            <th>OGD</th>
                            <th width="150">N° MATRICULE</th>
                            <th>PRENOM(S)</th>
                            <th>NOM</th>
                            <th>PSEUDO</th>
                            <th>EMAIL</th>
                            <th width="5"></th>
                            <th width="5"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $ligne = 1;
                        foreach ($utilisateurs as $utilisateur) {
                            ?>
                            <tr>
                                <td class="right_align"><?= $ligne;?></td>
                                <td><?= $utilisateur['code_ogd'];?></td>
                                <td><?= $utilisateur['num_matricule'];?></td>
                                <td><?= $utilisateur['prenom'];?></td>
                                <td><?= $utilisateur['nom'];?></td>
                                <td><b><?= $utilisateur['pseudo'];?></b></td>
                                <td><?= $utilisateur['email'];?></td>
                                <td class="center_align"><?= str_replace('0','<b class="fa fa-user-alt-slash text-danger"></b>',str_replace('1','<b class="fa fa-user text-success"></b>',$utilisateur['statut']));?></td>
                                <td class="center_align"><a href="<?= URL.'parametres/utilisateurs/details?id='.$utilisateur['user_id'];?>" class="badge badge-danger"><i class="fa fa-eye"></i></a></td>
                            </tr>
                            <?php
                            $ligne++;
                        }
                        ?>
                        </tbody>
                    </table>
                    <?php
                    echo '<script type="application/javascript" src="'.JS.'n2.js"></script>';
                }
                ?>
            </div>
            <?php
        }
    }
}
?>
<script>
    $('.dataTable').DataTable();
</script>
