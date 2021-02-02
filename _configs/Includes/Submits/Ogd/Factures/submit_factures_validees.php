<?php

require_once '../../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(!empty($utilisateur['user_id'])){
        $parametres = array(
            'num_factures' => $_POST['num_factures'],
            'num_deca' => $_POST['num_deca'],
            'code_ets' => $_POST['code_ets'],
            'statut' => $_POST['statut']
        );
        $log = $UTILISATEURS->ajouter_log_piste_audit(CLIENT_ADRESSE_IP,'RECHERCHE',json_encode($parametres),$utilisateur['user_id']);
        if($log['success'] == true) {
            if($parametres['num_factures']) {
                require_once '../../../../Classes/FACTURES.php';
                $FACTURES = new FACTURES();
                $succes = 0;
                $echec = 0;
                foreach ($parametres['num_factures'] as $num_facture) {
                    $edition = $FACTURES->edition_facture_statut($num_facture,'C',NULL,$utilisateur['user_id']);
                    if($edition['success'] == true) {
                        $succes++;
                    }else {
                        $echec++;
                    }
                }
                if($echec == 0 && $succes != 0) {
                    $json = array(
                        'success' => true
                    );
                }else {
                    $json = array(
                        'success' => false,
                        'message' => "Une erreur est survenue lors de la validation des factures. Prière contacter votre administrateur."
                    );
                }
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