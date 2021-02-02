<?php
require_once '../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(!empty($utilisateur['user_id'])) {
        $parametres = array(
            'type_fichier' => $_POST['type_fichier']
        );

        $log = $UTILISATEURS->ajouter_log_piste_audit(CLIENT_ADRESSE_IP,'RECHERCHE',json_encode($parametres),$utilisateur['user_id']);
        if($log['success'] == true) {
            require_once '../../../Classes/FACTURES.php';
            $FACTURES = new FACTURES();
            $etablissements = $FACTURES->lister_factures_ets($utilisateur['code_ogd'],$parametres['norme'],$parametres['num_fichier'],NULL,NULL,$parametres['code_statut']);
            foreach($etablissements as $etablissement){
                $json[$etablissement['code_ets']][] = $etablissement['raison_sociale'];
            }
        }else {
            $json = array(
                'success' => false,
                'message' => "Une erreur est survenue lors de la mise à jour de la piste d'autdit."
            );
        }
    }else {
        $json = array(
            'success' => false,
            'message' => "L'identifiant de l'utilisateur utilisé pour cette session est incorrect."
        );
    }
}else {
    $json = array(
        'success' => false,
        'message' => "Aucune session active."
    );
}
echo json_encode($json);