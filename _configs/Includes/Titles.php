<?php
if(ACTIVE_URL == URL) {define('TITLE','Auxilium');}
if(ACTIVE_URL == URL.'connexion') {define('TITLE','Connexion');}
if(ACTIVE_URL == URL.'manuel') {define('TITLE','Manuel d\'utilisation');}
if(ACTIVE_URL == URL.'maj-mot-de-passe') {define('TITLE','Mot de passe');}

if(ACTIVE_URL == URL.'ogd/') {define('TITLE','OGD');}
if(isset($_GET['code']) && ACTIVE_URL == URL.'ogd/details?code='.$_GET['code']) {
    require_once '../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code']);
    define('TITLE','Organisme '.$ogd['libelle']);
}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/populations?code-ogd='.$_GET['code-ogd']) {
    require_once '../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code-ogd']);
    define('TITLE','Populations '.$ogd['libelle']);
}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/chargements?code-ogd='.$_GET['code-ogd']) {
    require_once '../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code-ogd']);
    define('TITLE','Chargements '.$ogd['libelle']);
}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/imports?code-ogd='.$_GET['code-ogd']) {
    require_once '../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code-ogd']);
    define('TITLE','Imports '.$ogd['libelle']);
}
if(isset($_GET['code-ogd']) && isset($_GET['type']) && ACTIVE_URL == URL.'ogd/imports?code-ogd='.$_GET['code-ogd'].'&type='.$_GET['type']) {
    define('TITLE','Import '.$_GET['type']);
}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/exports?code-ogd='.$_GET['code-ogd']) {
    require_once '../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code-ogd']);
    define('TITLE','Exports '.$ogd['libelle']);
}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/factures?code-ogd='.$_GET['code-ogd']) {
    require_once '../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code-ogd']);
    define('TITLE','Factures '.$ogd['libelle']);
}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/factures-verification?code-ogd='.$_GET['code-ogd']) {
    require_once '../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code-ogd']);
    define('TITLE','Vérification des factures '.$ogd['libelle']);
}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/factures-liquidation?code-ogd='.$_GET['code-ogd']) {
    require_once '../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code-ogd']);
    define('TITLE','Liquidation des factures '.$ogd['libelle']);
}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/statistiques?code-ogd='.$_GET['code-ogd']) {
    require_once '../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code-ogd']);
    define('TITLE','Statistiques '.$ogd['libelle']);
}
if(ACTIVE_URL == URL.'statistiques/') {define('TITLE','Statistiques');}

if(ACTIVE_URL == URL.'parametres/') {define('TITLE','Paramètres');}
if(ACTIVE_URL == URL.'parametres/ogd/') {define('TITLE','Paramètres OGD');}
if(ACTIVE_URL == URL.'parametres/ogd/edition') {define('TITLE','Nouvel organisme');}
if(isset($_GET['code']) && ACTIVE_URL == URL.'parametres/ogd/details?code='.$_GET['code']) {
    require_once '../../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code']);
    define('TITLE','Organisme '.$ogd['libelle']);
}
if(ACTIVE_URL == URL.'parametres/utilisateurs/') {define('TITLE','UTILISATEURS');}
if(ACTIVE_URL == URL.'parametres/utilisateurs/edition.php') {define('TITLE','Nouvel utilisateur');}
if(isset($_GET['id']) && ACTIVE_URL == URL.'parametres/utilisateurs/details?id='.$_GET['id']) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_GET['id'],NULL,NULL);
    define('TITLE',$utilisateur['prenom'].' '.$utilisateur['nom']);
}
if(isset($_GET['id']) && ACTIVE_URL == URL.'parametres/utilisateurs/edition?id='.$_GET['id']) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_GET['id'],NULL,NULL);
    define('TITLE','Edition: '.$utilisateur['prenom'].' '.$utilisateur['nom']);
}
if(ACTIVE_URL == URL.'parametres/referentiels/') {define('TITLE','Référentiels');}
if(ACTIVE_URL == URL.'parametres/referentiels/generaux') {define('TITLE','Référentiels généraux');}
if(ACTIVE_URL == URL.'parametres/referentiels/generaux-chargements') {define('TITLE','Chargements des référentiels');}
if(ACTIVE_URL == URL.'parametres/referentiels/centres-sante') {define('TITLE','Centres de santé');}
if(isset($_GET['code']) && ACTIVE_URL == URL.'parametres/referentiels/centre-sante?code='.$_GET['code']) {
    require_once '../../_configs/Classes/ETABLISSEMENTSANTE.php';
    $ETABLISSEMENTSANTE = new ETABLISSEMENTSANTE();
    $ets = $ETABLISSEMENTSANTE->trouver($_GET['code']);
    define('TITLE',$ets['raison_sociale']);
}
if(ACTIVE_URL == URL.'parametres/referentiels/professionnels-sante') {define('TITLE','Professionnels de santé');}
if(isset($_GET['code']) && ACTIVE_URL == URL.'parametres/referentiels/professionnel-sante?code='.$_GET['code']) {
    require_once '../../_configs/Classes/PROFESSIONNELSANTE.php';
    $PROFESSIONNELSANTE = new PROFESSIONNELSANTE();
    $ps = $PROFESSIONNELSANTE->trouver($_GET['code']);
    define('TITLE',$ps['nom'].' '.$ps['prenom']);
}
if(ACTIVE_URL == URL.'parametres/referentiels/tables-de-valeur') {define('TITLE','Tables de valeur');}