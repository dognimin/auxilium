<?php

require_once '../../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(!empty($utilisateur['user_id'])){
        $type_ref = htmlentities(trim($_POST['type_ref_input']));
        if(!empty($type_ref)) {
            $path = DIR.'IMPORTS/REFERENTIELS/'.$type_ref.'/';
            $path_export = DIR.'EXPORTS/RAPPORTS_INTEGRATION/REFERENTIELS/';
            $lien_export = URL.'EXPORTS/RAPPORTS_INTEGRATION/REFERENTIELS/';
            $path_temp = DIR.'IMPORTS/REFERENTIELS/'.$type_ref.'/tmp/';
            if(!file_exists($path)){
                mkdir($path,0777,true);
                mkdir($path_temp,0777,true);
            }

            if(!file_exists($path_export)){
                mkdir($path_export,0777,true);
            }
            $valid_extensions = array('xml', 'txt');

            $filetemp = $_FILES["fichier_input"]["tmp_name"];
            $filename = $_FILES["fichier_input"]["name"];
            $filetype = $_FILES["fichier_input"]["type"];
            $filesize = number_format($_FILES["fichier_input"]["size"] / 1024, 0, ',', ' '). ' Ko';
            $fileerror = $_FILES["fichier_input"]["error"];
            $extension = str_replace('.','',strrchr($filename,'.'));
            if($fileerror == 0) {
                if(!empty($filename)) {
                    include "../../../../Fonctions/Fonction_conversion_caracteres_speciaux.php";
                    require_once '../../../../Classes/ETABLISSEMENTSANTE.php';
                    require_once '../../../../Classes/PROFESSIONNELSANTE.php';
                    require_once '../../../../Classes/ACTESMEDICAUX.php';
                    require_once '../../../../Classes/PATHOLOGIES.php';
                    require_once '../../../../Classes/MEDICAMENTS.php';
                    require_once '../../../../Classes/LETTRESCLES.php';
                    require_once '../../../../Classes/SCRIPTS.php';
                    require_once '../../../../Classes/LOGS.php';

                    $PROFESSIONNELSANTE = new PROFESSIONNELSANTE();
                    $ETABLISSEMENTSANTE = new ETABLISSEMENTSANTE();
                    $ACTESMEDICAUX = new ACTESMEDICAUX();
                    $MEDICAMENTS = new MEDICAMENTS();
                    $PATHOLOGIES = new PATHOLOGIES();
                    $LETTRESCLES = new LETTRESCLES();
                    $SCRIPTS = new SCRIPTS();
                    $LOGS = new LOGS();

                    if(in_array(strtolower($extension),$valid_extensions)) {
                        $log_lecture = $LOGS->ajouter_chargement_fichier('IMP',$filename,'DEB',"DEBUT DE LECTURE",$utilisateur['user_id']);
                        if($log_lecture['success'] == true) {
                            if($extension == 'txt'){
                                $lecture_fichier = fopen($filetemp,'r+');
                                $ligne = 0;
                                $nb_donnees = 0;
                                $erreurs = array();
                                if($type_ref == 'ETS') {
                                    $norme = 'CNAMREFETS01';
                                    $path_ets = $path_export.$norme.'/'.date('d_m_Y',time()).'/';
                                    $lien_ets = $lien_export.$norme.'/'.date('d_m_Y',time()).'/';
                                    if(!file_exists($path_ets)){
                                        mkdir($path_ets,0777,true);
                                    }
                                    $ets_filename = "RAPPORT_INTEGRATION_".str_replace('.txt','',$filename)."_".date('dmYHis').".txt";
                                    $fichier_rapport = fopen($path_ets.$ets_filename, "w") or die("Unable to open file!");

                                    while(($data = fgets($lecture_fichier)) !== false) {
                                        if($ligne == 0) {
                                            $type_enregistrement = trim(substr($data,0,3));
                                            $zone_reservee = trim(substr($data,3,9));
                                            $code_caisse_gestionnaire = trim(substr($data,12,6));
                                            $identification_fichier = trim(substr($data,18,6));
                                            $programme_emetteur = trim(substr($data,24,6));
                                            $date_creation_fichier = substr(trim(substr($data,30,8)),0,4).'-'.substr(trim(substr($data,30,8)),4,2).'-'.substr(trim(substr($data,30,8)),6,2);
                                            $numero_generation = trim(substr($data,38,7));
                                            $organisme_emetteur = trim(substr($data,45,4));
                                            $organisme_destinataire = trim(substr($data,49,7));
                                            $nature_fichier = trim(substr($data,56,1));
                                            $numero_chronologique = trim(substr($data,57,5));

                                            $donnees[$ligne] = array(
                                                'type_enregistrement' => $type_enregistrement,
                                                'zone_reservee' => $zone_reservee,
                                                'code_caisse_gestionnaire' => $code_caisse_gestionnaire,
                                                'identification_fichier' => $identification_fichier,
                                                'programme_emetteur' => $programme_emetteur,
                                                'date_creation_fichier' => $date_creation_fichier,
                                                'numero_generation' => $numero_generation,
                                                'organisme_emetteur' => $organisme_emetteur,
                                                'organisme_destinataire' => $organisme_destinataire,
                                                'nature_fichier' => $nature_fichier,
                                                'numero_chronologique' => $numero_chronologique
                                            );
                                        }
                                        else {
                                            $code_ets = trim(substr($data,0,9));
                                            $valeur_nulle = trim(substr($data,9,9));

                                            $filler_identifiant = trim(substr($data,18,2));
                                            $code_nature_emetteur = trim(substr($data,20,1));
                                            $numero_emetteur = trim(substr($data,21,3));
                                            $code_derniere_utilisation = trim(substr($data,24,1));
                                            $date_derniere_utilisation = '20'.substr(trim(substr($data,25,6)),0,2).'-'.substr(trim(substr($data,25,6)),2,2).'-'.substr(trim(substr($data,25,6)),4,2);

                                            $raison_sociale = utf8_encode(trim(substr($data,31,100)));
                                            $cpam_rattachement = trim(substr($data,131,3));
                                            $numero_domi_ets_gestionnaire = trim(substr($data,134,8));
                                            $cle_numero_domi = trim(substr($data,142,1));
                                            $numero_siret = trim(substr($data,143,14));
                                            $numero_telephone = trim(substr($data,157,10));
                                            $numero_fax = trim(substr($data,167,10));
                                            $filler_derniere_utilisation = trim(substr($data,177,3));

                                            $num_column = 180;
                                            for($a = 1; $a <= 3; $a++) {
                                                $code_categorie_psh[$a] = trim(substr($data,$num_column,3));
                                                $num_column = $num_column + 3;
                                                if(trim(substr($data,$num_column,6)) == '000000') {
                                                    $date_categorie_psh[$a] = NULL;
                                                }else {
                                                    $date_categorie_psh[$a] = '20'.substr(trim(substr($data,$num_column,6)),0,2).'-'.substr(trim(substr($data,$num_column,6)),2,2).'-'.substr(trim(substr($data,$num_column,6)),4,2);
                                                }
                                                $num_column = $num_column + 6;
                                            }
                                            for($b = 1; $b <= 3; $b++) {
                                                $code_statut_juridique[$b] = trim(substr($data,$num_column,2));
                                                $num_column = $num_column + 2;
                                                if(trim(substr($data,$num_column,6)) == '000000') {
                                                    $date_statut_juridique[$b] = NULL;
                                                }else {
                                                    $date_statut_juridique[$b] = '20'.substr(trim(substr($data,$num_column,6)),0,2).'-'.substr(trim(substr($data,$num_column,6)),2,2).'-'.substr(trim(substr($data,$num_column,6)),4,2);
                                                }
                                                $num_column = $num_column + 6;
                                            }
                                            for($c = 1; $c <= 3; $c++) {
                                                $code_convention[$c] = trim(substr($data,$num_column,2));
                                                $num_column = $num_column + 2;
                                                if(trim(substr($data,$num_column,6)) == '000000') {
                                                    $date_convention[$c] = NULL;
                                                }else {
                                                    $date_convention[$c] = '20'.substr(trim(substr($data,$num_column,6)),0,2).'-'.substr(trim(substr($data,$num_column,6)),2,2).'-'.substr(trim(substr($data,$num_column,6)),4,2);
                                                }
                                                $num_column = $num_column + 6;
                                            }
                                            for($d = 1; $d <= 3; $d++) {
                                                $code_honoraire[$d] = trim(substr($data,$num_column,1));
                                                $num_column = $num_column + 1;
                                                if(trim(substr($data,$num_column,6)) == '000000') {
                                                    $date_honoraire[$d] = NULL;
                                                }else {
                                                    $date_chonoraire[$d] = '20'.substr(trim(substr($data,$num_column,6)),0,2).'-'.substr(trim(substr($data,$num_column,6)),2,2).'-'.substr(trim(substr($data,$num_column,6)),4,2);
                                                }
                                                $num_column = $num_column + 6;
                                            }

                                            $filler_identification1 = trim(substr($data,$num_column,28));
                                            $num_column = $num_column + 28;
                                            $code_nature_emetteur2 = trim(substr($data,$num_column,1));
                                            $num_column = $num_column + 1;
                                            $numero_emetteur2 = trim(substr($data,$num_column,3));
                                            $num_column = $num_column + 3;
                                            $code_derniere_utilisation2 = trim(substr($data,$num_column,1));
                                            $num_column = $num_column + 1;
                                            $date_derniere_utilisation2 = '20'.substr(trim(substr($data,$num_column,6)),0,2).'-'.substr(trim(substr($data,$num_column,6)),2,2).'-'.substr(trim(substr($data,$num_column,6)),4,2);
                                            $num_column = $num_column + 6;

                                            for($e = 1; $e <= 3; $e++) {
                                                $code_activite[$e] = trim(substr($data,$num_column,1));
                                                $num_column = $num_column + 1;
                                                if(trim(substr($data,$num_column,6)) == '000000') {
                                                    $date_activite[$e] = NULL;
                                                }else {
                                                    $date_activite[$e] = '20'.substr(trim(substr($data,$num_column,6)),0,2).'-'.substr(trim(substr($data,$num_column,6)),2,2).'-'.substr(trim(substr($data,$num_column,6)),4,2);
                                                }
                                                $num_column = $num_column + 6;
                                            }
                                            for($f = 1; $f <= 3; $f++) {
                                                $code_agrement_radio[$f] = trim(substr($data,$num_column,1));
                                                $num_column = $num_column + 1;
                                                if(trim(substr($data,$num_column,6)) == '000000') {
                                                    $date_agrement_radio[$f] = NULL;
                                                }else {
                                                    $date_agrement_radio[$f] = '20'.substr(trim(substr($data,$num_column,6)),0,2).'-'.substr(trim(substr($data,$num_column,6)),2,2).'-'.substr(trim(substr($data,$num_column,6)),4,2);
                                                }
                                                $num_column = $num_column + 6;
                                            }
                                            for($g = 1; $g <= 3; $g++) {
                                                $zone_isd_tarif[$g] = trim(substr($data,$num_column,2));
                                                $num_column = $num_column + 2;
                                                $zone_ik_tarif[$g] = trim(substr($data,$num_column,1));
                                                $num_column = $num_column + 1;
                                                if(trim(substr($data,$num_column,6)) == '000000') {
                                                    $date_tarif[$g] = NULL;
                                                }else {
                                                    $date_tarif[$g] = '20'.substr(trim(substr($data,$num_column,6)),0,2).'-'.substr(trim(substr($data,$num_column,6)),2,2).'-'.substr(trim(substr($data,$num_column,6)),4,2);
                                                }
                                                $num_column = $num_column + 6;
                                            }
                                            for($h = 1; $h <= 3; $h++) {

                                                $code_tarif_soins_medicaux[$h] = trim(substr($data,$num_column,2));
                                                $num_column = $num_column+2;
                                                $t_soins_medicaux_date_effet[$h] = trim(substr($data,$num_column,6));
                                                if($t_soins_medicaux_date_effet[$h] == '000000') {
                                                    $tarif_soins_medicaux_date_effet[$h] = NULL;
                                                }else {
                                                    $tarif_soins_medicaux_date_effet[$h] = '20'.substr(trim(substr($data,$num_column,6)),0,2).'-'.substr(trim(substr($data,$num_column,6)),2,2).'-'.substr(trim(substr($data,$num_column,6)),4,2);
                                                }
                                                $num_column = $num_column+6;
                                            }
                                            for($i = 1; $i <= 6; $i++) {
                                                $code_tarif_soins_dentaires[$i] = trim(substr($data,$num_column,2));
                                                $num_column = $num_column + 2;
                                                $t_soins_dentaires_date_effet[$i] = trim(substr($data,$num_column,6));
                                                if($t_soins_dentaires_date_effet[$i] == '000000'   || $t_soins_dentaires_date_effet[$i] == '') {
                                                    $tarif_soins_dentaires_date_effet[$i] = NULL;
                                                }else {
                                                    $tarif_soins_dentaires_date_effet[$i] = '20'.substr(trim(substr($data,$num_column,6)),0,2).'-'.substr(trim(substr($data,$num_column,6)),2,2).'-'.substr(trim(substr($data,$num_column,6)),4,2);
                                                }
                                                $num_column = $num_column + 6;
                                            }
                                            for($j = 1;$j <= 80;$j++) {
                                                $existence_specialite[$j] = trim(substr($data,$num_column,1));
                                                $num_column = $num_column + 1;
                                            }

                                            $releve_statique_activite = trim(substr($data,$num_column,13));
                                            $num_column = $num_column + 13;

                                            for($k = 1; $k <= 3; $k++) {

                                                $coefficient_moderateur[$k] = trim(substr($data,$num_column,5));
                                                $num_column = $num_column + 5;
                                                $c_moderateur_date_effet[$k] = trim(substr($data,$num_column,6));
                                                if($c_moderateur_date_effet[$k] == '000000'   || $c_moderateur_date_effet[$k] == '') {
                                                    $coefficient_moderateur_date_effet[$k] = NULL;
                                                }else {
                                                    $coefficient_moderateur_date_effet[$k] = '20'.substr(trim(substr($data,$num_column,6)),0,2).'-'.substr(trim(substr($data,$num_column,6)),2,2).'-'.substr(trim(substr($data,$num_column,6)),4,2);
                                                }
                                                $num_column = $num_column + 6;
                                            }
                                            $vide = trim(substr($data,$num_column,2));

                                            $num_column = $num_column + 2;

                                            $code_nature_emetteur_adresse = trim(substr($data,$num_column,1));
                                            $num_column = $num_column + 1;
                                            $num_emetteur_adresse = trim(substr($data,$num_column,3));
                                            $num_column = $num_column + 3;
                                            $code_dern_utilisation_adresse = trim(substr($data,$num_column,1));
                                            $num_column = $num_column + 1;
                                            $date_dern_utilisation_adresse = '20'.substr(trim(substr($data,$num_column,6)),0,2).'-'.substr(trim(substr($data,$num_column,6)),2,2).'-'.substr(trim(substr($data,$num_column,6)),4,2);
                                            $num_column = $num_column + 6;
                                            $complement_adresse_1 = trim(substr($data,$num_column,100));
                                            $num_column = $num_column + 100;
                                            $complement_adresse_2 = trim(substr($data,$num_column,100));
                                            $num_column = $num_column + 100;

                                            $num_voie = trim(substr($data,$num_column,4));
                                            $num_column = $num_column + 4;
                                            $complement_num_voie = trim(substr($data,$num_column,1));
                                            $num_column = $num_column + 1;
                                            $nature_voie = trim(substr($data,$num_column,4));
                                            $num_column = $num_column + 4;
                                            $libelle_voie = trim(substr($data,$num_column,32));
                                            $num_column = $num_column + 32;
                                            $nom_commune = trim(substr($data,$num_column,38));
                                            $num_column = $num_column + 38;

                                            $num_bureau_distributeur = trim(substr($data,$num_column,5));
                                            $num_column = $num_column + 5;
                                            $nom_bureau_distributeur = trim(substr($data,$num_column,100));
                                            $num_column = $num_column + 100;
                                            $adresse_email = trim(substr($data,$num_column,128));
                                            $num_column = $num_column + 128;
                                            $filler4 = trim(substr($data,$num_column,5));
                                            $num_column = $num_column + 5;

                                            $code_nature_emetteur_domiciliation = trim(substr($data,$num_column,1));
                                            $num_column = $num_column + 1;
                                            $num_emetteur_domiciliation= trim(substr($data,$num_column,3));
                                            $num_column = $num_column + 3;
                                            $code_dern_utilisation_domiciliation = trim(substr($data,$num_column,1));
                                            $num_column = $num_column + 1;
                                            $date_dern_utilisation_domiciliation = '20'.substr(trim(substr($data,$num_column,6)),0,2).'-'.substr(trim(substr($data,$num_column,6)),2,2).'-'.substr(trim(substr($data,$num_column,6)),4,2);
                                            $num_column = $num_column + 6;

                                            for($l = 1; $l <= 2;$l++) {
                                                $mode_regelement[$l] = trim(substr($data,$num_column,1));
                                                $num_column = $num_column + 1;
                                                $civilite_titulaire[$l] = trim(substr($data,$num_column,1));
                                                $num_column = $num_column + 1;
                                                $num_mandataire[$l] = trim(substr($data,$num_column,13));
                                                $num_column = $num_column + 13;
                                                $cle_num_mandataire[$l] = trim(substr($data,$num_column,1));
                                                $num_column = $num_column + 1;
                                                $vide_[$l] = trim(substr($data,$num_column,26));
                                                $num_column = $num_column + 26;
                                                $num_banque[$l] = trim(substr($data,$num_column,5));
                                                $num_column = $num_column + 5;
                                                $num_agence[$l] = trim(substr($data,$num_column,5));
                                                $num_column = $num_column + 5;
                                                $num_compte[$l] = trim(substr($data,$num_column,11));
                                                $num_column = $num_column + 11;
                                                $cle_rib[$l] = trim(substr($data,$num_column,2));
                                                $num_column = $num_column + 2;
                                            }

                                            $donnees[$ligne] = array(
                                                'code_ets' => $code_ets,
                                                'code_nature_emetteur' => $code_nature_emetteur,
                                                'code_nature_emetteur2' => $code_nature_emetteur2,
                                                'numero_emetteur' => $numero_emetteur,
                                                'numero_emetteur2' => $numero_emetteur2,
                                                'code_derniere_utilisation' => $code_derniere_utilisation,
                                                'code_derniere_utilisation2' => $code_derniere_utilisation2,
                                                'date_derniere_utilisation' => $date_derniere_utilisation,
                                                'date_derniere_utilisation2' => $date_derniere_utilisation2,
                                                'raion_sociale' => strtoupper(conversionCaracteresSpeciaux($raison_sociale)),
                                                'cpam_rattachement' => $cpam_rattachement,
                                                'numero_siret' => $numero_siret,
                                                'numero_telephone' => $numero_telephone,
                                                'numero_fax' => $numero_fax,
                                                'code_categorie_psh' => $code_categorie_psh[1],
                                                'date_categorie_psh' => $date_categorie_psh[1],
                                                'code_statut_juridique' => $code_statut_juridique[1],
                                                'date_statut_juridique' => $date_statut_juridique[1],
                                                'code_convention' => $code_convention[1],
                                                'date_convention' => $date_convention[1],
                                                'code_honoraire' => $code_honoraire[1],
                                                'date_honoraire' => $date_honoraire[1],
                                                'code_activite' => $code_activite[1],
                                                'date_activite' => $date_activite[1],
                                                'code_agrement_radio' => $code_agrement_radio[1],
                                                'date_agrement_radio' => $date_agrement_radio[1],
                                                'zone_isd_tarif' => $zone_isd_tarif[1],
                                                'zone_ik_tarif' => $zone_ik_tarif[1],
                                                'date_tarif' => $date_tarif[1],
                                                'date_dern_utilisation_adresse' => $code_dern_utilisation_adresse,
                                                'complement_adresse_1' => strtoupper(conversionCaracteresSpeciaux($complement_adresse_1)),
                                                'complement_adresse_2' => strtoupper(conversionCaracteresSpeciaux(utf8_encode($complement_adresse_2))),
                                                'num_voie' => $num_voie,
                                                'complement_num_voie' => $complement_num_voie,
                                                'nature_voie' => $nature_voie,
                                                'libelle_voie' => $libelle_voie,
                                                'nom_commune' => strtoupper(conversionCaracteresSpeciaux($nom_commune)),
                                                'num_bureau_distributeur' => $num_bureau_distributeur,
                                                'nom_bureau_distributeur' => $nom_bureau_distributeur,
                                                'adresse_email' => $adresse_email
                                            );
                                        }
                                        $nb_donnees = count($donnees);
                                        $ligne++;
                                    }
                                    $fichier = $LOGS->trouver_fichier($norme,$numero_generation,$filename,$numero_chronologique);
                                    if(!$fichier) {
                                        if($nb_donnees != 0) {
                                            $donnees_rapport = date('d-m-Y H:i:s',time())." DEBUT DE LECTURE DU FICHIER {$filename}\n\n";
                                            fwrite($fichier_rapport, $donnees_rapport);
                                            $ligne = 0;
                                            $nb_erreurs = 0;
                                            foreach ($donnees as $donnee) {
                                                $lectures = $ETABLISSEMENTSANTE->lecture_ficher_txt($type_enregistrement,$zone_reservee,$code_caisse_gestionnaire,$identification_fichier,$programme_emetteur,$date_creation_fichier,$numero_generation,$organisme_emetteur,$organisme_destinataire,$nature_fichier,$numero_chronologique,$donnee['code_ets'],$donnee['code_nature_emetteur'],$donnee['code_nature_emetteur2'],$donnee['numero_emetteur'], $donnee['numero_emetteur2'],$donnee['code_derniere_utilisation'],$donnee['code_derniere_utilisation2'],$donnee['date_derniere_utilisation'],$donnee['date_derniere_utilisation2'],$donnee['raion_sociale'],$donnee['cpam_rattachement'],$donnee['numero_siret'],$donnee['numero_telephone'],$donnee['numero_fax'],$donnee['code_categorie_psh'],$donnee['date_categorie_psh'],$donnee['code_statut_juridique'],$donnee['date_statut_juridique'],$donnee['code_convention'],$donnee['date_convention'],$donnee['code_honoraire'],$donnee['date_honoraire'],$donnee['code_activite'],$donnee['date_activite'],$donnee['code_agrement_radio'],$donnee['date_agrement_radio'],$donnee['zone_isd_tarif'],$donnee['zone_ik_tarif'],$donnee['date_tarif'],$donnee['date_dern_utilisation_adresse'],$donnee['complement_adresse_1'],$donnee['complement_adresse_2'],$donnee['num_voie'],$donnee['complement_num_voie'],$donnee['nature_voie'],$donnee['libelle_voie'],$donnee['nom_commune'],$donnee['num_bureau_distributeur'],$donnee['nom_bureau_distributeur'],$donnee['adresse_email']);
                                                $nb_retours = count($lectures);
                                                if($nb_retours!= 0)  {
                                                    foreach ($lectures as $lecture) {
                                                        if($lecture['message']) {
                                                            $donnees_rapport = "Erreur ligne {$ligne}: {$lecture['message']}\n";
                                                            fwrite($fichier_rapport, $donnees_rapport);
                                                            $nb_erreurs++;
                                                        }
                                                    }
                                                }
                                                $ligne++;
                                            }
                                            $donnees_rapport = "\n\n\n".date('d-m-Y H:i:s',time())." FIN DE LECTURE DU FICHIER {$filename}\n";
                                            fwrite($fichier_rapport, $donnees_rapport);
                                            $donnees_rapport = "NOMBRE D'ERREURS IDENTIFIEES: {$nb_erreurs}\n\n";
                                            fwrite($fichier_rapport, $donnees_rapport);

                                            if($nb_erreurs != 0) {
                                                $donnees_rapport = "LE TRAITEMENT A ETE INTERROMPU.\n";
                                                fwrite($fichier_rapport, $donnees_rapport);
                                                $json = array(
                                                    'success' => false,
                                                    'message' => $donnees_rapport
                                                );
                                            }
                                            else {
                                                $log_lecture = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',"FIN DE LECTURE",$utilisateur['user_id']);
                                                if($log_lecture['success'] == true) {
                                                    $log_chargement = $LOGS->ajouter_chargement_fichier('IMP',$filename,'DEB',"DEBUT DE CHARGEMENT",$utilisateur['user_id']);
                                                    if($log_chargement['success'] == true) {
                                                        $nouveau_script = $SCRIPTS->ajouter('SCRIPT_CHARGEMENT_REFERENTIEL_ETABLISSEMENTS_SANTE',date('Y-m-d H:i:s',time()),NULL,'ENC','CHARGEMENT DU REFERNTIEL ETABLISSEMENTS DE SANTE NOMME: '.$filename,$utilisateur['user_id']);
                                                        if($nouveau_script['success'] == true) {
                                                            $donnees_rapport = date('d-m-Y H:i:s',time())." DEBUT DU TRAITEMENT DU FICHIER {$filename}\n\n";
                                                            fwrite($fichier_rapport, $donnees_rapport);
                                                            $ligne = 0;
                                                            $succes = 0;
                                                            $echecs = 0;
                                                            $creation = 0;
                                                            $maj = 0;
                                                            foreach ($donnees as $donnee) {
                                                                if($ligne != 0) {
                                                                    $edition = $ETABLISSEMENTSANTE->edition($date_creation_fichier,$numero_generation,$donnee['code_ets'],$donnee['code_nature_emetteur'],$donnee['code_nature_emetteur2'],$donnee['numero_emetteur'], $donnee['numero_emetteur2'],$donnee['code_derniere_utilisation'],$donnee['code_derniere_utilisation2'],$donnee['date_derniere_utilisation'],$donnee['date_derniere_utilisation2'],$donnee['raion_sociale'],$donnee['cpam_rattachement'],$donnee['numero_siret'],$donnee['numero_telephone'],$donnee['numero_fax'],$donnee['code_categorie_psh'],$donnee['date_categorie_psh'],$donnee['code_statut_juridique'],$donnee['date_statut_juridique'],$donnee['code_convention'],$donnee['date_convention'],$donnee['code_honoraire'],$donnee['date_honoraire'],$donnee['code_activite'],$donnee['date_activite'],$donnee['code_agrement_radio'],$donnee['date_agrement_radio'],$donnee['zone_isd_tarif'],$donnee['zone_ik_tarif'],$donnee['date_tarif'],$donnee['date_dern_utilisation_adresse'],$donnee['complement_adresse_1'],$donnee['complement_adresse_2'],$donnee['num_voie'],$donnee['complement_num_voie'],$donnee['nature_voie'],$donnee['libelle_voie'],$donnee['nom_commune'],$donnee['num_bureau_distributeur'],$donnee['nom_bureau_distributeur'],$donnee['adresse_email'],$utilisateur['user_id']);
                                                                    if($edition['success'] == true) {
                                                                        $succes++;
                                                                        if($edition['type'] == 0) {
                                                                            $creation++;
                                                                        }else {
                                                                            $maj++;
                                                                        }
                                                                    }else {
                                                                        $echecs++;
                                                                    }
                                                                }
                                                                $ligne++;
                                                            }
                                                            if($echecs == 0) {
                                                                $log_chargement = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',"FIN DE CHARGEMENT",$utilisateur['user_id']);
                                                                if($log_chargement['success'] == true) {
                                                                    $historique = $LOGS->ajouter_historique_fichier('IMP',$numero_generation,$norme,$numero_chronologique,$date_creation_fichier,$organisme_emetteur,$organisme_destinataire,($creation+$maj),$creation,$maj,$filename,$utilisateur['user_id']);
                                                                    if($historique['success'] == true) {
                                                                        $mise_a_jour_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],$log_lecture['id_log'],$log_chargement['id_log'],date('Y-m-d H:i:s',time()),'FIN',"CHARGEMENT DU REFERNTIEL ETABLISSEMENTS DE SANTE NOMME: {$filename} EFFECTUE AVEC SUCCES",$utilisateur['user_id']);
                                                                        if($mise_a_jour_script['success'] == true) {
                                                                            $donnees_rapport = date('d-m-Y H:i:s',time())." FIN DU TRAITEMENT DU FICHIER {$filename}\n\nFICHIER CHARGE AVEC SUCCES\nNOM DU FICHIER: {$filename}\nNOMBRE DE LIGNES: ".($succes+$echecs)."\nREUSSITE: {$succes}\nCREATION: {$creation}\nMISE A JOUR: {$maj}\nECHECS: {$echecs}";
                                                                            fwrite($fichier_rapport, $donnees_rapport);
                                                                            fclose($fichier_rapport);
                                                                            $message = "FICHIER CHARGE AVEC SUCCES.<br />NOM DU FICHIER: <b>{$filename}</b><br />NOMBRE DE LIGNES: <b>".($succes+$echecs)."</b><br />REUSSITE: <b>{$succes}</b><br />CREATION: <b>{$creation}</b><br />MISE A JOUR: <b>{$maj}</b><br />ECHECS: <b>{$echecs}</b><br /><a target='_blank' href='".$lien_ets.$ets_filename."' download='".$fichier_rapport."'>Cliquez ici pour télécharger le fichier</a>";
                                                                            $json = array(
                                                                                'success' => true,
                                                                                'message' => $message
                                                                            );
                                                                        }else {
                                                                            $json = $mise_a_jour_script;
                                                                        }
                                                                    }else {
                                                                        $json = $historique;
                                                                    }
                                                                }else {
                                                                    $json = $log_chargement;
                                                                }
                                                            }else {
                                                                $message = "Une erreur est survenue lors du chargement du fichier.";
                                                                $json = array(
                                                                    'success' => false,
                                                                    'message' => $message
                                                                );
                                                            }
                                                        }else {
                                                            $json = $nouveau_script;
                                                        }
                                                    }else {
                                                        $json = $log_chargement;
                                                    }
                                                }else {
                                                    $json = $log_lecture;
                                                }
                                            }
                                        }
                                    }else {
                                        $message = "Le fichier {$filename} a déjà servi pour un autre chargement.";
                                        $json = array(
                                            'success' => false,
                                            'message' => $message
                                        );
                                        $log_lecture = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',$message,$utilisateur['user_id']);
                                    }

                                }
                                elseif($type_ref == 'PS'){
                                    $norme = 'REFPROF01';
                                    $path_ps = $path_export.$norme.'/'.date('d_m_Y',time()).'/';
                                    $lien_ps = $lien_export.$norme.'/'.date('d_m_Y',time()).'/';
                                    if(!file_exists($path_ps)){
                                        mkdir($path_ps,0777,true);
                                    }
                                    $ps_filename = "RAPPORT_INTEGRATION_".str_replace('.txt','',$filename)."_".date('dmYHis').".txt";
                                    $fichier_rapport = fopen($path_ps.$ps_filename, "w") or die("Unable to open file!");

                                    while(($data = fgets($lecture_fichier)) !== false){
                                        if($ligne == 0) {
                                            $type_enregistrement = trim(substr($data,0,3));
                                            $zone_reservee = trim(substr($data,3,9));
                                            $code_caisse_gestionnaire = trim(substr($data,12,6));
                                            $identification_fichier = trim(substr($data,18,6));
                                            $programme_emetteur = trim(substr($data,24,6));
                                            $date_creation_fichier = substr(trim(substr($data,30,8)),0,4).'-'.substr(trim(substr($data,30,8)),4,2).'-'.substr(trim(substr($data,30,8)),6,2);
                                            $numero_generation = trim(substr($data,38,7));
                                            $organisme_emetteur = trim(substr($data,45,4));
                                            $organisme_destinataire = trim(substr($data,49,7));
                                            $nature_fichier = trim(substr($data,56,1));
                                            $numero_chronologique = trim(substr($data,57,5));

                                            $donnees[$ligne] = array(
                                                'type_enregistrement' => $type_enregistrement,
                                                'zone_reservee' => $zone_reservee,
                                                'code_caisse_gestionnaire' => $code_caisse_gestionnaire,
                                                'identification_fichier' => $identification_fichier,
                                                'programme_emetteur' => $programme_emetteur,
                                                'date_creation_fichier' => $date_creation_fichier,
                                                'numero_generation' => $numero_generation,
                                                'organisme_emetteur' => $organisme_emetteur,
                                                'organisme_destinataire' => $organisme_destinataire,
                                                'nature_fichier' => $nature_fichier,
                                                'numero_chronologique' => $numero_chronologique
                                            );
                                        }
                                        else {
                                            $type_enregistrement = trim(substr($data,0,3));
                                            $code_ps = trim(substr($data,3,9));
                                            $code_caisse_gestionnaire = trim(substr($data,12,6));
                                            $num_immat = trim(substr($data,18,15));
                                            $nouveau_num_immat = trim(substr($data,33,15));
                                            $cpam_gestionnaire = trim(substr($data,48,6));
                                            $civilite = trim(substr($data,54,3));
                                            $code_appartenance_cee = trim(substr($data,57,1));
                                            $nom_patronymique = strtoupper(conversionCaracteresSpeciaux(trim(utf8_encode(substr($data,58,50)))));
                                            $prenom_usuel = strtoupper(conversionCaracteresSpeciaux(trim(utf8_encode(substr($data,108,50)))));
                                            $prenom_2 = trim(utf8_encode(substr($data,158,50)));
                                            $prenom_3 = trim(utf8_encode(substr($data,208,50)));
                                            $nom_usage = strtoupper(conversionCaracteresSpeciaux(trim(utf8_encode(substr($data,258,25)))));
                                            $complement_adresse = strtoupper(conversionCaracteresSpeciaux(trim(utf8_encode(substr($data,283,100)))));
                                            $complement_adresse_2 = trim(utf8_encode(substr($data,383,100)));
                                            $num_voie = trim(utf8_encode(substr($data,483,4)));
                                            $complement_num_voie = trim(utf8_encode(substr($data,487,1)));
                                            $type_voie = trim(utf8_encode(substr($data,488,4)));
                                            $libelle_voie = trim(utf8_encode(substr($data,492,32)));
                                            $libelle_lieu_dit = trim(utf8_encode(substr($data,524,38)));
                                            $code_postal = trim(substr($data,562,5)); //doit être 5
                                            $libelle_commune = trim(utf8_encode(substr($data,567,100)));

                                            $code_separt_commune = trim(substr($data,667,2));
                                            $code_commune = trim(substr($data,669,3));
                                            $libelle_pays = trim(utf8_encode(substr($data,672,50)));
                                            $num_telephone = trim(substr($data,722,15));
                                            $num_fax = trim(substr($data,737,15));
                                            $adresse_email = trim(utf8_encode(substr($data,752,128)));

                                            $num_line = 880;
                                            for($i = 1; $i <= 3; $i++) {

                                                $code_affiliation[$i] = trim(substr($data,$num_line,1));
                                                $num_line = $num_line + 1;
                                                $date_affiliation[$i] = trim(substr($data,$num_line,8));
                                                if($date_affiliation[$i] == '00000000' || $date_affiliation[$i] == '') {
                                                    $date_effet_affiliation[$i] = NULL;
                                                }else {
                                                    $date_effet_affiliation[$i] = substr($date_affiliation[$i],0,4).'-'.substr($date_affiliation[$i],4,2).'-'.substr($date_affiliation[$i],6,2);
                                                }
                                                $num_line = $num_line + 8;
                                            }
                                            $categorie_pam = trim(substr($data,$num_line,2));
                                            $date_debut_activite_liberale = trim(substr($data,909,8));
                                            $mode_exercice_particulier = trim(substr($data,917,2));
                                            $annee_obtention_these = trim(substr($data,919,4));
                                            $departement_ville_faculte = trim(substr($data,923,2));
                                            $code_commune_ville_faculte = trim(substr($data,925,3));
                                            $num_line = 928;
                                            for($i = 1; $i <= 3; $i++) {
                                                $code_diplome_etudes_specialisees[$i] = trim(substr($data,$num_line,2));
                                                $num_line = $num_line + 2;
                                                $annee_obtention_diplome[$i] = trim(substr($data,$num_line,4));
                                                $num_line = $num_line + 4;
                                            }
                                            for($i = 1; $i <= 3; $i++) {

                                                $code_specialite[$i] = trim(substr($data,$num_line,2));
                                                $num_line = $num_line + 2;

                                                $d_debut_specialite[$i] = trim(substr($data,$num_line,8));
                                                if($d_debut_specialite[$i] == '00000000' || $d_debut_specialite[$i] == '') {
                                                    $date_debut_specialite[$i] = NULL;
                                                }else {
                                                    $date_debut_specialite[$i] = substr($d_debut_specialite[$i],0,4).'-'.substr($d_debut_specialite[$i],4,2).'-'.substr($d_debut_specialite[$i],6,2);
                                                }
                                                $num_line = $num_line + 8;

                                                $d_fin_specialite[$i] = trim(substr($data,$num_line,8));
                                                if($d_fin_specialite[$i] == '00000000' || $d_fin_specialite[$i] == '') {
                                                    $date_fin_specialite[$i] = NULL;
                                                }else {
                                                    $date_fin_specialite[$i] = substr($d_fin_specialite[$i],0,4).'-'.substr($d_fin_specialite[$i],4,2).'-'.substr($d_fin_specialite[$i],6,2);
                                                }
                                                $num_line = $num_line + 8;
                                            }
                                            for($i = 1; $i <= 5; $i++) {
                                                $code_convention[$i] = trim(substr($data,$num_line,2));
                                                $num_line = $num_line + 2;
                                                $d_effet_convention[$i] = trim(substr($data,$num_line,8));
                                                if($d_effet_convention[$i] == '00000000'   || $d_effet_convention[$i] == '') {
                                                    $date_effet_convention[$i] = '2017-01-01';
                                                }else {
                                                    $date_effet_convention[$i] = substr($d_effet_convention[$i],0,4).'-'.substr($d_effet_convention[$i],4,2).'-'.substr($d_effet_convention[$i],6,2);
                                                }
                                                $num_line = $num_line + 8;
                                                $motif_sortie_convention = trim(substr($data,$num_line,2));
                                                $num_line = $num_line + 2;
                                            }
                                            $niveau_cotation_preferentiel = trim(substr($data,1060,1));
                                            $autorisation_refus_libelle_specialite = trim(substr($data,1061,1));
                                            $specialite_ddas = trim(substr($data,1062,2));

                                            $num_line = 1064;
                                            for($i = 1; $i <= 2; $i++) {
                                                $code_orientation[$i] = trim(substr($data,$num_line,2));
                                                $num_line = $num_line + 2;
                                                $libelle_orientation[$i] = trim(substr($data,$num_line,30));
                                                $num_line = $num_line + 30;
                                            }

                                            for($i = 1; $i <= 3; $i++) {
                                                $code_attribution[$i] = trim(substr($data,$num_line,2));
                                                $num_line = $num_line + 2;
                                                $libelle_attribution[$i] = trim(substr($data,$num_line,30));
                                                $num_line = $num_line + 30;
                                            }

                                            $code_specialisation = trim(substr($data,$num_line,2));
                                            $libelle_specialisation = trim(substr($data,1226,30));
                                            $inscription_tableau_annexe = trim(substr($data,1256,2));
                                            $formation_particuliere = trim(substr($data,1258,2));
                                            $code_titre_professionnel = trim(substr($data,1260,2));

                                            $num_line = 1262;
                                            for($i = 1; $i <= 5; $i++) {
                                                $code_nature_exercice[$i] = trim(substr($data,$num_line,2));
                                                $num_line = $num_line + 2;

                                                $date_effet_nature_exercice[$i] = trim(substr($data,$num_line,8));
                                                if($date_effet_nature_exercice[$i] == '00000000' || $date_effet_nature_exercice[$i] == '') {
                                                    $date_debut_nature[$i] = NULL;
                                                }else {
                                                    $date_debut_nature[$i] = substr($date_effet_nature_exercice[$i],0,4).'-'.substr($date_effet_nature_exercice[$i],4,2).'-'.substr($date_effet_nature_exercice[$i],6,2);
                                                }
                                                $num_line = $num_line + 8;

                                                $date_fin_nature_exercice[$i] = trim(substr($data,$num_line,8));
                                                if($date_fin_nature_exercice[$i] == '00000000'   || $date_fin_nature_exercice[$i] == '') {
                                                    $date_fin_nature[$i] = NULL;
                                                }else {
                                                    $date_fin_nature[$i] = substr($date_fin_nature_exercice[$i],0,4).'-'.substr($date_fin_nature_exercice[$i],4,2).'-'.substr($date_fin_nature_exercice[$i],6,2);
                                                }
                                                $num_line = $num_line + 8;
                                                $motif_fin_nature_exercice[$i] = trim(substr($data,$num_line,2));
                                                $num_line = $num_line + 2;

                                            }

                                            $donnees[$ligne] = array(
                                                'type_enregistrement' => $type_enregistrement,
                                                'code_ps' => $code_ps,
                                                'code_caisse_gestionnaire' => $code_caisse_gestionnaire,
                                                'num_immat' => $num_immat,
                                                'nouveau_num_immat' => $nouveau_num_immat,
                                                'cpam_gestionnaire' => $cpam_gestionnaire,
                                                'civilite' => $civilite,
                                                'code_appartenance_cee' => $code_appartenance_cee,
                                                'nom_patronymique' => $nom_patronymique,
                                                'prenom_usuel' => $prenom_usuel,
                                                'prenom_2' => $prenom_2,
                                                'prenom_3' => $prenom_3,
                                                'nom_usage' => $nom_usage,
                                                'complement_adresse' => $complement_adresse,
                                                'complement_adresse_2' => $complement_adresse_2,
                                                'num_voie' => $num_voie,
                                                'complement_num_voie' => $complement_num_voie,
                                                'type_voie' => $type_voie,
                                                'libelle_voie' => $libelle_voie,
                                                'libelle_lieu_dit' => $libelle_lieu_dit,
                                                'code_postal' => $code_postal,
                                                'libelle_commune' => $libelle_commune,
                                                'code_separt_commune' => $code_separt_commune,
                                                'code_commune' => $code_commune,
                                                'libelle_pays' => $libelle_pays,
                                                'num_telephone' => $num_telephone,
                                                'num_fax' => $num_fax,
                                                'adresse_email' => $adresse_email,
                                                'code_affiliation' => $code_affiliation[1],
                                                'date_effet_affiliation' => $date_effet_affiliation[1],
                                                'categorie_pam' => $categorie_pam,
                                                'date_debut_activite_liberale' => $date_debut_activite_liberale,
                                                'mode_exercice_particulier' => $mode_exercice_particulier,
                                                'annee_obtention_these' => $annee_obtention_these,
                                                'departement_ville_faculte' => $departement_ville_faculte,
                                                'code_commune_ville_faculte' => $code_commune_ville_faculte,
                                                'code_diplome_etudes_specialisees' => $code_diplome_etudes_specialisees[1],
                                                'annee_obtention_diplome' => $annee_obtention_diplome[1],
                                                'code_specialite' => $code_specialite[1],
                                                'date_debut_specialite' => $date_debut_specialite[1],
                                                'date_fin_specialite' => $date_fin_specialite[1],
                                                'code_convention' => $code_convention[1],
                                                'date_effet_convention' => $date_effet_convention[1],
                                                'motif_sortie_convention' => $motif_sortie_convention[1],
                                                'niveau_cotation_preferentiel' => $niveau_cotation_preferentiel,
                                                'autorisation_refus_libelle_specialite' => $autorisation_refus_libelle_specialite,
                                                'specialite_ddas' => $specialite_ddas,
                                                'code_orientation' => $code_orientation[1],
                                                'libelle_orientation' => $libelle_orientation[1],
                                                'code_attribution' => $code_attribution[1],
                                                'libelle_attribution' => $libelle_attribution[1],
                                                'code_specialisation' => $code_specialisation,
                                                'libelle_specialisation' => $libelle_specialisation,
                                                'inscription_tableau_annexe' => $inscription_tableau_annexe,
                                                'formation_particuliere' => $formation_particuliere,
                                                'code_titre_professionnel' => $code_titre_professionnel,
                                                'code_nature_exercice' => $code_nature_exercice[1],
                                                'date_debut_nature' => $date_debut_nature[1],
                                                'date_fin_nature' => $date_fin_nature[1],
                                                'motif_fin_nature_exercice' => $motif_fin_nature_exercice[1]
                                            );
                                        }
                                        $nb_donnees = count($donnees);
                                        $ligne++;
                                    }
                                    $fichier = $LOGS->trouver_fichier($norme,$numero_generation,$filename,$numero_chronologique);
                                    if(!$fichier) {
                                        if($nb_donnees != 0) {
                                            $donnees_rapport = date('d-m-Y H:i:s',time())." DEBUT DE LECTURE DU FICHIER {$filename}\n\n";
                                            fwrite($fichier_rapport, $donnees_rapport);
                                            $ligne = 0;
                                            $nb_erreurs = 0;
                                            foreach ($donnees as $donnee) {
                                                $lectures = $PROFESSIONNELSANTE->lecture_ficher_txt($donnee['type_enregistrement'],$donnee['zone_reservee'],$donnee['code_caisse_gestionnaire'],$donnee['identification_fichier'],$donnee['programme_emetteur'],$donnee['date_creation_fichier'],$donnee['numero_generation'],$donnee['organisme_emetteur'],$donnee['organisme_destinataire'],$donnee['nature_fichier'],$donnee['numero_chronologique'],$donnee['code_ps'],$donnee['num_immat'],$donnee['nouveau_num_immat'],$donnee['cpam_gestionnaire'],$donnee['civilite'],$donnee['code_appartenance_cee'],$donnee['nom_patronymique'],$donnee['prenom_usuel'],$donnee['prenom_2'],$donnee['prenom_3'],$donnee['nom_usage'],$donnee['complement_adresse'],$donnee['complement_adresse_2'],$donnee['num_voie'],$donnee['complement_num_voie'],$donnee['type_voie'],$donnee['libelle_voie'],$donnee['libelle_lieu_dit'],$donnee['code_postal'],$donnee['libelle_commune'],$donnee['code_separt_commune'],$donnee['code_commune'],$donnee['libelle_pays'],$donnee['num_telephone'],$donnee['num_fax'],$donnee['adresse_email'],$donnee['code_affiliation'],$donnee['date_effet_affiliation'],$donnee['categorie_pam'],$donnee['date_debut_activite_liberale'],$donnee['mode_exercice_particulier'],$donnee['annee_obtention_these'],$donnee['departement_ville_faculte'],$donnee['code_commune_ville_faculte'],$donnee['code_diplome_etudes_specialisees'],$donnee['annee_obtention_diplome'],$donnee['code_specialite'],$donnee['date_debut_specialite'],$donnee['date_fin_specialite'],$donnee['code_convention'],$donnee['date_effet_convention'],$donnee['motif_sortie_convention'],$donnee['niveau_cotation_preferentiel'],$donnee['autorisation_refus_libelle_specialite'],$donnee['specialite_ddas'],$donnee['code_orientation'],$donnee['libelle_orientation'],$donnee['code_attribution'],$donnee['libelle_attribution'],$donnee['code_specialisation'],$donnee['libelle_specialisation'],$donnee['inscription_tableau_annexe'],$donnee['formation_particuliere'],$donnee['code_titre_professionnel'],$donnee['code_nature_exercice'],$donnee['date_debut_nature'],$donnee['date_fin_nature'],$donnee['motif_fin_nature_exercice']);
                                                $nb_retours = count($lectures);
                                                if($nb_retours!= 0)  {
                                                    foreach ($lectures as $lecture) {
                                                        if($lecture['message']) {
                                                            $donnees_rapport = "Erreur ligne {$ligne}: {$lecture['message']}\n";
                                                            fwrite($fichier_rapport, $donnees_rapport);
                                                            $nb_erreurs++;
                                                        }
                                                    }
                                                }
                                                $ligne++;
                                            }

                                            $donnees_rapport = "\n\n\n".date('d-m-Y H:i:s',time())." FIN DE LECTURE DU FICHIER {$filename}\n";
                                            fwrite($fichier_rapport, $donnees_rapport);
                                            $donnees_rapport = "NOMBRE D'ERREURS IDENTIFIEES: {$nb_erreurs}\n\n";
                                            fwrite($fichier_rapport, $donnees_rapport);

                                            if($nb_erreurs != 0) {
                                                $donnees_rapport = "LE TRAITEMENT A ETE INTERROMPU.\n";
                                                fwrite($fichier_rapport, $donnees_rapport);
                                                $json = array(
                                                    'success' => false,
                                                    'message' => $donnees_rapport
                                                );
                                            }
                                            else {
                                                $log_lecture = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',"FIN DE LECTURE",$utilisateur['user_id']);
                                                if($log_lecture['success'] == true) {
                                                    $log_chargement = $LOGS->ajouter_chargement_fichier('IMP',$filename,'DEB',"DEBUT DE CHARGEMENT",$utilisateur['user_id']);
                                                    if($log_chargement['success'] == true) {
                                                        $nouveau_script = $SCRIPTS->ajouter('SCRIPT_CHARGEMENT_REFERENTIEL_PROFESSIONNEL_SANTE',date('Y-m-d H:i:s',time()),NULL,'ENC','CHARGEMENT DU REFERNTIEL ETABLISSEMENTS DE SANTE NOMME: '.$filename,$utilisateur['user_id']);
                                                        if($nouveau_script['success'] == true) {
                                                            $donnees_rapport = date('d-m-Y H:i:s',time())." DEBUT DU TRAITEMENT DU FICHIER {$filename}\n\n";
                                                            fwrite($fichier_rapport, $donnees_rapport);
                                                            $ligne = 0;
                                                            $succes = 0;
                                                            $echecs = 0;
                                                            $creation = 0;
                                                            $retour_echec = array();
                                                            $maj = 0;
                                                            foreach ($donnees as $donnee) {
                                                                if($ligne != 0) {
                                                                    $edition = $PROFESSIONNELSANTE->edition($type_enregistrement,$numero_generation,$date_creation_fichier,$donnee['code_ps'],$donnee['civilite'],$donnee['nom_patronymique'],$donnee['prenom_usuel'],$donnee['categorie_pam'],$donnee['num_voie'],$donnee['type_voie'],NULL,$donnee['libelle_voie'],NULL,$donnee['libelle_commune'],$donnee['code_postal'],NULL,$donnee['num_telephone'],$donnee['num_fax'],$donnee['adresse_email'],$donnee['code_diplome_etudes_specialisees'],NULL,$donnee['annee_obtention_diplome'],$donnee['code_specialite'],$donnee['date_debut_specialite'],$donnee['date_fin_specialite'],$donnee['code_convention'],$donnee['date_effet_convention'],$donnee['motif_sortie_convention'],$donnee['code_nature_exercice'],$donnee['date_debut_nature'],$donnee['date_fin_nature'],$donnee['motif_fin_nature_exercice'],$utilisateur['user_id']);
                                                                    if($edition['success'] == true) {
                                                                        if($edition['type'] == 0) {
                                                                            $creation++;
                                                                            $succes++;
                                                                        }elseif($edition['type'] == 1) {
                                                                            $maj++;
                                                                            $succes++;
                                                                        }else {
                                                                            0;
                                                                        }
                                                                    }else {
                                                                        $retour_echec[] = $edition;
                                                                        $echecs++;
                                                                    }
                                                                }
                                                                $ligne++;
                                                            }
                                                            if($echecs == 0) {
                                                                $log_chargement = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',"FIN DE CHARGEMENT",$utilisateur['user_id']);
                                                                if($log_chargement['success'] == true) {
                                                                    $historique = $LOGS->ajouter_historique_fichier('IMP',$numero_generation,$norme,$numero_chronologique,$date_creation_fichier,$organisme_emetteur,$organisme_destinataire,($creation+$maj),$creation,$maj,$filename,$utilisateur['user_id']);
                                                                    if($historique['success'] == true) {
                                                                        $mise_a_jour_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],$log_lecture['id_log'],$log_chargement['id_log'],date('Y-m-d H:i:s',time()),'FIN',"CHARGEMENT DU REFERNTIEL PROFESSIONNELS DE SANTE NOMME: {$filename} EFFECTUE AVEC SUCCES",$utilisateur['user_id']);
                                                                        if($mise_a_jour_script['success'] == true) {
                                                                            $donnees_rapport = date('d-m-Y H:i:s',time())." FIN DU TRAITEMENT DU FICHIER {$filename}\n\nFICHIER CHARGE AVEC SUCCES\nNOM DU FICHIER: {$filename}\nNOMBRE DE LIGNES: ".($succes+$echecs)."\nREUSSITE: {$succes}\nCREATION: {$creation}\nMISE A JOUR: {$maj}\nECHECS: {$echecs}";
                                                                            fwrite($fichier_rapport, $donnees_rapport);
                                                                            fclose($fichier_rapport);
                                                                            $message = "FICHIER CHARGE AVEC SUCCES.<br />NOM DU FICHIER: <b>{$filename}</b><br />NOMBRE DE LIGNES: <b>".($succes+$echecs)."</b><br />REUSSITE: <b>{$succes}</b><br />CREATION: <b>{$creation}</b><br />MISE A JOUR: <b>{$maj}</b><br />ECHECS: <b>{$echecs}</b><br /><a target='_blank' href='".$lien_ps.$ps_filename."' download='".$ps_filename."'>Cliquez ici pour télécharger le fichier</a>";
                                                                            $json = array(
                                                                                'success' => true,
                                                                                'message' => $message
                                                                            );
                                                                        }else {
                                                                            $json = $mise_a_jour_script;
                                                                        }
                                                                    }else {
                                                                        $json = $historique;
                                                                    }
                                                                }else {
                                                                    $json = $log_chargement;
                                                                }
                                                            }else {
                                                                $message = "Une erreur est survenue lors du chargement du fichier.";
                                                                $json = array(
                                                                    'success' => false,
                                                                    'message' => json_encode($retour_echec)
                                                                );
                                                            }
                                                        }else {
                                                            $json = $nouveau_script;
                                                        }
                                                    }else {
                                                        $json = $log_chargement;
                                                    }
                                                }else {
                                                    $json = $log_lecture;
                                                }
                                            }

                                        }
                                    }else {
                                        $message = "Le fichier {$filename} a déjà servi pour un autre chargement.";
                                        $json = array(
                                            'success' => false,
                                            'message' => $message
                                        );
                                        $log_lecture = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',$message,$utilisateur['user_id']);
                                    }
                                }
                                elseif($type_ref == 'LC'){
                                    $norme = 'REFLETCLE01';
                                    $path_lc = $path_export.$norme.'/'.date('d_m_Y',time()).'/';
                                    $lien_lc = $lien_export.$norme.'/'.date('d_m_Y',time()).'/';
                                    if(!file_exists($path_lc)){
                                        mkdir($path_lc,0777,true);
                                    }
                                    $lc_filename = "RAPPORT_INTEGRATION_".str_replace('.txt','',$filename)."_".date('dmYHis').".txt";
                                    $fichier_rapport = fopen($path_lc.$lc_filename, "w") or die("Unable to open file!");
                                    while(($data = fgets($lecture_fichier)) !== false) {
                                        if($ligne == 0) {
                                            $type_enregistrement = trim(substr($data,0,3));
                                            $type_emetteur = trim(substr($data,3,2));
                                            $numero_emetteur = trim(substr($data,5,14));
                                            $programme_emetteur = trim(substr($data,19,6));
                                            $type_destinataire = trim(substr($data,25,2));
                                            $numero_destinataire = trim(substr($data,27,14));
                                            $programme_destinataire = trim(substr($data,41,6));
                                            $type_echange = trim(substr($data,47,2));
                                            $identification_fichier = trim(substr($data,49,3));
                                            $date_creation_fichier = substr(trim(substr($data,52,8)),0,4).'-'.substr(trim(substr($data,52,8)),4,2).'-'.substr(trim(substr($data,52,8)),6,2);
                                            $informations_NOEMIE = trim(substr($data,60,24));
                                            $numero_chronologique = trim(substr($data,84,5));
                                            $version_fichier = trim(substr($data,89,4));
                                            $type_fichier = trim(substr($data,93,1));
                                            $filler = trim(substr($data,94,34));

                                            $donnees[$ligne] = array(
                                                'type_enregistrement' => $type_enregistrement,
                                                'type_emetteur' => $type_emetteur,
                                                'numero_emetteur' => $numero_emetteur,
                                                'programme_emetteur' => $programme_emetteur,
                                                'type_destinataire' => $type_destinataire,
                                                'numero_destinataire' => $numero_destinataire,
                                                'programme_destinataire' => $programme_destinataire,
                                                'type_echange' => $type_echange,
                                                'identification_fichier' => $identification_fichier,
                                                'date_creation_fichier' => $date_creation_fichier,
                                                'informations_NOEMIE' => $informations_NOEMIE,
                                                'numero_chronologique' => $numero_chronologique,
                                                'version_fichier' => $version_fichier,
                                                'type_fichier' => $type_fichier
                                            );
                                        }else {
                                            $niveau = trim(substr($data,0,7));

                                            if($niveau == '0010101') {
                                                $code_lettre_cle = trim(substr($data,7,6));
                                                $designation_lettre_cle = trim(utf8_encode(substr($data,13,30)));
                                                $date_creation_lettre = trim(substr($data,43,8));
                                                $date_debut_validite = date('Y-m-d',strtotime(substr($date_creation_lettre,0,4).'-'.substr($date_creation_lettre,4,2).'-'.substr($date_creation_lettre,6,2)));

                                                $code_convention = '';
                                                $libelle_convention = '';

                                                $prix_unitaire = '';
                                                $date_effet_lettre_cle = '';

                                            }

                                            if($niveau == '0010201') {
                                                $code_convention = trim(substr($data,7,2));
                                                $libelle_convention = trim(substr($data,9,30));
                                            }

                                            if($niveau == '0020101') {
                                                $prix_unitaire = trim(intval(substr($data,7,9)));
                                                $date_effet = trim(substr($data,21,8));
                                                $date_effet_lettre_cle = date('Y-m-d',strtotime(substr($date_effet,0,4).'-'.substr($date_effet,4,2).'-'.substr($date_effet,6,2)));
                                            }
                                            $donnees[$ligne] = array(
                                                'niveau' => $niveau,
                                                'code' => $code_lettre_cle,
                                                'designation' => conversionCaracteresSpeciaux($designation_lettre_cle),
                                                'date_creation' => $date_debut_validite,
                                                'code_convention' => $code_convention,
                                                'libelle_convention' => conversionCaracteresSpeciaux($libelle_convention),
                                                'prix_unitaire' => intval($prix_unitaire),
                                                'date_effet_lettre_cle' => $date_effet_lettre_cle
                                            );
                                        }
                                        $nb_donnees = count($donnees);
                                        $ligne++;
                                    }
                                    $fichier = $LOGS->trouver_fichier($norme,$numero_chronologique,$filename,$numero_chronologique);
                                    if(!$fichier) {
                                        if($nb_donnees != 0) {
                                            $donnees_rapport = date('d-m-Y H:i:s',time())." DEBUT DE LECTURE DU FICHIER {$filename}\n\n";
                                            fwrite($fichier_rapport, $donnees_rapport);
                                            $ligne = 0;
                                            $nb_erreurs = 0;
                                            foreach ($donnees as $donnee) {
                                                $lectures = $LETTRESCLES->lecture_ficher_txt($donnee['type_enregistrement'],$donnee['type_emetteur'],$donnee['numero_emetteur'],$donnee['programme_emetteur'],$donnee['type_destinataire'],$donnee['numero_destinataire'],$donnee['programme_destinataire'],$donnee['type_echange'],$donnee['identification_fichier'],$donnee['date_creation_fichier'],$donnee['informations_NOEMIE'],$donnee['numero_chronologique'],$donnee['version_fichier'],$donnee['type_fichier'],$donnee['filler'],$donnee['code'],$donnee['designation'],$donnee['date_creation'],$donnee['code_convention'],$donnee['libelle_convention'],$donnee['prix_unitaire'],$donnee['date_effet_lettre_cle']);
                                                //var_dump($lectures);
                                                $nb_retours = count($lectures);
                                                if($nb_retours!= 0)  {
                                                    foreach ($lectures as $lecture) {
                                                        if($lecture['message']) {
                                                            $donnees_rapport = "Erreur ligne {$ligne}: {$lecture['message']}\n";
                                                            fwrite($fichier_rapport, $donnees_rapport);
                                                            $nb_erreurs++;
                                                        }
                                                    }
                                                }
                                                $ligne++;
                                            }

                                            $donnees_rapport = "\n\n\n".date('d-m-Y H:i:s',time())." FIN DE LECTURE DU FICHIER {$filename}\n";
                                            fwrite($fichier_rapport, $donnees_rapport);
                                            $donnees_rapport = "NOMBRE D'ERREURS IDENTIFIEES: {$nb_erreurs}\n\n";
                                            fwrite($fichier_rapport, $donnees_rapport);

                                            if($nb_erreurs != 0) {
                                                $donnees_rapport = "LE TRAITEMENT A ETE INTERROMPU.\n";
                                                fwrite($fichier_rapport, $donnees_rapport);
                                                fclose($fichier_rapport);
                                                $message = "<a target='_blank' href='".$lien_lc.$lc_filename."' download='".$lc_filename."'>{$donnees_rapport}<br />Cliquez ici pour télécharger le fichier</a>";
                                                $json = array(
                                                    'success' => false,
                                                    'message' => $message
                                                );
                                            }
                                            else {
                                                $log_lecture = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',"FIN DE LECTURE",$utilisateur['user_id']);
                                                if($log_lecture['success'] == true) {
                                                    $log_chargement = $LOGS->ajouter_chargement_fichier('IMP',$filename,'DEB',"DEBUT DE CHARGEMENT",$utilisateur['user_id']);
                                                    if($log_chargement['success'] == true) {
                                                        $nouveau_script = $SCRIPTS->ajouter('SCRIPT_CHARGEMENT_REFERENTIEL_LETTRES_CLES',date('Y-m-d H:i:s',time()),NULL,'ENC','CHARGEMENT DU REFERNTIEL ETABLISSEMENTS DE SANTE NOMME: '.$filename,$utilisateur['user_id']);
                                                        if($nouveau_script['success'] == true) {
                                                            $donnees_rapport = date('d-m-Y H:i:s',time())." DEBUT DU TRAITEMENT DU FICHIER {$filename}\n\n";
                                                            fwrite($fichier_rapport, $donnees_rapport);
                                                            $ligne = 0;
                                                            $succes = 0;
                                                            $echecs = 0;
                                                            $creation = 0;
                                                            $retour_echec = array();
                                                            $maj = 0;
                                                            foreach ($donnees as $donnee) {
                                                                if($ligne != 0) {
                                                                    if($donnee['niveau'] == '0090101') {
                                                                        $edition = $LETTRESCLES->edition($numero_chronologique,$donnee['date_creation'],$donnee['code'],$donnee['designation'],$donnee['code_convention'],$donnee['prix_unitaire'],$donnee['date_effet_lettre_cle'],$donnee['date_creation'],$utilisateur['user_id']);
                                                                        if($edition['success'] == true) {
                                                                            if($edition['type'] == 0) {
                                                                                $creation++;
                                                                                $succes++;
                                                                            }elseif($edition['type'] == 1) {
                                                                                $maj++;
                                                                                $succes++;
                                                                            }else {
                                                                                0;
                                                                            }
                                                                        }
                                                                        else {
                                                                            $retour_echec[] = $edition;
                                                                            $echecs++;
                                                                        }
                                                                    }
                                                                }
                                                                $ligne++;
                                                            }

                                                            if($echecs == 0) {
                                                                $log_chargement = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',"FIN DE CHARGEMENT",$utilisateur['user_id']);
                                                                if($log_chargement['success'] == true) {
                                                                    $historique = $LOGS->ajouter_historique_fichier('IMP',$version_fichier,$norme,$numero_chronologique,$date_creation_fichier,$programme_emetteur,$programme_destinataire,($creation+$maj),$creation,$maj,$filename,$utilisateur['user_id']);
                                                                    if($historique['success'] == true) {
                                                                        $mise_a_jour_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],$log_lecture['id_log'],$log_chargement['id_log'],date('Y-m-d H:i:s',time()),'FIN',"CHARGEMENT DU REFERNTIEL PROFESSIONNELS DE SANTE NOMME: {$filename} EFFECTUE AVEC SUCCES",$utilisateur['user_id']);
                                                                        if($mise_a_jour_script['success'] == true) {
                                                                            $donnees_rapport = date('d-m-Y H:i:s',time())." FIN DU TRAITEMENT DU FICHIER {$filename}\n\nFICHIER CHARGE AVEC SUCCES\nNOM DU FICHIER: {$filename}\nNOMBRE DE LIGNES: ".($succes+$echecs)."\nREUSSITE: {$succes}\nCREATION: {$creation}\nMISE A JOUR: {$maj}\nECHECS: {$echecs}";
                                                                            fwrite($fichier_rapport, $donnees_rapport);
                                                                            fclose($fichier_rapport);
                                                                            $message = "FICHIER CHARGE AVEC SUCCES.<br />NOM DU FICHIER: <b>{$filename}</b><br />NOMBRE DE LIGNES: <b>".($succes+$echecs)."</b><br />REUSSITE: <b>{$succes}</b><br />CREATION: <b>{$creation}</b><br />MISE A JOUR: <b>{$maj}</b><br />ECHECS: <b>{$echecs}</b><br /><a target='_blank' href='".$lien_lc.$lc_filename."' download='".$lc_filename."'>Cliquez ici pour télécharger le fichier</a>";
                                                                            $json = array(
                                                                                'success' => true,
                                                                                'message' => $message
                                                                            );
                                                                        }else {
                                                                            $json = $mise_a_jour_script;
                                                                        }
                                                                    }else {
                                                                        $json = $historique;
                                                                    }
                                                                }else {
                                                                    $json = $log_chargement;
                                                                }
                                                            }
                                                            else {
                                                                $message = "Une erreur est survenue lors du chargement du fichier.";
                                                                $json = array(
                                                                    'success' => false,
                                                                    'message' => json_encode($retour_echec)
                                                                );
                                                            }
                                                        }else {
                                                            $json = $nouveau_script;
                                                        }
                                                    }else {
                                                        $json = $log_chargement;
                                                    }
                                                }else {
                                                    $json = $log_lecture;
                                                }
                                            }
                                        }
                                    }else {
                                        $message = "Le fichier {$filename} a déjà servi pour un autre chargement.";
                                        $json = array(
                                            'success' => false,
                                            'message' => $message
                                        );
                                        $log_lecture = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',$message,$utilisateur['user_id']);
                                    }
                                }
                                elseif($type_ref == 'NGAMBCI'){

                                }
                                elseif($type_ref == 'FH'){

                                }
                                elseif($type_ref == 'MED'){

                                }
                                elseif($type_ref == 'PATH'){

                                }
                                else {
                                    $message = "Le type de référentiel <b>{$type_ref}</b> n'est pas reconnu par le système.";
                                    $json = array(
                                        'success' => false,
                                        'message' => $message
                                    );
                                }

                            }
                            elseif ($extension == 'xml') {
                                if($type_ref == 'ETS'){}
                                elseif($type_ref == 'PS'){}
                                elseif($type_ref == 'LC'){}
                                elseif($type_ref == 'NGAMBCI'){}
                                elseif($type_ref == 'FH'){}
                                elseif($type_ref == 'MED'){}
                                elseif($type_ref == 'PATH'){}

                                $message = 'Le type de référentiel <b>.'.$type_ref.'</b> n\'est pas reconnu par le système.';
                                $json = array(
                                    'success' => false,
                                    'message' => $message
                                );
                            }
                            else{
                                $message = 'L\'extension <b>.'.$extension.'</b> n\'est pas accepté par le système.';
                                $json = array(
                                    'success' => false,
                                    'message' => $message
                                );
                            }
                        }else {
                            $json = $log_lecture;
                        }
                    }else{
                        $message = 'L\'extension <b>.'.$extension.'</b> n\'est pas accepté par le système.';
                        $json = array(
                            'success' => false,
                            'message' => $message
                        );
                    }
                }else {
                    $message = 'Le fichier sélectionné est introuvable pour chargement.';
                    $json = array(
                        'success' => false,
                        'message' => $message
                    );
                }
            }else {
                $message = 'Le fichier sélectionné semble êtree corrompu, Veuillez en sélectionner un autre.';
                $json = array(
                    'success' => false,
                    'message' => $message
                );
            }
        }else {
            $message = 'Veuiller sélectionner le type de référentiel à charger SVP.';
            $json = array(
                'success' => false,
                'message' => $message
            );
        }

    }else {
        $message = 'Aucune session disponible pour cet utilisateur.!!! contactez votre administrateur.';
        $json = array(
            'success' => false,
            'message' => $message
        );
    }
}else {
    $message = 'Aucune session disponible pour cet utilisateur.!!! contactez votre administrateur.';
    $json = array(
        'success' => false,
        'message' => $message
    );
}
echo json_encode($json);
?>