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
                    <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-adjust"></i> Organismes</li>
                </ol>
            </nav>
            <div class="col">
                <?php
                require_once "../../../../Classes/OGD.php";
                $OGD = new OGD();
                if(empty($user['code_ogd'])) {
                    $organismes = $OGD->lister();
                    $nb_organismes = count($organismes);
                    ?>
                    <p class="right_align btn_p_danger">
                        <a href="<?= URL.'parametres/ogd/edition.php';?>" class="btn btn-danger btn-sm"><i class="fa fa-plus-circle"></i> Nouvel organisme</a>
                    </p>
                    <?php
                    if($nb_organismes != 0) {
                        ?>
                        <table class="table table-bordered table-sm table-hover table-striped dataTable">
                            <thead class="bg-secondary">
                            <tr>
                                <th width="5">N°</th>
                                <th width="100">CODE</th>
                                <th>LIBELLE</th>
                                <th width="100">N° CENTRE</th>
                                <th width="100">N° REGIME</th>
                                <th width="100">N° CAISSE</th>
                                <th width="100">DATE DEBUT</th>
                                <th width="100">DATE FIN</th>
                                <th width="5"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $ligne = 1;
                            foreach ($organismes as $organisme) {
                                ?>
                                <tr>
                                    <td class="right_align"><?= $ligne;?></td>
                                    <td><b><?= $organisme['code'];?></b></td>
                                    <td><?= $organisme['libelle'];?></td>
                                    <td class="right_align"><?= $organisme['num_centre'];?></td>
                                    <td class="right_align"><?= $organisme['grand_regime'];?></td>
                                    <td class="right_align"><?= $organisme['caisse'];?></td>
                                    <td class="center_align"><?= date('d/m/Y',strtotime($organisme['date_debut']));?></td>
                                    <td class="center_align"><?php if(!empty($organisme['date_fin'])){echo date('d/m/Y',strtotime($organisme['date_fin']));}?></td>
                                    <td><a href="<?= URL.'parametres/ogd/details.php?code='.$organisme['code'];?>" class="badge badge-danger"><i class="fa fa-eye"></i></a></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                        <?php
                    }
                }else {

                }
                ?>
            </div>
            <?php
            echo '<script type="application/javascript" src="'.JS.'n1.js"></script>';
        }
    }
}
?>
<script>
    $('.dataTable').DataTable();
</script>
