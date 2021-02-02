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
            require_once "../../../../Classes/SCRIPTS.php";
            $SCRIPTS = new SCRIPTS();
            $scripts = $SCRIPTS->lister($user['code_ogd'],NULL);
            ?>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= URL;?>"><i class="fa fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="<?= URL.'parametres/';?>"><i class="fa fa-cogs"></i> Paramètres</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><i class="fa fa-code"></i> Scripts</li>
                </ol>
            </nav>
            <div class="col">
                <?php
                $nb_scripts = count($scripts);
                if($nb_scripts != 0) {
                    ?>
                    <table class="table table-bordered table-sm table-hover table-striped dataTable">
                        <thead class="bg-secondary">
                        <tr>
                            <th width="5">N°</th>
                            <th width="5">ID</th>
                            <th>NOM</th>
                            <th width="120">DEBUT</th>
                            <th>STATUT</th>
                            <th width="120">FIN</th>
                            <th width="5"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $ligne = 1;
                        foreach ($scripts as $script) {
                            ?>
                            <tr>
                                <td class="right_align"><?= $ligne;?></td>
                                <td class="right_align"><?= $script['id'];?></td>
                                <td><?= $script['nom'];?></td>
                                <td class="center_align"><?= date('d/m/Y H:i:s',strtotime($script['date_debut']));?></td>
                                <td><?= $script['statut'];?></td>
                                <td class="center_align"><?php if($script['date_fin']){echo date('d/m/Y H:i:s',strtotime($script['date_fin']));}?></td>
                                <td>
                                    <?php
                                    if($script['statut'] == 'ENC') {
                                        ?>
                                        <button type="button" class="badge badge-danger button_stop" data-toggle="modal" data-target="#stopScriptleModal" id="<?= $script['id'];?>"><i class="fa fa-stop"></i></button>
                                        <?php
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
                    <div class="modal fade" id="stopScriptleModal" tabindex="-1" aria-labelledby="stopScriptleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="stopScriptleModalLabel"></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <?php include "../../Forms/form_script_stop.php";?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
    }
}
?>
<script>
    $(".button_stop").click(function () {
        let this_id = this.id;
        $("#stopScriptleModalLabel").html("Arrêt du script n° "+this_id);
        $("#script_id_input").val(this_id);
    });
    $("#form_script_stop").submit(function () {
        let script_id = $("#script_id_input").val();
        if(script_id) {
            $("#btn_arret_script")
                .prop('disabled',true)
                .removeClass('btn-danger')
                .addClass('btn-warning').html('...');
            $.ajax({
                url: '../../_configs/Includes/Submits/Parametres/Scripts/submit_stop_script.php',
                type: 'POST',
                data: {
                    'script_id': script_id
                },
                dataType: 'json',
                success: function (data) {
                    $("#btn_arret_script")
                        .prop('disabled',false)
                        .removeClass('btn-warnin')
                        .addClass('btn-danger').html('<i class="fa fa-stop"></i> Arrêter');
                    if(data['success'] === true) {
                        $("#form_script_stop").hide();
                        $("#p_results_arret_script").removeClass('text-danger')
                            .addClass('text-success')
                            .html(data['message']);
                        setTimeout(function(){
                            window.location.href="";
                        }, 3000);
                    }else{
                        $("#p_results_valider_facture").removeClass('text-success')
                            .addClass('text-danger')
                            .html(data['message']);
                    }
                }
            });
        }
        return false;
    });
    $('.dataTable').DataTable();
</script>
