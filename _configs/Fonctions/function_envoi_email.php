<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
function envoi_email($send_to_email, $send_to_name, $subject, $message) {
    require '../../vendor/autoload.php';
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
        $mail->addAddress($send_to_email,$send_to_name);
        $mail->addReplyTo('support@techouse.io', 'Auxilium');

        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = '';

        if(!$mail->send()) {
            $json = array(
                'status' => false,
                'message' => "Le message n'a pu être envoyé pour les raisons suivantes: {$mail->ErrorInfo}"
            );
        }else{
            $json = array(
                'status' => true,
                'message' => "Message envoyé"
            );
        }
    }catch (Exception $e) {
        $json = array(
            'status' => false,
            'message' => "Le message n'a pu être envoyé pour les raisons suivantes: {$mail->ErrorInfo}"
        );
    }
    return $json;
}
