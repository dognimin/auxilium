<?php
require_once '../../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    if(!empty($_SESSION['auxilium_user_id'])) {
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
        if(!empty($utilisateur['user_id'])) {
            $actuel_mot_de_passe = trim($_POST['actuel_mot_de_passe']);
            $nouveau_mot_de_passe = trim($_POST['nouveau_mot_de_passe']);
            $confirmer_nouveau_mot_de_passe = trim($_POST['confirmer_nouveau_mot_de_passe']);
            if(!empty($actuel_mot_de_passe) && !empty($nouveau_mot_de_passe) && !empty($confirmer_nouveau_mot_de_passe)) {
                $json = $UTILISATEURS->maj_mot_de_passe(CLIENT_ADRESSE_IP,$_SESSION['auxilium_user_id'],$actuel_mot_de_passe,$nouveau_mot_de_passe,$confirmer_nouveau_mot_de_passe);
            }else {
                $json = array(
                    'success' => false,
                    'message' => 'Veuillez renseigner tous les champs SVP.'
                );
            }
            echo json_encode($json,1);
        }else {
            session_destroy();
            echo '<script type="application/javascript">window.location.href="'.URL.'connexion.php"</script>';
        }
    }else {
        session_destroy();
        echo '<script type="application/javascript">window.location.href="'.URL.'connexion.php"</script>';
    }
}else {
    session_destroy();
    echo '<script type="application/javascript">window.location.href="'.URL.'connexion.php"</script>';
}
?>