<?php
$username = trim($_POST['username']);
$password = trim($_POST['password']);

if(!empty($username) && !empty($password)) {
    require_once '../../Classes/UTILISATEURS.php';
    session_destroy();
    $UTILISATEURS = new UTILISATEURS();
    $connexion = $UTILISATEURS->connexion(CLIENT_ADRESSE_IP, $username, $password);
    if($connexion['success'] === true) {
        session_start();
        $_SESSION['auxilium_user_id'] = $connexion['user_id'];
        $json = array(
            'status' => true,
            'message' => $connexion['message']
        );
        $maj = $UTILISATEURS->maj_derniere_connexion($connexion['user_id'],CLIENT_ADRESSE_IP);
    }else {
        $json = $connexion;
    }

}
echo json_encode($json,NULL);