<?php
$parametres = array(
    'script_id' => $_POST['script_id']
);
require_once '../../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(!empty($utilisateur['user_id'])){
        $statut = 'SUS';
        $description = "SCRIPT SUSPENDU PAR L'UTILISATEUR.";
        require_once '../../../../Classes/SCRIPTS.php';
        $SCRIPTS = new SCRIPTS();
        $edition = $SCRIPTS->mise_a_jour($parametres['script_id'],NULL,NULL,date('Y-m-d H:i:s',time()),$statut,$description,$utilisateur['user_id']);
        if($edition['success'] == true) {
            $message = 'Enregistrement effectué avec succès.';
            $json = array(
                'success' => true,
                'message' => $message
            );
        }else {
            $json = $edition;
        }

    }else {
        $message = 'Aucune session disponible pour cet utilisateur.!!! contactez votre administrateur.';
        $json = array(
            'success' => false,
            'message' => $message
        );
    }
}else {
    $message = 'Aucune session disponible pour cet utilisateur.!!! contactez votre administrateur.';
    $json = array(
        'success' => false,
        'message' => $message
    );
}
echo json_encode($json);
?>