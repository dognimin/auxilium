<?php
$parametres = array(
    'modules' => trim($_POST['modules']),
    'sous_modules' => trim($_POST['sous_modules']),
    'user_id' => trim($_POST['user_id'])
);
require_once '../../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    if(!empty($_SESSION['auxilium_user_id'])) {
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
        if(!empty($utilisateur['user_id'])) {
            $json = $UTILISATEURS->maj_habilitations($parametres['user_id'],$parametres['modules'],$parametres['sous_modules'],$utilisateur['user_id']);
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