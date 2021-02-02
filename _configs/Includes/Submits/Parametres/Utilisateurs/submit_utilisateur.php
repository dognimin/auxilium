<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$parametres = array(
    'code_ogd' => str_replace('',NULL,trim($_POST['code_ogd'])),
    'code_statut' => str_replace('-1','0',trim($_POST['code_statut'])),
    'user_id' => trim($_POST['user_id']),
    'email' => trim($_POST['email']),
    'username' => trim($_POST['username']),
    'firstname' => trim($_POST['firstname']),
    'lastname' => trim($_POST['lastname']),
    'direction' => trim($_POST['direction']),
    'service' => trim($_POST['service']),
    'fonction' => trim($_POST['fonction']),
    'num_telephone' => trim($_POST['num_telephone'])
);



require_once '../../../../Classes/UTILISATEURS.php';
$UTILISATEURS = new UTILISATEURS();
$utilisateurs = $UTILISATEURS->lister(NULL);
$nb_utilisateurs = count($utilisateurs);
if($nb_utilisateurs == 0) {
    $user = 1;
    $audit = $UTILISATEURS->ajouter_log_piste_audit(CLIENT_ADRESSE_IP,'EDITION',json_encode($parametres),$user);
    if($audit['success'] == true) {
        $edition = $UTILISATEURS->editer($parametres['user_id'],$parametres['code_ogd'],NULL,$parametres['email'],$parametres['username'],$parametres['firstname'],$parametres['lastname'],$parametres['direction'],$parametres['service'],$parametres['fonction'],$parametres['num_telephone'],$parametres['code_statut'],$user);
        if($edition['success'] == true) {
            if($edition['type'] == 'creation') {
                if(date('A',time()) == 'AM') {
                    $salutation = 'Bonjour';
                }else {
                    $salutation = 'Bonsoir';
                }
                $sujet = '[Auxilium]: Création de compte';
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
<i>Cet e-mail a été envoyé automatiquement par Auxilium</i><br /><p>---------------</p>
<p class="h3">'.$salutation.' '.$parametres['firstname'].',</p>
<p>Votre compte vient d\être créé sur la plateforme Auxilium.</p>
<p>Vos identifiants provisoires d\'accès à la plateforme sont les suivants:<br /><small>Identifiant: </small><b>'.$parametres['username'].'</b><br /><small>Mot de passe: </small><b>'.$edition['password'].'</b></p>
<p>Afin de vous connecter, nous vous prions de bien vouloir cliquer sur le lien ci-dessous.</p>
<p><b><a href="'.URL.'" target="_blank">'.URL.'</a></b></p>

<p align="right"><b>Cordialement, l\'équipe support.</b></p><hr />
<small><i>&copy; Powered By <a href="https://techouse.io">TecHouse</a></i></small>
</body>
</html>';

                require '../../../../../vendor/autoload.php';
                $mail = new PHPMailer(true);
                try {
                    //$mail->SMTPDebug = 2;
                    $mail->CharSet = 'UTF-8';
                    $mail->isSMTP();                                      // Set mailer to use SMTP
                    $mail->SMTPAuth = true;                               // Enable SMTP authentication
                    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
                    $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
                    $mail->Port = 465;
                    $mail->Username = 'techouseci@gmail.com';             // SMTP username
                    $mail->Password = 'TecH@use#2@!8';                    // SMTP password

                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );

                    $mail->setFrom('support@techouse.io', 'Auxilium','');
                    $mail->addAddress($parametres['email'],$parametres['firstname'].' '.$parametres['lastname']);     // Add a recipient
                    $mail->addReplyTo('support@techouse.io', 'Auxilium');

                    $mail->isHTML(true);                                  // Set email format to HTML

                    $mail->Subject = $sujet;
                    $mail->Body    = $message;
                    $mail->AltBody = '';

                    if(!$mail->send()) {
                        $json = array(
                            'success' => false,
                            'message' => "Le message n'a pu être envoyé pour les raisons suivantes: {$mail->ErrorInfo}"
                        );
                    }else{
                        $json = array(
                            'success' => true,
                            'message' => 'Le compte a été créé avec succès, un message contenant les identifiants de connexion a été envoyé à l\'adresse email: <b>'.$parametres['email'].'</b>.<br /> Veuillez SVP emander à l\'utilisateur de consulter sa boite de récepetion.<br /> Qu\'il n\'hésitez pas à regarder dans ses SPAMS.'
                        );
                    }
                }catch (Exception $e) {
                    $json = array(
                        'success' => false,
                        'pass' => $edition['password'],
                        'message' => "Le message n'a pu être envoyé pour les raisons suivantes: {$mail->ErrorInfo}"
                    );
                }
            }else {
                $json = array(
                    'success' => true,
                    'message' => 'Edition effectuée avec succès.'
                );
            }

        }else {
            $json = array(
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'enregistrement de l\'utilisateur.'
            );
        }
    }else {
        $json = array(
            'success' => false,
            'message' => 'Une erreur est survenue lors de l\'initialisation de la fonction d\'audit.'
        );
    }
}else {
    if(isset($_SESSION['auxilium_user_id'])) {
        $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
        if(!empty($utilisateur['user_id'])) {
            $audit = $UTILISATEURS->ajouter_log_piste_audit(CLIENT_ADRESSE_IP,'EDITION',json_encode($parametres),$utilisateur['user_id']);
            if($audit['success'] == true) {
                $edition = $UTILISATEURS->editer($parametres['user_id'],$parametres['code_ogd'],NULL,$parametres['email'],$parametres['username'],$parametres['firstname'],$parametres['lastname'],$parametres['direction'],$parametres['service'],$parametres['fonction'],$parametres['num_telephone'],$parametres['code_statut'],$utilisateur['user_id']);
                if($edition['success'] == true) {
                    if($edition['type'] == 'creation') {
                        if(date('A',time()) == 'AM') {
                            $salutation = 'Bonjour';
                        }else {
                            $salutation = 'Bonsoir';
                        }
                        $sujet = '[Auxilium]: Création de compte';
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
<i>Cet e-mail a été envoyé automatiquement par Auxilium</i><br /><p>---------------</p>
<p class="h3">'.$salutation.' '.$parametres['firstname'].',</p>
<p>Votre compte vient d\être créé sur la plateforme Auxilium.</p>
<p>Vos identifiants provisoires d\'accès à la plateforme sont les suivants:<br /><small>Identifiant: </small><b>'.$parametres['username'].'</b><br /><small>Mot de passe: </small><b>'.$edition['password'].'</b></p>
<p>Afin de vous connecter, nous vous prions de bien vouloir cliquer sur le lien ci-dessous.</p>
<p><b><a href="'.URL.'" target="_blank">'.URL.'</a></b></p>

<p align="right"><b>Cordialement, l\'équipe support.</b></p><hr />
<small><i>&copy; Powered By <a href="https://techouse.io">TecHouse</a></i></small>
</body>
</html>';

                        require '../../../../../vendor/autoload.php';
                        $mail = new PHPMailer(true);
                        try {
                            //$mail->SMTPDebug = 2;
                            $mail->CharSet = 'UTF-8';
                            $mail->isSMTP();                                      // Set mailer to use SMTP
                            $mail->SMTPAuth = true;                               // Enable SMTP authentication
                            $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
                            $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
                            $mail->Port = 465;
                            $mail->Username = 'techouseci@gmail.com';             // SMTP username
                            $mail->Password = 'TecH@use#2@!8';                    // SMTP password

                            $mail->SMTPOptions = array(
                                'ssl' => array(
                                    'verify_peer' => false,
                                    'verify_peer_name' => false,
                                    'allow_self_signed' => true
                                )
                            );

                            $mail->setFrom('support@techouse.io', 'Auxilium','');
                            $mail->addAddress($parametres['email'],$parametres['firstname'].' '.$parametres['lastname']);     // Add a recipient
                            $mail->addReplyTo('support@techouse.io', 'Auxilium');

                            $mail->isHTML(true);                                  // Set email format to HTML

                            $mail->Subject = $sujet;
                            $mail->Body    = $message;
                            $mail->AltBody = '';

                            if(!$mail->send()) {
                                $json = array(
                                    'success' => false,
                                    'message' => "Le message n'a pu être envoyé pour les raisons suivantes: {$mail->ErrorInfo}"
                                );
                            }else{
                                $json = array(
                                    'success' => true,
                                    'message' => 'Le compte a été créé avec succès, un message contenant les identifiants de connexion a été envoyé à l\'adresse email: <b>'.$parametres['email'].'</b>.<br /> Veuillez SVP emander à l\'utilisateur de consulter sa boite de récepetion.<br /> Qu\'il n\'hésitez pas à regarder dans ses SPAMS.'
                                );
                            }
                        }catch (Exception $e) {
                            $json = array(
                                'success' => false,
                                'pass' => $edition['password'],
                                'message' => "Le message n'a pu être envoyé pour les raisons suivantes: {$mail->ErrorInfo}"
                            );
                        }
                    }else {
                        $json = array(
                            'success' => true,
                            'message' => 'Edition effectuée avec succès.'
                        );
                    }

                }else {
                    $json = $edition;
                }
            }else {
                $json = array(
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de l\'initialisation de la fonction d\'audit.'
                );
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
}
echo json_encode($json);