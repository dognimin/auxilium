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
                    require_once '../../../../Classes/REJETS.php';
                    require_once '../../../../Classes/LOGS.php';

                    $PROFESSIONNELSANTE = new PROFESSIONNELSANTE();
                    $ETABLISSEMENTSANTE = new ETABLISSEMENTSANTE();
                    $ACTESMEDICAUX = new ACTESMEDICAUX();
                    $MEDICAMENTS = new MEDICAMENTS();
                    $PATHOLOGIES = new PATHOLOGIES();
                    $LETTRESCLES = new LETTRESCLES();
                    $SCRIPTS = new SCRIPTS();
                    $REJETS = new REJETS();
                    $LOGS = new LOGS();

                    if(in_array(strtolower($extension),$valid_extensions)) {
                        $log_lecture = $LOGS->ajouter_chargement_fichier('IMP',$filename,'DEB',"DEBUT DE LECTURE",$utilisateur['user_id']);
                        if($log_lecture['success'] == true) {
                            if(strtolower($extension) == 'txt'){
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
                                        }
                                        else {
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
                                    $norme = 'REFNGAMBCI01';
                                    $path_actes = $path_export.$norme.'/'.date('d_m_Y',time()).'/';
                                    $lien_actes = $lien_export.$norme.'/'.date('d_m_Y',time()).'/';
                                    if(!file_exists($path_actes)){
                                        mkdir($path_actes,0777,true);
                                    }
                                    $actes_filename = "RAPPORT_INTEGRATION_".str_replace('.txt','',$filename)."_".date('dmYHis').".txt";
                                    $fichier_rapport = fopen($path_actes.$actes_filename, "w") or die("Unable to open file!");
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
                                                'type_fichier' => $type_fichier,
                                                'filler' => $filler
                                            );
                                        }
                                        else {
                                            $niveau = trim(substr($data,0,7));
                                            if($niveau == '0010101') {
                                                $type_acte = "NGAMBCI";
                                                $code_ngap = trim(substr($data,7,8));
                                                $designation_ngap = trim(utf8_encode(substr($data,15,100)));
                                                $date_creation_ngap = trim(substr($data,115,8));
                                                $date_debut_validite = date('Y-m-d',strtotime(substr($date_creation_ngap,0,4).'-'.substr($date_creation_ngap,4,2).'-'.substr($date_creation_ngap,6,2)));
                                                $libelle_titre = '';
                                                $libelle_section = '';
                                                $libelle_article = '';
                                                $libelle_chapitre = '';
                                                $code_lettre_cle = '';
                                                $coefficient = '';

                                                $l=$ligne+1;
                                            }

                                            if($niveau == '0010201') {
                                                $libelle_titre = trim(utf8_encode(substr($data,11,46)));
                                                $libelle_section = trim(utf8_encode(substr($data,61,47)));
                                            }

                                            if($niveau == '0010301') {
                                                $libelle_article = trim(utf8_encode(substr($data,11,46)));
                                                $libelle_chapitre = trim(utf8_encode(substr($data,61,47)));
                                            }

                                            if($niveau == '0020101') {
                                                $code_lettre_cle = trim(substr($data,7,6));
                                                $coefficient = trim(substr($data,103,5));
                                            }

                                            $donnees[$ligne] = array(
                                                'niveau' => $niveau,
                                                'type_acte' => $type_acte,
                                                'code_ngap' => $code_ngap,
                                                'designation_ngap' => strtoupper(conversionCaracteresSpeciaux($designation_ngap)),
                                                'date_debut_validite' => $date_debut_validite,
                                                'libelle_titre' => strtoupper(conversionCaracteresSpeciaux($libelle_titre)),
                                                'libelle_section' => strtoupper(conversionCaracteresSpeciaux($libelle_section)),
                                                'libelle_article' => strtoupper(conversionCaracteresSpeciaux($libelle_article)),
                                                'libelle_chapitre' => strtoupper(conversionCaracteresSpeciaux($libelle_chapitre)),
                                                'code_lettre_cle' => $code_lettre_cle,
                                                'coefficient' => $coefficient
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
                                                if($donnee['type_enregistrement'] != '' || $donnee['niveau'] == '0090101') {
                                                    $lectures = $ACTESMEDICAUX->lecture_ficher_txt($donnee['type_enregistrement'],$donnee['type_emetteur'],$donnee['numero_emetteur'],$donnee['programme_emetteur'],$donnee['type_destinataire'],$donnee['numero_destinataire'],$donnee['programme_destinataire'],$donnee['type_echange'],$donnee['identification_fichier'],$donnee['date_creation_fichier'],$donnee['informations_NOEMIE'],$donnee['numero_chronologique'],$donnee['version_fichier'],$donnee['type_fichier'],$donnee['type_acte'],$donnee['code_ngap'],$donnee['designation_ngap'],$donnee['libelle_titre'],$donnee['libelle_section'],$donnee['libelle_article'],$donnee['libelle_chapitre'],$donnee['code_lettre_cle']);
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
                                                $message = "<a target='_blank' href='".$lien_actes.$actes_filename."' download='".$actes_filename."'>{$donnees_rapport}<br />Cliquez ici pour télécharger le fichier</a>";
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
                                                        $nouveau_script = $SCRIPTS->ajouter('SCRIPT_CHARGEMENT_REFERENTIEL_ACTES_MECICAUX',date('Y-m-d H:i:s',time()),NULL,'ENC','CHARGEMENT DU REFERNTIEL ACTES MEDICAUX NOMME: '.$filename,$utilisateur['user_id']);
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
                                                                        $edition = $ACTESMEDICAUX->edition($numero_chronologique,$date_creation_fichier,$donnee['type_acte'],$donnee['code_ngap'],$donnee['designation_ngap'],$donnee['libelle_titre'],$donnee['libelle_chapitre'],$donnee['libelle_section'],$donnee['libelle_article'],$donnee['code_lettre_cle'],$donnee['coefficient'],NULL,$utilisateur['user_id']);
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
                                                                        $mise_a_jour_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],$log_lecture['id_log'],$log_chargement['id_log'],date('Y-m-d H:i:s',time()),'FIN',"CHARGEMENT DU REFERNTIEL ACTES MEDICAUX NOMME: {$filename} EFFECTUE AVEC SUCCES",$utilisateur['user_id']);
                                                                        if($mise_a_jour_script['success'] == true) {
                                                                            $donnees_rapport = date('d-m-Y H:i:s',time())." FIN DU TRAITEMENT DU FICHIER {$filename}\n\nFICHIER CHARGE AVEC SUCCES\nNOM DU FICHIER: {$filename}\nNOMBRE DE LIGNES: ".($succes+$echecs)."\nREUSSITE: {$succes}\nCREATION: {$creation}\nMISE A JOUR: {$maj}\nECHECS: {$echecs}";
                                                                            fwrite($fichier_rapport, $donnees_rapport);
                                                                            fclose($fichier_rapport);
                                                                            $message = "FICHIER CHARGE AVEC SUCCES.<br />NOM DU FICHIER: <b>{$filename}</b><br />NOMBRE DE LIGNES: <b>".($succes+$echecs)."</b><br />REUSSITE: <b>{$succes}</b><br />CREATION: <b>{$creation}</b><br />MISE A JOUR: <b>{$maj}</b><br />ECHECS: <b>{$echecs}</b><br /><a target='_blank' href='".$lien_actes.$actes_filename."' download='".$actes_filename."'>Cliquez ici pour télécharger le fichier</a>";
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
                                        $log_lecture = $LOGS->ajouter_chargement_fichier('IMP', $filename, 'FIN', $message, $utilisateur['user_id']);
                                    }
                                }
                                elseif($type_ref == 'FH'){
                                    $norme = 'REFFH01';
                                    $path_fh = $path_export.$norme.'/'.date('d_m_Y',time()).'/';
                                    $lien_fh = $lien_export.$norme.'/'.date('d_m_Y',time()).'/';
                                    if(!file_exists($path_fh)){
                                        mkdir($path_fh,0777,true);
                                    }
                                    $fh_filename = "RAPPORT_INTEGRATION_".str_replace('.txt','',$filename)."_".date('dmYHis').".txt";
                                    $fichier_rapport = fopen($path_fh.$fh_filename, "w") or die("Unable to open file!");
                                    while(($data = fgets($lecture_fichier)) !== false) {
                                        if($ligne == 0) {
                                            $type_enregistrement = trim(substr($data,0,3));
                                            $type_emetteur = trim(substr($data,3,2));
                                            $numero_emetteur = trim(substr($data,5,14));
                                            $programme_emetteur = trim(substr($data,19,10));
                                            $type_destinataire = trim(substr($data,29,2));
                                            $numero_destinataire = trim(substr($data,31,14));
                                            $programme_destinataire = trim(substr($data,45,6));
                                            $type_echange = trim(substr($data,51,2));
                                            $identification_fichier = trim(substr($data,53,7));
                                            $date_creation_fichier = substr(trim(substr($data,60,8)),0,4).'-'.substr(trim(substr($data,60,8)),4,2).'-'.substr(trim(substr($data,60,8)),6,2);
                                            $informations_NOEMIE = trim(substr($data,68,24));
                                            $numero_chronologique = trim(substr($data,92,5));
                                            $type_fichier = trim(substr($data,97,1));
                                            $version_fichier = trim(substr($data,98,4));
                                            $filler = trim(substr($data,102,34));

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
                                                'type_fichier' => $type_fichier,
                                                'filler' => $filler
                                            );
                                        }
                                        else {
                                            $niveau = trim(substr($data,0,7));

                                            if($niveau == '1010101') {
                                                $type_acte = "FH";
                                                $code_forfait = trim(substr($data,7,15));
                                                $libelle_forfait = trim(utf8_encode(substr($data,22,100)));

                                                $l=$ligne+1;
                                            }
                                            if($niveau == '1100101') {
                                                $secteur = trim(substr($data,22,1));
                                                $dms = trim(substr($data,23,3));
                                                $seuil_haut = trim(substr($data,26,3));
                                                $seuil_bas = trim(substr($data,29,3));
                                                $date_debut_validite = trim(substr($data,32,8));
                                                $date_fin_forfait = trim(substr($data,40,8));

                                                $prix_unitaire = trim(substr($data,48,8));

                                                $pu_centime = intval(substr($prix_unitaire,0,6)).'.'.substr($prix_unitaire,6,2);

                                                $exh_possible = trim(substr($data,56,1));
                                                $pu_exh = trim(substr($data,57,8));
                                                $coefficient_reducteur_possible = trim(substr($data,65,1));
                                                $coefficient_reducteur = trim(substr($data,66,3));
                                                $implique_fj = trim(substr($data,69,1));
                                                $nature_assurance = trim(substr($data,70,10));
                                                $age_mini = trim(substr($data,80,2));
                                                $age_maxi = trim(substr($data,82,2));
                                                $sexe_compatible = trim(substr($data,84,1));
                                                $cmd = trim(substr($data,85,2));
                                                $domaine = trim(substr($data,87,1));
                                                $date_arete_ministeriel = trim(substr($data,88,8));
                                                $date_publication_jo = trim(substr($data,96,8));
                                                $date_effet = trim(substr($data,104,8));
                                                $date_effet_maj = date('Y-m-d',strtotime(substr($date_effet,0,4).'-'.substr($date_effet,4,2).'-'.substr($date_effet,6,2)));
                                            }

                                            $donnees[$ligne] = array(
                                                'niveau' => $niveau,
                                                'type_acte' => $type_acte,
                                                'code_forfait' => $code_forfait,
                                                'libelle_forfait' => strtoupper(conversionCaracteresSpeciaux($libelle_forfait)),
                                                'tarif' => $pu_centime
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
                                                if($donnee['type_enregistrement'] != '' || $donnee['niveau'] == '0090101') {
                                                    $lectures = $ACTESMEDICAUX->lecture_ficher_txt($donnee['type_enregistrement'],$donnee['type_emetteur'],$donnee['numero_emetteur'],$donnee['programme_emetteur'],$donnee['type_destinataire'],$donnee['numero_destinataire'],$donnee['programme_destinataire'],$donnee['type_echange'],$donnee['identification_fichier'],$donnee['date_creation_fichier'],$donnee['informations_NOEMIE'],$donnee['numero_chronologique'],$donnee['version_fichier'],$donnee['type_fichier'],$donnee['type_acte'],$donnee['code_ngap'],$donnee['designation_ngap'],$donnee['libelle_titre'],$donnee['libelle_section'],$donnee['libelle_article'],$donnee['libelle_chapitre'],$donnee['code_lettre_cle']);
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
                                                $message = "<a target='_blank' href='".$lien_fh.$fh_filename."' download='".$fh_filename."'>{$donnees_rapport}<br />Cliquez ici pour télécharger le fichier</a>";
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
                                                        $nouveau_script = $SCRIPTS->ajouter('SCRIPT_CHARGEMENT_REFERENTIEL_ACTES_MECICAUX',date('Y-m-d H:i:s',time()),NULL,'ENC','CHARGEMENT DU REFERNTIEL ACTES MEDICAUX NOMME: '.$filename,$utilisateur['user_id']);
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
                                                                        $edition = $ACTESMEDICAUX->edition($numero_chronologique,$date_creation_fichier,$donnee['type_acte'],$donnee['code_forfait'],$donnee['libelle_forfait'],NULL,NULL,NULL,NULL,NULL,NULL,$donnee['tarif'],$utilisateur['user_id']);
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
                                                                        $mise_a_jour_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],$log_lecture['id_log'],$log_chargement['id_log'],date('Y-m-d H:i:s',time()),'FIN',"CHARGEMENT DU REFERNTIEL ACTES MEDICAUX NOMME: {$filename} EFFECTUE AVEC SUCCES",$utilisateur['user_id']);
                                                                        if($mise_a_jour_script['success'] == true) {
                                                                            $donnees_rapport = date('d-m-Y H:i:s',time())." FIN DU TRAITEMENT DU FICHIER {$filename}\n\nFICHIER CHARGE AVEC SUCCES\nNOM DU FICHIER: {$filename}\nNOMBRE DE LIGNES: ".($succes+$echecs)."\nREUSSITE: {$succes}\nCREATION: {$creation}\nMISE A JOUR: {$maj}\nECHECS: {$echecs}";
                                                                            fwrite($fichier_rapport, $donnees_rapport);
                                                                            fclose($fichier_rapport);
                                                                            $message = "FICHIER CHARGE AVEC SUCCES.<br />NOM DU FICHIER: <b>{$filename}</b><br />NOMBRE DE LIGNES: <b>".($succes+$echecs)."</b><br />REUSSITE: <b>{$succes}</b><br />CREATION: <b>{$creation}</b><br />MISE A JOUR: <b>{$maj}</b><br />ECHECS: <b>{$echecs}</b><br /><a target='_blank' href='".$lien_fh.$fh_filename."' download='".$fh_filename."'>Cliquez ici pour télécharger le fichier</a>";
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
                                        $log_lecture = $LOGS->ajouter_chargement_fichier('IMP', $filename, 'FIN', $message, $utilisateur['user_id']);
                                    }
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
                            elseif (strtolower($extension) == 'xml') {
                                $xml = simplexml_load_file($filetemp);
                                $chaineXml = $xml->asXML();
                                $xmlTransform = simplexml_load_string($chaineXml);
                                $entete = $xmlTransform->ENTETE;

                                $tableau_entete = array(
                                    'nom_modele' => trim($entete->NOM_MODELE),
                                    'version' => trim($entete->VERSION),
                                    'code_systeme_gestion' => trim($entete->CODE_SYSTEME_GESTION),
                                    'code_organisme_emet' => trim($entete->CODE_ORGANISME_EMET),
                                    'nom_organisme_emet' => trim($entete->NOM_ORGANISME_EMET),
                                    'code_organisme_dest' => trim($entete->CODE_ORGANISME_DEST),
                                    'nom_organisme_dest' => trim($entete->NOM_ORGANISME_DEST),
                                    'num_transmission' => trim($entete->NUM_TRANSMISSION),
                                    'date_fichier' => trim($entete->DATE_FICHIER),
                                    'occurrences' => trim($entete->OCCURRENCES)
                                );
                                $path = $path_export.$type_ref.'/'.date('d_m_Y',time()).'/';
                                $lien = $lien_export.$type_ref.'/'.date('d_m_Y',time()).'/';
                                if(!file_exists($path)){
                                    mkdir($path,0777,true);
                                }
                                $report_filename = "RAPPORT_INTEGRATION_".str_replace('.txt','',$filename)."_".date('dmYHis').".txt";

                                $nb_tableau_entete = count($tableau_entete);
                                if($nb_tableau_entete != 0) {
                                    $fichier_rapport = fopen($path.$report_filename, "w") or die("Unable to open file!");
                                    $donnees_rapport = date('d-m-Y H:i:s',time())." DEBUT DE LECTURE DU FICHIER {$filename}\n\n";
                                    fwrite($fichier_rapport, $donnees_rapport);

                                    $lecture_entetes = $SCRIPTS->lecture_entete_fichier_xml($tableau_entete['nom_modele'],$tableau_entete['version'],$tableau_entete['code_systeme_gestion'],$tableau_entete['code_organisme_emet'],$tableau_entete['nom_organisme_emet'],$tableau_entete['code_organisme_dest'],$tableau_entete['nom_organisme_dest'],$tableau_entete['num_transmission'],$tableau_entete['date_fichier'],$tableau_entete['occurrences'],$filename);
                                    $nb_retours_lecture_entete = (count($lecture_entetes) - 1);
                                    if($nb_retours_lecture_entete == 0){
                                        if($type_ref == 'ETS'){
                                            $liste_etablissements = $xmlTransform->LISTE_ETABLISSEMENTS;
                                            $ligne = 0;
                                            $donnees = NULL;
                                            foreach ($liste_etablissements->ETABLISSEMENT as $etablissement) {
                                                $donnees[$ligne] = array(
                                                    'code' => trim($etablissement->CODE),
                                                    'raison_sociale' => strtoupper(conversionCaracteresSpeciaux(trim($etablissement->RAISON_SOCIALE))),
                                                    'region' => trim($etablissement->REGION),
                                                    'departement' => trim($etablissement->DEPARTEMENT),
                                                    'ville' => strtoupper(conversionCaracteresSpeciaux(trim($etablissement->VILLE))),
                                                    'village' => trim($etablissement->VILLAGE),
                                                    'code_specialite' => trim($etablissement->CODE_SPECIALITE),
                                                    'code_type' => trim($etablissement->CODE_TYPE),
                                                    'code_niveau' => trim($etablissement->CODE_NIVEAU),
                                                    'code_secteur_activite' => trim($etablissement->CODE_SECTEUR_ACTIVITE),
                                                    'adresse_postale' => trim($etablissement->ADRESSE_POSTALE),
                                                    'adresse_geographique' => trim($etablissement->ADRESSE_GEOGRAPHIQUE),
                                                    'latitude' => trim($etablissement->LATITUDE),
                                                    'longitude' => trim($etablissement->LONGITUDE),
                                                    'num_telephone_1' => trim($etablissement->NUM_TELEPHONE_1),
                                                    'num_telephone_2' => trim($etablissement->NUM_TELEPHONE_2),
                                                    'fax' => trim($etablissement->FAX),
                                                    'email' => trim($etablissement->EMAIL),
                                                    'date_debut_validite' => trim($etablissement->DATE_DEBUT_VALIDITE),
                                                    'date_fin_validite' => trim($etablissement->DATE_FIN_VALIDITE),
                                                    'motif_fin_validite' => trim($etablissement->MOTIF_FIN_VALIDITE)
                                                );
                                                $ligne++;
                                            }
                                            $nb_donnees = count($donnees);
                                            if($nb_donnees == $tableau_entete['occurrences']) {
                                                foreach ($donnees as $donnee) {
                                                    $lectures = $ETABLISSEMENTSANTE->lecture_fichier_xml($donnee['code'],$donnee['raison_sociale'],$donnee['ville'],$donnee['code_secteur_activite'],$donnee['adresse_geographique'],$donnee['num_telephone_1'],$donnee['date_debut_validite'],$donnee['date_fin_validite']);
                                                    $nb_retours = count($lectures);
                                                    if($nb_retours!= 0)  {
                                                        foreach ($lectures as $lecture) {
                                                            if($lecture['message']) {
                                                                $donnees_rapport = "Erreur établissement {$donnee['code']}: {$lecture['message']}\n";
                                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                                $nb_erreurs++;
                                                            }
                                                        }
                                                    }
                                                }
                                                $donnees_rapport = "\n\n\n".date('d-m-Y H:i:s',time())." FIN DE LECTURE DU FICHIER {$filename}\n";
                                                fwrite($fichier_rapport, $donnees_rapport);
                                                $donnees_rapport = "NOMBRE D'ERREURS IDENTIFIEES: {$nb_erreurs}\n\n";
                                                fwrite($fichier_rapport, $donnees_rapport);

                                                if($nb_erreurs != 0) {
                                                    $donnees_rapport = "LE TRAITEMENT A ETE INTERROMPU.\n";
                                                    $message = $donnees_rapport."NOM DU FICHIER: <b>{$filename}</b><a target='_blank' href='".$lien.$report_filename."' download='".$fichier_rapport."'>Cliquez ici pour télécharger le fichier</a>";
                                                    fwrite($fichier_rapport, $donnees_rapport);
                                                    $json = array(
                                                        'success' => false,
                                                        'message' => $message
                                                    );
                                                }else {
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
                                                                        $edition = $ETABLISSEMENTSANTE->edition(date('Y-m-d',strtotime($donnee['date_debut_validite'])),$tableau_entete['num_transmission'],$donnee['code'],NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,$donnee['raison_sociale'],NULL,NULL,$donnee['num_telephone_1'],$donnee['fax'],NULL,NULL,NULL,NULL,NULL,NULL,$donnee['code_secteur_activite'],NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,$donnee['ville'],NULL,$donnee['ville'],$donnee['email'],$utilisateur['user_id']);
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
                                                                        $historique = $LOGS->ajouter_historique_fichier('IMP',$tableau_entete['num_transmission'],$tableau_entete['nom_modele'],$tableau_entete['num_transmission'],date('Y-m-d',strtotime($tableau_entete['date_fichier'])),$tableau_entete['code_organisme_emet'],$tableau_entete['code_organisme_dest'],($creation+$maj),$creation,$maj,$filename,$utilisateur['user_id']);
                                                                        if($historique['success'] == true) {
                                                                            $mise_a_jour_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],$log_lecture['id_log'],$log_chargement['id_log'],date('Y-m-d H:i:s',time()),'FIN',"CHARGEMENT DU REFERNTIEL ETABLISSEMENTS DE SANTE NOMME: {$filename} EFFECTUE AVEC SUCCES",$utilisateur['user_id']);
                                                                            if($mise_a_jour_script['success'] == true) {
                                                                                $donnees_rapport = date('d-m-Y H:i:s',time())." FIN DU TRAITEMENT DU FICHIER {$filename}\n\nFICHIER CHARGE AVEC SUCCES\nNOM DU FICHIER: {$filename}\nNOMBRE DE LIGNES: ".($succes+$echecs)."\nREUSSITE: {$succes}\nCREATION: {$creation}\nMISE A JOUR: {$maj}\nECHECS: {$echecs}";
                                                                                fwrite($fichier_rapport, $donnees_rapport);
                                                                                fclose($fichier_rapport);
                                                                                $message = "FICHIER CHARGE AVEC SUCCES.<br />NOM DU FICHIER: <b>{$filename}</b><br />NOMBRE DE LIGNES: <b>".($succes+$echecs)."</b><br />REUSSITE: <b>{$succes}</b><br />CREATION: <b>{$creation}</b><br />MISE A JOUR: <b>{$maj}</b><br />ECHECS: <b>{$echecs}</b><br /><a target='_blank' href='".$lien.$report_filename."' download='".$fichier_rapport."'>Cliquez ici pour télécharger le fichier</a>";
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
                                            }else {
                                                $donnees_rapport = "Erreur nombre etablissements: Le nombre d'occurences défini dans l'entête est différent du nombre effectif d'occurrences dans le fichier\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                            }
                                        }
                                        elseif($type_ref == 'PS'){
                                            $liste_professionnels_sante = $xmlTransform->LISTE_PROFESSIONNELS_SANTE;
                                            $ligne = 0;
                                            $donnees = NULL;
                                            foreach ($liste_professionnels_sante->PROFESSIONNEL_SANTE as $professionnel_sante) {
                                                $donnees[$ligne] = array(
                                                    'code' => trim($professionnel_sante->CODE),
                                                    'nom' => strtoupper(conversionCaracteresSpeciaux(trim($professionnel_sante->NOM))),
                                                    'prenoms' => strtoupper(conversionCaracteresSpeciaux(trim($professionnel_sante->PRENOMS))),
                                                    'code_civilite' => trim($professionnel_sante->CODE_CIVILITE),
                                                    'date_naissance' => trim($professionnel_sante->DATE_NAISSANCE),
                                                    'lieu_naissance' => trim($professionnel_sante->LIEU_NAISSANCE),
                                                    'type_ordre' => trim($professionnel_sante->TYPE_ORDRE),
                                                    'numero_ordre' => trim($professionnel_sante->NUMERO_ORDRE),
                                                    'code_categorie_professionnelle' => trim($professionnel_sante->CODE_CATEGORIE_PROFESSIONNELLE),
                                                    'code_specialite' => trim($professionnel_sante->CODE_SPECIALITE),
                                                    'adresse_postale' => trim($professionnel_sante->ADRESSE_POSTALE),
                                                    'adresse_geographique' => trim($professionnel_sante->ADRESSE_GEOGRAPHIQUE),
                                                    'ville' => trim($professionnel_sante->VILLE),
                                                    'region' => trim($professionnel_sante->REGION),
                                                    'departement' => trim($professionnel_sante->DEPARTEMENT),
                                                    'telephone' => trim($professionnel_sante->TELEPHONE),
                                                    'fax' => trim($professionnel_sante->FAX),
                                                    'email' => trim($professionnel_sante->EMAIL),
                                                    'date_debut_validite' => trim($professionnel_sante->DATE_DEBUT_VALIDITE),
                                                    'date_fin_validite' => trim($professionnel_sante->DATE_FIN_VALIDITE),
                                                    'motif_fin_validite' => trim($professionnel_sante->MOTIF_FIN_VALIDITE)
                                                );
                                                $ligne++;
                                            }
                                            $nb_donnees = count($donnees);
                                            if($nb_donnees == $tableau_entete['occurrences']) {
                                                foreach ($donnees as $donnee) {
                                                    $lectures = $PROFESSIONNELSANTE->lecture_fichier_xml($donnee['code'],$donnee['nom'],$donnee['prenoms'],$donnee['code_specialite'],$donnee['ville'],$donnee['num_telephone'],$donnee['date_debut_validite'],$donnee['date_fin_validite']);
                                                    $nb_retours = count($lectures);
                                                    if($nb_retours!= 0)  {
                                                        foreach ($lectures as $lecture) {
                                                            if($lecture['message']) {
                                                                $donnees_rapport = "Erreur professionnel de santé {$donnee['code']}: {$lecture['message']}\n";
                                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                                $nb_erreurs++;
                                                            }
                                                        }
                                                    }
                                                }
                                                $donnees_rapport = "\n\n\n".date('d-m-Y H:i:s',time())." FIN DE LECTURE DU FICHIER {$filename}\n";
                                                fwrite($fichier_rapport, $donnees_rapport);
                                                $donnees_rapport = "NOMBRE D'ERREURS IDENTIFIEES: {$nb_erreurs}\n\n";
                                                fwrite($fichier_rapport, $donnees_rapport);

                                                if($nb_erreurs != 0) {
                                                    $donnees_rapport = "LE TRAITEMENT A ETE INTERROMPU.\n";
                                                    $message = $donnees_rapport."NOM DU FICHIER: <b>{$filename}</b><a target='_blank' href='".$lien.$report_filename."' download='".$fichier_rapport."'>Cliquez ici pour télécharger le fichier</a>";
                                                    fwrite($fichier_rapport, $donnees_rapport);
                                                    $json = array(
                                                        'success' => false,
                                                        'message' => $message
                                                    );
                                                }else {
                                                    $log_lecture = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',"FIN DE LECTURE",$utilisateur['user_id']);
                                                    if($log_lecture['success'] == true) {
                                                        $log_chargement = $LOGS->ajouter_chargement_fichier('IMP',$filename,'DEB',"DEBUT DE CHARGEMENT",$utilisateur['user_id']);
                                                        if($log_chargement['success'] == true) {
                                                            $nouveau_script = $SCRIPTS->ajouter('SCRIPT_CHARGEMENT_REFERENTIEL_PROFESSIONNEL_SANTE',date('Y-m-d H:i:s',time()),NULL,'ENC','CHARGEMENT DU REFERNTIEL PROFESSIONNELS DE SANTE NOMME: '.$filename,$utilisateur['user_id']);
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
                                                                        $edition = $PROFESSIONNELSANTE->edition(NULL,$tableau_entete['num_transmission'],date('Y-m-d',strtotime($donnee['date_debut_validite'])),$donnee['code'],$donnee['code_civilite'],$donnee['nom'],$donnee['prenoms'],$donnee['code_categorie_professionnelle'],NULL,NULL,NULL,NULL,$donnee['adresse_postale'],$donnee['ville'],NULL,$donnee['departement'],$donnee['telephone'],$donnee['fax'],$donnee['email'],NULL,NULL,NULL,$donnee['code_specialite'],NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,$utilisateur['user_id']);
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
                                                                        $historique = $LOGS->ajouter_historique_fichier('IMP',$tableau_entete['num_transmission'],$tableau_entete['nom_modele'],$tableau_entete['num_transmission'],date('Y-m-d',strtotime($tableau_entete['date_fichier'])),$tableau_entete['code_organisme_emet'],$tableau_entete['code_organisme_dest'],($creation+$maj),$creation,$maj,$filename,$utilisateur['user_id']);
                                                                        if($historique['success'] == true) {
                                                                            $mise_a_jour_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],$log_lecture['id_log'],$log_chargement['id_log'],date('Y-m-d H:i:s',time()),'FIN',"CHARGEMENT DU REFERNTIEL PROFESSIONNELS DE SANTE NOMME: {$filename} EFFECTUE AVEC SUCCES",$utilisateur['user_id']);
                                                                            if($mise_a_jour_script['success'] == true) {
                                                                                $donnees_rapport = date('d-m-Y H:i:s',time())." FIN DU TRAITEMENT DU FICHIER {$filename}\n\nFICHIER CHARGE AVEC SUCCES\nNOM DU FICHIER: {$filename}\nNOMBRE DE LIGNES: ".($succes+$echecs)."\nREUSSITE: {$succes}\nCREATION: {$creation}\nMISE A JOUR: {$maj}\nECHECS: {$echecs}";
                                                                                fwrite($fichier_rapport, $donnees_rapport);
                                                                                fclose($fichier_rapport);
                                                                                $message = "FICHIER CHARGE AVEC SUCCES.<br />NOM DU FICHIER: <b>{$filename}</b><br />NOMBRE DE LIGNES: <b>".($succes+$echecs)."</b><br />REUSSITE: <b>{$succes}</b><br />CREATION: <b>{$creation}</b><br />MISE A JOUR: <b>{$maj}</b><br />ECHECS: <b>{$echecs}</b><br /><a target='_blank' href='".$lien.$report_filename."' download='".$report_filename."'>Cliquez ici pour télécharger le fichier</a>";
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
                                            }else {
                                                $donnees_rapport = "Erreur nombre professionnels de santé: Le nombre d'occurences défini dans l'entête est différent du nombre effectif d'occurrences dans le fichier\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                            }
                                        }
                                        elseif($type_ref == 'LC'){
                                            $liste_lettres_cle = $xmlTransform->LISTE_LETTRES_CLE;
                                            $ligne = 0;
                                            $donnees = NULL;
                                            foreach ($liste_lettres_cle->LETTRE_CLE as $lettre_cle) {
                                                $donnees[$ligne] = array(
                                                    'code' => trim($lettre_cle->CODE),
                                                    'libelle' => strtoupper(conversionCaracteresSpeciaux(trim($lettre_cle->LIBELLE))),
                                                    'convention' => trim($lettre_cle->CONVENTION),
                                                    'prix_unitaire' => trim($lettre_cle->PRIX_UNITAIRE),
                                                    'date_debut' => trim($lettre_cle->DATE_DEBUT_VALIDITE),
                                                    'date_fin' => trim($lettre_cle->DATE_FIN_VALIDITE)
                                                );
                                                $ligne++;
                                            }
                                            $nb_donnees = count($donnees);
                                            if($nb_donnees == $tableau_entete['occurrences']) {
                                                foreach ($donnees as $donnee) {
                                                    $lectures = $LETTRESCLES->lecture_fichier_xml($donnee['code'],$donnee['libelle'],$donnee['convention'],$donnee['prix_unitaire'],$donnee['date_debut'],$donnee['date_fin']);
                                                    $nb_retours = count($lectures);
                                                    if($nb_retours!= 0)  {
                                                        foreach ($lectures as $lecture) {
                                                            if($lecture['message']) {
                                                                $donnees_rapport = "Erreur lettre clé {$donnee['code']}: {$lecture['message']}\n";
                                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                                $nb_erreurs++;
                                                            }
                                                        }
                                                    }
                                                }
                                                $donnees_rapport = "\n\n\n".date('d-m-Y H:i:s',time())." FIN DE LECTURE DU FICHIER {$filename}\n";
                                                fwrite($fichier_rapport, $donnees_rapport);
                                                $donnees_rapport = "NOMBRE D'ERREURS IDENTIFIEES: {$nb_erreurs}\n\n";
                                                fwrite($fichier_rapport, $donnees_rapport);

                                                if($nb_erreurs != 0) {
                                                    $donnees_rapport = "LE TRAITEMENT A ETE INTERROMPU.\n";
                                                    $message = $donnees_rapport."NOM DU FICHIER: <b>{$filename}</b><a target='_blank' href='".$lien.$report_filename."' download='".$fichier_rapport."'>Cliquez ici pour télécharger le fichier</a>";
                                                    fwrite($fichier_rapport, $donnees_rapport);
                                                    $json = array(
                                                        'success' => false,
                                                        'message' => $message
                                                    );
                                                }else {
                                                    $log_lecture = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',"FIN DE LECTURE",$utilisateur['user_id']);
                                                    if($log_lecture['success'] == true) {
                                                        $log_chargement = $LOGS->ajouter_chargement_fichier('IMP',$filename,'DEB',"DEBUT DE CHARGEMENT",$utilisateur['user_id']);
                                                        if($log_chargement['success'] == true) {
                                                            $nouveau_script = $SCRIPTS->ajouter('SCRIPT_CHARGEMENT_REFERENTIEL_LETTRES_CLES',date('Y-m-d H:i:s',time()),NULL,'ENC','CHARGEMENT DU REFERNTIEL LETTRES CLES NOMME: '.$filename,$utilisateur['user_id']);
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
                                                                        $edition = $LETTRESCLES->edition($tableau_entete['num_transmission'],date('Y-m-d',strtotime($tableau_entete['date_fichier'])),$donnee['code'],$donnee['libelle'],$donnee['convention'],$donnee['prix_unitaire'],date('Y-m-d',strtotime($tableau_entete['date_fichier'])),date('Y-m-d',strtotime($donnee['date_debut'])),$utilisateur['user_id']);
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
                                                                        $historique = $LOGS->ajouter_historique_fichier('IMP',$tableau_entete['num_transmission'],$tableau_entete['nom_modele'],$tableau_entete['num_transmission'],date('Y-m-d',strtotime($tableau_entete['date_fichier'])),$tableau_entete['code_organisme_emet'],$tableau_entete['code_organisme_dest'],($creation+$maj),$creation,$maj,$filename,$utilisateur['user_id']);
                                                                        if($historique['success'] == true) {
                                                                            $mise_a_jour_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],$log_lecture['id_log'],$log_chargement['id_log'],date('Y-m-d H:i:s',time()),'FIN',"CHARGEMENT DU REFERNTIEL LETTRES CLES NOMME: {$filename} EFFECTUE AVEC SUCCES",$utilisateur['user_id']);
                                                                            if($mise_a_jour_script['success'] == true) {
                                                                                $donnees_rapport = date('d-m-Y H:i:s',time())." FIN DU TRAITEMENT DU FICHIER {$filename}\n\nFICHIER CHARGE AVEC SUCCES\nNOM DU FICHIER: {$filename}\nNOMBRE DE LIGNES: ".($succes+$echecs)."\nREUSSITE: {$succes}\nCREATION: {$creation}\nMISE A JOUR: {$maj}\nECHECS: {$echecs}";
                                                                                fwrite($fichier_rapport, $donnees_rapport);
                                                                                fclose($fichier_rapport);
                                                                                $message = "FICHIER CHARGE AVEC SUCCES.<br />NOM DU FICHIER: <b>{$filename}</b><br />NOMBRE DE LIGNES: <b>".($succes+$echecs)."</b><br />REUSSITE: <b>{$succes}</b><br />CREATION: <b>{$creation}</b><br />MISE A JOUR: <b>{$maj}</b><br />ECHECS: <b>{$echecs}</b><br /><a target='_blank' href='".$lien.$report_filename."' download='".$report_filename."'>Cliquez ici pour télécharger le fichier</a>";
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
                                            }else {
                                                $donnees_rapport = "Erreur nombre lettres clés: Le nombre d'occurences défini dans l'entête est différent du nombre effectif d'occurrences dans le fichier\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                            }

                                        }
                                        elseif($type_ref == 'NGAMBCI'){
                                            $liste_actes_medicaux = $xmlTransform->LISTE_ACTES_MEDICAUX;
                                            $ligne = 0;
                                            $donnees = NULL;
                                            foreach ($liste_actes_medicaux->ACTE_MEDICAL as $acte_medical) {
                                                $donnees[$ligne] = array(
                                                    'code' => trim($acte_medical->CODE),
                                                    'titre' => strtoupper(conversionCaracteresSpeciaux(trim($acte_medical->TITRE))),
                                                    'chapitre' => strtoupper(conversionCaracteresSpeciaux(trim($acte_medical->CHAPITRE))),
                                                    'section' => strtoupper(conversionCaracteresSpeciaux(trim($acte_medical->SECTION))),
                                                    'article' => strtoupper(conversionCaracteresSpeciaux(trim($acte_medical->ARTICLE))),
                                                    'libelle' => strtoupper(conversionCaracteresSpeciaux(trim($acte_medical->LIBELLE))),
                                                    'code_lettre_cle_1' => trim($acte_medical->CODE_LETTRE_CLE_1),
                                                    'code_lettre_cle_2' => trim($acte_medical->CODE_LETTRE_CLE_2),
                                                    'coefficient_1' => trim($acte_medical->COEFFICIENT_1),
                                                    'coefficient_2' => trim($acte_medical->COEFFICIENT_2),
                                                    'code_correspondance' => trim($acte_medical->CODE_CORRESPONDANCE),
                                                    'panier_de_soin' => trim($acte_medical->PANIER_DE_SOIN),
                                                    'entente_prealable' => trim($acte_medical->ENTENTE_PREALABLE),
                                                    'date_effet' => trim($acte_medical->DATE_EFFET),
                                                    'date_fin' => trim($acte_medical->DATE_FIN)
                                                );
                                                $ligne++;
                                            }
                                            $nb_donnees = count($donnees);
                                            if($nb_donnees == $tableau_entete['occurrences']) {
                                                foreach ($donnees as $donnee) {
                                                    $lectures = $ACTESMEDICAUX->lecture_fichier_xml('NGAP',$donnee['code'],$donnee['titre'],$donnee['chapitre'],$donnee['section'],$donnee['article'],$donnee['libelle'],$donnee['code_lettre_cle_1'],NULL,$donnee['date_effet'],$donnee['date_fin']);
                                                    $nb_retours = count($lectures);
                                                    if($nb_retours!= 0)  {
                                                        foreach ($lectures as $lecture) {
                                                            if($lecture['message']) {
                                                                $donnees_rapport = "Erreur acte médical {$donnee['code']}: {$lecture['message']}\n";
                                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                                $nb_erreurs++;
                                                            }
                                                        }
                                                    }
                                                }
                                                $donnees_rapport = "\n\n\n".date('d-m-Y H:i:s',time())." FIN DE LECTURE DU FICHIER {$filename}\n";
                                                fwrite($fichier_rapport, $donnees_rapport);
                                                $donnees_rapport = "NOMBRE D'ERREURS IDENTIFIEES: {$nb_erreurs}\n\n";
                                                fwrite($fichier_rapport, $donnees_rapport);

                                                if($nb_erreurs != 0) {
                                                    $donnees_rapport = "LE TRAITEMENT A ETE INTERROMPU.\n";
                                                    $message = $donnees_rapport."NOM DU FICHIER: <b>{$filename}</b><a target='_blank' href='".$lien.$report_filename."' download='".$fichier_rapport."'>Cliquez ici pour télécharger le fichier</a>";
                                                    fwrite($fichier_rapport, $donnees_rapport);
                                                    $json = array(
                                                        'success' => false,
                                                        'message' => $message
                                                    );
                                                }else {
                                                    $log_lecture = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',"FIN DE LECTURE",$utilisateur['user_id']);
                                                    if($log_lecture['success'] == true) {
                                                        $log_chargement = $LOGS->ajouter_chargement_fichier('IMP',$filename,'DEB',"DEBUT DE CHARGEMENT",$utilisateur['user_id']);
                                                        if($log_chargement['success'] == true) {
                                                            $nouveau_script = $SCRIPTS->ajouter('SCRIPT_CHARGEMENT_REFERENTIEL_ACTES_MEDICAUX',date('Y-m-d H:i:s',time()),NULL,'ENC','CHARGEMENT DU REFERNTIEL ACTES MEDICAUX NOMME: '.$filename,$utilisateur['user_id']);
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
                                                                        $edition = $ACTESMEDICAUX->edition($tableau_entete['num_transmission'],date('Y-m-d',strtotime($donnee['date_effet'])),'NGAP',$donnee['code'],$donnee['libelle'],$donnee['titre'],$donnee['chapitre'],$donnee['section'],$donnee['article'],$donnee['code_lettre_cle_1'],$donnee['coefficient'],NULL,$utilisateur['user_id']);
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
                                                                        $historique = $LOGS->ajouter_historique_fichier('IMP',$tableau_entete['num_transmission'],$tableau_entete['nom_modele'],$tableau_entete['num_transmission'],date('Y-m-d',strtotime($tableau_entete['date_fichier'])),$tableau_entete['code_organisme_emet'],$tableau_entete['code_organisme_dest'],($creation+$maj),$creation,$maj,$filename,$utilisateur['user_id']);
                                                                        if($historique['success'] == true) {
                                                                            $mise_a_jour_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],$log_lecture['id_log'],$log_chargement['id_log'],date('Y-m-d H:i:s',time()),'FIN',"CHARGEMENT DU REFERNTIEL ACTES MEDICAUX NOMME: {$filename} EFFECTUE AVEC SUCCES",$utilisateur['user_id']);
                                                                            if($mise_a_jour_script['success'] == true) {
                                                                                $donnees_rapport = date('d-m-Y H:i:s',time())." FIN DU TRAITEMENT DU FICHIER {$filename}\n\nFICHIER CHARGE AVEC SUCCES\nNOM DU FICHIER: {$filename}\nNOMBRE DE LIGNES: ".($succes+$echecs)."\nREUSSITE: {$succes}\nCREATION: {$creation}\nMISE A JOUR: {$maj}\nECHECS: {$echecs}";
                                                                                fwrite($fichier_rapport, $donnees_rapport);
                                                                                fclose($fichier_rapport);
                                                                                $message = "FICHIER CHARGE AVEC SUCCES.<br />NOM DU FICHIER: <b>{$filename}</b><br />NOMBRE DE LIGNES: <b>".($succes+$echecs)."</b><br />REUSSITE: <b>{$succes}</b><br />CREATION: <b>{$creation}</b><br />MISE A JOUR: <b>{$maj}</b><br />ECHECS: <b>{$echecs}</b><br /><a target='_blank' href='".$lien.$report_filename."' download='".$report_filename."'>Cliquez ici pour télécharger le fichier</a>";
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
                                            }else {
                                                $donnees_rapport = "Erreur nombre lettres clés: Le nombre d'occurences défini dans l'entête est différent du nombre effectif d'occurrences dans le fichier\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                            }
                                        }
                                        elseif($type_ref == 'FH'){
                                            $liste_forfaits_hospitaliers = $xmlTransform->LISTE_FORFAITS_HOSPITALIERS;
                                            $ligne = 0;
                                            $donnees = NULL;
                                            foreach ($liste_forfaits_hospitaliers->FORFAIT_HOSPITALIER as $forfait_hospitalier) {
                                                $donnees[$ligne] = array(
                                                    'code' => trim($forfait_hospitalier->CODE),
                                                    'titre' => strtoupper(conversionCaracteresSpeciaux(trim($forfait_hospitalier->TITRE))),
                                                    'chapitre' => strtoupper(conversionCaracteresSpeciaux(trim($forfait_hospitalier->CHAPITRE))),
                                                    'section' => strtoupper(conversionCaracteresSpeciaux(trim($forfait_hospitalier->SECTION))),
                                                    'article' => strtoupper(conversionCaracteresSpeciaux(trim($forfait_hospitalier->ARTICLE))),
                                                    'libelle' => strtoupper(conversionCaracteresSpeciaux(trim($forfait_hospitalier->LIBELLE))),
                                                    'code_correspondance' => trim($forfait_hospitalier->CODE_CORRESPONDANCE),
                                                    'tarif' => trim($forfait_hospitalier->TARIF),
                                                    'panier_de_soin' => trim($forfait_hospitalier->PANIER_DE_SOIN),
                                                    'entente_prealable' => trim($forfait_hospitalier->ENTENTE_PREALABLE),
                                                    'date_effet' => trim($forfait_hospitalier->DATE_EFFET),
                                                    'date_fin' => trim($forfait_hospitalier->DATE_FIN)
                                                );
                                                $ligne++;
                                            }
                                            $nb_donnees = count($donnees);
                                            if($nb_donnees == $tableau_entete['occurrences']) {
                                                foreach ($donnees as $donnee) {
                                                    $lectures = $ACTESMEDICAUX->lecture_fichier_xml('FH',$donnee['code'],$donnee['titre'],$donnee['chapitre'],$donnee['section'],$donnee['article'],$donnee['libelle'],NULL,$donnee['tarif'],$donnee['date_effet'],$donnee['date_fin']);
                                                    $nb_retours = count($lectures);
                                                    if($nb_retours!= 0)  {
                                                        foreach ($lectures as $lecture) {
                                                            if($lecture['message']) {
                                                                $donnees_rapport = "Erreur acte médical {$donnee['code']}: {$lecture['message']}\n";
                                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                                $nb_erreurs++;
                                                            }
                                                        }
                                                    }
                                                }
                                                $donnees_rapport = "\n\n\n".date('d-m-Y H:i:s',time())." FIN DE LECTURE DU FICHIER {$filename}\n";
                                                fwrite($fichier_rapport, $donnees_rapport);
                                                $donnees_rapport = "NOMBRE D'ERREURS IDENTIFIEES: {$nb_erreurs}\n\n";
                                                fwrite($fichier_rapport, $donnees_rapport);

                                                if($nb_erreurs != 0) {
                                                    $donnees_rapport = "LE TRAITEMENT A ETE INTERROMPU.\n";
                                                    $message = $donnees_rapport."NOM DU FICHIER: <b>{$filename}</b><a target='_blank' href='".$lien.$report_filename."' download='".$fichier_rapport."'>Cliquez ici pour télécharger le fichier</a>";
                                                    fwrite($fichier_rapport, $donnees_rapport);
                                                    $json = array(
                                                        'success' => false,
                                                        'message' => $message
                                                    );
                                                }else {
                                                    $log_lecture = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',"FIN DE LECTURE",$utilisateur['user_id']);
                                                    if($log_lecture['success'] == true) {
                                                        $log_chargement = $LOGS->ajouter_chargement_fichier('IMP',$filename,'DEB',"DEBUT DE CHARGEMENT",$utilisateur['user_id']);
                                                        if($log_chargement['success'] == true) {
                                                            $nouveau_script = $SCRIPTS->ajouter('SCRIPT_CHARGEMENT_REFERENTIEL_FORFAITS_HOSPITALIERS',date('Y-m-d H:i:s',time()),NULL,'ENC','CHARGEMENT DU REFERNTIEL FORFAITS HOSPITALIERS NOMME: '.$filename,$utilisateur['user_id']);
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
                                                                        $edition = $ACTESMEDICAUX->edition($tableau_entete['num_transmission'],date('Y-m-d',strtotime($donnee['date_effet'])),'FH',$donnee['code'],$donnee['libelle'],NULL,NULL,NULL,NULL,NULL,NULL,$donnee['tarif'],$utilisateur['user_id']);
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
                                                                        $historique = $LOGS->ajouter_historique_fichier('IMP',$tableau_entete['num_transmission'],$tableau_entete['nom_modele'],$tableau_entete['num_transmission'],date('Y-m-d',strtotime($tableau_entete['date_fichier'])),$tableau_entete['code_organisme_emet'],$tableau_entete['code_organisme_dest'],($creation+$maj),$creation,$maj,$filename,$utilisateur['user_id']);
                                                                        if($historique['success'] == true) {
                                                                            $mise_a_jour_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],$log_lecture['id_log'],$log_chargement['id_log'],date('Y-m-d H:i:s',time()),'FIN',"CHARGEMENT DU REFERNTIEL FORFAIT HOSPITALIER NOMME: {$filename} EFFECTUE AVEC SUCCES",$utilisateur['user_id']);
                                                                            if($mise_a_jour_script['success'] == true) {
                                                                                $donnees_rapport = date('d-m-Y H:i:s',time())." FIN DU TRAITEMENT DU FICHIER {$filename}\n\nFICHIER CHARGE AVEC SUCCES\nNOM DU FICHIER: {$filename}\nNOMBRE DE LIGNES: ".($succes+$echecs)."\nREUSSITE: {$succes}\nCREATION: {$creation}\nMISE A JOUR: {$maj}\nECHECS: {$echecs}";
                                                                                fwrite($fichier_rapport, $donnees_rapport);
                                                                                fclose($fichier_rapport);
                                                                                $message = "FICHIER CHARGE AVEC SUCCES.<br />NOM DU FICHIER: <b>{$filename}</b><br />NOMBRE DE LIGNES: <b>".($succes+$echecs)."</b><br />REUSSITE: <b>{$succes}</b><br />CREATION: <b>{$creation}</b><br />MISE A JOUR: <b>{$maj}</b><br />ECHECS: <b>{$echecs}</b><br /><a target='_blank' href='".$lien.$report_filename."' download='".$report_filename."'>Cliquez ici pour télécharger le fichier</a>";
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
                                            }else {
                                                $donnees_rapport = "Erreur nombre lettres clés: Le nombre d'occurences défini dans l'entête est différent du nombre effectif d'occurrences dans le fichier\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                            }
                                        }
                                        elseif($type_ref == 'MED'){
                                            $liste_medicaments = $xmlTransform->LISTE_MEDICAMENTS;
                                            $ligne = 0;
                                            $donnees = NULL;
                                            foreach ($liste_medicaments->MEDICAMENT as $medicament) {
                                                $donnees[$ligne] = array(
                                                    'code' => trim($medicament->CODE_PREMIUM),
                                                    'code_ean13' => trim($medicament->CODE_EAN13),
                                                    'libelle' => strtoupper(conversionCaracteresSpeciaux(trim($medicament->LIBELLE))),
                                                    'presentation' => trim($medicament->PRESENTATION),
                                                    'laboratoire' => trim($medicament->LABORATOIRE),
                                                    'dci' => trim($medicament->DCI),
                                                    'forme_administration_dci' => trim($medicament->FORME_ADMINISTRATION_DCI),
                                                    'dosage' => trim($medicament->DOSAGE),
                                                    'unite' => trim($medicament->UNITE),
                                                    'famille_forme' => trim($medicament->FAMILLE_FORME),
                                                    'forme' => trim($medicament->FORME),
                                                    'classe_therapeutique' => trim($medicament->CLASSE_THERAPEUTIQUE),
                                                    'restriction' => trim($medicament->RESTRICTION),
                                                    'code_correspondance' => trim($medicament->CODE_CORRESPONDANCE),
                                                    'tarif' => trim($medicament->TARIF),
                                                    'panier_cmu' => trim($medicament->PANIER_CMU),
                                                    'date_debut' => trim($medicament->DATE_DEBUT_REMBOURSEMENT),
                                                    'date_fin' => trim($medicament->DATE_FIN_REMBOURSEMENT)
                                                );
                                                $ligne++;
                                            }
                                            $nb_donnees = count($donnees);
                                            if($nb_donnees == $tableau_entete['occurrences']) {
                                                foreach ($donnees as $donnee) {
                                                    $lectures = $MEDICAMENTS->lecture_fichier_xml($donnee['code'],$donnee['code_ean13'],$donnee['libelle'],$donnee['dosage'],$donnee['unite'],$donnee['tarif'],$donnee['date_debut'],$donnee['date_fin']);
                                                    $nb_retours = count($lectures);
                                                    if($nb_retours!= 0)  {
                                                        foreach ($lectures as $lecture) {
                                                            if($lecture['message']) {
                                                                $donnees_rapport = "Erreur medicament {$donnee['code']}: {$lecture['message']}\n";
                                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                                $nb_erreurs++;
                                                            }
                                                        }
                                                    }
                                                }
                                                $donnees_rapport = "\n\n\n".date('d-m-Y H:i:s',time())." FIN DE LECTURE DU FICHIER {$filename}\n";
                                                fwrite($fichier_rapport, $donnees_rapport);
                                                $donnees_rapport = "NOMBRE D'ERREURS IDENTIFIEES: {$nb_erreurs}\n\n";
                                                fwrite($fichier_rapport, $donnees_rapport);

                                                if($nb_erreurs != 0) {
                                                    $donnees_rapport = "LE TRAITEMENT A ETE INTERROMPU.\n";
                                                    $message = $donnees_rapport."NOM DU FICHIER: <b>{$filename}</b><a target='_blank' href='".$lien.$report_filename."' download='".$fichier_rapport."'>Cliquez ici pour télécharger le fichier</a>";
                                                    fwrite($fichier_rapport, $donnees_rapport);
                                                    $json = array(
                                                        'success' => false,
                                                        'message' => $message
                                                    );
                                                }else {
                                                    $log_lecture = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',"FIN DE LECTURE",$utilisateur['user_id']);
                                                    if($log_lecture['success'] == true) {
                                                        $log_chargement = $LOGS->ajouter_chargement_fichier('IMP',$filename,'DEB',"DEBUT DE CHARGEMENT",$utilisateur['user_id']);
                                                        if($log_chargement['success'] == true) {
                                                            $nouveau_script = $SCRIPTS->ajouter('SCRIPT_CHARGEMENT_REFERENTIEL_MEDIACAMENTS',date('Y-m-d H:i:s',time()),NULL,'ENC','CHARGEMENT DU REFERNTIEL MEDICAMENTS NOMME: '.$filename,$utilisateur['user_id']);
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
                                                                        $edition = $MEDICAMENTS->edition($tableau_entete['num_transmission'],$donnee['code'],$donnee['laboratoire'],$donnee['code_ean13'],$donnee['libelle'],$donnee['dosage'],$donnee['unite'],$donnee['forme'],NULL,NULL,$donnee['tarif'],NULL,NULL,NULL,NULL,NULL,date('Y-m-d',strtotime($donnee['date_debut'])),$utilisateur['user_id']);
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
                                                                        $historique = $LOGS->ajouter_historique_fichier('IMP',$tableau_entete['num_transmission'],$tableau_entete['nom_modele'],$tableau_entete['num_transmission'],date('Y-m-d',strtotime($tableau_entete['date_fichier'])),$tableau_entete['code_organisme_emet'],$tableau_entete['code_organisme_dest'],($creation+$maj),$creation,$maj,$filename,$utilisateur['user_id']);
                                                                        if($historique['success'] == true) {
                                                                            $mise_a_jour_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],$log_lecture['id_log'],$log_chargement['id_log'],date('Y-m-d H:i:s',time()),'FIN',"CHARGEMENT DU REFERNTIEL LETTRES CLES NOMME: {$filename} EFFECTUE AVEC SUCCES",$utilisateur['user_id']);
                                                                            if($mise_a_jour_script['success'] == true) {
                                                                                $donnees_rapport = date('d-m-Y H:i:s',time())." FIN DU TRAITEMENT DU FICHIER {$filename}\n\nFICHIER CHARGE AVEC SUCCES\nNOM DU FICHIER: {$filename}\nNOMBRE DE LIGNES: ".($succes+$echecs)."\nREUSSITE: {$succes}\nCREATION: {$creation}\nMISE A JOUR: {$maj}\nECHECS: {$echecs}";
                                                                                fwrite($fichier_rapport, $donnees_rapport);
                                                                                fclose($fichier_rapport);
                                                                                $message = "FICHIER CHARGE AVEC SUCCES.<br />NOM DU FICHIER: <b>{$filename}</b><br />NOMBRE DE LIGNES: <b>".($succes+$echecs)."</b><br />REUSSITE: <b>{$succes}</b><br />CREATION: <b>{$creation}</b><br />MISE A JOUR: <b>{$maj}</b><br />ECHECS: <b>{$echecs}</b><br /><a target='_blank' href='".$lien.$report_filename."' download='".$report_filename."'>Cliquez ici pour télécharger le fichier</a>";
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
                                            }else {
                                                $donnees_rapport = "Erreur nombre médicaments: Le nombre d'occurences défini dans l'entête est différent du nombre effectif d'occurrences dans le fichier\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                            }
                                        }
                                        elseif($type_ref == 'PATH'){
                                            $liste_affections = $xmlTransform->LISTE_AFFECTIONS;
                                            $ligne = 0;
                                            $donnees = NULL;
                                            foreach ($liste_affections->AFFECTION as $affection) {
                                                $donnees[$ligne] = array(
                                                    'code' => trim($affection->CODE),
                                                    'libelle' => trim($affection->LIBELLE),
                                                    'code_chapitre' => trim($affection->CODE_CHAPITRE),
                                                    'code_sous_chapitre' => trim($affection->CODE_SOUS_CHAPITRE),
                                                    'panier_soins' => trim($affection->PANIER_SOINS),
                                                    'date_debut' => trim($affection->DATE_DEBUT_VALIDITE),
                                                    'date_fin' => trim($affection->DATE_FIN_VALIDITE)
                                                );
                                                $ligne++;
                                            }
                                            $nb_donnees = count($donnees);
                                            if($nb_donnees == $tableau_entete['occurrences']) {
                                                foreach ($donnees as $donnee) {
                                                    $lectures = $PATHOLOGIES->lecture_fichier_xml($donnee['code'],$donnee['libelle'],$donnee['date_debut'],$donnee['date_fin']);
                                                    $nb_retours = count($lectures);
                                                    if($nb_retours!= 0)  {
                                                        foreach ($lectures as $lecture) {
                                                            if($lecture['message']) {
                                                                $donnees_rapport = "Erreur lettre clé {$donnee['code']}: {$lecture['message']}\n";
                                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                                $nb_erreurs++;
                                                            }
                                                        }
                                                    }
                                                }
                                                $donnees_rapport = "\n\n\n".date('d-m-Y H:i:s',time())." FIN DE LECTURE DU FICHIER {$filename}\n";
                                                fwrite($fichier_rapport, $donnees_rapport);
                                                $donnees_rapport = "NOMBRE D'ERREURS IDENTIFIEES: {$nb_erreurs}\n\n";
                                                fwrite($fichier_rapport, $donnees_rapport);

                                                if($nb_erreurs != 0) {
                                                    $donnees_rapport = "LE TRAITEMENT A ETE INTERROMPU.\n";
                                                    $message = $donnees_rapport."NOM DU FICHIER: <b>{$filename}</b><a target='_blank' href='".$lien.$report_filename."' download='".$fichier_rapport."'>Cliquez ici pour télécharger le fichier</a>";
                                                    fwrite($fichier_rapport, $donnees_rapport);
                                                    $json = array(
                                                        'success' => false,
                                                        'message' => $message
                                                    );
                                                }else {
                                                    $log_lecture = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',"FIN DE LECTURE",$utilisateur['user_id']);
                                                    if($log_lecture['success'] == true) {
                                                        $log_chargement = $LOGS->ajouter_chargement_fichier('IMP',$filename,'DEB',"DEBUT DE CHARGEMENT",$utilisateur['user_id']);
                                                        if($log_chargement['success'] == true) {
                                                            $nouveau_script = $SCRIPTS->ajouter('SCRIPT_CHARGEMENT_REFERENTIEL_AFFACTIONS',date('Y-m-d H:i:s',time()),NULL,'ENC','CHARGEMENT DU REFERNTIEL AFFECTIONS NOMME: '.$filename,$utilisateur['user_id']);
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
                                                                        $edition = $PATHOLOGIES->edition($tableau_entete['num_transmission'],$donnee['code'],$donnee['code_chapitre'],$donnee['code_sous_chapitre'],$donnee['libelle'],$donnee['panier_soins'],date('Y-m-d',strtotime($donnee['date_debut'])),$utilisateur['user_id']);
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
                                                                        $historique = $LOGS->ajouter_historique_fichier('IMP',$tableau_entete['num_transmission'],$tableau_entete['nom_modele'],$tableau_entete['num_transmission'],date('Y-m-d',strtotime($tableau_entete['date_fichier'])),$tableau_entete['code_organisme_emet'],$tableau_entete['code_organisme_dest'],($creation+$maj),$creation,$maj,$filename,$utilisateur['user_id']);
                                                                        if($historique['success'] == true) {
                                                                            $mise_a_jour_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],$log_lecture['id_log'],$log_chargement['id_log'],date('Y-m-d H:i:s',time()),'FIN',"CHARGEMENT DU REFERNTIEL AFFECTIONS NOMME: {$filename} EFFECTUE AVEC SUCCES",$utilisateur['user_id']);
                                                                            if($mise_a_jour_script['success'] == true) {
                                                                                $donnees_rapport = date('d-m-Y H:i:s',time())." FIN DU TRAITEMENT DU FICHIER {$filename}\n\nFICHIER CHARGE AVEC SUCCES\nNOM DU FICHIER: {$filename}\nNOMBRE DE LIGNES: ".($succes+$echecs)."\nREUSSITE: {$succes}\nCREATION: {$creation}\nMISE A JOUR: {$maj}\nECHECS: {$echecs}";
                                                                                fwrite($fichier_rapport, $donnees_rapport);
                                                                                fclose($fichier_rapport);
                                                                                $message = "FICHIER CHARGE AVEC SUCCES.<br />NOM DU FICHIER: <b>{$filename}</b><br />NOMBRE DE LIGNES: <b>".($succes+$echecs)."</b><br />REUSSITE: <b>{$succes}</b><br />CREATION: <b>{$creation}</b><br />MISE A JOUR: <b>{$maj}</b><br />ECHECS: <b>{$echecs}</b><br /><a target='_blank' href='".$lien.$report_filename."' download='".$report_filename."'>Cliquez ici pour télécharger le fichier</a>";
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
                                            }else {
                                                $donnees_rapport = "Erreur nombre affections: Le nombre d'occurences défini dans l'entête est différent du nombre effectif d'occurrences dans le fichier\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                            }
                                        }
                                        elseif($type_ref == 'REJ'){
                                            $liste_motifs_rejets = $xmlTransform->LISTE_MOTIFS_REJETS;
                                            $ligne = 0;
                                            $donnees = NULL;
                                            foreach ($liste_motifs_rejets->MOTIF_REJET as $motif_rejet) {
                                                $donnees[$ligne] = array(
                                                    'code' => trim($motif_rejet->CODE),
                                                    'libelle' => strtoupper(conversionCaracteresSpeciaux(trim($motif_rejet->LIBELLE))),
                                                    'date_debut' => trim($motif_rejet->DATE_DEBUT_VALIDITE),
                                                    'date_fin' => trim($motif_rejet->DATE_FIN_VALIDITE)
                                                );
                                                $ligne++;
                                            }
                                            $nb_donnees = count($donnees);
                                            if($nb_donnees == $tableau_entete['occurrences']) {
                                                foreach ($donnees as $donnee) {
                                                    $lectures = $REJETS->lecture_fichier_xml($donnee['code'],$donnee['libelle'],$donnee['date_debut'],$donnee['date_fin']);
                                                    $nb_retours = count($lectures);
                                                    if($nb_retours!= 0)  {
                                                        foreach ($lectures as $lecture) {
                                                            if($lecture['message']) {
                                                                $donnees_rapport = "Erreur lettre clé {$donnee['code']}: {$lecture['message']}\n";
                                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                                $nb_erreurs++;
                                                            }
                                                        }
                                                    }
                                                }
                                                $donnees_rapport = "\n\n\n".date('d-m-Y H:i:s',time())." FIN DE LECTURE DU FICHIER {$filename}\n";
                                                fwrite($fichier_rapport, $donnees_rapport);
                                                $donnees_rapport = "NOMBRE D'ERREURS IDENTIFIEES: {$nb_erreurs}\n\n";
                                                fwrite($fichier_rapport, $donnees_rapport);

                                                if($nb_erreurs != 0) {
                                                    $donnees_rapport = "LE TRAITEMENT A ETE INTERROMPU.\n";
                                                    $message = $donnees_rapport."NOM DU FICHIER: <b>{$filename}</b><a target='_blank' href='".$lien.$report_filename."' download='".$fichier_rapport."'>Cliquez ici pour télécharger le fichier</a>";
                                                    fwrite($fichier_rapport, $donnees_rapport);
                                                    $json = array(
                                                        'success' => false,
                                                        'message' => $message
                                                    );
                                                }else {
                                                    $log_lecture = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',"FIN DE LECTURE",$utilisateur['user_id']);
                                                    if($log_lecture['success'] == true) {
                                                        $log_chargement = $LOGS->ajouter_chargement_fichier('IMP',$filename,'DEB',"DEBUT DE CHARGEMENT",$utilisateur['user_id']);
                                                        if($log_chargement['success'] == true) {
                                                            $nouveau_script = $SCRIPTS->ajouter('SCRIPT_CHARGEMENT_REFERENTIEL_MOTIFS_REJETS',date('Y-m-d H:i:s',time()),NULL,'ENC','CHARGEMENT DU REFERNTIEL MOTIFS REJETS NOMME: '.$filename,$utilisateur['user_id']);
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
                                                                        $edition = $REJETS->edition($tableau_entete['num_transmission'],$donnee['code'],$donnee['libelle'],date('Y-m-d',strtotime($donnee['date_debut'])),$utilisateur['user_id']);
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
                                                                        $historique = $LOGS->ajouter_historique_fichier('IMP',$tableau_entete['num_transmission'],$tableau_entete['nom_modele'],$tableau_entete['num_transmission'],date('Y-m-d',strtotime($tableau_entete['date_fichier'])),$tableau_entete['code_organisme_emet'],$tableau_entete['code_organisme_dest'],($creation+$maj),$creation,$maj,$filename,$utilisateur['user_id']);
                                                                        if($historique['success'] == true) {
                                                                            $mise_a_jour_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],$log_lecture['id_log'],$log_chargement['id_log'],date('Y-m-d H:i:s',time()),'FIN',"CHARGEMENT DU REFERNTIEL MOTIFS REJETS NOMME: {$filename} EFFECTUE AVEC SUCCES",$utilisateur['user_id']);
                                                                            if($mise_a_jour_script['success'] == true) {
                                                                                $donnees_rapport = date('d-m-Y H:i:s',time())." FIN DU TRAITEMENT DU FICHIER {$filename}\n\nFICHIER CHARGE AVEC SUCCES\nNOM DU FICHIER: {$filename}\nNOMBRE DE LIGNES: ".($succes+$echecs)."\nREUSSITE: {$succes}\nCREATION: {$creation}\nMISE A JOUR: {$maj}\nECHECS: {$echecs}";
                                                                                fwrite($fichier_rapport, $donnees_rapport);
                                                                                fclose($fichier_rapport);
                                                                                $message = "FICHIER CHARGE AVEC SUCCES.<br />NOM DU FICHIER: <b>{$filename}</b><br />NOMBRE DE LIGNES: <b>".($succes+$echecs)."</b><br />REUSSITE: <b>{$succes}</b><br />CREATION: <b>{$creation}</b><br />MISE A JOUR: <b>{$maj}</b><br />ECHECS: <b>{$echecs}</b><br /><a target='_blank' href='".$lien.$report_filename."' download='".$report_filename."'>Cliquez ici pour télécharger le fichier</a>";
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
                                            }else {
                                                $donnees_rapport = "Erreur nombre affections: Le nombre d'occurences défini dans l'entête est différent du nombre effectif d'occurrences dans le fichier\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                            }
                                        }
                                        else {
                                            $message = 'Le type de référentiel <b>.'.$type_ref.'</b> n\'est pas reconnu pour ce type de fichier par le système.';
                                            $json = array(
                                                'success' => false,
                                                'message' => $message
                                            );
                                        }
                                    }else {
                                        foreach ($lecture_entetes as $lecture_entete) {
                                            if($lecture_entete['message']) {
                                                $donnees_rapport = "Erreur entete: {$lecture_entete['message']}\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                $nb_erreurs++;
                                            }
                                        }
                                        $donnees_rapport = "\n\n\n".date('d-m-Y H:i:s',time())." FIN DE LECTURE DU FICHIER {$filename}\n";
                                        fwrite($fichier_rapport, $donnees_rapport);
                                        $donnees_rapport = "NOMBRE D'ERREURS IDENTIFIEES: {$nb_erreurs}\n\n";
                                        fwrite($fichier_rapport, $donnees_rapport);

                                        $donnees_rapport = "LE TRAITEMENT A ETE INTERROMPU.\n";
                                        $message = $donnees_rapport."NOM DU FICHIER: <b>{$filename}</b><a target='_blank' href='".$lien.$report_filename."' download='".$fichier_rapport."'>Cliquez ici pour télécharger le fichier</a>";
                                        fwrite($fichier_rapport, $donnees_rapport);
                                        $json = array(
                                            'success' => false,
                                            'message' => $message
                                        );
                                    }
                                }else {
                                    $message = "Erreur dans l'entête du fichier.";
                                    $json = array(
                                        'success' => false,
                                        'message' => $message
                                    );
                                }
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