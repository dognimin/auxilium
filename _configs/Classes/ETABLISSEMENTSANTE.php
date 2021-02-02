<?php


class ETABLISSEMENTSANTE extends BDD
{
    public function trouver($code) {
        $a = $this->bdd->prepare('
            SELECT 
                   ets_code AS code, 
                   ets_raison_sociale AS raison_sociale, 
                   ets_psh_code AS code_psh, 
                   ets_honoraire_code AS secteur_activite, 
                   ets_nom_bureau_distributeur AS ville, 
                   ets_date_debut AS date_debut_validite, 
                   date_reg, 
                   user_reg, 
                   date_edit, 
                   user_edit 
            FROM 
                 auxil_ref_etablissement_sante 
            WHERE 
                  ets_code = ? 
              AND ets_date_fin IS NULL
        ');
        $a->execute(array($code));
        return $a->fetch();
    }

    public function lecture_ficher_txt($type_enregistrement, $zone_reservee, $code_caisse_gestionnaire, $identification_fichier, $programme_emetteur, $date_creation_fichier, $numero_generation, $organisme_emetteur, $organisme_destinataire, $nature_fichier, $numero_chronologique, $code_ets, $code_nature_emetteur, $code_nature_emetteur2, $numero_emetteur, $numero_emetteur2, $code_derniere_utilisation, $code_derniere_utilisation2, $date_derniere_utilisation, $date_derniere_utilisation2, $raison_sociale, $cpam_rattachement, $numero_siret, $numero_telephone, $numero_fax, $code_categorie_psh, $date_categorie_psh, $code_statut_juridique, $date_statut_juridique, $code_convention, $date_convention, $code_honoraire, $date_honoraire, $code_activite, $date_activite, $code_agrement_radio, $date_agrement_radio, $zone_isd_tarif, $zone_ik_tarif, $date_tarif, $code_dern_utilisation_adresse, $complement_adresse_1, $complement_adresse_2, $num_voie, $complement_num_voie, $nature_voie, $libelle_voie,$nom_commune, $num_bureau_distributeur, $nom_bureau_distributeur, $adresse_email) {
        $norme = 'CNAMREFETS01';
        $LOGS = new LOGS();
        if($type_enregistrement) {
            if(strlen($type_enregistrement.$zone_reservee.$code_caisse_gestionnaire.$identification_fichier.$programme_emetteur.str_replace('-','',$date_creation_fichier).$numero_generation.$organisme_emetteur.$organisme_destinataire.$nature_fichier.$numero_chronologique) == 50) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[0] = array(
                    'message' => "L'entete du fichier est incompatible avec la documentation fournie."
                );
            }
            if($type_enregistrement == '000') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[1] = array(
                    'message' => "Le type d'enregistrement: {$type_enregistrement} du fichier est incorrect."
                );
            }
            if($zone_reservee == '000000000') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[2] = array(
                    'message' => "La zone réservée: {$zone_reservee} du fichier est incorrecte."
                );
            }
            if($code_caisse_gestionnaire == '000000') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[3] = array(
                    'message' => "Le code caisse du gestionnaire: {$code_caisse_gestionnaire} du fichier est incorrect."
                );
            }
            if(!$identification_fichier) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[4] = array(
                    'message' => "L'identification: {$identification_fichier} du fichier est incorrect."
                );
            }
            if(!$programme_emetteur) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[5] = array(
                    'message' => "Le programme emetteur: {$programme_emetteur} du fichier est incorrect."
                );
            }
            if(strlen($date_creation_fichier) == 8) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[6] = array(
                    'message' => "La date création du fichier: {$date_creation_fichier} du fichier est incorrecte."
                );
            }
            if(strlen($numero_generation) == 7) {
                $version_fichier = $LOGS->trouver_historique_version_fichier($norme,$numero_generation);
                if($version_fichier) {
                    $json[7] = array(
                        'message' => "La version: {$numero_generation} a déjà été utilisée pour un autre chargement"
                    );
                }else {
                    $json = array(
                        'message' => NULL
                    );
                }
            }else {
                $json[7] = array(
                    'message' => "La date création du fichier: {$date_creation_fichier} du fichier est incorrecte."
                );
            }
            if($organisme_emetteur == 'CNAM') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[8] = array(
                    'message' => "L'organisme emetteur: {$organisme_emetteur} du fichier est incorrect."
                );
            }
            if($organisme_destinataire == 'ETABLIS') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[9] = array(
                    'message' => "L'organisme destinataire: {$organisme_destinataire} du fichier est incorrect."
                );
            }
            if($nature_fichier == 'D') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[10] = array(
                    'message' => "La nature': {$nature_fichier} du fichier est incorrecte."
                );
            }
            if(strlen($numero_chronologique) == 5) {
                $num_fichier = $LOGS->trouver_fichier($norme,$numero_chronologique,NULL,NULL);
                if($num_fichier) {
                    $json[11] = array(
                        'message' => "Le numéro chronologique: {$numero_chronologique} a déjà été utilisé pour un autre chargement"
                    );
                }else {
                    $json = array(
                        'message' => NULL
                    );
                }
            }else {
                $json[11] = array(
                    'message' => "Le numero chronologique: {$numero_chronologique} du fichier est incorrect."
                );
            }
        }
        if($code_ets) {
            if(strlen($code_ets) == 9) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[0] = array(
                    'message' => "Le code de l'etablissement: {$code_ets} est incorrect."
                );
            }
            if($code_nature_emetteur == 'C') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[1] = array(
                    'message' => "Le code nature de l'emetteur: {$code_nature_emetteur} est incorrect."
                );
            }
            if($numero_emetteur == '000') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[2] = array(
                    'message' => "Le numero de l'emetteur: {$numero_emetteur} est incorrect."
                );
            }
            if($code_derniere_utilisation == 'C') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[3] = array(
                    'message' => "Le code derniere utilisation: {$code_derniere_utilisation} est incorrect."
                );
            }
            if(strlen(str_replace('-','',$date_derniere_utilisation)) == 8) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[5] = array(
                    'message' => "La date derniere utilisation: {$date_derniere_utilisation} est incorrecte."
                );
            }
            if($raison_sociale) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[6] = array(
                    'message' => "La raison sociale de l'etablissement: {$raison_sociale} est incorrecte."
                );
            }
            if($cpam_rattachement == '001') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[7] = array(
                    'message' => "Le CPAM rattachement: {$cpam_rattachement} est incorrect."
                );
            }
            if(strlen($code_categorie_psh) == 2) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[8] = array(
                    'message' => "Le code categorie PSH: {$code_categorie_psh} est incorrect."
                );
            }
            if(strlen(str_replace('-','',$date_categorie_psh)) == 8) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[9] = array(
                    'message' => "La date categorie PSH: {$date_categorie_psh} est incorrecte."
                );
            }
            if(strlen($code_statut_juridique) == 2) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[10] = array(
                    'message' => "Le code statut juridique: {$code_statut_juridique} est incorrect."
                );
            }
            if(strlen(str_replace('-','',$date_statut_juridique)) == 8) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[11] = array(
                    'message' => "La date statut juridique: {$date_statut_juridique} est incorrecte."
                );
            }
            if(strlen($code_convention) == 2) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[12] = array(
                    'message' => "Le code convention: {$code_convention} est incorrect."
                );
            }
            if(strlen(str_replace('-','',$date_convention)) == 8) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[13] = array(
                    'message' => "La date convention: {$date_convention} est incorrecte."
                );
            }
            if(strlen($code_honoraire) == 1) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[14] = array(
                    'message' => "Le code honoraire: {$code_honoraire} est incorrect."
                );
            }
            if(strlen(str_replace('-','',$date_honoraire)) == 8) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[15] = array(
                    'message' => "La date honoraire: {$date_honoraire} est incorrecte."
                );
            }
            if($code_nature_emetteur2 == 'C') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[16] = array(
                    'message' => "Le code nature de l'emetteur 2: {$code_nature_emetteur2} est incorrect."
                );
            }
            if($numero_emetteur2 == '000') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[17] = array(
                    'message' => "Le numero de l'emetteur 2: {$numero_emetteur2} est incorrect."
                );
            }
            if($code_derniere_utilisation2 == 'C') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[18] = array(
                    'message' => "Le code derniere utilisation 2: {$code_derniere_utilisation2} est incorrect."
                );
            }
            if(strlen(str_replace('-','',$date_derniere_utilisation2)) == 8) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[19] = array(
                    'message' => "La date derniere utilisation 2: {$date_derniere_utilisation2} est incorrecte."
                );
            }
            if(strlen($code_activite) == 1) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[20] = array(
                    'message' => "Le code activite: {$code_activite} est incorrect."
                );
            }
            if(strlen(str_replace('-','',$date_activite)) == 8) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[21] = array(
                    'message' => "La date activite: {$date_activite} est incorrecte."
                );
            }
            if(strlen($code_agrement_radio) == 1) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[22] = array(
                    'message' => "Le code agrement radio: {$code_agrement_radio} est incorrect."
                );
            }
            if(strlen(str_replace('-','',$date_agrement_radio)) == 8) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[23] = array(
                    'message' => "La date agrement radio: {$date_agrement_radio} est incorrecte."
                );
            }
            if(strlen($zone_isd_tarif) == 2) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[24] = array(
                    'message' => "Le tarif de la zone ISD: {$zone_isd_tarif} est incorrect."
                );
            }
            if(strlen($zone_ik_tarif) == 1) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[25] = array(
                    'message' => "Le tarif de la zone IK: {$zone_ik_tarif} est incorrect."
                );
            }
            if(strlen(str_replace('-','',$date_tarif)) == 8) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[26] = array(
                    'message' => "La date du tarif: {$date_tarif} est incorrecte."
                );
            }
        }
        return $json;
    }

    public function lecture_fichier_xml($code,$raison_sociale,$ville,$code_secteur_activite,$adresse_geographique,$num_telephone_1,$date_debut_validite,$date_fin_validite) {
        if($code) {
            if(strlen($code) == 9) {
                $ets = $this->trouver($code);
                if(!$ets['code']) {
                    $json[0] = NULL;
                }else {
                    if(strtotime($ets['date_debut_validite']) != strtotime(date('Y-m-d',strtotime($date_debut_validite)))) {
                        $json[0] = NULL;
                    }else {
                        $json[1] = array(
                            'message' => "L'établissement a déjà été enregistré à la date {$date_debut_validite}"
                        );
                    }
                }
            }else {
                $json[1] = array(
                    'message' => "La longueur du code établissement ne correspond pas à celle requise."
                );
            }
        }else {
            $json[1] = array(
                'message' => "Le code de l'établissement: {$raison_sociale} n'est pas renseigné."
            );
        }
        if($raison_sociale) {
            $json[0] = NULL;
        }else {
            $json[2] = array(
                'message' => "La raison sociale de l'établissement n'est pas renseignée."
            );
        }
        if($ville) {
            $json[0] = NULL;
        }else {
            $json[3] = array(
                'message' => "La ville de l'établissement n'est pas renseignée."
            );
        }
        if($code_secteur_activite) {
            $json[0] = NULL;
        }else {
            $json[4] = array(
                'message' => "Le code du secteur d'activité de l'établissement n'est pas renseigné."
            );
        }
        /*if($adresse_geographique) {
            $json[0] = NULL;
        }else {
            $json[5] = array(
                'message' => "L'adresse géographique de l'établissement n'est pas renseignée."
            );
        }
        if($num_telephone_1) {
            $json[0] = NULL;
        }else {
            $json[6] = array(
                'message' => "Le numéro de téléphone de l'établissement n'est pas renseigné."
            );
        }*/
        if($date_debut_validite) {
            if(checkdate(date('m',strtotime($date_debut_validite)),date('d',strtotime($date_debut_validite)),date('Y',strtotime($date_debut_validite)))) {
                $json[0] = NULL;
            }else {
                $json[7] = array(
                    'message' => "Le format de la date de début de la validité de l'établissement est incorrect."
                );
            }
        }else {
            $json[7] = array(
                'message' => "La date de début de la validité de l'établissement n'est pas renseignée."
            );
        }
        if(!empty($date_fin_validite)) {
            if(checkdate(date('m',strtotime($date_fin_validite)),date('d',strtotime($date_fin_validite)),date('Y',strtotime($date_fin_validite)))) {
                if($date_debut_validite == $date_fin_validite) {
                    $json[8] = array(
                        'message' => "Les dates de début: {$date_debut_validite} et de fin: {$date_fin_validite} de la validité de l'établissement sont incorrects."
                    );
                }else {
                    $json[0] = NULL;
                }
            }else {
                $json[8] = array(
                    'message' => "Le format de la date de fin de la validité de l'établissement: {$code} est incorrect."
                );
            }
        }else {
            $json[0] = NULL;
        }
        return $json;
    }

    public function ajouter($date_creation_fichier, $numero_generation, $code_ets, $code_nature_emetteur, $code_nature_emetteur2, $numero_emetteur, $numero_emetteur2, $code_derniere_utilisation, $code_derniere_utilisation2, $date_derniere_utilisation, $date_derniere_utilisation2, $raison_sociale, $cpam_rattachement, $numero_siret, $numero_telephone, $numero_fax, $code_categorie_psh, $date_categorie_psh, $code_statut_juridique, $date_statut_juridique, $code_convention, $date_convention, $code_honoraire, $date_honoraire, $code_activite, $date_activite, $code_agrement_radio, $date_agrement_radio, $zone_isd_tarif, $zone_ik_tarif, $date_tarif, $date_dern_utilisation_adresse, $complement_adresse_1, $complement_adresse_2, $num_voie, $complement_num_voie, $nature_voie, $libelle_voie,$nom_commune, $num_bureau_distributeur, $nom_bureau_distributeur, $adresse_email, $user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_ref_etablissement_sante(fichier_num, ets_code, ets_emetteur_code_nature1, ets_emetteur_code_nature2, ets_emetteur_num1, ets_emetteur_num2, ets_derniere_utilisation_code1, ets_derniere_utilisation_code2, ets_derniere_utilisation_date1, ets_derniere_utilisation_date2, ets_raison_sociale, ets_cpam_rattachement, ets_num_siret, ets_num_telephone, ets_num_fax, ets_psh_code, ets_psh_date_effet, ets_statut_juridique_code, ets_statut_juridique_date_efftet, ets_convention_code, ets_convention_date_effet, ets_honoraire_code, ets_honoraire_date_effet, ets_activite_code, ets_activite_date_effet, ets_agrement_radio_code, ets_agrement_radio_date_effet, ets_tarif_zone_isd, ets_tarif_zone_ik, ets_tarif_date_effet, ets_date_export, ets_cplt_adresse_1, ets_cplt_adresse_2, ets_num_voie, ets_cplt_num_voie, ets_nature_voie, ets_nom_voie, ets_nom_commune, ets_num_bureau_distributeur, ets_nom_bureau_distributeur, ets_email, ets_date_debut, user_reg)
                                 VALUES(:fichier_num, :ets_code, :ets_emetteur_code_nature1, :ets_emetteur_code_nature2, :ets_emetteur_num1, :ets_emetteur_num2, :ets_derniere_utilisation_code1, :ets_derniere_utilisation_code2, :ets_derniere_utilisation_date1, :ets_derniere_utilisation_date2, :ets_raison_sociale, :ets_cpam_rattachement, :ets_num_siret, :ets_num_telephone, :ets_num_fax, :ets_psh_code, :ets_psh_date_effet, :ets_statut_juridique_code, :ets_statut_juridique_date_efftet, :ets_convention_code, :ets_convention_date_effet, :ets_honoraire_code, :ets_honoraire_date_effet, :ets_activite_code, :ets_activite_date_effet, :ets_agrement_radio_code, :ets_agrement_radio_date_effet, :ets_tarif_zone_isd, :ets_tarif_zone_ik, :ets_tarif_date_effet, :ets_date_export, :ets_cplt_adresse_1, :ets_cplt_adresse_2, :ets_num_voie, :ets_cplt_num_voie, :ets_nature_voie, :ets_nom_voie, :ets_nom_commune, :ets_num_bureau_distributeur, :ets_nom_bureau_distributeur, :ets_email, :ets_date_debut, :user_reg)');
        $a->execute(array(
            'fichier_num' => $numero_generation,
            'ets_code' => $code_ets,
            'ets_emetteur_code_nature1' => $code_nature_emetteur,
            'ets_emetteur_code_nature2' => $code_nature_emetteur2,
            'ets_emetteur_num1' => $numero_emetteur,
            'ets_emetteur_num2' => $numero_emetteur2,
            'ets_derniere_utilisation_code1' => $code_derniere_utilisation,
            'ets_derniere_utilisation_code2' => $code_derniere_utilisation2,
            'ets_derniere_utilisation_date1' => $date_derniere_utilisation,
            'ets_derniere_utilisation_date2' => $date_derniere_utilisation2,
            'ets_raison_sociale' => $raison_sociale,
            'ets_cpam_rattachement' => $cpam_rattachement,
            'ets_num_siret' => $numero_siret,
            'ets_num_telephone' => $numero_telephone,
            'ets_num_fax' => $numero_fax,
            'ets_psh_code' => $code_categorie_psh,
            'ets_psh_date_effet' => $date_categorie_psh,
            'ets_statut_juridique_code' => $code_statut_juridique,
            'ets_statut_juridique_date_efftet' => $date_statut_juridique,
            'ets_convention_code' => $code_convention,
            'ets_convention_date_effet' => $date_convention,
            'ets_honoraire_code' => $code_honoraire,
            'ets_honoraire_date_effet' => $date_honoraire,
            'ets_activite_code' => $code_activite,
            'ets_activite_date_effet' => $date_activite,
            'ets_agrement_radio_code' => $code_agrement_radio,
            'ets_agrement_radio_date_effet' => $date_agrement_radio,
            'ets_tarif_zone_isd' => $zone_isd_tarif,
            'ets_tarif_zone_ik' => $zone_ik_tarif,
            'ets_tarif_date_effet' => $date_tarif,
            'ets_date_export' => $date_derniere_utilisation,
            'ets_cplt_adresse_1' => $complement_adresse_1,
            'ets_cplt_adresse_2' => $complement_adresse_2,
            'ets_num_voie' => $num_voie,
            'ets_cplt_num_voie' => $complement_num_voie,
            'ets_nature_voie' => $nature_voie,
            'ets_nom_voie' => $libelle_voie,
            'ets_nom_commune' => $nom_commune,
            'ets_num_bureau_distributeur' => $num_bureau_distributeur,
            'ets_nom_bureau_distributeur' => $nom_bureau_distributeur,
            'ets_email' => $adresse_email,
            'ets_date_debut' => $date_creation_fichier,
            'user_reg' => $user
        ));
        if($a->errorCode() === '00000') {
            $json = array(
                'success' => true
            );
        }else {
            $json = array(
                'success' => false,
                'message' => 'Erreur: '.$a->errorCode().' <=> '.$a->errorInfo()[1].' <=> '.$a->errorInfo()[2]
            );
        }
        return $json;
    }

    public function mise_a_jour($date_debut_validite, $date_fin_validite, $code_ets, $user) {
        $a = $this->bdd->prepare('UPDATE auxil_ref_etablissement_sante SET ets_date_fin = ?, date_edit = ?, user_edit = ? WHERE ets_code = ? AND ets_date_debut = ?');
        $a->execute(array($date_fin_validite,date('Y-m-d H:i:s',time()),$user,$code_ets,$date_debut_validite));
        if($a->errorCode() === '00000') {
            $json = array(
                'success' => true
            );
        }else {
            $json = array(
                'success' => false,
                'message' => 'Erreur: '.$a->errorCode().' <=> '.$a->errorInfo()[1].' <=> '.$a->errorInfo()[2]
            );
        }
        return $json;
    }

    public function edition($date_creation_fichier, $numero_generation, $code_ets, $code_nature_emetteur, $code_nature_emetteur2, $numero_emetteur, $numero_emetteur2, $code_derniere_utilisation, $code_derniere_utilisation2, $date_derniere_utilisation, $date_derniere_utilisation2, $raison_sociale, $cpam_rattachement, $numero_siret, $numero_telephone, $numero_fax, $code_categorie_psh, $date_categorie_psh, $code_statut_juridique, $date_statut_juridique, $code_convention, $date_convention, $code_honoraire, $date_honoraire, $code_activite, $date_activite, $code_agrement_radio, $date_agrement_radio, $zone_isd_tarif, $zone_ik_tarif, $date_tarif, $date_dern_utilisation_adresse, $complement_adresse_1, $complement_adresse_2, $num_voie, $complement_num_voie, $nature_voie, $libelle_voie,$nom_commune, $num_bureau_distributeur, $nom_bureau_distributeur, $adresse_email, $user) {
        $ets = $this->trouver($code_ets);
        if($ets['code']) {
            $type = 1;
            if(strtotime($ets['date_debut_validite']) == strtotime($date_creation_fichier)) {
                $retour = array(
                    'success' => false,
                    'message' => 'Les données contenues dans ce fichier ont déjà été chargées. Veuillez vérifier votre fichier.'
                );
            }else {
                $date_fin_validite = date('Y-m-d', strtotime("-1 days", strtotime($date_creation_fichier)));
                $mise_a_jour = $this->mise_a_jour($ets['date_debut_validite'],$date_fin_validite,$code_ets,$user);
                if($mise_a_jour['success'] == true) {
                    $retour = $this->ajouter($date_creation_fichier,$numero_generation,$code_ets,$code_nature_emetteur,$code_nature_emetteur2,$numero_emetteur,$numero_emetteur2,$code_derniere_utilisation,$code_derniere_utilisation2,$date_derniere_utilisation,$date_derniere_utilisation2,$raison_sociale,$cpam_rattachement,$numero_siret,$numero_telephone,$numero_fax,$code_categorie_psh,$date_categorie_psh,$code_statut_juridique,$date_statut_juridique,$code_convention,$date_convention,$code_honoraire,$date_honoraire,$code_activite,$date_activite,$code_agrement_radio,$date_agrement_radio,$zone_isd_tarif,$zone_ik_tarif,$date_tarif,$date_dern_utilisation_adresse,$complement_adresse_1,$complement_adresse_2,$num_voie,$complement_num_voie,$nature_voie,$libelle_voie,$nom_commune,$num_bureau_distributeur,$nom_bureau_distributeur,$adresse_email,$user);
                }else{
                    $retour = $mise_a_jour;
                }
            }
        }else {
            $type = 0;
            $retour = $this->ajouter($date_creation_fichier,$numero_generation,$code_ets,$code_nature_emetteur,$code_nature_emetteur2,$numero_emetteur,$numero_emetteur2,$code_derniere_utilisation,$code_derniere_utilisation2,$date_derniere_utilisation,$date_derniere_utilisation2,$raison_sociale,$cpam_rattachement,$numero_siret,$numero_telephone,$numero_fax,$code_categorie_psh,$date_categorie_psh,$code_statut_juridique,$date_statut_juridique,$code_convention,$date_convention,$code_honoraire,$date_honoraire,$code_activite,$date_activite,$code_agrement_radio,$date_agrement_radio,$zone_isd_tarif,$zone_ik_tarif,$date_tarif,$date_dern_utilisation_adresse,$complement_adresse_1,$complement_adresse_2,$num_voie,$complement_num_voie,$nature_voie,$libelle_voie,$nom_commune,$num_bureau_distributeur,$nom_bureau_distributeur,$adresse_email,$user);
        }
        if($retour['success' == true]) {
            $json = array(
                'success' => true,
                'type' => $type
            );
        }else {
            $json = $retour;
        }
        return $json;
    }

    public function lister_secteurs_activite() {
        $a = $this->bdd->prepare('SELECT secteur_code AS code,secteur_libelle AS libelle FROM auxil_ref_ets_secteur_activite ORDER BY secteur_libelle');
        $a->execute(array());
        return $a->fetchAll();
    }

    public function lister_villes() {
        $a = $this->bdd->prepare('SELECT DISTINCT(ets_nom_commune) AS ville FROM auxil_ref_etablissement_sante ORDER BY ets_nom_commune ASC');
        $a->execute(array());
        return $a->fetchAll();
    }

    public function moteur_recherche($code,$nom,$secteur,$ville) {
        $a = $this->bdd->prepare('SELECT * FROM auxil_ref_etablissement_sante WHERE ets_code LIKE ? AND ets_raison_sociale LIKE ? AND ets_honoraire_code LIKE ? AND ets_nom_bureau_distributeur LIKE ? AND ets_date_fin IS NULL ORDER BY ets_raison_sociale');
        $a->execute(array('%'.$code.'%','%'.$nom.'%','%'.$secteur.'%','%'.$ville.'%'));
        return $a->fetchAll();
    }

    public function lister_historique($code) {
        $a = $this->bdd->prepare('SELECT * FROM auxil_ref_etablissement_sante WHERE ets_code = ? AND ets_date_fin IS NOT NULL ORDER BY ets_date_fin DESC');
        $a->execute(array($code));
        return $a->fetchAll();
    }
}