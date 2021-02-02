<?php
$parametres = array(
    'date_debut' => $_POST['date_debut'],
    'date_fin' => $_POST['date_fin'],
    'num_centre' => $_POST['num_centre'],
    'regime' => $_POST['regime'],
    'caisse' => $_POST['caisse'],
    'code_ogd' => $_POST['code_ogd'],
    'libelle' => $_POST['libelle']
);
require_once '../../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(!empty($utilisateur['user_id'])) {
        require_once '../../../../Classes/OGD.php';
        $OGD = new OGD();
        if(!empty($parametres['date_debut'])) {
            $date_debut = date('Y-m-d',strtotime(str_replace('/','-',$_POST['date_debut'])));
        }else {
            $date_debut = NULL;
        }
        if(!empty($parametres['date_fin'])) {
            $date_fin = date('Y-m-d',strtotime(str_replace('/','-',$_POST['date_fin'])));
        }else {
            $date_fin = NULL;
        }
        $edition = $OGD->editer($parametres['code_ogd'],$parametres['libelle'],$parametres['num_centre'],$parametres['regime'],$parametres['caisse'],$date_debut,$date_fin,$utilisateur['user_id']);
        if($edition['success'] == true) {
            $audit = $UTILISATEURS->ajouter_log_piste_audit(CLIENT_ADRESSE_IP,'EDITION',json_encode($parametres),$utilisateur['user_id']);
            if($audit['success'] == true) {
                $json = array(
                    'success' => true
                );
            }else {
                $json = $audit;
            }
        }else {
            $json = $edition;
        }
    }else {
        $json = array(
            'success' => false,
            'message' => 'Aucune session disponible pour cet utilisateur.!!! contactez votre administrateur.'
        );
    }
}else {
    $json = array(
        'success' => false,
        'message' => 'Aucune session disponible pour cet utilisateur.!!! contactez votre administrateur.'
    );
}
echo json_encode($json);
?>