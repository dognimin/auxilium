<?php


class PROFESSIONNELSANTE extends BDD
{
    public function trouver($code) {
        $a = $this->bdd->prepare('
        SELECT
          ps_code AS code,
          ps_civilite AS civilite,
          ps_nom AS nom,
          ps_prenom AS prenom,
          categorie_code AS categorie_professionnelle,
          ps_num_voie AS num_voie,
          ps_type_voie AS type_voie,
          ps_btq AS btq,
          nom_voie AS libelle_voie,
          ps_adresse_postale AS adresse_postale,
          ps_ville AS ville,
          ps_code_postal AS code_postal,
          ps_departement AS departement,
          ps_telephone AS telephone,
          ps_fax AS fax,
          ps_email AS email,
          libelle_diplome AS diplome,
          libelle_specialite_diplome AS specialite_diplome,
          annee_obtention_diplome AS annee_obtention_diplome,
          specialite_code AS code_specialite,
          specialite_date_debut AS date_debut_specialite,
          specialite_date_fin AS date_fin_specialite,
          convention_code AS code_convention,
          convention_date_effet AS date_effet_convention,
          convention_motif_fin AS motif_fin_convention,
          nature_exercice_code AS code_nature_exercice,
          nature_exercice_date_debut AS date_debut_nature_exercice,
          nature_exercice_date_fin AS date_fin_nature_exercice,
          nature_exercice_motif_fin AS motif_fin_nature_exercice,
          validite_date_debut AS date_debut_validite,
          date_reg,
          user_reg,
          date_edit,
          user_edit
        FROM
          auxil_ref_professionnel_sante 
        WHERE 
          ps_code = ? AND 
          validite_date_fin IS NULL
        ');
        $a->execute(array($code));
        return $a->fetch();
    }

    public function lecture_ficher_txt($type_enregistrement, $zone_reservee, $code_caisse_gestionnaire, $identification_fichier, $programme_emetteur, $date_creation_fichier, $numero_generation, $organisme_emetteur, $organisme_destinataire, $nature_fichier, $numero_chronologique, $code_ps, $num_immat, $nouveau_num_immat, $cpam_gestionnaire, $civilite, $code_appartenance_cee, $nom_patronymique, $prenom_usuel, $prenom_2, $prenom_3, $nom_usage, $complement_adresse, $complement_adresse_2, $num_voie, $complement_num_voie, $type_voie, $libelle_voie, $libelle_lieu_dit, $code_postal, $libelle_commune, $code_separt_commune , $code_commune, $libelle_pays, $num_telephone, $num_fax, $adresse_email, $code_affiliation, $date_effet_affiliation, $categorie_pam, $date_debut_activite_liberale, $mode_exercice_particulier, $annee_obtention_these, $departement_ville_faculte, $code_commune_ville_faculte, $code_diplome_etudes_specialisees, $annee_obtention_diplome, $code_specialite, $date_debut_specialite, $date_fin_specialite, $code_convention, $date_effet_convention, $motif_sortie_convention, $niveau_cotation_preferentiel, $autorisation_refus_libelle_specialite, $specialite_ddas, $code_orientation, $libelle_orientation, $code_attribution, $libelle_attribution, $code_specialisation, $libelle_specialisation, $inscription_tableau_annexe, $formation_particuliere, $code_titre_professionnel, $code_nature_exercice, $date_debut_nature, $date_fin_nature, $motif_fin_nature_exercice) {
        $norme = 'REFPROF01';
        $LOGS = new LOGS();
        if($type_enregistrement == '000') {
            if(strlen($type_enregistrement.$zone_reservee.$code_caisse_gestionnaire.$identification_fichier.$programme_emetteur.str_replace('-','',$date_creation_fichier).$numero_generation.$organisme_emetteur.$organisme_destinataire.$nature_fichier.$numero_chronologique) == 49) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[0] = array(
                    'message' => "L'entete du fichier est incompatible avec la documentation fournie."
                );
            }
            if($zone_reservee == '000000000') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[1] = array(
                    'message' => "La zone réservée: {$zone_reservee} du fichier est incorrecte."
                );
            }
            if($code_caisse_gestionnaire == '000000') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[2] = array(
                    'message' => "Le code caisse du gestionnaire: {$code_caisse_gestionnaire} du fichier est incorrect."
                );
            }
            if(!$identification_fichier) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[3] = array(
                    'message' => "L'identification: {$identification_fichier} du fichier est incorrect."
                );
            }
            if(!$programme_emetteur) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[4] = array(
                    'message' => "Le programme emetteur: {$programme_emetteur} du fichier est incorrect."
                );
            }
            if(strlen($date_creation_fichier) == 8) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[5] = array(
                    'message' => "La date création du fichier: {$date_creation_fichier} du fichier est incorrecte."
                );
            }
            if(strlen($numero_generation) == 7) {
                $version_fichier = $LOGS->trouver_historique_version_fichier($norme,$numero_generation);
                if($version_fichier) {
                    $json[6] = array(
                        'message' => "La version: {$numero_generation} a déjà été utilisée pour un autre chargement"
                    );
                }else {
                    $json = array(
                        'message' => NULL
                    );
                }
            }else {
                $json[6] = array(
                    'message' => "La date création du fichier: {$date_creation_fichier} du fichier est incorrecte."
                );
            }
            if($organisme_emetteur == 'CNAM') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[7] = array(
                    'message' => "L'organisme emetteur: {$organisme_emetteur} du fichier est incorrect."
                );
            }
            if($organisme_destinataire == 'AUTREGD') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[8] = array(
                    'message' => "L'organisme destinataire: {$organisme_destinataire} du fichier est incorrect."
                );
            }
            if($nature_fichier == '0') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[9] = array(
                    'message' => "La nature': {$nature_fichier} du fichier est incorrecte."
                );
            }
            if(strlen($numero_chronologique) == 4) {
                $num_fichier = $LOGS->trouver_fichier($norme,$numero_chronologique,NULL,NULL);
                if($num_fichier) {
                    $json[10] = array(
                        'message' => "Le numéro chronologique: {$numero_chronologique} a déjà été utilisé pour un autre chargement"
                    );
                }else {
                    $json = array(
                        'message' => NULL
                    );
                }
            }else {
                $json[10] = array(
                    'message' => "Le numero chronologique: {$numero_chronologique} du fichier est incorrect."
                );
            }
        }
        if ($type_enregistrement == '110') {
            if(strlen($code_ps) == 9) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[0] = array(
                    'message' => "Le code du professionnel: {$code_ps} est incorrect."
                );
            }
            if($code_caisse_gestionnaire == '000000') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[1] = array(
                    'message' => "Le code caisse du gestionnaire: {$code_caisse_gestionnaire} du fichier est incorrect."
                );
            }
            if($cpam_gestionnaire == '000000') {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[2] = array(
                    'message' => "Le CPAM du gestionnaire: {$cpam_gestionnaire} du fichier est incorrect."
                );
            }
        }
        if ($type_enregistrement == '115') {
            if(strlen($code_ps) == 9) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[0] = array(
                    'message' => "Le code du professionnel: {$code_ps} est incorrect."
                );
            }
        }
        return $json;
    }

    public function lecture_fichier_xml($code, $nom, $prenoms, $code_specialite, $ville, $num_telephone, $date_debut_validite, $date_fin_validite) {
        if($code) {
            if(strlen($code) == 9) {
                $ps = $this->trouver($code);
                if(!$ps['code']) {
                    $json[0] = NULL;
                }else {
                    if(strtotime($ps['date_debut_validite']) != strtotime(date('Y-m-d',strtotime($date_debut_validite)))) {
                        $json[0] = NULL;
                    }else {
                        $json[1] = array(
                            'message' => "Le PS a déjà été enregistré à la date {$date_debut_validite}"
                        );
                    }
                }
            }else {
                $json[1] = array(
                    'message' => "La longueur du code PS ne correspond pas à celle requise."
                );
            }
        }else {
            $json[1] = array(
                'message' => "Le code du PS: {$nom} n'est pas renseigné."
            );
        }
        if($nom && $prenoms) {
            $json[0] = NULL;
        }else {
            $json[2] = array(
                'message' => "Le nom et/ou le prénom du PS ne sont pas renseignés."
            );
        }
        if($code_specialite) {
            $json[0] = NULL;
        }else {
            $json[3] = array(
                'message' => "La spécialité du PS n'est pas renseignée."
            );
        }
        /*if($ville) {
            $json[0] = NULL;
        }else {
            $json[4] = array(
                'message' => "La ville de résidence du PS n'est pas renseignée."
            );
        }
        if($num_telephone) {
            $json[0] = NULL;
        }else {
            $json[5] = array(
                'message' => "Le numéro de téléphone du PS n'est pas renseigné."
            );
        }*/
        if($date_debut_validite) {
            if(checkdate(date('m',strtotime($date_debut_validite)),date('d',strtotime($date_debut_validite)),date('Y',strtotime($date_debut_validite)))) {
                $json[0] = NULL;
            }else {
                $json[6] = array(
                    'message' => "Le format de la date de début de la validité du PS est incorrect."
                );
            }
        }else {
            $json[6] = array(
                'message' => "La date de début de la validité du PS n'est pas renseignée."
            );
        }
        if(!empty($date_fin_validite)) {
            if(checkdate(date('m',strtotime($date_fin_validite)),date('d',strtotime($date_fin_validite)),date('Y',strtotime($date_fin_validite)))) {
                if($date_debut_validite == $date_fin_validite) {
                    $json[7] = array(
                        'message' => "Les dates de début: {$date_debut_validite} et de fin: {$date_fin_validite} de la validité du PS sont incorrects."
                    );
                }else {
                    $json[0] = NULL;
                }
            }else {
                $json[7] = array(
                    'message' => "Le format de la date de fin de la validité du PS est incorrect."
                );
            }
        }else {
            $json[0] = NULL;
        }
        return $json;
    }

    private function ajouter($num_fichier, $date_creation_fichier, $code_ps, $ps_civilite, $ps_nom, $ps_prenom, $categorie_code, $ps_num_voie, $ps_type_voie, $ps_btq, $nom_voie, $ps_adresse_postale, $ville, $code_postal, $departement, $telephone, $fax, $email, $libelle_diplome, $libelle_specialite_diplome, $annee_obtention_diplome, $specialite_code, $specialite_date_debut, $specialite_date_fin, $convention_code, $convention_date_effet, $convention_motif_fin, $nature_exercice_code, $nature_exercice_date_debut, $nature_exercice_date_fin, $nature_exercice_motif_fin, $user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_ref_professionnel_sante(fichier_num, ps_code, ps_civilite, ps_nom, ps_prenom, categorie_code, ps_num_voie, ps_type_voie, ps_btq, nom_voie, ps_adresse_postale, ps_ville, ps_code_postal, ps_departement, ps_telephone, ps_fax, ps_email, libelle_diplome, libelle_specialite_diplome, annee_obtention_diplome, specialite_code, specialite_date_debut, specialite_date_fin, convention_code, convention_date_effet, convention_motif_fin, nature_exercice_code, nature_exercice_date_debut, nature_exercice_date_fin, nature_exercice_motif_fin, validite_date_debut, user_reg)
            VALUES(:fichier_num, :ps_code, :ps_civilite, :ps_nom, :ps_prenom, :categorie_code, :ps_num_voie, :ps_type_voie, :ps_btq, :nom_voie, :ps_adresse_postale, :ps_ville, :ps_code_postal, :ps_departement, :ps_telephone, :ps_fax, :ps_email, :libelle_diplome, :libelle_specialite_diplome, :annee_obtention_diplome, :specialite_code, :specialite_date_debut, :specialite_date_fin, :convention_code, :convention_date_effet, :convention_motif_fin, :nature_exercice_code, :nature_exercice_date_debut, :nature_exercice_date_fin, :nature_exercice_motif_fin, :validite_date_debut, :user_reg)
            ');
        $a->execute(array(
            'fichier_num' => $num_fichier,
            'ps_code' => $code_ps,
            'ps_civilite' => $ps_civilite,
            'ps_nom' => $ps_nom,
            'ps_prenom' => $ps_prenom,
            'categorie_code' => $categorie_code,
            'ps_num_voie' => $ps_num_voie,
            'ps_type_voie' => $ps_type_voie,
            'ps_btq' => $ps_btq,
            'nom_voie' => $nom_voie,
            'ps_adresse_postale' => $ps_adresse_postale,
            'ps_ville' => $ville,
            'ps_code_postal' => $code_postal,
            'ps_departement' => $departement,
            'ps_telephone' => $telephone,
            'ps_fax' => $fax,
            'ps_email' => $email,
            'libelle_diplome' => $libelle_diplome,
            'libelle_specialite_diplome' => $libelle_specialite_diplome,
            'annee_obtention_diplome' => $annee_obtention_diplome,
            'specialite_code' => $specialite_code,
            'specialite_date_debut' => $specialite_date_debut,
            'specialite_date_fin' => $specialite_date_fin,
            'convention_code' => $convention_code,
            'convention_date_effet' => $convention_date_effet,
            'convention_motif_fin' => $convention_motif_fin,
            'nature_exercice_code' => $nature_exercice_code,
            'nature_exercice_date_debut' => $nature_exercice_date_debut,
            'nature_exercice_date_fin' => $nature_exercice_date_fin,
            'nature_exercice_motif_fin' => $nature_exercice_motif_fin,
            'validite_date_debut' => $date_creation_fichier,
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

    private function mise_a_jour($date_debut_validite, $date_fin_validite, $code_ps, $user) {
        $a = $this->bdd->prepare('UPDATE auxil_ref_professionnel_sante SET validite_date_fin = ?, date_edit = ?, user_edit = ? WHERE ps_code = ? AND validite_date_debut = ?');
        $a->execute(array($date_fin_validite,date('Y-m-d H:i:s',time()),$user,$code_ps,$date_debut_validite));
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

    public function edition($type_enregistrement, $num_fichier, $date_creation_fichier, $code_ps, $ps_civilite, $ps_nom, $ps_prenom, $categorie_code, $ps_num_voie, $ps_type_voie, $ps_btq, $nom_voie, $ps_adresse_postale, $ville, $code_postal, $departement, $telephone, $fax, $email, $libelle_diplome, $libelle_specialite_diplome, $annee_obtention_diplome, $specialite_code, $specialite_date_debut, $specialite_date_fin, $convention_code, $convention_date_effet, $convention_motif_fin, $nature_exercice_code, $nature_exercice_date_debut, $nature_exercice_date_fin, $nature_exercice_motif_fin, $user) {
        $ps = $this->trouver($code_ps);
        if($ps['code']) {
            $type = 1;
            if(strtotime($ps['date_debut_validite']) == strtotime($date_creation_fichier)) {
                $retour = array(
                    'success' => true,
                    'type' => 2
                );
            }else {
                $date_fin_validite = date('Y-m-d', strtotime("-1 days", strtotime($date_creation_fichier)));
                $mise_a_jour = $this->mise_a_jour($ps['date_debut_validite'],$date_fin_validite,$code_ps,$user);
                if($mise_a_jour['success'] == true) {
                    $retour = $this->ajouter($num_fichier, $date_creation_fichier, $code_ps, $ps_civilite, $ps_nom, $ps_prenom, $categorie_code, $ps_num_voie, $ps_type_voie, $ps_btq, $nom_voie, $ps_adresse_postale, $ville, $code_postal, $departement, $telephone, $fax, $email, $libelle_diplome, $libelle_specialite_diplome, $annee_obtention_diplome, $specialite_code, $specialite_date_debut, $specialite_date_fin, $convention_code, $convention_date_effet, $convention_motif_fin, $nature_exercice_code, $nature_exercice_date_debut, $nature_exercice_date_fin, $nature_exercice_motif_fin, $user);
                }else{
                    $retour = $mise_a_jour;
                }
            }
        }else {
            $type = 0;
            $retour = $this->ajouter($num_fichier, $date_creation_fichier, $code_ps, $ps_civilite, $ps_nom, $ps_prenom, $categorie_code, $ps_num_voie, $ps_type_voie, $ps_btq, $nom_voie, $ps_adresse_postale, $ville, $code_postal, $departement, $telephone, $fax, $email, $libelle_diplome, $libelle_specialite_diplome, $annee_obtention_diplome, $specialite_code, $specialite_date_debut, $specialite_date_fin, $convention_code, $convention_date_effet, $convention_motif_fin, $nature_exercice_code, $nature_exercice_date_debut, $nature_exercice_date_fin, $nature_exercice_motif_fin, $user);
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

    public function lister_specialites_medicales() {
        $a = $this->bdd->prepare('SELECT specialite_code AS code, specialite_libelle AS libelle, specialite_date_debut AS date_debut, specialite_date_fin AS date_fin FROM auxil_ref_specialites_medicales WHERE specialite_date_fin IS NULL ORDER BY specialite_libelle ASC');
        $a->execute(array());
        return $a->fetchAll();
    }

    public function trouver_specialite_medicale($code) {
        $a = $this->bdd->prepare('SELECT specialite_code AS code, specialite_libelle AS libelle, specialite_date_debut AS date_debut, specialite_date_fin AS date_fin FROM auxil_ref_specialites_medicales WHERE specialite_code = ?');
        $a->execute(array($code));
        return $a->fetch();
    }

    public function lister_villes() {
        $a = $this->bdd->prepare('SELECT DISTINCT(ps_ville) AS ville FROM auxil_ref_professionnel_sante WHERE validite_date_fin IS NULL ORDER BY ps_ville ASC');
        $a->execute(array());
        return $a->fetchAll();
    }

    public function moteur_recherche($code_ps,$nom_prenom,$specialite,$ville){
        $a = $this->bdd->prepare('SELECT * FROM auxil_ref_professionnel_sante WHERE ps_code LIKE ? AND (ps_nom LIKE ? OR ps_prenom LIKE ?) AND specialite_code LIKE ? AND ps_ville LIKE ? AND validite_date_fin IS NULL');
        $a->execute(array('%'.$code_ps.'%','%'.$nom_prenom.'%','%'.$nom_prenom.'%','%'.$specialite.'%','%'.$ville.'%'));
        return $a->fetchAll();
    }

    public function lister_historique($code) {
        $a = $this->bdd->prepare('SELECT * FROM auxil_ref_professionnel_sante WHERE ps_code = ? AND validite_date_fin IS NOT NULL ORDER BY validite_date_debut DESC');
        $a->execute(array($code));
        return $a->fetchAll();
    }
}