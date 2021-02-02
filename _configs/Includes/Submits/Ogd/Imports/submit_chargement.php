<?php

require_once '../../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(!empty($utilisateur['user_id'])){
        $type_ref = htmlentities(trim($_POST['type_ref_input']));
        if(!empty($type_ref)) {
            $path = DIR.'IMPORTS/CHARGEMENTS/'.$type_ref.'/';
            $path_export = DIR.'EXPORTS/RAPPORTS_INTEGRATION/CHARGEMENTS/';
            $lien_export = URL.'EXPORTS/RAPPORTS_INTEGRATION/CHARGEMENTS/';
            $path_temp = DIR.'IMPORTS/CHARGEMENTS/'.$type_ref.'/tmp/';
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
                    require_once '../../../../Classes/FACTURES.php';
                    require_once '../../../../Classes/ASSURES.php';
                    require_once '../../../../Classes/SCRIPTS.php';
                    require_once '../../../../Classes/REJETS.php';
                    require_once '../../../../Classes/LOGS.php';

                    $PROFESSIONNELSANTE = new PROFESSIONNELSANTE();
                    $ETABLISSEMENTSANTE = new ETABLISSEMENTSANTE();
                    $ACTESMEDICAUX = new ACTESMEDICAUX();
                    $MEDICAMENTS = new MEDICAMENTS();
                    $PATHOLOGIES = new PATHOLOGIES();
                    $LETTRESCLES = new LETTRESCLES();
                    $FACTURES = new FACTURES();
                    $ASSURES = new ASSURES();
                    $SCRIPTS = new SCRIPTS();
                    $REJETS = new REJETS();
                    $LOGS = new LOGS();

                    if(in_array(strtolower($extension),$valid_extensions)) {
                        $log_lecture = $LOGS->ajouter_chargement_fichier('IMP',$filename,'DEB',"DEBUT DE LECTURE",$utilisateur['user_id']);
                        if($log_lecture['success'] == true) {
                            if (strtolower($extension) == 'xml') {
                                $xml = simplexml_load_file($filetemp);
                                $chaineXml = $xml->asXML();
                                $xmlTransform = simplexml_load_string($chaineXml);

                                if($type_ref == 'SITFAMILLE') {
                                    $tableau_entete = array(
                                        'nom_modele' => trim($xmlTransform->NOM_MODELE),
                                        'version' => trim($xmlTransform->VERSION),
                                        'code_systeme_gestion' => trim($xmlTransform->CODE_SYSTEME_GESTION),
                                        'code_organisme_emet' => trim($xmlTransform->CODE_ORGANISME_EMET),
                                        'nom_organisme_emet' => trim($xmlTransform->NOM_ORGANISME_EMET),
                                        'code_organisme_dest' => trim($xmlTransform->CODE_ORGANISME_DEST),
                                        'nom_organisme_dest' => trim($xmlTransform->NOM_ORGANISME_DEST),
                                        'num_transmission' => trim($xmlTransform->NUM_TRANSMISSION),
                                        'date_transmission' => trim($xmlTransform->DATE_TRANSMISSION),
                                        'prefixe_flux' => trim($xmlTransform->PREFIXE_FLUX),
                                        'occurrences_famille' => trim($xmlTransform->OCCURRENCES_FAMILLE)
                                    );

                                    $path = $path_export.$type_ref.'/'.date('d_m_Y',time()).'/';
                                    $lien = $lien_export.$type_ref.'/'.date('d_m_Y',time()).'/';
                                    if(!file_exists($path)){
                                        mkdir($path,0777,true);
                                    }
                                    $report_filename = "RAPPORT_INTEGRATION_".str_replace('.txt','',str_replace('.xml','',str_replace('.XML','',$filename)))."_".date('dmYHis').".txt";

                                    $nb_tableau_entete = count($tableau_entete);
                                    if($nb_tableau_entete != 0) {


                                        $fichier_rapport = fopen($path.$report_filename, "w") or die("Unable to open file!");
                                        $donnees_rapport = date('d-m-Y H:i:s',time())." DEBUT DE LECTURE DU FICHIER {$filename}\n\n";
                                        fwrite($fichier_rapport, $donnees_rapport);

                                        $lecture_entetes = $SCRIPTS->lecture_entete_fichier_xml($tableau_entete['nom_modele'],$tableau_entete['version'],$tableau_entete['code_systeme_gestion'],$tableau_entete['code_organisme_emet'],$tableau_entete['nom_organisme_emet'],$tableau_entete['code_organisme_dest'],$tableau_entete['nom_organisme_dest'],$tableau_entete['num_transmission'],$tableau_entete['date_transmission'],$tableau_entete['occurrences_famille'],$filename);
                                        $nb_retours_lecture_entete = (count($lecture_entetes) - 1);

                                        $donnees_familles = null;
                                        $tableau_personnes = null;
                                        $nb_erreurs = 0;
                                        if($nb_retours_lecture_entete == 0) {
                                            $list_familles = $xmlTransform->LIST_FAMILLES;
                                            $ligne_famille = 0;
                                            $ligne_personne = 0;
                                            $ligne_coordonnee = 0;
                                            foreach ($list_familles->FAMILLE as $famille) {
                                                $donnees_familles[$ligne_famille] = array(
                                                    'type_mouvement' => trim($famille->TYPE_MOUVEMENT),
                                                    'date_situation' => trim($famille->DATE_SITUATION),
                                                    'occurrences_personne' => trim($famille->OCCURRENCES_PERSONNE)
                                                );
                                                $list_personnes = $famille->LIST_PERSONNES;

                                                for ($ligne_personne = 0; $ligne_personne < $donnees_familles[$ligne_famille]['occurrences_personne']; $ligne_personne++) {
                                                    $personne = $list_personnes->PERSONNE[$ligne_personne];
                                                    $infos_naissance = $personne->INFOS_NAISSANCE;
                                                    $adresse = $personne->ADRESSE;
                                                    $donnees_personnes[] = array(
                                                        'type_mouvement' => trim($famille->TYPE_MOUVEMENT),
                                                        'date_situation' => trim($famille->DATE_SITUATION),
                                                        'identifiant_personne' => trim($personne->IDENTIFIANT_PERSONNE),
                                                        'num_secu' => trim($personne->NUM_SECU),
                                                        'cle_secu' => trim($personne->CLE_SECU),
                                                        'code_civilite' => trim($personne->CODE_CIVILITE),
                                                        'nom' => trim($personne->NOM),
                                                        'nom_patronymique' => trim($personne->NOM_PATRONYMIQUE),
                                                        'prenom' => trim($personne->PRENOM),
                                                        'code_sexe' => trim($personne->CODE_SEXE),
                                                        'code_qualite' => trim($personne->CODE_QUALITE),
                                                        'date_naissance' => trim($personne->DATE_NAISSANCE),
                                                        'rang_gemellaire' => trim($personne->RANG_GEMELLAIRE),
                                                        'code_nationalite' => trim($personne->CODE_NATIONALITE),
                                                        'code_situation_familiale' => trim($personne->CODE_SITUATION_FAMILIALE),
                                                        'code_cat_socio_professionnelle' => trim($personne->CODE_CAT_SOCIO_PROFESSIONNELLE),
                                                        'code_grand_regime' => trim($personne->CODE_GRAND_REGIME),
                                                        'code_caisse' => trim($personne->CODE_CAISSE),
                                                        'code_centre_payeur' => trim($personne->CODE_CENTRE_PAYEUR),
                                                        'date_premiere_immat' => trim($personne->DATE_PREMIERE_IMMAT),
                                                        'code_cpl_grand_regime' => trim($personne->CODE_CPL_GRAND_REGIME),
                                                        'date_entree_organisme' => trim($personne->DATE_ENTREE_ORGANISME),
                                                        'date_anciennete_organisme' => trim($personne->DATE_ANCIENNETE_ORGANISME),
                                                        'code_langue' => trim($personne->CODE_LANGUE),
                                                        'code_profession' => trim($personne->CODE_PROFESSION),
                                                        'code_executant_referent' => trim($personne->CODE_EXECUTANT_REFERENT),
                                                        'date_sortie_organisme' => trim($personne->DATE_SORTIE_ORGANISME),
                                                        'code_motif_sortie_organisme' => trim($personne->CODE_MOTIF_SORTIE_ORGANISME),
                                                        'date_deces' => trim($personne->DATE_DECES),
                                                        'code_statut_personne' => trim($personne->CODE_STATUT_PERSONNE),
                                                        'code_agence' => trim($personne->CODE_AGENCE),
                                                        'code_commercial' => trim($personne->CODE_COMMERCIAL),
                                                        'nom_usage' => trim($personne->NOM_USAGE),
                                                        'occurrences_collectivite' => trim($personne->OCCURRENCES_COLLECTIVITE),
                                                        'occurrences_coordonnee' => trim($personne->OCCURRENCES_COORDONNEE),
                                                        'occurrences_identifiant' => trim($personne->OCCURRENCES_IDENTIFIANT),
                                                        'occurrences_lien_personne' => trim($personne->OCCURRENCES_LIEN_PERSONNE),
                                                        'occurrences_contrat' => trim($personne->OCCURRENCES_CONTRAT),
                                                        'occurrences_rattachement' => trim($personne->OCCURRENCES_RATTACHEMENT),
                                                        'code_pays_naissance' => trim($infos_naissance->CODE_PAYS),
                                                        'lieu_naissance' => trim($infos_naissance->LIEU_NAISSANCE),
                                                        'code_secteur' => trim($infos_naissance->CODE_SECTEUR),
                                                        'code_type_adresse' => trim($adresse->CODE_TYPE_ADRESSE),
                                                        'code_pays_adresse' => trim($adresse->CODE_PAYS),
                                                        'adresse_normee_o_n' => trim($adresse->ADRESSE_NORMEE_O_N),
                                                        'auxiliaire_adresse_1' => trim($adresse->AUXILIAIRE_ADRESSE_1),
                                                        'auxiliaire_adresse_2' => trim($adresse->AUXILIAIRE_ADRESSE_2),
                                                        'num_voie' => trim($adresse->NUM_VOIE),
                                                        'cplt_num_voie' => trim($adresse->CPLT_NUM_VOIE),
                                                        'lib_voie' => trim($adresse->LIB_VOIE),
                                                        'nom_acheminement' => trim($adresse->NOM_ACHEMINEMENT),
                                                        'nom_lieu_dit' => trim($adresse->NOM_LIEU_DIT),
                                                        'lieu_dit_o_n' => trim($adresse->LIEU_DIT_O_N),
                                                        'code_postal' => trim($adresse->CODE_POSTAL),
                                                        'ref_adresse' => trim($adresse->REF_ADRESSE)
                                                    );



                                                    $list_collectivites = $personne->LIST_COLLECTIVITES;
                                                    $collectivite = $list_collectivites->COLLECTIVITE;
                                                    $donnees_collectivites[] = array(
                                                        'num_secu' => trim($personne->NUM_SECU),
                                                        'code_collectivite_employeur' => trim($collectivite->CODE_COLLECTIVITE_EMPLOYEUR),
                                                        'num_siret_employeur' => trim($collectivite->NUM_SIRET_EMPLOYEUR),
                                                        'code_college' => trim($collectivite->CODE_COLLEGE),
                                                        'matricule_salarie' => trim($collectivite->MATRICULE_SALARIE),
                                                        'service_collectivite' => trim($collectivite->SERVICE_COLLECTIVITE),
                                                        'code_fonction' => trim($collectivite->CODE_FONCTION)
                                                    );

                                                    $list_coordonnees = $personne->LIST_COORDONNEES;
                                                    for ($ligne_coordonnee = 0; $ligne_coordonnee < 10; $ligne_coordonnee++) {
                                                        $coordonnee = $list_coordonnees->COORDONNEE[$ligne_coordonnee];
                                                        if(trim($coordonnee->CODE_TYPE_COORDONNEE)) {
                                                            $donnees_coordonnees[] = array(
                                                                'num_secu' => trim($personne->NUM_SECU),
                                                                'code_type_coordonnee' => trim($coordonnee->CODE_TYPE_COORDONNEE),
                                                                'valeur_coordonnee' => trim($coordonnee->VALEUR_COORDONNEE),
                                                                'date_debut_coordonnee' => trim($coordonnee->DATE_DEBUT_COORDONNEE),
                                                                'date_fin_coordonnee' => trim($coordonnee->DATE_FIN_COORDONNEE)
                                                            );
                                                        }
                                                    }

                                                    $list_identifiants = $personne->LIST_IDENTIFIANTS;
                                                    for ($ligne_identifiant = 0; $ligne_identifiant < 10; $ligne_identifiant++) {
                                                        $identifiant = $list_identifiants->IDENTIFIANT[$ligne_identifiant];
                                                        if(trim($identifiant->CODE_TYPE_IDENTIFIANT)) {
                                                            $donnees_identifiants[] = array(
                                                                'num_secu' => trim($personne->NUM_SECU),
                                                                'code_type_identifiant' => trim($identifiant->CODE_TYPE_IDENTIFIANT),
                                                                'num_identifiant' => trim($identifiant->NUM_IDENTIFIANT),
                                                                'date_debut_identifiant' => trim($identifiant->DATE_DEBUT_IDENTIFIANT),
                                                                'date_fin_identifiant' => trim($identifiant->DATE_FIN_IDENTIFIANT)
                                                            );
                                                        }
                                                    }

                                                    $list_contrats = $personne->LIST_CONTRATS;
                                                    for ($ligne_contrat = 0; $ligne_contrat < 10; $ligne_contrat++) {
                                                        $contrat = $list_contrats->CONTRAT[$ligne_contrat];
                                                        if(trim($contrat->NUM_CONTRAT_FAMILLE)) {
                                                            $donnees_contrats[] = array(
                                                                'num_secu' => trim($personne->NUM_SECU),
                                                                'num_contrat_famille' => trim($contrat->NUM_CONTRAT_FAMILLE),
                                                                'code_produit' => trim($contrat->CODE_PRODUIT),
                                                                'code_collectivite' => trim($contrat->CODE_COLLECTIVITE),
                                                                'num_siret' => trim($contrat->NUM_SIRET),
                                                                'date_debut_contrat' => trim($contrat->DATE_DEBUT_CONTRAT),
                                                                'date_fin_contrat' => trim($contrat->DATE_FIN_CONTRAT)
                                                            );
                                                        }
                                                    }

                                                    $list_rattachements = $personne->LIST_RATTACHEMENTS;
                                                    for ($ligne_rattachement = 0; $ligne_rattachement < 10; $ligne_rattachement++) {
                                                        $rattachement = $list_rattachements->RATTACHEMENT[$ligne_rattachement];
                                                        if(trim($rattachement->NUM_CONTRAT_FAMILLE)) {
                                                            $donnees_rattachements[] = array(
                                                                'num_secu' => trim($personne->NUM_SECU),
                                                                'num_contrat_famille' => trim($rattachement->NUM_CONTRAT_FAMILLE),
                                                                'identifiant_contractant' => trim($rattachement->IDENTIFIANT_CONTRACTANT),
                                                                'date_debut_rattachement' => trim($rattachement->DATE_DEBUT_RATTACHEMENT),
                                                                'date_fin_rattachement' => trim($rattachement->DATE_FIN_RATTACHEMENT),
                                                                'occurrences_droit' => trim($rattachement->OCCURRENCES_DROIT)
                                                            );
                                                        }

                                                        $list_droits = $rattachement->LIST_DROITS;
                                                        for ($ligne_droit = 0; $ligne_droit < 10; $ligne_droit++) {
                                                            $droits = $list_droits->DROITS[$ligne_droit];
                                                            if(trim($droits->CODE_GROUPE_ACTE)) {
                                                                $tableau_droit[] = array(
                                                                    'num_secu' => trim($personne->NUM_SECU),
                                                                    'code_groupe_acte' => trim($droits->CODE_GROUPE_ACTE),
                                                                    'date_debut_droit' => trim($droits->DATE_DEBUT_DROIT),
                                                                    'date_fin_droit' => trim($droits->DATE_FIN_DROIT)
                                                                );
                                                            }
                                                        }
                                                    }

                                                }
                                                $ligne_famille++;
                                            }
                                            $nb_familles = count($donnees_familles);
                                            if($nb_familles == $tableau_entete['occurrences_famille']) {
                                                $ligne_famille = 0;
                                                $occurrences_personne = 0;
                                                $occurrences_coordonnee = 0;
                                                foreach ($donnees_familles as $donnee_famille) {
                                                    $occurrences_personne = $occurrences_personne + $donnee_famille['occurrences_personne'];
                                                    $lectures_familles = $ASSURES->lecture_familles($donnee_famille['type_mouvement'],$donnee_famille['date_situation'],$donnee_famille['occurrences_personne']);
                                                    $nb_retours_familles = count($lectures_familles);
                                                    if($nb_retours_familles!= 0) {
                                                        foreach ($lectures_familles as $lecture_famille) {
                                                            if($lecture_famille['message']) {
                                                                $donnees_rapport = "Erreur famille n° {$ligne_famille}: {$lecture_famille['message']}\n";
                                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                                $nb_erreurs++;
                                                            }
                                                        }
                                                    }
                                                    $ligne_famille++;
                                                }
                                            }
                                            else {
                                                $nb_erreurs++;
                                                $donnees_rapport = "Erreur nombre familles: Le nombre d'occurences des famille défini dans l'entête est différent de l'effectif familial dans le fichier\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                            }

                                            $nb_personnes = count($donnees_personnes);
                                            if($nb_personnes == $occurrences_personne) {
                                                foreach ($donnees_personnes as $donnee_personne) {

                                                    $occurrences_coordonnee = $occurrences_coordonnee + $donnee_personne['occurrences_coordonnee'];
                                                    $occurrences_identifiant = $occurrences_identifiant + $donnee_personne['occurrences_identifiant'];
                                                    $occurrences_contrat = $occurrences_contrat + $donnee_personne['occurrences_contrat'];
                                                    $occurrences_rattachement = $occurrences_rattachement + $donnee_personne['occurrences_rattachement'];

                                                    $lectures_personnes = $ASSURES->lecture_personnes($donnee_personne['identifiant_personne'], $donnee_personne['num_secu'], $donnee_personne['cle_secu'], $donnee_personne['code_civilite'], $donnee_personne['nom'], $donnee_personne['nom_patronymique'], $donnee_personne['prenom'], $donnee_personne['code_sexe'], $donnee_personne['code_qualite'], $donnee_personne['date_naissance'], $donnee_personne['rang_gemellaire'], $donnee_personne['code_nationalite'], $donnee_personne['code_situation_familiale'], $donnee_personne['code_cat_socio_professionnelle'], $donnee_personne['code_grand_regime'], $donnee_personne['code_caisse'], $donnee_personne['code_centre_payeur'], $donnee_personne['date_premiere_immat'], $donnee_personne['code_cpl_grand_regime'], $donnee_personne['date_entree_organisme'], $donnee_personne['date_anciennete_organisme'], $donnee_personne['code_langue'], $donnee_personne['code_profession'], $donnee_personne['code_executant_referent'], $donnee_personne['date_sortie_organisme'], $donnee_personne['code_motif_sortie_organisme'], $donnee_personne['date_deces'], $donnee_personne['code_statut_personne'], $donnee_personne['code_agence'], $donnee_personne['code_commercial'], $donnee_personne['nom_usage'], $donnee_personne['occurrences_collectivite'], $donnee_personne['occurrences_coordonnee'], $donnee_personne['occurrences_identifiant'], $donnee_personne['occurrences_lien_personne'], $donnee_personne['occurrences_contrat'], $donnee_personne['occurrences_rattachement']);
                                                    $nb_retours_personnes = count($lectures_personnes);
                                                    if($nb_retours_personnes!= 0) {
                                                        $ligne_personne = 0;
                                                        foreach ($lectures_personnes as $lecture_personne) {
                                                            if($lecture_personne['message']) {
                                                                $donnees_rapport = "Erreur personne n° {$donnee_personne['num_secu']}: {$lecture_personne['message']}\n";
                                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                                $nb_erreurs++;
                                                            }
                                                            $ligne_personne++;
                                                        }
                                                    }
                                                }
                                            }
                                            else {
                                                $nb_erreurs++;
                                                $donnees_rapport = "Erreur nombre personnes: Le nombre total d'occurences des personnes défini dans l'entête est différent de l'effectif dans le fichier\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                            }

                                            $nb_coordonnees = count($donnees_coordonnees);
                                            if($nb_coordonnees == $occurrences_coordonnee) {
                                                foreach ($donnees_coordonnees as $donnee_coordonnee) {
                                                    $lectures_coordonnees = $ASSURES->lecture_coordonnee($donnee_coordonnee['num_secu'],$donnee_coordonnee['code_type_coordonnee'],$donnee_coordonnee['valeur_coordonnee'],$donnee_coordonnee['date_debut_coordonnee'],$donnee_coordonnee['date_fin_coordonnee']);
                                                    $nb_retours_coordonnees = count($lectures_coordonnees);
                                                    if($nb_retours_coordonnees!= 0) {
                                                        $ligne_coordonnee = 0;
                                                        foreach ($lectures_coordonnees as $lecture_coordonnee) {
                                                            if($lecture_coordonnee['message']) {
                                                                $donnees_rapport = "Erreur coordonnée n° {$donnee_coordonnee['num_secu']}: {$lecture_coordonnee['message']}\n";
                                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                                $nb_erreurs++;
                                                            }
                                                            $ligne_coordonnee++;
                                                        }
                                                    }
                                                }
                                            }
                                            else {
                                                $nb_erreurs++;
                                                $donnees_rapport = "Erreur nombre coordonnées: Le nombre total d'occurences des coordonnées défini dans l'entête est différent de l'effectif dans le fichier\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                            }

                                            $nb_identifiants = count($donnees_identifiants);
                                            if($nb_identifiants == $occurrences_identifiant) {
                                                foreach ($donnees_identifiants as $donnee_identifiant) {
                                                    $lectures_identifiants = $ASSURES->lecture_identifiant($donnee_identifiant['num_secu'],$donnee_identifiant['code_type_identifiant'],$donnee_identifiant['num_identifiant'],$donnee_identifiant['date_debut_identifiant'],$donnee_identifiant['date_fin_identifiant']);
                                                    $nb_retours_identifiants = count($lectures_identifiants);
                                                    if($nb_retours_identifiants!= 0) {
                                                        $ligne_identifiant = 0;
                                                        foreach ($lectures_identifiants as $lecture_identifiant) {
                                                            if($lecture_identifiant['message']) {
                                                                $donnees_rapport = "Erreur identifiant n° {$donnee_identifiant['num_secu']}: {$lecture_identifiant['message']}\n";
                                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                                $nb_erreurs++;
                                                            }
                                                            $ligne_identifiant++;
                                                        }
                                                    }
                                                }
                                            }
                                            else {
                                                $nb_erreurs++;
                                                $donnees_rapport = "Erreur nombre identifiants: Le nombre total d'occurences des identifiants défini dans l'entête est différent de l'effectif dans le fichier\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                            }

                                            $nb_contrats = count($donnees_contrats);
                                            if($nb_contrats == $occurrences_contrat) {
                                                foreach ($donnees_contrats as $donnee_contrat) {
                                                    $lectures_contrats = $ASSURES->lecture_contrat($donnee_contrat['num_secu'],$donnee_contrat['num_contrat_famille'],$donnee_contrat['code_produit'],$donnee_contrat['code_collectivite'],$donnee_contrat['num_siret'],$donnee_contrat['date_debut_contrat'],$donnee_contrat['date_fin_contrat']);
                                                    $nb_retours_contrats = count($lectures_contrats);
                                                    if($nb_retours_contrats != 0) {
                                                        $ligne_contrat = 0;
                                                        foreach ($lectures_contrats as $lecture_contrat) {
                                                            if($lecture_contrat['message']) {
                                                                $donnees_rapport = "Erreur identifiant n° {$donnee_contrat['num_secu']}: {$lecture_contrat['message']}\n";
                                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                                $nb_erreurs++;
                                                            }
                                                            $ligne_contrat++;
                                                        }
                                                    }
                                                }
                                            }
                                            else {
                                                $nb_erreurs++;
                                                $donnees_rapport = "Erreur nombre contrats: Le nombre total d'occurences des contrats défini dans l'entête est différent de l'effectif dans le fichier\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                            }

                                            $nb_rattachements = count($donnees_rattachements);
                                            if($nb_rattachements == $occurrences_rattachement) {
                                                foreach ($donnees_rattachements as $donnee_rattachement) {
                                                    $lectures_rattachements = $ASSURES->lecture_rattachement($donnee_rattachement['num_secu'], $donnee_rattachement['num_contrat_famille'], $donnee_rattachement['identifiant_contractant'], $donnee_rattachement['date_debut_rattachement'], $donnee_rattachement['date_fin_rattachement'], $donnee_rattachement['occurrences_droit']);
                                                    $nb_retours_rattachements = count($lectures_rattachements);
                                                    if($nb_retours_rattachements != 0) {
                                                        $ligne_rattachement = 0;
                                                        foreach ($lectures_rattachements as $lecture_rattachement) {
                                                            if($lecture_rattachement['message']) {
                                                                $donnees_rapport = "Erreur rattachement n° {$donnee_rattachement['num_secu']}: {$lecture_rattachement['message']}\n";
                                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                                $nb_erreurs++;
                                                            }
                                                            $ligne_rattachement++;
                                                        }
                                                    }
                                                }
                                            }
                                            else {
                                                $nb_erreurs++;
                                                $donnees_rapport = "Erreur nombre rattachement: Le nombre total d'occurences des rattachements défini dans l'entête est différent de l'effectif dans le fichier\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                            }
                                        }
                                        else {
                                            foreach ($lecture_entetes as $lecture_entete) {
                                                if($lecture_entete['message']) {
                                                    $donnees_rapport = "Erreur entete: {$lecture_entete['message']}\n";
                                                    fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                    $nb_erreurs++;
                                                }
                                            }
                                        }

                                        $donnees_rapport = "\n\n\n".date('d-m-Y H:i:s',time())." FIN DE LECTURE DU FICHIER {$filename}\n";
                                        fwrite($fichier_rapport, $donnees_rapport);
                                        $donnees_rapport = "NOMBRE D'ERREURS IDENTIFIEES: {$nb_erreurs}\n\n";
                                        fwrite($fichier_rapport, $donnees_rapport);

                                        if($nb_erreurs != 0) {
                                            $donnees_rapport = "LE TRAITEMENT A ETE INTERROMPU.\n";
                                            $message = $donnees_rapport."NOM DU FICHIER: <b>{$filename}</b><a target='_blank' href='".$lien.$report_filename."' download='".$report_filename."'>Cliquez ici pour télécharger le fichier</a>";
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
                                                    $nouveau_script = $SCRIPTS->ajouter('SCRIPT_CHARGEMENT_DIFPOP',date('Y-m-d H:i:s',time()),NULL,'ENC','CHARGEMENT DU DIFPOP NOMME: '.$filename,$utilisateur['user_id']);
                                                    if($nouveau_script['success'] == true) {
                                                        $donnees_rapport = date('d-m-Y H:i:s',time())." DEBUT DU TRAITEMENT DU FICHIER {$filename}\n\n";
                                                        fwrite($fichier_rapport, $donnees_rapport);
                                                        $succes = 0;
                                                        $echecs = 0;
                                                        $creation = 0;
                                                        $maj = 0;
                                                        $sup = 0;
                                                        foreach ($donnees_personnes as $donnee_personne) {
                                                            if($donnee_personne['date_situation']) {$date_situation = date('Y-m-d',strtotime(str_replace('/','-',$donnee_personne['date_situation'])));}else {$date_situation = NULL;}
                                                            if($donnee_personne['date_naissance']) {$date_naissance = date('Y-m-d',strtotime(str_replace('/','-',$donnee_personne['date_naissance'])));}else {$date_naissance = NULL;}
                                                            if($donnee_personne['date_premiere_immat']) {$date_premiere_immat = date('Y-m-d',strtotime(str_replace('/','-',$donnee_personne['date_premiere_immat'])));}else {$date_premiere_immat = NULL;}
                                                            if($donnee_personne['date_entree_organisme']) {$date_entree_organisme = date('Y-m-d',strtotime(str_replace('/','-',$donnee_personne['date_entree_organisme'])));}else {$date_entree_organisme = NULL;}
                                                            if($donnee_personne['date_anciennete_organisme']) {$date_anciennete_organisme = date('Y-m-d',strtotime(str_replace('/','-',$donnee_personne['date_anciennete_organisme'])));}else {$date_anciennete_organisme = NULL;}
                                                            if($donnee_personne['date_sortie_organisme']) {$date_sortie_organisme = date('Y-m-d',strtotime(str_replace('/','-',$donnee_personne['date_sortie_organisme'])));}else {$date_sortie_organisme = NULL;}
                                                            if($donnee_personne['date_deces']) {$date_deces = date('Y-m-d',strtotime(str_replace('/','-',$donnee_personne['date_deces'])));}else {$date_deces = NULL;}

                                                            $edition_assure = $ASSURES->edition_assure($tableau_entete['code_organisme_dest'],$tableau_entete['num_transmission'],$donnee_personne['type_mouvement'],$date_situation,$donnee_personne['identifiant_personne'],$donnee_personne['num_secu'],$donnee_personne['cle_secu'],$donnee_personne['code_civilite'],$donnee_personne['nom'],$donnee_personne['nom_patronymique'],$donnee_personne['prenom'],$donnee_personne['code_sexe'],$donnee_personne['code_qualite'],$date_naissance,$donnee_personne['rang_gemellaire'],$donnee_personne['code_nationalite'],$donnee_personne['code_situation_familiale'],$donnee_personne['code_cat_socio_professionnelle'],$donnee_personne['code_grand_regime'],$donnee_personne['code_caisse'],$donnee_personne['code_centre_payeur'],$date_premiere_immat,$donnee_personne['code_cpl_grand_regime'],$date_entree_organisme,$date_anciennete_organisme,$donnee_personne['code_langue'],$donnee_personne['code_profession'],$donnee_personne['code_executant_referent'],$date_sortie_organisme,$donnee_personne['code_motif_sortie_organisme'],$date_deces,$donnee_personne['code_statut_personne'],$donnee_personne['code_agence'],$donnee_personne['code_commercial'],$donnee_personne['nom_usage'],$donnee_personne['code_pays_naissance'],$donnee_personne['lieu_naissance'],$donnee_personne['code_secteur'],$donnee_personne['code_type_adresse'],$donnee_personne['code_pays_adresse'],$donnee_personne['adresse_normee_o_n'],$donnee_personne['auxiliaire_adresse_1'],$donnee_personne['auxiliaire_adresse_2'],$donnee_personne['num_voie'],$donnee_personne['cplt_num_voie'],$donnee_personne['lib_voie'],$donnee_personne['nom_acheminement'],$donnee_personne['nom_lieu_dit'],$donnee_personne['lieu_dit_o_n'],NULL,NULL,$donnee_personne['code_postal'],$donnee_personne['ref_adresse'],$utilisateur['user_id']);
                                                            if($edition_assure['success'] == true) {
                                                                $succes++;
                                                                if($donnee_personne['type_mouvement'] == 'CRE') {$creation++;}elseif($donnee_personne['type_mouvement'] == 'MDF') {$maj++;}else {$sup++;}
                                                            }else {
                                                                $echecs++;
                                                            }
                                                        }

                                                        if($echecs == 0) {
                                                            $succes_collectivite = 0;
                                                            $echecs_collectivite = 0;
                                                            foreach ($donnees_collectivites as $donnee_collectivite) {
                                                                $edition_assure_collectivite = $ASSURES->edition_assure_collectivite($tableau_entete['code_organisme_dest'],$donnee_collectivite['num_secu'], $donnee_collectivite['code_collectivite_employeur'], $donnee_collectivite['num_siret_employeur'], $donnee_collectivite['code_college'], $donnee_collectivite['matricule_salarie'], $donnee_collectivite['service_collectivite'], $donnee_collectivite['code_fonction'],$utilisateur['user_id']);
                                                                if($edition_assure_collectivite['success'] == true) {
                                                                    $succes_collectivite++;
                                                                }else {
                                                                    $echecs_collectivite++;
                                                                }
                                                            }

                                                            if($echecs_collectivite == 0) {
                                                                $succes_coordonnees = 0;
                                                                $echecs_coordonnees = 0;
                                                                foreach ($donnees_coordonnees as $donnee_coordonnee) {
                                                                    if($donnee_coordonnee['date_debut_coordonnee']) {
                                                                        $date_debut = date('Y-m-d',strtotime(str_replace('/','-',$donnee_coordonnee['date_debut_coordonnee'])));
                                                                    }else {
                                                                        $date_debut = NULL;
                                                                    }
                                                                    if($donnee_coordonnee['date_fin_coordonnee']) {
                                                                        $date_fin = date('Y-m-d',strtotime(str_replace('/','-',$donnee_coordonnee['date_fin_coordonnee'])));
                                                                    }else {
                                                                        $date_fin = NULL;
                                                                    }
                                                                    $edition_assure_coordonnee = $ASSURES->edition_assure_coordonnees($tableau_entete['code_organisme_dest'],$donnee_coordonnee['num_secu'],$donnee_coordonnee['code_type_coordonnee'],$donnee_coordonnee['valeur_coordonnee'],$date_debut,$date_fin,$utilisateur['user_id']);
                                                                    if($edition_assure_coordonnee['success'] == true) {
                                                                        $succes_coordonnees++;
                                                                    }else {
                                                                        $echecs_coordonnees++;
                                                                    }
                                                                }

                                                                if($echecs_coordonnees == 0) {
                                                                    $succes_identifiants = 0;
                                                                    $echecs_identifiants = 0;
                                                                    foreach ($donnees_identifiants as $donnee_identifiant) {
                                                                        if($donnee_identifiant['date_debut_identifiant']) {
                                                                            $date_debut = date('Y-m-d',strtotime(str_replace('/','-',$donnee_identifiant['date_debut_identifiant'])));
                                                                        }else {
                                                                            $date_debut = NULL;
                                                                        }
                                                                        if($donnee_identifiant['date_fin_identifiant']) {
                                                                            $date_fin = date('Y-m-d',strtotime(str_replace('/','-',$donnee_identifiant['date_fin_identifiant'])));
                                                                        }else {
                                                                            $date_fin = NULL;
                                                                        }
                                                                        $edition_assure_identifiant = $ASSURES->edition_assure_identifiant($tableau_entete['code_organisme_dest'],$donnee_identifiant['num_secu'],$donnee_identifiant['code_type_identifiant'],$donnee_identifiant['num_identifiant'],$date_debut,$date_fin, $utilisateur['user_id']);
                                                                        if($edition_assure_identifiant['success'] == true) {
                                                                            $succes_identifiants++;
                                                                        }else {
                                                                            $echecs_identifiants++;
                                                                        }
                                                                    }

                                                                    if($echecs_identifiants == 0) {
                                                                        $succes_contrats = 0;
                                                                        $echecs_contrats = 0;
                                                                        foreach ($donnees_contrats as $donnee_contrat) {
                                                                            if($donnee_contrat['date_debut_contrat']) {
                                                                                $date_debut = date('Y-m-d',strtotime(str_replace('/','-',$donnee_contrat['date_debut_contrat'])));
                                                                            }else {
                                                                                $date_debut = NULL;
                                                                            }
                                                                            if($donnee_contrat['date_fin_contrat']) {
                                                                                $date_fin = date('Y-m-d',strtotime(str_replace('/','-',$donnee_contrat['date_fin_contrat'])));
                                                                            }else {
                                                                                $date_fin = NULL;
                                                                            }
                                                                            $edition_assure_contrat = $ASSURES->edition_assure_contrat($tableau_entete['code_organisme_dest'],$donnee_contrat['num_secu'],$donnee_contrat['num_contrat_famille'],$donnee_contrat['code_produit'],$donnee_contrat['code_collectivite'],$donnee_contrat['num_siret'],$date_debut,$date_fin,$utilisateur['user_id']);
                                                                            if($edition_assure_contrat['success'] == true) {
                                                                                $succes_contrats++;
                                                                            }else {
                                                                                $echecs_contrats++;
                                                                            }
                                                                        }

                                                                        if($echecs_contrats == 0) {
                                                                            $succes_rattachements = 0;
                                                                            $echecs_rattachements = 0;
                                                                            foreach ($donnees_rattachements as $donnee_rattachement) {
                                                                                if($donnee_rattachement['date_debut_rattachement']) {
                                                                                    $date_debut = date('Y-m-d',strtotime(str_replace('/','-',$donnee_rattachement['date_debut_rattachement'])));
                                                                                }else {
                                                                                    $date_debut = NULL;
                                                                                }
                                                                                if($donnee_rattachement['date_fin_rattachement']) {
                                                                                    $date_fin = date('Y-m-d',strtotime(str_replace('/','-',$donnee_rattachement['date_fin_rattachement'])));
                                                                                }else {
                                                                                    $date_fin = NULL;
                                                                                }
                                                                                $edition_assure_rattachement = $ASSURES->edition_assure_rattachement($tableau_entete['code_organisme_dest'],$donnee_rattachement['num_secu'],$donnee_rattachement['num_contrat_famille'],$donnee_rattachement['identifiant_contractant'],$date_debut,$date_fin,$utilisateur['user_id']);
                                                                                if($edition_assure_rattachement['success'] == true) {
                                                                                    $succes_rattachements++;
                                                                                }else {
                                                                                    $echecs_rattachements++;
                                                                                }
                                                                            }
                                                                            if($echecs_rattachements == 0) {
                                                                                $log_chargement = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',"FIN DE CHARGEMENT",$utilisateur['user_id']);
                                                                                if($log_chargement['success'] == true) {
                                                                                    $historique = $LOGS->ajouter_historique_fichier('IMP',$tableau_entete['num_transmission'],$tableau_entete['nom_modele'],$tableau_entete['num_transmission'],date('Y-m-d',strtotime($tableau_entete['date_fichier'])),$tableau_entete['code_organisme_emet'],$tableau_entete['code_organisme_dest'],($creation+$maj),$creation,$maj,$filename,$utilisateur['user_id']);
                                                                                    if($historique['success'] == true) {
                                                                                        $mise_a_jour_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],$log_lecture['id_log'],$log_chargement['id_log'],date('Y-m-d H:i:s',time()),'FIN',"CHARGEMENT DU REFERNTIEL MOTIFS REJETS NOMME: {$filename} EFFECTUE AVEC SUCCES",$utilisateur['user_id']);
                                                                                        if($mise_a_jour_script['success'] == true) {
                                                                                            $donnees_rapport = date('d-m-Y H:i:s',time())." FIN DU TRAITEMENT DU FICHIER {$filename}\n\nFICHIER CHARGE AVEC SUCCES\nNOM DU FICHIER: {$filename}\nNOMBRE DE LIGNES: ".($succes+$echecs)."\nREUSSITE: {$succes}\nCREATION: {$creation}\nMISE A JOUR: {$maj}\nECHECS: {$echecs}";
                                                                                            fwrite($fichier_rapport, $donnees_rapport);
                                                                                            fclose($fichier_rapport);
                                                                                            $message = "FICHIER CHARGE AVEC SUCCES.<br />NOM DU FICHIER: <b>{$filename}</b><br />NOMBRE DE LIGNES: <b>".($succes+$echecs)."</b><br />REUSSITE: <b>{$succes}</b><br />CREATION: <b>{$creation}</b><br />MISE A JOUR: <b>{$maj}</b><br />SUPPRESSION: <b>{$sup}</b><br />ECHECS: <b>{$echecs}</b><br /><a target='_blank' href='".$lien.$report_filename."' download='".$report_filename."'>Cliquez ici pour télécharger le fichier</a>";
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
                                                                                $message = "Une erreur est survenue lors de l'edition des rattachements.";
                                                                                $json = array(
                                                                                    'success' => false,
                                                                                    'message' => $message
                                                                                );
                                                                            }
                                                                        }
                                                                        else {
                                                                            $message = "Une erreur est survenue lors de l'edition des contrats.";
                                                                            $json = array(
                                                                                'success' => false,
                                                                                'message' => $message
                                                                            );
                                                                        }
                                                                    }
                                                                    else {
                                                                        $message = "Une erreur est survenue lors de l'edition des identifiants.";
                                                                        $json = array(
                                                                            'success' => false,
                                                                            'message' => $message
                                                                        );
                                                                    }
                                                                }
                                                                else {
                                                                    $message = "Une erreur est survenue lors de l'edition des coordonnées.";
                                                                    $json = array(
                                                                        'success' => false,
                                                                        'message' => $message
                                                                    );
                                                                }

                                                            }
                                                            else {
                                                                $message = "Une erreur est survenue lors de l'edition des collectivités.";
                                                                $json = array(
                                                                    'success' => false,
                                                                    'message' => $message
                                                                );
                                                            }
                                                        }
                                                        else {
                                                            $message = "Une erreur est survenue lors de l'edition des personnes.";
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
                                    else {
                                        $message = "Erreur dans l'entête du fichier.";
                                        $json = array(
                                            'success' => false,
                                            'message' => $message
                                        );
                                    }
                                }
                                elseif($type_ref == 'DECA') {
                                    $facture_statut = 'N';
                                    $tableau_entete = array(
                                        'code_norme' => trim($xmlTransform->CODE_NORME),
                                        'version_norme' => trim($xmlTransform->VERSION_NORME),
                                        'date_fichier' => trim($xmlTransform->DATE_FICHIER),
                                        'num_fichier' => trim($xmlTransform->NUM_FICHIER),
                                        'type_emetteur' => trim($xmlTransform->TYPE_EMETTEUR),
                                        'num_emetteur' => trim($xmlTransform->NUM_EMETTEUR),
                                        'type_destinataire' => trim($xmlTransform->TYPE_DESTINATAIRE),
                                        'num_destinataire' => trim($xmlTransform->NUM_DESTINATAIRE),
                                        'occurrences_decompte' => trim($xmlTransform->OCCURRENCES_DECOMPTE)
                                    );
                                    $path = $path_export.$type_ref.'/'.date('d_m_Y',time()).'/';
                                    $lien = $lien_export.$type_ref.'/'.date('d_m_Y',time()).'/';
                                    if(!file_exists($path)){
                                        mkdir($path,0777,true);
                                    }
                                    $report_filename = "RAPPORT_INTEGRATION_".str_replace('.txt','',str_replace('.xml','',str_replace('.XML','',$filename)))."_".date('dmYHis').".txt";

                                    $nb_tableau_entete = count($tableau_entete);
                                    if($nb_tableau_entete != 0)  {
                                        $fichier_rapport = fopen($path.$report_filename, "w") or die("Unable to open file!");
                                        $donnees_rapport = date('d-m-Y H:i:s',time())." DEBUT DE LECTURE DU FICHIER {$filename}\n\n";
                                        fwrite($fichier_rapport, $donnees_rapport);

                                        $lecture_entetes = $SCRIPTS->lecture_entete_fichier_xml($tableau_entete['code_norme'],$tableau_entete['version_norme'],NULL,$tableau_entete['num_emetteur'],$tableau_entete['type_emetteur'],$tableau_entete['num_destinataire'],$tableau_entete['type_destinataire'],$tableau_entete['num_fichier'],$tableau_entete['date_fichier'],$tableau_entete['occurrences_decompte'],$filename);
                                        $nb_retours_lecture_entete = (count($lecture_entetes) - 1);
                                        $donnees_decomptes = null;
                                        $nb_erreurs = 0;

                                        if($nb_retours_lecture_entete == 0) {
                                            $ligne_decompte = 0;
                                            foreach($xmlTransform->DECOMPTE AS $decompte) {
                                                $donnees_decomptes[] = array(
                                                    'lien_archivage' => trim($decompte->LIEN_ARCHIVAGE),
                                                    'date_reception' => trim($decompte->DATE_RECEPTION),
                                                    'date_liquidation' => trim($decompte->DATE_LIQUIDATION),
                                                    'num_secu' => trim($decompte->NUM_SECU_BENEFICIAIRE),
                                                    'date_naissance' => trim($decompte->DATE_NAISSANCE_BENEFICIAIRE),
                                                    'rang_gemellaire' => trim($decompte->RANG_GEMELLAIRE_BENEFICIAIRE),
                                                    'nom' => trim($decompte->NOM_BENEFICIAIRE),
                                                    'prenom' => trim($decompte->PRENOM_BENEFICIAIRE),
                                                    'type_decompte' => trim($decompte->TYPE_DECOMPTE),
                                                    'code_etablissement' => trim($decompte->CODE_ETABLISSEMENT),
                                                    'num_ordonnance' => trim($decompte->NUM_ORDONNANCE),
                                                    'num_accident' => trim($decompte->NUM_ACCIDENT),
                                                    'date_accident' => trim($decompte->DATE_ACCIDENT),
                                                    'code_nature_assurance' => trim($decompte->CODE_NATURE_ASSURANCE),
                                                    'date_facture' => trim($decompte->DATE_FACTURE),
                                                    'num_facture' => trim($decompte->NUM_FACTURE),
                                                    'num_dossier_soins' => trim($decompte->NUM_DOSSIER_SOINS),
                                                    'code_etab_dossier_soins' => trim($decompte->CODE_ETAB_DOSSIER_SOINS),
                                                    'code_ps_dossier_soins' => trim($decompte->CODE_PS_DOSSIER_SOINS),
                                                    'code_typologie_dossier_soins' => trim($decompte->CODE_TYPOLOGIE_DOSSIER_SOINS),
                                                    'code_pathologie_dossier_soins' => trim($decompte->CODE_PATHOLOGIE_DOSSIER_SOINS),
                                                    'date_debut_dossier_soins' => trim($decompte->DATE_DEBUT_DOSSIER_SOINS),
                                                    'date_fin_dossier_soins' => trim($decompte->DATE_FIN_DOSSIER_SOINS),
                                                    'num_destinataire_reglement' => trim($decompte->NUM_DESTINATAIRE_REGLEMENT),
                                                    'date_paiement' => trim($decompte->DATE_PAIEMENT),
                                                    'mode_paiement' => trim($decompte->MODE_PAIEMENT),
                                                    'infos_bancaires_paiement' => trim($decompte->INFOS_BANCAIRES_PAIEMENT),
                                                    'occurrences_ligne_acte' => trim($decompte->OCCURRENCES_LIGNE_ACTE)
                                                );

                                                for ($ligne = 0; $ligne < $donnees_decomptes[$ligne_decompte]['occurrences_ligne_acte']; $ligne++) {
                                                    $ligne_acte = $decompte->LIGNE_ACTE[$ligne];
                                                    $donnees_actes[] = array(
                                                        'num_facture' => trim($decompte->NUM_FACTURE),
                                                        'num_ligne_acte' => trim($ligne_acte->NUM_LIGNE_ACTE),
                                                        'date_debut_soins' => trim($ligne_acte->DATE_DEBUT_SOINS),
                                                        'date_fin_soins' => trim($ligne_acte->DATE_FIN_SOINS),
                                                        'code_acte' => trim($ligne_acte->CODE_ACTE),
                                                        'code_complement_acte' => trim($ligne_acte->CODE_COMPLEMENT_ACTE),
                                                        'code_specialite' => trim($ligne_acte->CODE_SPECIALITE),
                                                        'code_dmt' => trim($ligne_acte->CODE_DMT),
                                                        'code_pathologie' => trim($ligne_acte->CODE_PATHOLOGIE),
                                                        'code_origine_prescription' => trim($ligne_acte->CODE_ORIGINE_PRESCRIPTION),
                                                        'code_indicateur_parcours_soins' => trim($ligne_acte->CODE_INDICATEUR_PARCOURS_SOINS),
                                                        'code_prescripteur' => trim($ligne_acte->CODE_PRESCRIPTEUR),
                                                        'code_specialite_prescripteur' => trim($ligne_acte->CODE_SPECIALITE_PRESCRIPTEUR),
                                                        'date_prescription' => trim($ligne_acte->DATE_PRESCRIPTION),
                                                        'code_executant' => trim($ligne_acte->CODE_EXECUTANT),
                                                        'code_zone_tarif_executant' => trim($ligne_acte->CODE_ZONE_TARIF_EXECUTANT),
                                                        'type_controle_droits' => trim($ligne_acte->TYPE_CONTROLE_DROITS),
                                                        'num_transaction_ctrl_droits' => trim($ligne_acte->NUM_TRANSACTION_CTRL_DROITS),
                                                        'capitation_o_n' => trim($ligne_acte->CAPITATION_O_N),
                                                        'montant_depense' => trim($ligne_acte->MONTANT_DEPENSE),
                                                        'quantite_acte' => trim($ligne_acte->QUANTITE_ACTE),
                                                        'coefficient_acte' => trim($ligne_acte->COEFFICIENT_ACTE),
                                                        'prix_unitaire_acte' => trim($ligne_acte->PRIX_UNITAIRE_ACTE),
                                                        'base_remboursement' => trim($ligne_acte->BASE_REMBOURSEMENT),
                                                        'taux_remboursement_ro' => trim($ligne_acte->TAUX_REMBOURSEMENT_RO),
                                                        'montant_remboursement_ro' => trim($ligne_acte->MONTANT_REMBOURSEMENT_RO),
                                                        'signe' => trim($ligne_acte->SIGNE)
                                                    );
                                                }
                                                $ligne_decompte++;
                                            }

                                            $nb_donnees_decomptes = count($donnees_decomptes);
                                            if($nb_donnees_decomptes == $tableau_entete['occurrences_decompte']) {
                                                $nb_lignes_actes = 0;
                                                foreach ($donnees_decomptes as $donnee_decompte) {
                                                    $nb_lignes_actes = $nb_lignes_actes + intval($donnee_decompte['occurrences_ligne_acte']);
                                                    $lectures_decomptes = $FACTURES->lecture_decompte($tableau_entete['code_norme'],$donnee_decompte['lien_archivage'],$donnee_decompte['date_reception'],$donnee_decompte['date_liquidation'],$donnee_decompte['num_secu'],$donnee_decompte['date_naissance'],$donnee_decompte['rang_gemellaire'],$donnee_decompte['nom'],$donnee_decompte['prenom'],$donnee_decompte['type_decompte'],$donnee_decompte['code_etablissement'],$donnee_decompte['num_ordonnance'],$donnee_decompte['num_accident'],$donnee_decompte['date_accident'],$donnee_decompte['code_nature_assurance'],$donnee_decompte['date_facture'],$donnee_decompte['num_facture'],$donnee_decompte['num_dossier_soins'],$donnee_decompte['code_etab_dossier_soins'],$donnee_decompte['code_ps_dossier_soins'],$donnee_decompte['code_typologie_dossier_soins'],$donnee_decompte['code_pathologie_dossier_soins'],$donnee_decompte['date_debut_dossier_soins'],$donnee_decompte['date_fin_dossier_soins'],$donnee_decompte['num_destinataire_reglement'],$donnee_decompte['date_paiement'],$donnee_decompte['mode_paiement'],$donnee_decompte['infos_bancaires_paiement'],$donnee_decompte['occurrences_ligne_acte'],NULL);
                                                    $nb_retours_decomptes = count($lectures_decomptes);
                                                    if($nb_retours_decomptes!= 0) {
                                                        $ligne_decompte = 0;
                                                        foreach ($lectures_decomptes as $lecture_decompte) {
                                                            if($lecture_decompte['message']) {
                                                                $donnees_rapport = "Erreur facture n° {$donnee_decompte['num_facture']}: {$lecture_decompte['message']}\n";
                                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                                $nb_erreurs++;
                                                            }
                                                            $ligne_decompte++;
                                                        }
                                                    }
                                                }

                                                if($nb_lignes_actes == count($donnees_actes)) {
                                                    $ligne_acte = 0;
                                                    foreach ($donnees_actes as $donnee_acte) {
                                                        $lectures_lignes_actes = $FACTURES->lecture_ligne_acte($tableau_entete['code_norme'],$donnee_acte['num_facture'], $donnee_acte['num_ligne_acte'], $donnee_acte['date_debut_soins'], $donnee_acte['date_fin_soins'], $donnee_acte['code_acte'], $donnee_acte['code_complement_acte'], $donnee_acte['code_specialite'], $donnee_acte['code_dmt'], $donnee_acte['code_pathologie'], $donnee_acte['code_origine_prescription'], $donnee_acte['code_indicateur_parcours_soins'], $donnee_acte['code_prescripteur'], $donnee_acte['code_specialite_prescripteur'], $donnee_acte['date_prescription'], $donnee_acte['code_executant'], $donnee_acte['code_zone_tarif_executant'], $donnee_acte['type_controle_droits'], $donnee_acte['num_transaction_ctrl_droits'], $donnee_acte['capitation_o_n'], $donnee_acte['montant_depense'], $donnee_acte['quantite_acte'], $donnee_acte['coefficient_acte'], $donnee_acte['prix_unitaire_acte'], $donnee_acte['base_remboursement'], $donnee_acte['taux_remboursement_ro'], $donnee_acte['montant_remboursement_ro'], $donnee_acte['signe'], $donnee_acte['code_rejet'], $donnee_acte['libelle_rejet']);
                                                        if($lectures_lignes_actes['message']) {
                                                            $donnees_rapport = "Erreur acte de facture n° {$donnee_acte['num_facture']}: {$lectures_lignes_actes['message']}\n";
                                                            fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                            $nb_erreurs++;
                                                        }
                                                        $ligne_acte++;
                                                    }
                                                }
                                                else {
                                                    $nb_erreurs++;
                                                    $donnees_rapport = "Erreur nombre lignes actes: Le nombre d'occurences des lignes d'actes défini dans l'entête est différent de l'effectif des lignes d'actes dans le fichier\n";
                                                    fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                }

                                            }
                                            else {
                                                $nb_erreurs++;
                                                $donnees_rapport = "Erreur nombre décomptes: Le nombre d'occurences des décomptes défini dans l'entête est différent de l'effectif des décomptes dans le fichier\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                            }
                                        }
                                        else {
                                            foreach ($lecture_entetes as $lecture_entete) {
                                                if($lecture_entete['message']) {
                                                    $donnees_rapport = "Erreur entete: {$lecture_entete['message']}\n";
                                                    fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                    $nb_erreurs++;
                                                }
                                            }
                                        }


                                        $donnees_rapport = "\n\n\n".date('d-m-Y H:i:s',time())." FIN DE LECTURE DU FICHIER {$filename}\n";
                                        fwrite($fichier_rapport, $donnees_rapport);
                                        $donnees_rapport = "NOMBRE D'ERREURS IDENTIFIEES: {$nb_erreurs}\n\n";
                                        fwrite($fichier_rapport, $donnees_rapport);

                                        if($nb_erreurs != 0) {
                                            $donnees_rapport = "LE TRAITEMENT A ETE INTERROMPU.\n";
                                            $message = $donnees_rapport."NOM DU FICHIER: <b>{$filename}</b><a target='_blank' href='".$lien.$report_filename."' download='".$report_filename."'>Cliquez ici pour télécharger le fichier</a>";
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
                                                    $nouveau_script = $SCRIPTS->ajouter('SCRIPT_CHARGEMENT_DECRET',date('Y-m-d H:i:s',time()),NULL,'ENC','CHARGEMENT DU DECRET NOMME: '.$filename,$utilisateur['user_id']);
                                                    if($nouveau_script['success'] == true) {
                                                        $donnees_rapport = date('d-m-Y H:i:s',time())." DEBUT DU TRAITEMENT DU FICHIER {$filename}\n\n";
                                                        fwrite($fichier_rapport, $donnees_rapport);
                                                        $succes = 0;
                                                        $echecs = 0;
                                                        $creation = 0;
                                                        $maj = 0;
                                                        $sup = 0;

                                                        foreach ($donnees_decomptes as $donnee_decompte) {
                                                            if($donnee_decompte['date_reception']) {
                                                                $date_reception = date('Y-m-d',strtotime(str_replace('/','-',$donnee_decompte['date_reception'])));
                                                            }else {
                                                                $date_reception = NULL;
                                                            }
                                                            if($donnee_decompte['date_liquidation']) {
                                                                if($donnee_decompte['date_liquidation'] == '00/00/0000') {
                                                                    $date_liquidation = NULL;
                                                                }else {
                                                                    $date_liquidation = date('Y-m-d',strtotime(str_replace('/','-',$donnee_decompte['date_liquidation'])));
                                                                }
                                                            }else {
                                                                $date_liquidation = NULL;
                                                            }
                                                            if($donnee_decompte['date_naissance']) {
                                                                $date_naissance = date('Y-m-d',strtotime(str_replace('/','-',$donnee_decompte['date_naissance'])));
                                                            }else {
                                                                $date_naissance = NULL;
                                                            }
                                                            if($donnee_decompte['date_accident']) {
                                                                $date_accident = date('Y-m-d',strtotime(str_replace('/','-',$donnee_decompte['date_accident'])));
                                                            }else {
                                                                $date_accident = NULL;
                                                            }
                                                            if($donnee_decompte['date_facture']) {
                                                                $date_facture = date('Y-m-d',strtotime(str_replace('/','-',$donnee_decompte['date_facture'])));
                                                            }else {
                                                                $date_facture = NULL;
                                                            }
                                                            if($donnee_decompte['date_debut_dossier_soins']) {
                                                                $date_debut_dossier_soins = date('Y-m-d',strtotime(str_replace('/','-',$donnee_decompte['date_debut_dossier_soins'])));
                                                            }else {
                                                                $date_debut_dossier_soins = NULL;
                                                            }
                                                            if($donnee_decompte['date_fin_dossier_soins']) {
                                                                $date_fin_dossier_soins = date('Y-m-d',strtotime(str_replace('/','-',$donnee_decompte['date_fin_dossier_soins'])));
                                                            }else {
                                                                $date_fin_dossier_soins = NULL;
                                                            }
                                                            if($donnee_decompte['date_paiement']) {
                                                                $date_paiement = date('Y-m-d',strtotime(str_replace('/','-',$donnee_decompte['date_paiement'])));
                                                            }else {
                                                                $date_paiement = NULL;
                                                            }

                                                            $edition_facture = $FACTURES->edition_facture($tableau_entete['num_destinataire'],$tableau_entete['num_fichier'],NULL,$tableau_entete['num_fichier'],$date_reception,$date_liquidation,$donnee_decompte['lien_archivage'],$donnee_decompte['code_typologie_dossier_soins'],$donnee_decompte['num_facture'],$date_facture,$donnee_decompte['num_secu'],$donnee_decompte['nom'],$donnee_decompte['prenom'],$date_naissance,$donnee_decompte['rang_gemellaire'],$donnee_decompte['num_ordonnance'],$donnee_decompte['type_decompte'],$donnee_decompte['code_etablissement'],$donnee_decompte['num_accident'],$date_accident,$donnee_decompte['code_nature_assurance'],$donnee_decompte['num_dossier_soins'],$donnee_decompte['code_etab_dossier_soins'],$donnee_decompte['code_ps_dossier_soins'],$donnee_decompte['code_typologie_dossier_soins'],$donnee_decompte['code_pathologie_dossier_soins'],$date_debut_dossier_soins,$date_fin_dossier_soins,$donnee_decompte['num_destinataire_reglement'],$date_paiement,$donnee_decompte['mode_paiement'],$donnee_decompte['infos_bancaires_paiement'],$facture_statut,$utilisateur['user_id']);
                                                            if($edition_facture['success'] == true) {
                                                                $succes++;
                                                            }else {
                                                                $echecs++;
                                                            }
                                                        }

                                                        if($echecs == 0) {
                                                            $succes_actes = 0;
                                                            $echecs_actes = 0;
                                                            foreach ($donnees_actes as $donnee_acte) {
                                                                if($donnee_acte['date_debut_soins']) {
                                                                    if($donnee_decompte['date_liquidation'] == '00/00/0000') {
                                                                        $date_debut_soins = NULL;
                                                                    }else {
                                                                        $date_debut_soins = date('Y-m-d',strtotime(str_replace('/','-',$donnee_decompte['date_debut_soins'])));
                                                                    }
                                                                }else {
                                                                    $date_debut_soins = NULL;
                                                                }
                                                                if($donnee_acte['date_fin_soins']) {
                                                                    if($donnee_decompte['date_liquidation'] == '00/00/0000') {
                                                                        $date_fin_soins = NULL;
                                                                    }else {
                                                                        $date_fin_soins = date('Y-m-d',strtotime(str_replace('/','-',$donnee_decompte['date_fin_soins'])));
                                                                    }
                                                                }else {
                                                                    $date_fin_soins = NULL;
                                                                }
                                                                if($donnee_acte['date_prescription']) {
                                                                    $date_prescription = date('Y-m-d',strtotime(str_replace('/','-',$donnee_decompte['date_prescription'])));
                                                                }else {
                                                                    $date_prescription = NULL;
                                                                }
                                                                $edition_facture = $FACTURES->edition_acte($donnee_acte['num_facture'],$date_debut_soins,$date_fin_soins,$donnee_acte['code_acte'],$donnee_acte['code_complement_acte'],$donnee_acte['code_specialite'],$donnee_acte['code_dmt'],$donnee_acte['code_pathologie'],$donnee_acte['code_origine_prescription'],$donnee_acte['code_indicateur_parcours_soins'],$donnee_acte['code_prescripteur'],$donnee_acte['code_specialite_prescripteur'],$date_prescription,$donnee_acte['code_executant'],$donnee_acte['code_zone_tarif_executant'],$donnee_acte['type_controle_droits'],$donnee_acte['num_transaction_ctrl_droits'],$donnee_acte['capitation_o_n'],$donnee_acte['montant_depense'],$donnee_acte['quantite_acte'],$donnee_acte['coefficient_acte'],$donnee_acte['prix_unitaire_acte'],$donnee_acte['base_remboursement'],$donnee_acte['taux_remboursement_ro'],$donnee_acte['montant_remboursement_ro'],$donnee_acte['signe'],NULL,$utilisateur['user_id']);
                                                                if($edition_facture['success'] == true) {
                                                                    $succes_actes++;
                                                                }else {
                                                                    $echecs_actes++;
                                                                }
                                                            }
                                                            if($echecs_actes == 0) {
                                                                $log_chargement = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',"FIN DE CHARGEMENT",$utilisateur['user_id']);
                                                                if($log_chargement['success'] == true) {
                                                                    $historique = $LOGS->ajouter_historique_fichier('IMP',$tableau_entete['num_fichier'],$tableau_entete['code_norme'],$tableau_entete['num_fichier'],date('Y-m-d',strtotime($tableau_entete['date_fichier'])),$tableau_entete['num_emetteur'],$tableau_entete['num_destinataire'],($creation+$maj),$creation,$maj,$filename,$utilisateur['user_id']);
                                                                    if($historique['success'] == true) {
                                                                        $mise_a_jour_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],$log_lecture['id_log'],$log_chargement['id_log'],date('Y-m-d H:i:s',time()),'FIN',"CHARGEMENT DU REFERNTIEL MOTIFS REJETS NOMME: {$filename} EFFECTUE AVEC SUCCES",$utilisateur['user_id']);
                                                                        if($mise_a_jour_script['success'] == true) {
                                                                            $donnees_rapport = date('d-m-Y H:i:s',time())." FIN DU TRAITEMENT DU FICHIER {$filename}\n\nFICHIER CHARGE AVEC SUCCES\nNOM DU FICHIER: {$filename}\nNOMBRE DE LIGNES: ".($succes+$echecs)."\nREUSSITE: {$succes}\nCREATION: {$creation}\nMISE A JOUR: {$maj}\nECHECS: {$echecs}";
                                                                            fwrite($fichier_rapport, $donnees_rapport);
                                                                            fclose($fichier_rapport);
                                                                            $message = "FICHIER CHARGE AVEC SUCCES.<br />NOM DU FICHIER: <b>{$filename}</b><br />NOMBRE DE LIGNES: <b>".($succes+$echecs)."</b><br />REUSSITE: <b>{$succes}</b></b><br />ECHECS: <b>{$echecs}</b><br /><a target='_blank' href='".$lien.$report_filename."' download='".$report_filename."'>Cliquez ici pour télécharger le fichier</a>";
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
                                                                $message = "Une erreur est survenue lors de l'edition des actes.";
                                                                $json = array(
                                                                    'success' => false,
                                                                    'message' => $message
                                                                );
                                                            }


                                                        }
                                                        else {
                                                            $message = "Une erreur est survenue lors de l'edition des factures.";
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
                                        $message = "Erreur dans l'entête du fichier.";
                                        $json = array(
                                            'success' => false,
                                            'message' => $message
                                        );
                                    }
                                }
                                elseif($type_ref == 'DECRET') {
                                    $entete_fichier = $xmlTransform->ENTETE_FICHIER;
                                    $tableau_entete = array(
                                        'code_norme' => trim($entete_fichier->CODE_NORME),
                                        'version_norme' => trim($entete_fichier->VERSION_NORME),
                                        'date_fichier' => trim($entete_fichier->DATE_FICHIER),
                                        'num_fichier' => trim($entete_fichier->NUM_FICHIER),
                                        'type_emetteur' => trim($entete_fichier->TYPE_EMETTEUR),
                                        'num_emetteur' => trim($entete_fichier->NUM_EMETTEUR),
                                        'type_destinataire' => trim($entete_fichier->TYPE_DESTINATAIRE),
                                        'num_destinataire' => trim($entete_fichier->NUM_DESTINATAIRE),
                                        'occurrences_decompte' => trim($entete_fichier->OCCURRENCES_DECOMPTE)
                                    );
                                    $path = $path_export.$type_ref.'/'.date('d_m_Y',time()).'/';
                                    $lien = $lien_export.$type_ref.'/'.date('d_m_Y',time()).'/';
                                    if(!file_exists($path)){
                                        mkdir($path,0777,true);
                                    }
                                    $report_filename = "RAPPORT_INTEGRATION_".str_replace('.txt','',str_replace('.xml','',str_replace('.XML','',$filename)))."_".date('dmYHis').".txt";

                                    $nb_tableau_entete = count($tableau_entete);
                                    if($nb_tableau_entete != 0)  {
                                        $fichier_rapport = fopen($path.$report_filename, "w") or die("Unable to open file!");
                                        $donnees_rapport = date('d-m-Y H:i:s',time())." DEBUT DE LECTURE DU FICHIER {$filename}\n\n";
                                        fwrite($fichier_rapport, $donnees_rapport);

                                        $lecture_entetes = $SCRIPTS->lecture_entete_fichier_xml($tableau_entete['code_norme'],$tableau_entete['version_norme'],NULL,$tableau_entete['num_emetteur'],$tableau_entete['type_emetteur'],$tableau_entete['num_destinataire'],$tableau_entete['type_destinataire'],$tableau_entete['num_fichier'],$tableau_entete['date_fichier'],$tableau_entete['occurrences_decompte'],$filename);
                                        $nb_retours_lecture_entete = (count($lecture_entetes) - 1);
                                        $donnees_decomptes = null;
                                        $nb_erreurs = 0;

                                        if($nb_retours_lecture_entete == 0) {
                                            $ligne_decompte = 0;
                                            foreach($entete_fichier->RETOUR_DECOMPTE AS $retour_decompte) {
                                                $donnees_decomptes[] = array(
                                                    'lien_archivage' => trim($retour_decompte->LIEN_ARCHIVAGE),
                                                    'date_liquidation' => trim($retour_decompte->DATE_LIQUIDATION),
                                                    'num_secu_beneficiaire' => trim($retour_decompte->NUM_SECU_BENEFICIAIRE),
                                                    'code_etablissement' => trim($retour_decompte->CODE_ETABLISSEMENT),
                                                    'date_facture' => trim($retour_decompte->DATE_FACTURE),
                                                    'num_facture' => trim($retour_decompte->NUM_FACTURE),
                                                    'num_dossier_soins' => trim($retour_decompte->NUM_DOSSIER_SOINS),
                                                    'code_validation' => trim($retour_decompte->CODE_VALIDATION),
                                                    'occurrences_ligne_acte' => trim($retour_decompte->OCCURRENCES_LIGNE_ACTE)
                                                );

                                                for ($ligne_acte = 0; $ligne_acte < $donnees_decomptes[$ligne_decompte]['occurrences_ligne_acte']; $ligne_acte++) {
                                                    $REJET_LIGNE_ACTE = $retour_decompte->REJET_LIGNE_ACTE[$ligne_acte];
                                                    $donnees_rejets_actes[] = array(
                                                        'num_facture' => trim($retour_decompte->NUM_FACTURE),
                                                        'num_ligne_acte' => trim($REJET_LIGNE_ACTE->NUM_LIGNE_ACTE),
                                                        'date_debut_soins' => trim($REJET_LIGNE_ACTE->DATE_DEBUT_SOINS),
                                                        'date_fin_soins' => trim($REJET_LIGNE_ACTE->DATE_FIN_SOINS),
                                                        'code_acte' => trim($REJET_LIGNE_ACTE->CODE_ACTE),
                                                        'code_complement_acte' => trim($REJET_LIGNE_ACTE->CODE_COMPLEMENT_ACTE),
                                                        'code_executant' => trim($REJET_LIGNE_ACTE->CODE_EXECUTANT),
                                                        'montant_depense' => trim($REJET_LIGNE_ACTE->MONTANT_DEPENSE),
                                                        'montant_remboursement_ro' => trim($REJET_LIGNE_ACTE->MONTANT_REMBOURSEMENT_RO),
                                                        'signe' => trim($REJET_LIGNE_ACTE->SIGNE),
                                                        'code_rejet' => trim($REJET_LIGNE_ACTE->CODE_REJET),
                                                        'libelle_rejet' => trim($REJET_LIGNE_ACTE->LIBELLE_REJET)
                                                    );
                                                }
                                                $ligne_decompte++;
                                            }

                                            $nb_donnees_decomptes = count($donnees_decomptes);
                                            if($nb_donnees_decomptes == $tableau_entete['occurrences_decompte']) {
                                                $nb_lignes_actes = 0;
                                                foreach ($donnees_decomptes as $donnee_decompte) {
                                                    $nb_lignes_actes = $nb_lignes_actes + intval($donnee_decompte['occurrences_ligne_acte']);
                                                    $lectures_decomptes = $FACTURES->lecture_decompte($tableau_entete['code_norme'],$donnee_decompte['lien_archivage'],NULL,$donnee_decompte['date_liquidation'],$donnee_decompte['num_secu_beneficiaire'],NULL,NULL,NULL,NULL,NULL,$donnee_decompte['code_etablissement'],NULL,NULL,NULL,NULL,$donnee_decompte['date_facture'],$donnee_decompte['num_facture'],NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,$donnee_decompte['occurrences_ligne_acte'],$donnee_decompte['code_validation']);
                                                    $nb_retours_decomptes = count($lectures_decomptes);
                                                    if($nb_retours_decomptes!= 0) {
                                                        $ligne_decompte = 0;
                                                        foreach ($lectures_decomptes as $lecture_decompte) {
                                                            if($lecture_decompte['message']) {
                                                                $donnees_rapport = "Erreur facture n° {$donnee_decompte['num_facture']}: {$lecture_decompte['message']}\n";
                                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                                $nb_erreurs++;
                                                            }
                                                            $ligne_decompte++;
                                                        }
                                                    }
                                                }

                                                if($nb_lignes_actes == count($donnees_rejets_actes)) {
                                                    $ligne_acte = 0;
                                                    foreach ($donnees_rejets_actes as $donnee_rejet_acte) {
                                                        $lectures_lignes_actes = $FACTURES->lecture_ligne_acte($tableau_entete['code_norme'],$donnee_rejet_acte['num_facture'],$donnee_rejet_acte['num_ligne_acte'],$donnee_rejet_acte['date_debut_soins'],$donnee_rejet_acte['date_debut_soins'],$donnee_rejet_acte['code_acte'],$donnee_rejet_acte['code_complement_acte'],$donnee_rejet_acte['code_executant'],$donnee_rejet_acte['montant_depense'],$donnee_rejet_acte['montant_remboursement_ro'],$donnee_rejet_acte['signe'],$donnee_rejet_acte['code_rejet'],$donnee_rejet_acte['libelle_rejet'],NULL,NULL,NULL,NULL,NULL,NULL,NULL,$donnee_rejet_acte['montant_depense'],NULL,NULL,NULL,NULL,NULL,$donnee_rejet_acte['montant_remboursement_ro'],$donnee_rejet_acte['signe'],$donnee_rejet_acte['code_rejet'],$donnee_rejet_acte['libelle_rejet']);
                                                        if($lectures_lignes_actes['message']) {
                                                            $donnees_rapport = "Erreur acte de facture n° {$donnee_rejet_acte['num_facture']}: {$lectures_lignes_actes['message']}\n";
                                                            fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                            $nb_erreurs++;
                                                        }
                                                        $ligne_acte++;
                                                    }
                                                }
                                                else {
                                                    $nb_erreurs++;
                                                    $donnees_rapport = "Erreur nombre lignes actes: Le nombre d'occurences des lignes d'actes défini dans l'entête est différent de l'effectif des lignes d'actes dans le fichier\n";
                                                    fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                }

                                            }
                                            else {
                                                $nb_erreurs++;
                                                $donnees_rapport = "Erreur nombre décomptes: Le nombre d'occurences des décomptes défini dans l'entête est différent de l'effectif des décomptes dans le fichier\n";
                                                fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                            }
                                        }
                                        else {
                                            foreach ($lecture_entetes as $lecture_entete) {
                                                if($lecture_entete['message']) {
                                                    $donnees_rapport = "Erreur entete: {$lecture_entete['message']}\n";
                                                    fwrite($fichier_rapport, utf8_decode($donnees_rapport));
                                                    $nb_erreurs++;
                                                }
                                            }
                                        }


                                        $donnees_rapport = "\n\n\n".date('d-m-Y H:i:s',time())." FIN DE LECTURE DU FICHIER {$filename}\n";
                                        fwrite($fichier_rapport, $donnees_rapport);
                                        $donnees_rapport = "NOMBRE D'ERREURS IDENTIFIEES: {$nb_erreurs}\n\n";
                                        fwrite($fichier_rapport, $donnees_rapport);

                                        if($nb_erreurs != 0) {
                                            $donnees_rapport = "LE TRAITEMENT A ETE INTERROMPU.\n";
                                            $message = $donnees_rapport."NOM DU FICHIER: <b>{$filename}</b><a target='_blank' href='".$lien.$report_filename."' download='".$report_filename."'>Cliquez ici pour télécharger le fichier</a>";
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
                                                    $nouveau_script = $SCRIPTS->ajouter('SCRIPT_CHARGEMENT_DECRET',date('Y-m-d H:i:s',time()),NULL,'ENC','CHARGEMENT DU DECRET NOMME: '.$filename,$utilisateur['user_id']);
                                                    if($nouveau_script['success'] == true) {
                                                        $donnees_rapport = date('d-m-Y H:i:s',time())." DEBUT DU TRAITEMENT DU FICHIER {$filename}\n\n";
                                                        fwrite($fichier_rapport, $donnees_rapport);
                                                        $succes = 0;
                                                        $echecs = 0;
                                                        $creation = 0;
                                                        $maj = 0;
                                                        $sup = 0;

                                                        foreach ($donnees_decomptes as $donnee_decompte) {
                                                            $edition_facture = $FACTURES->edition_facture($tableau_entete['code_organisme_dest'],NULL,$tableau_entete['num_fichier'],NULL,date('Y-m-d',strtotime(str_replace('/','-',$tableau_entete['date_fichier']))),NULL,NULL,NULL,$donnee_decompte['num_facture'],NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,$donnee_decompte['code_validation'],$utilisateur['user_id']);
                                                            if($edition_facture['success'] == true) {
                                                                $succes++;
                                                            }else {
                                                                $echecs++;
                                                            }
                                                        }
                                                        if($echecs == 0) {
                                                            $succes_actes = 0;
                                                            $echecs_actes = 0;
                                                            foreach ($donnees_rejets_actes as $donnee_rejet_acte) {
                                                                $edition_facture = $FACTURES->edition_acte($donnee_rejet_acte['num_facture'],date('Y-m-d',strtotime(str_replace('/','-',$donnee_rejet_acte['date_debut_soins']))),date('Y-m-d',strtotime(str_replace('/','-',$donnee_rejet_acte['date_fin_soins']))),$donnee_rejet_acte['code_acte'],$donnee_rejet_acte['code_complement_acte'],NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,$donnee_rejet_acte['code_executant'],NULL,NULL,NULL,NULL,$donnee_rejet_acte['montant_depense'],NULL,NULL,NULL,NULL,NULL,$donnee_rejet_acte['montant_remboursement_ro'],$donnee_rejet_acte['signe'],$donnee_rejet_acte['code_rejet'],$utilisateur['user_id']);
                                                                if($edition_facture['success'] == true) {
                                                                    $succes_actes++;
                                                                }else {
                                                                    $echecs_actes++;
                                                                }
                                                            }
                                                            if($echecs_actes == 0) {
                                                                $log_chargement = $LOGS->ajouter_chargement_fichier('IMP',$filename,'FIN',"FIN DE CHARGEMENT",$utilisateur['user_id']);
                                                                if($log_chargement['success'] == true) {
                                                                    $historique = $LOGS->ajouter_historique_fichier('IMP',$tableau_entete['num_fichier'],$tableau_entete['code_norme'],$tableau_entete['version_norme'],date('Y-m-d',strtotime(str_replace('/','-',$tableau_entete['date_fichier']))),$tableau_entete['num_emetteur'],$tableau_entete['num_destinataire'],($creation+$maj),$creation,$maj,$filename,$utilisateur['user_id']);
                                                                    if($historique['success'] == true) {
                                                                        $mise_a_jour_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],$log_lecture['id_log'],$log_chargement['id_log'],date('Y-m-d H:i:s',time()),'FIN',"CHARGEMENT DU DECRET NOMME: {$filename} EFFECTUE AVEC SUCCES",$utilisateur['user_id']);
                                                                        if($mise_a_jour_script['success'] == true) {
                                                                            $donnees_rapport = date('d-m-Y H:i:s',time())." FIN DU TRAITEMENT DU FICHIER {$filename}\n\nFICHIER CHARGE AVEC SUCCES\nNOM DU FICHIER: {$filename}\nNOMBRE DE LIGNES: ".($succes+$echecs)."\nREUSSITE: {$succes}\nCREATION: {$creation}\nMISE A JOUR: {$maj}\nECHECS: {$echecs}";
                                                                            fwrite($fichier_rapport, $donnees_rapport);
                                                                            fclose($fichier_rapport);
                                                                            $message = "FICHIER CHARGE AVEC SUCCES.<br />NOM DU FICHIER: <b>{$filename}</b><br />NOMBRE DE LIGNES: <b>".($succes+$echecs)."</b><br />REUSSITE: <b>{$succes}</b></b><br />ECHECS: <b>{$echecs}</b><br /><a target='_blank' href='".$lien.$report_filename."' download='".$report_filename."'>Cliquez ici pour télécharger le fichier</a>";
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
                                                                $message = "Une erreur est survenue lors de l'edition des actes.";
                                                                $json = array(
                                                                    'success' => false,
                                                                    'message' => $message
                                                                );
                                                            }


                                                        }
                                                        else {
                                                            $message = "Une erreur est survenue lors de l'edition des factures.";
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
                                        $message = "Erreur dans l'entête du fichier.";
                                        $json = array(
                                            'success' => false,
                                            'message' => $message
                                        );
                                    }
                                }
                                else {
                                    $message = 'Le type de chargement <b>.'.$type_ref.'</b> n\'est pas reconnu pour ce type de fichier par le système.';
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
                        }
                        else {
                            $json = $log_lecture;
                        }
                    }
                    else{
                        $message = 'L\'extension <b>.'.$extension.'</b> n\'est pas accepté par le système.';
                        $json = array(
                            'success' => false,
                            'message' => $message
                        );
                    }
                }
                else {
                    $message = 'Le fichier sélectionné est introuvable pour chargement.';
                    $json = array(
                        'success' => false,
                        'message' => $message
                    );
                }
            }
            else {
                $message = 'Le fichier sélectionné semble êtree corrompu, Veuillez en sélectionner un autre.';
                $json = array(
                    'success' => false,
                    'message' => $message
                );
            }
        }
        else {
            $message = 'Veuiller sélectionner le type de référentiel à charger SVP.';
            $json = array(
                'success' => false,
                'message' => $message
            );
        }

    }
    else {
        $message = 'Aucune session disponible pour cet utilisateur.!!! contactez votre administrateur.';
        $json = array(
            'success' => false,
            'message' => $message
        );
    }
}
else {
    $message = 'Aucune session disponible pour cet utilisateur.!!! contactez votre administrateur.';
    $json = array(
        'success' => false,
        'message' => $message
    );
}
echo json_encode($json);
?>