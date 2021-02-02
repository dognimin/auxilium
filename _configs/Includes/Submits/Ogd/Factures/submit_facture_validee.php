<?php
require_once '../../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(!empty($utilisateur['user_id'])){
        $parametres = array(
            'num_facture' => $_POST['num_facture'],
            'statut' => $_POST['statut']
        );
        $log = $UTILISATEURS->ajouter_log_piste_audit(CLIENT_ADRESSE_IP,'EDITION',json_encode($parametres),$utilisateur['user_id']);
        if($log['success'] == true) {
            require_once '../../../../Classes/FACTURES.php';
            $FACTURES = new FACTURES();
            $edition = $FACTURES->edition_facture_statut($parametres['num_facture'],$parametres['statut'],NULL,$utilisateur['user_id']);
            if($edition['success'] == true) {
                $message = "Facture liquidée avec succès.";
                $json = array(
                    'success' => true,
                    'message' => $message
                );
            }else {
                $json = $edition;
            }
        }else {
            $message = "Une erreur est survenue lors de la mise à jour de la piste d'audit.";
            $json = array(
                'success' => false,
                'message' => $message
            );
        }
    }
    else {
        $message = "Aucune session disponible pour cet utilisateur.!!! contactez votre administrateur.";
        $json = array(
            'success' => false,
            'message' => $message
        );
    }
}
else {
    $message = "Aucune session disponible pour cet utilisateur.!!! contactez votre administrateur.";
    $json = array(
        'success' => false,
        'message' => $message
    );
}
echo json_encode($json);
?>