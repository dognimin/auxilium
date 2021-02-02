<?php


require_once '_configs/Classes/UTILISATEURS.php';
require_once '_configs/Includes/Titles.php';

if(isset($_SESSION['auxilium_user_id'])) {
    $UTILISATEURS = new UTILISATEURS();
    $audit = $UTILISATEURS->ajouter_log_piste_audit(CLIENT_ADRESSE_IP,'VISITE',ACTIVE_URL,$_SESSION['auxilium_user_id']);
    if($audit['success'] != true) {
        header('Location: '.URL);
    }

}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="<?= NODE_MODULES.'aos/dist/aos.css';?>" />
    <link rel="stylesheet" type="text/css" href="<?= NODE_MODULES.'jqueryui/jquery-ui.css';?>" />
    <link rel="stylesheet" type="text/css" href="<?= NODE_MODULES.'bootstrap/dist/css/bootstrap.css';?>" />
    <link rel="stylesheet" type="text/css" href="<?= NODE_MODULES.'@fortawesome/fontawesome-free/css/all.css';?>" />
    <link rel="stylesheet" type="text/css" href="<?= NODE_MODULES.'@fortawesome/fontawesome-free/css/fontawesome.min.css';?>" />
    <link rel="stylesheet" type="text/css" href="<?= CSS.'auxilium.css';?>" />
    <link rel="icon" type="image/png" href="<?= IMAGES.'favicon.jpg';?>" />
    <title><?= TITLE;?></title>
</head>
<body id="page">