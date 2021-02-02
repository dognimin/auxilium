<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$email = trim($_POST['email']);

if(!empty($email)) {
    if(filter_var($email,FILTER_VALIDATE_EMAIL)) {
        require_once '../../Classes/UTILISATEURS.php';
        session_destroy();
        $UTILISATEURS = new UTILISATEURS();
        $user = $UTILISATEURS->trouver(NULL, $email,NULL);
        if(!empty($user['user_id'])) {
            $options = [
                'cost' => 11
            ];
            $mot_de_passe = strtoupper(substr(sha1(time().$user['user_id'].$user['mot_de_passe'].$user['nom']),4,10));
            $voucher = password_hash(time().$user['user_id'].$user['mot_de_passe'].$user['nom'].$user['prenom'], PASSWORD_BCRYPT, $options);
            if(date('A',time()) == 'AM') {
                $salutation = 'Bonjour';
            }else {
                $salutation = 'Bonsoir';
            }
            $sujet = 'Réinitialisation de mot de passe';
            $message = '
<!Doctype html>
<html>
 <head>
 <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body class="container">
<nav class="navbar navbar-light bg-light">
  <a class="navbar-brand" href="#">
    <img src="'.IMAGES.'favicon.png" width="30" height="30" class="d-inline-block align-top" alt="Logo Auxilium">
  </a>
</nav>
<p class="h3">'.$salutation.' '.$user['prenom'].',</p>
<p>Une demande de réinitialisation de votre mot de passe a été effectuée depuis le serveur: <a target="_blank" href="'.URL.'">'.URL.'</a>. Nous venons donc par la présente vous informer que votre mot de passe a été modifié.</p>
<p>Vos identifiants provisoires sont donc les suivants:<br /><small>Identifiant: </small><b>'.$user['pseudo'].'</b><br /><small>Mot de passe: </small><b>'.$mot_de_passe.'</b></p>
<p>Afin de finaliser la procédure enclenchée, nous vous prions de bien vouloir cliquer sur le lien ci-dessous.</p>
<p><b><a href="'.URL.'maj-mot-de-passe.php?u='.$user['user_id'].'&v='.$voucher.'" target="_blank">'.URL.'maj-mot-de-passe.php?u='.$user['user_id'].'&v='.$voucher.'</a></b></p>
<p align="right"><b>Cordialement, l\'équipe support.</b></p><hr />
<small><i>&copy; Powered By <a href="https://techouse.io">TecHouse</a></i></small>
</body>
</html>';

            require '../../Fonctions/function_envoi_email.php';
            $envoi = envoi_email($user['email'],$user['nom'].' '.$user['prenom'],$sujet,$message);
            if($envoi['success'] == true) {
                $maj = $UTILISATEURS->reinitialiser_mot_de_passe($user['user_id'],$mot_de_passe,$voucher);
                if($maj['status'] === true) {
                    $json = array(
                        'status' => true,
                        'message' => 'Un message contenant votre nouveau mot de passe <b>provisoire</b> a été envoyé à l\'adresse email: <b>'.$user['email'].'</b>.<br /> Veuillez SVP consulter votre boite de récepetion afin de terminer la procédure de réinitialisation du mot de passe.<br /> N\'hésitez pas à regarder dans vos SPAMS.'
                    );
                }else {
                    $json = $maj;
                }
            }else {
                $json = $envoi;
            }
            /*

            require '../../../vendor/autoload.php';
            $mail = new PHPMailer(true);
            try {
                //$mail->SMTPDebug = 2;
                $mail->CharSet = 'UTF-8';
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'ssl';
                $mail->Host = SMTP_HOST;
                $mail->Port = SMTP_PORT;
                $mail->Username = SMTP_USERNAME;
                $mail->Password = SMTP_PASSWORD;

                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                $mail->setFrom('support@techouse.io', 'Auxilium','');
                $mail->addAddress($user['email'],$user['nom'].' '.$user['prenom']);
                $mail->addReplyTo('support@techouse.io', 'Auxilium');

                $mail->isHTML(true);

                $mail->Subject = $sujet;
                $mail->Body    = $message;
                $mail->AltBody = '';

                if(!$mail->send()) {
                    $json = array(
                        'status' => false,
                        'message' => "Le message n'a pu être envoyé pour les raisons suivantes: {$mail->ErrorInfo}"
                    );
                }else{
                    $maj = $UTILISATEURS->reinitialiser_mot_de_passe($user['user_id'],$mot_de_passe,$voucher);
                    if($maj['status'] === true) {
                        $json = array(
                            'status' => true,
                            'message' => 'Un message contenant votre nouveau mot de passe <b>provisoire</b> a été envoyé à l\'adresse email: <b>'.$user['email'].'</b>.<br /> Veuillez SVP consulter votre boite de récepetion afin de terminer la procédure de réinitialisation du mot de passe.<br /> N\'hésitez pas à regarder dans vos SPAMS.'
                        );
                    }else {
                        $json = $maj;
                    }
                }
            }
            catch (Exception $e) {
                $json = array(
                    'status' => false,
                    'message' => "Le message n'a pu être envoyé pour les raisons suivantes: {$mail->ErrorInfo}"
                );
            }
            */
        }else {
            $json = array(
                'status' => false,
                'message' => 'Cette adresse Email est incorrecte. Prière d\'en saisir une valide.'
            );
        }
    }else {
        $json = array(
            'status' => false,
            'message' => 'Veuillez SVP saisir une adresse email valide.'
        );
    }


}
echo json_encode($json,NULL);