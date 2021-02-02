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
if(ACTIVE_URL == URL.'ogd/chargements') {
    define('TITLE','Chargements');
}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/chargements?code-ogd='.$_GET['code-ogd']) {
    require_once '../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code-ogd']);
    define('TITLE','Chargements '.$ogd['libelle']);
}
if(ACTIVE_URL == URL.'ogd/imports') {
    define('TITLE','Imports');
}
if(ACTIVE_URL == URL.'ogd/exports') {
    define('TITLE','Exports');
}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/imports?code-ogd='.$_GET['code-ogd']) {
    require_once '../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code-ogd']);
    define('TITLE','Imports '.$ogd['libelle']);
}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/exports?code-ogd='.$_GET['code-ogd']) {
    require_once '../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code-ogd']);
    define('TITLE','Exports '.$ogd['libelle']);
}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/populations?code-ogd='.$_GET['code-ogd']) {
    require_once '../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code-ogd']);
    define('TITLE','Populations '.$ogd['libelle']);
}
if(isset($_GET['num-secu']) && ACTIVE_URL == URL.'ogd/assure?num-secu='.$_GET['num-secu']) {
    require_once '../_configs/Classes/ASSURES.php';
    $ASSURES = new ASSURES();
    $assure = $ASSURES->trouver($_GET['num-secu']);
    define('TITLE',$assure['nom'].' '.$assure['prenom']);
}

if(ACTIVE_URL == URL.'ogd/populations') {
    define('TITLE','Populations');
}
if(ACTIVE_URL == URL.'ogd/factures') {
    define('TITLE','Factures');
}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/factures?code-ogd='.$_GET['code-ogd']) {
    require_once '../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code-ogd']);
    define('TITLE','Factures '.$ogd['libelle']);
}
if(ACTIVE_URL == URL.'ogd/factures-recherche') {
    define('TITLE','Recherche de factures');
}
if(ACTIVE_URL == URL.'ogd/factures-verification') {
    define('TITLE','Vérification des factures');
}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/factures-verification?code-ogd='.$_GET['code-ogd']) {
    require_once '../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code-ogd']);
    define('TITLE','Vérification des factures '.$ogd['libelle']);
}
if(ACTIVE_URL == URL.'ogd/factures-liquidation') {
    define('TITLE','Liquidation des factures');
}
if(isset($_GET['code-ogd']) && ACTIVE_URL == URL.'ogd/factures-liquidation?code-ogd='.$_GET['code-ogd']) {
    require_once '../_configs/Classes/OGD.php';
    $OGD = new OGD();
    $ogd = $OGD->trouver($_GET['code-ogd']);
    define('TITLE','Liquidation des factures '.$ogd['libelle']);
}
if(isset($_GET['num']) && ACTIVE_URL == URL.'ogd/facture?num='.$_GET['num']) {
    define('TITLE','Facture n°: '.$_GET['num']);
}
if(ACTIVE_URL == URL.'ogd/statistiques') {define('TITLE','Statistiques');}

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
if(ACTIVE_URL == URL.'parametres/utilisateurs/edition') {define('TITLE','Nouvel utilisateur');}
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
if(ACTIVE_URL == URL.'parametres/referentiels/lettres-cles') {define('TITLE','Lettres clés');}
if(isset($_GET['code']) && ACTIVE_URL == URL.'parametres/referentiels/lettre-cle?code='.$_GET['code']) {
    require_once '../../_configs/Classes/LETTRESCLES.php';
    $LETTRESCLES = new LETTRESCLES();
    $lc = $LETTRESCLES->trouver($_GET['code']);
    define('TITLE',$lc['libelle']);
}

if(ACTIVE_URL == URL.'parametres/referentiels/actes-medicaux') {define('TITLE','Actes médicaux');}
if(isset($_GET['code']) && ACTIVE_URL == URL.'parametres/referentiels/acte-medical?code='.$_GET['code']) {
    require_once '../../_configs/Classes/ACTESMEDICAUX.php';
    $ACTESMEDICAUX = new ACTESMEDICAUX();
    $acte = $ACTESMEDICAUX->trouver($_GET['code']);
    define('TITLE',$acte['libelle']);
}
if(ACTIVE_URL == URL.'parametres/referentiels/tables-de-valeur') {define('TITLE','Tables de valeur');}
if(ACTIVE_URL == URL.'parametres/scripts/') {define('TITLE','Scripts');}