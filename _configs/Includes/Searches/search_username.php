<?php
$email = trim($_POST['email']);
if($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        require_once '../../Classes/UTILISATEURS.php';
        $UTILISATEURS = new UTILISATEURS();
        $utilisateur_email = $UTILISATEURS->trouver(NULL,$email,NULL);
        if(!$utilisateur_email) {
            $exploded = array_filter(explode('@',$email));
            $utilisateur_pseudo = $UTILISATEURS->trouver(NULL,NULL,$exploded[0]);
            if(!$utilisateur_pseudo) {
                $username = $exploded[0];
            }else {
                $types = $UTILISATEURS->lister_type($exploded[0],NULL,NULL,NULL);
                $nb_types = count($types);
                $username = $exploded[0].str_pad($nb_types,2,'0',STR_PAD_LEFT);
            }
            $json = array(
                'success' => true,
                'subject' => 'username',
                'username' => $username
            );
        }else {
            $json = array(
                'success' => false,
                'subject' => 'email',
                'message' => 'Cette adresse email a deja ete utilisee par un autre utilisateur. Veuillez en saisir une autre.'
            );
        }
    }else {
        $json = array(
            'success' => false,
            'subject' => 'email',
            'message' => 'Veuillez renseigner une adresse email correcte SVP.'
        );
    }
}else {
    $json = array(
        'success' => false,
        'subject' => 'email',
        'message' => 'Veuillez renseigner l\'adresse email de l\'utilisateur SVP.'
    );
}
echo json_encode($json);