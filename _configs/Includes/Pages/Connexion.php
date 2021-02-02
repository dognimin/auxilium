<?php
require_once "../../Classes/UTILISATEURS.php";
if(isset($_SESSION['auxilium_user_id'])){
    echo '<script>window.location.href="'.URL.'"</script>';
}else {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateurs = $UTILISATEURS->lister(NULL);
    $nb_utilisateurs = count($utilisateurs);
    ?>
    <div class="col">
        <div class="row justify-content-md-center">
            <div class="col-sm-6 bg-white" data-aos="flip-left">
                <div class="row">
                    <?php
                    if($nb_utilisateurs == 0) {
                        ?>
                        <div class="col">
                            <p class="h3">
                                <img width="20%" src="<?= IMAGES . 'auxilium_logo.png'; ?>" alt="Logo Auxilium" />
                                Enregistrer un administrateur
                            </p>
                            <div><?php include "Forms/form_utilisateur.php";?></div>
                        </div>
                        <?php
                    }else {
                        ?>
                        <div class="col-sm-6" id="div_login_logo">
                            <img width="80%" src="<?= IMAGES . 'auxilium_logo.png'; ?>" alt="Logo Auxilium" />
                        </div>
                        <div class="col-sm-6">
                            <p align="center" class="display-4">Connexion</p>
                            <p align="center" id="p_login_results"></p>
                            <?php
                            include "Forms/form_connexion.php";
                            include "Forms/form_mot_de_passe_oublie.php";
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script type="application/javascript" src="<?= JS.'connexion.js';?>"></script>
    <?php
}
