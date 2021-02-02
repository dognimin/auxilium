<?php


class ASSURES extends BDD
{
    public function trouver($num_secu) {
        $a = $this->bdd->prepare("SELECT * FROM auxil_assures WHERE num_secu = ?");
        $a->execute(array($num_secu));
        return $a->fetch();
    }

    public function trouver_collectivite($num_secu) {
        $a = $this->bdd->prepare("SELECT * FROM auxil_assures_collectivites WHERE num_secu = ?");
        $a->execute(array($num_secu));
        return $a->fetch();
    }

    public function lister_identifiants($num_secu) {
        $a = $this->bdd->prepare("SELECT identifiant_type AS type, identifiant_numero AS numero, identifiant_date_debut AS date_debut FROM auxil_assures_identifiants WHERE num_secu = ? AND identifiant_date_fin IS NULL");
        $a->execute(array($num_secu));
        return $a->fetchAll();
    }

    public function lister_coordonnees($num_secu) {
        $a = $this->bdd->prepare("SELECT coordonnee_type AS type, coordonnee_valeur AS valeur FROM auxil_assures_coordonnees WHERE num_secu = ?");
        $a->execute(array($num_secu));
        return $a->fetchAll();
    }

    public function lecture_familles($type_mouvement, $date_situation, $occurrences_personne) {
        $json = null;
        $types = array('CRE','MDF','SUP');
        if($type_mouvement) {
            if(in_array($type_mouvement,$types)) {
                $json[0] = NULL;
            }else {
                $json[1] = array(
                    'message' => "Le type mouvement renseigné n'est pas reconnu par le système."
                );
            }
        }else {
            $json[1] = array(
                'message' => "Le type mouvement n'est pas renseigné."
            );
        }
        if($date_situation) {
            if(checkdate(date('m',strtotime($date_situation)),date('d',strtotime($date_situation)),date('Y',strtotime($date_situation)))) {
                $json[0] = NULL;
            }else {
                $json[2] = array(
                    'message' => "Le format de la date situation de la famille est incorrect."
                );
            }
        }else {
            $json[2] = array(
                'message' => "La date situation n'est pas renseignée."
            );
        }
        if($occurrences_personne) {
            if(intval($occurrences_personne)) {
                $json[0] = NULL;
            }else {
                $json[3] = array(
                    'message' => "L'occurrence personne est invalide."
                );
            }
        }else {
            $json[3] = array(
                'message' => "Le nombre de personnes dans la famille n'est pas rensigné."
            );
        }
        return $json;
    }

    public function lecture_personnes($identifiant_personne, $num_secu, $cle_secu, $code_civilite, $nom, $nom_patronymique, $prenom, $code_sexe, $code_qualite, $date_naissance, $rang_gemellaire, $code_nationalite, $code_situation_familiale, $code_cat_socio_professionnelle, $code_grand_regime, $code_caisse, $code_centre_payeur, $date_premiere_immat, $code_cpl_grand_regime, $date_entree_organisme, $date_anciennete_organisme, $code_langue, $code_profession, $code_executant_referent, $date_sortie_organisme, $code_motif_sortie_organisme, $date_deces, $code_statut_personne, $code_agence, $code_commercial, $nom_usage, $occurrences_collectivite, $occurrences_coordonnee, $occurrences_identifiant, $occurrences_lien_personne, $occurrences_contrat, $occurrences_rattachement) {
        if($identifiant_personne) {
            $json[0] = NULL;
        }else {
            $json[1] = array(
                'message' => "La balise IDENTIFIANT_PERSONNE n'est pas renseignée."
            );
        }
        if($num_secu) {
            if(strlen($num_secu) ==  13) {
                $json[0] = NULL;
            }else {
                $json[2] = array(
                    'message' => "Le n° sécu renseigné n'est pas correct."
                );
            }

        }else {
            $json[2] = array(
                'message' => "La balise NUM_SECU n'est pas renseignée."
            );
        }
        return $json;
    }

    public function lecture_coordonnee($num_secu, $code_type_coordonnee, $valeur_coordonnee, $date_debut, $date_fin) {
        if($code_type_coordonnee) {
            $json[0] = NULL;
        }else {
            $json[1] = array(
                'message' => "La balise CODE_TYPE_COORDONNEE n'est pas renseignée."
            );
        }
        return $json;
    }

    public function lecture_identifiant($num_secu, $code_type_identifiant, $num_identifiant, $date_debut, $date_fin){
        if($code_type_identifiant) {
            $json[0] = NULL;
        }else {
            $json[1] = array(
                'message' => "La balise CODE_TYPE_IDENTIFIANT n'est pas renseignée."
            );
        }
        return $json;
    }

    public function lecture_contrat($num_secu, $num_contrat_famille, $code_produit, $code_collectivite, $num_siret, $date_debut, $date_fin) {
        if($num_contrat_famille) {
            $json[0] = NULL;
        }else {
            $json[1] = array(
                'message' => "La balise NUM_CONTRAT_FAMILLE n'est pas renseignée."
            );
        }
        return $json;
    }

    public function lecture_rattachement($num_secu, $num_contrat_famille, $identifiant_contractant, $date_debut, $date_fin, $occurrences_droit) {
        if($num_contrat_famille) {
            $json[0] = NULL;
        }else {
            $json[1] = array(
                'message' => "La balise NUM_CONTRAT_FAMILLE n'est pas renseignée."
            );
        }
        return $json;
    }

    public function lecture_rattachement_droits($num_secu,$contractant,$groupe_acte,$date_debut,$date_fin) {
        if($contractant) {
            $json[0] = NULL;
        }else {
            $json[1] = array(
                'message' => "La balise IDENTIFIANT_CONTRACTANT n'est pas renseignée."
            );
        }
        return $json;
    }

    public function edition_assure($code_ogd, $num_fichier,$type_mouvement,$date_situation,$identifiant_personne,$num_secu,$cle_secu,$civilite,$nom,$nom_patronymique,$prenom,$sexe,$qualite_civile,$date_naissance,$rang_gemellaire,$nationalite,$situation_familiale,$categorie_professionnelle,$grand_regime,$caisse,$centre_payeur,$date_premiere_immatriculation,$cplt_grand_regime,$date_entree_organisme,$date_anciennete_organisme,$langue,$profession,$executant_referent,$date_sortie_organisme,$motif_sortie_organisme,$date_deces,$statut_personne,$code_agence,$code_commercial,$nom_usage,$pays_naissance,$lieu_naissance,$code_secteur_naissance,$type_adresse,$pays_residence,$adresse_normee,$adresse_1,$adresse_2,$num_voie,$cplt_num_voie,$lib_voie,$nom_acheminement_residence,$nom_lieu_dit_residence,$lieu_dit_residence,$adresse_lieu,$adresse_cplt_lieu,$code_postal_residence,$reference_adresse,$user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_assures(ogd_code,num_fichier,type_mouvement,date_situation,identifiant_personne,num_secu,cle_secu,civilite_code,nom,nom_patronymique,prenom,genre_code,qualite_code,date_naissance,rang_gemellaire,nationalite_code,situation_familiale,categorie_professionnelle,grand_regime,caisse,centre_payeur,date_premiere_immatriculation,cplt_grand_regime,date_entree_organisme,date_anciennete_organisme,langue,profession_code,executant_referent,date_sortie_organisme,motif_sortie_organisme,date_deces,statut_personne,code_agence,code_commercial,nom_usage,naissance_pays,naissance_lieu,naissance_code_secteur,adresse_type,adresse_pays,adresse_normee,adresse_1,adresse_2,adresse_num_voie,adresse_cplt_num_voie,adresse_lib_voie,adresse_nom_acheminement,adresse_nom_lieu_dit,adresse_lieu_dit,adresse_lieu,adresse_cplt_lieu,adresse_code_postal,adresse_reference,user_reg)
            VALUES(:ogd_code, :num_fichier, :type_mouvement, :date_situation, :identifiant_personne, :num_secu, :cle_secu, :civilite_code, :nom, :nom_patronymique, :prenom, :genre_code, :qualite_code, :date_naissance, :rang_gemellaire, :nationalite_code, :situation_familiale, :categorie_professionnelle, :grand_regime, :caisse, :centre_payeur, :date_premiere_immatriculation, :cplt_grand_regime, :date_entree_organisme, :date_anciennete_organisme, :langue, :profession_code, :executant_referent, :date_sortie_organisme, :motif_sortie_organisme, :date_deces, :statut_personne, :code_agence, :code_commercial, :nom_usage, :naissance_pays, :naissance_lieu, :naissance_code_secteur, :adresse_type, :adresse_pays, :adresse_normee, :adresse_1, :adresse_2, :adresse_num_voie, :adresse_cplt_num_voie, :adresse_lib_voie, :adresse_nom_acheminement, :adresse_nom_lieu_dit, :adresse_lieu_dit, :adresse_lieu, :adresse_cplt_lieu, :adresse_code_postal, :adresse_reference, :user_reg)
            ON DUPLICATE KEY UPDATE ogd_code = :ogd_code, num_fichier = :num_fichier, type_mouvement = :type_mouvement,date_situation = :date_situation,identifiant_personne = :identifiant_personne,cle_secu = :cle_secu,civilite_code = :civilite_code,nom = :nom,nom_patronymique = :nom_patronymique,prenom = :prenom,genre_code = :genre_code,qualite_code = :qualite_code,date_naissance = :date_naissance,rang_gemellaire = :rang_gemellaire,nationalite_code = :nationalite_code,situation_familiale = :situation_familiale,categorie_professionnelle = :categorie_professionnelle,grand_regime = :grand_regime,caisse = :caisse,centre_payeur = :centre_payeur,date_premiere_immatriculation = :date_premiere_immatriculation,cplt_grand_regime = :cplt_grand_regime,date_entree_organisme = :date_entree_organisme,date_anciennete_organisme = :date_anciennete_organisme,langue = :langue,profession_code = :profession_code,executant_referent = :executant_referent,date_sortie_organisme = :date_sortie_organisme,motif_sortie_organisme = :motif_sortie_organisme,date_deces = :date_deces,statut_personne = :statut_personne,code_agence = :code_agence,code_commercial = :code_commercial,nom_usage = :nom_usage,naissance_pays = :naissance_pays,naissance_lieu = :naissance_lieu,naissance_code_secteur = :naissance_code_secteur,adresse_type = :adresse_type,adresse_pays = :adresse_pays,adresse_normee = :adresse_normee,adresse_1 = :adresse_1,adresse_2 = :adresse_2,adresse_num_voie = :adresse_num_voie,adresse_cplt_num_voie = :adresse_cplt_num_voie,adresse_lib_voie = :adresse_lib_voie,adresse_nom_acheminement = :adresse_nom_acheminement,adresse_nom_lieu_dit = :adresse_nom_lieu_dit,adresse_lieu_dit = :adresse_lieu_dit,adresse_lieu = :adresse_lieu,adresse_cplt_lieu = :adresse_cplt_lieu,adresse_code_postal = :adresse_code_postal,adresse_reference = :adresse_reference,date_edit = :date_edit,user_edit = :user_edit
        ');
        $a->execute(array(
            'ogd_code' => $code_ogd,
            'num_fichier' => $num_fichier,
            'type_mouvement' => $type_mouvement,
            'date_situation' => $date_situation,
            'identifiant_personne' => $identifiant_personne,
            'num_secu' => $num_secu,
            'cle_secu' => $cle_secu,
            'civilite_code' => $civilite,
            'nom' => $nom,
            'nom_patronymique' => $nom_patronymique,
            'prenom' => $prenom,
            'genre_code' => $sexe,
            'qualite_code' => $qualite_civile,
            'date_naissance' => $date_naissance,
            'rang_gemellaire' => $rang_gemellaire,
            'nationalite_code' => $nationalite,
            'situation_familiale' => $situation_familiale,
            'categorie_professionnelle' => $categorie_professionnelle,
            'grand_regime' => $grand_regime,
            'caisse' => $caisse,
            'centre_payeur' => $centre_payeur,
            'date_premiere_immatriculation' => $date_premiere_immatriculation,
            'cplt_grand_regime' => $cplt_grand_regime,
            'date_entree_organisme' => $date_entree_organisme,
            'date_anciennete_organisme' => $date_anciennete_organisme,
            'langue' => $langue,
            'profession_code' => $profession,
            'executant_referent' => $executant_referent,
            'date_sortie_organisme' => $date_sortie_organisme,
            'motif_sortie_organisme' => $motif_sortie_organisme,
            'date_deces' => $date_deces,
            'statut_personne' => $statut_personne,
            'code_agence' => $code_agence,
            'code_commercial' => $code_commercial,
            'nom_usage' => $nom_usage,
            'naissance_pays' => $pays_naissance,
            'naissance_lieu' => $lieu_naissance,
            'naissance_code_secteur' => $code_secteur_naissance,
            'adresse_type' => $type_adresse,
            'adresse_pays' => $pays_residence,
            'adresse_normee' => $adresse_normee,
            'adresse_1' => $adresse_1,
            'adresse_2' => $adresse_2,
            'adresse_num_voie' => $num_voie,
            'adresse_cplt_num_voie' => $cplt_num_voie,
            'adresse_lib_voie' => $lib_voie,
            'adresse_nom_acheminement' => $nom_acheminement_residence,
            'adresse_nom_lieu_dit' => $nom_lieu_dit_residence,
            'adresse_lieu_dit' => $lieu_dit_residence,
            'adresse_lieu'=>$adresse_lieu,
            'adresse_cplt_lieu'=>$adresse_cplt_lieu,
            'adresse_code_postal' => $code_postal_residence,
            'adresse_reference' => $reference_adresse,
            'user_reg' => $user,
            'date_edit' => date('Y-m-d H:i:s',time()),
            'user_edit' => $user
        ));
        if($a->errorCode() === '00000') {
            $x = $this->bdd->prepare('INSERT INTO auxil_assures_historique_mouvements(ogd_code, mouvement_type, num_secu, fichier_num, date_fichier, user_reg) 
        VALUES(:ogd_code, :mouvement_type, :num_secu, :fichier_num, :date_fichier, :user_reg)');
            $x->execute(array(
                'ogd_code' => $code_ogd,
                'mouvement_type' => $type_mouvement,
                'num_secu' => $num_secu,
                'fichier_num' => $num_fichier,
                'date_fichier' => $date_situation,
                'user_reg' => $user
            ));
            if($x->errorCode() === '00000') {
                $json = array(
                    'success' => true
                );
            }else {
                $json = array(
                    'success' => false,
                    'message' => 'Erreur: '.$x->errorCode().' <=> '.$x->errorInfo()[1].' <=> '.$x->errorInfo()[2]
                );
            }

        }else {
            $json = array(
                'success' => false,
                'message' => 'Erreur: '.$a->errorCode().' <=> '.$a->errorInfo()[1].' <=> '.$a->errorInfo()[2]
            );
        }
        return $json;
    }

    public function edition_assure_collectivite($code_ogd,$num_secu,$collectivite_code,$num_siret_employeur,$college,$matricule_salarie,$service,$fonction,$user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_assures_collectivites(ogd_code,num_secu,collectivite_code,num_siret_employeur,college,matricule_salarie,service,fonction,user_reg)
        VALUES(:ogd_code,:num_secu,:collectivite_code,:num_siret_employeur,:college,:matricule_salarie,:service,:fonction,:user_reg)
        ON DUPLICATE KEY UPDATE ogd_code = :ogd_code, num_siret_employeur = :num_siret_employeur,college = :college,matricule_salarie = :matricule_salarie,service = :service,fonction = :fonction,date_edit = :date_edit,user_edit = :user_edit
        ');
        $a->execute(array(
            'ogd_code' => $code_ogd,
            'num_secu' => $num_secu,
            'collectivite_code' => $collectivite_code,
            'num_siret_employeur' => $num_siret_employeur,
            'college' => $college,
            'matricule_salarie' => $matricule_salarie,
            'service' => $service,
            'fonction' => $fonction,
            'user_reg' => $user,
            'date_edit' => date('Y-m-d H:i:s',time()),
            'user_edit' => $user
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

    public function edition_assure_coordonnees($code_ogd,$num_secu,$type_coord,$valeur,$date_debut,$date_fin,$user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_assures_coordonnees(ogd_code,num_secu,coordonnee_type,coordonnee_valeur,coordonnee_date_debut,coordonnee_date_fin ,user_reg)
            VALUES(:ogd_code,:num_secu,:coordonnee_type,:coordonnee_valeur,:coordonnee_date_debut,:coordonnee_date_fin ,:user_reg)
            ON DUPLICATE KEY UPDATE ogd_code = :ogd_code, num_secu = :num_secu,coordonnee_type = :coordonnee_type,coordonnee_valeur = :coordonnee_valeur,coordonnee_date_debut = :coordonnee_date_debut,coordonnee_date_fin = :coordonnee_date_fin,date_edit = :date_edit,user_edit = :user_edit
         ');
        $a->execute(array(
            'ogd_code' => $code_ogd,
            'num_secu' => $num_secu,
            'coordonnee_type' => $type_coord,
            'coordonnee_valeur' => $valeur,
            'coordonnee_date_debut' => $date_debut,
            'coordonnee_date_fin' => $date_fin,
            'user_reg' => $user,
            'date_edit' => date('Y-m-d H:i:s',time()),
            'user_edit' => $user
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

    public function edition_assure_identifiant($code_ogd,$num_secu,$type_identifiant,$numero,$date_debut,$date_fin,$user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_assures_identifiants(ogd_code,num_secu,identifiant_type,identifiant_numero,identifiant_date_debut,identifiant_date_fin,user_reg)
        VALUES(:ogd_code, :num_secu,:identifiant_type,:identifiant_numero,:identifiant_date_debut,:identifiant_date_fin,:user_reg)
        ON DUPLICATE KEY UPDATE ogd_code = :ogd_code,num_secu = :num_secu,identifiant_type = :identifiant_type,identifiant_numero = :identifiant_numero,identifiant_date_debut = :identifiant_date_debut,identifiant_date_fin = :identifiant_date_fin,date_edit = :date_edit,user_edit = :user_edit
        ');
        $a->execute(array(
            'ogd_code' => $code_ogd,
            'num_secu' => $num_secu,
            'identifiant_type' => $type_identifiant,
            'identifiant_numero' => $numero,
            'identifiant_date_debut' => $date_debut,
            'identifiant_date_fin' => $date_fin,
            'user_reg' => $user,
            'date_edit' => date('Y-m-d H:i:s',time()),
            'user_edit' => $user
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

    public function edition_assure_lien_personne($code_ogd,$num_secu,$type_lien_personne,$identifiant_personne_ref,$date_debut,$date_fin,$user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_assures_liens_personne(ogd_code,num_secu,lien_type,lien_identifiant,lien_date_debut,lien_date_fin,user_reg) 
        VALUES(:ogd_code, :num_secu,:lien_type,:lien_identifiant,:lien_date_debut,:lien_date_fin,:user_reg)
        ON DUPLICATE KEY UPDATE ogd_code = :ogd_code,lien_date_debut = :lien_date_debut,lien_date_fin = :lien_date_fin,date_edit = :date_edit,user_edit = :user_edit
        ');
        $a->execute(array(
            'ogd_code' => $code_ogd,
            'num_secu' => $num_secu,
            'lien_type' => $type_lien_personne,
            'lien_identifiant ' => $identifiant_personne_ref,
            'lien_date_debut' => $date_debut,
            'lien_date_fin' => $date_fin,
            'user_reg' => $user,
            'date_edit' => date('Y-m-d H:i:s',time()),
            'user_edit' => $user
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

    public function edition_assure_contrat($code_ogd,$num_secu,$num_contrat_famille,$produit,$collectivite,$siret,$date_debut,$date_fin,$user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_assures_contrats(ogd_code,num_secu, contrat_num_famille, contrat_produit, collectivite_code, num_siret,contrat_date_debut, contrat_date_fin, user_reg)
            VALUES(:ogd_code,:num_secu, :contrat_num_famille, :contrat_produit, :collectivite_code, :num_siret, :contrat_date_debut, :contrat_date_fin, :user_reg)
           ON DUPLICATE KEY UPDATE num_secu = :num_secu, contrat_num_famille = :contrat_num_famille, contrat_produit = :contrat_produit, collectivite_code = :collectivite_code, num_siret = :num_siret, contrat_date_debut = :contrat_date_debut, contrat_date_fin = :contrat_date_fin, date_edit = :date_edit, user_edit = :user_edit
        ');
        $a->execute(array(
            'ogd_code' => $code_ogd,
            'num_secu' => $num_secu,
            'contrat_num_famille' => $num_contrat_famille,
            'contrat_produit' => $produit,
            'collectivite_code' => $collectivite,
            'num_siret' => $siret,
            'contrat_date_debut' => $date_debut,
            'contrat_date_fin' => $date_fin,
            'user_reg' => $user,
            'date_edit' => date('Y-m-d H:i:s',time()),
            'user_edit' => $user
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

    public function edition_assure_rattachement($code_ogd,$num_secu,$num_contrat_famille,$contractant,$date_debut,$date_fin,$user) {

        $collectivite = $this->trouver_collectivite($num_secu);
        if($collectivite) {
            $code_collectivite = $collectivite['collectivite_code'];
        }else {
            $code_collectivite = NULL;
        }

        $a = $this->bdd->prepare('INSERT INTO auxil_assures_rattachements(ogd_code, collectivite_code, num_secu, num_contrat_famille, contractant, date_debut, date_fin, user_reg)
          VALUES(:ogd_code, :collectivite_code, :num_secu, :num_contrat_famille, :contractant, :date_debut, :date_fin, :user_reg)
          ON DUPLICATE KEY UPDATE ogd_code = :ogd_code, date_debut = :date_debut,date_fin = :date_fin,date_edit = :date_edit,user_edit = :user_edit
          ');
        $a->execute(array(
            'ogd_code' => $code_ogd,
            'collectivite_code' => $code_collectivite,
            'num_secu' => $num_secu,
            'num_contrat_famille' => $num_contrat_famille,
            'contractant' => $contractant,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'user_reg' => $user,
            'date_edit' => date('Y-m-d H:i:s',time()),
            'user_edit' => $user
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

    public function edition_assure_rattachement_droits($code_ogd,$num_secu,$contractant,$groupe_acte,$date_debut,$date_fin,$user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_assures_rattachements_droits(ogd_code,num_secu,contractant,groupe_acte,date_debut,date_fin,user_reg)
          VALUES(:ogd_code,:num_secu,:contractant,:groupe_acte,:date_debut,:date_fin,:user_reg)
          ON DUPLICATE KEY UPDATE ogd_code = :ogd_code,date_debut = :date_debut,date_fin = :date_fin,date_edit = :date_edit,user_edit = :user_edit
        ');
        $a->execute(array(
            'ogd_code' => $code_ogd,
            'num_secu' => $num_secu,
            'contractant' => $contractant,
            'groupe_acte' => $groupe_acte,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'user_reg' => $user,
            'date_edit' => date('Y-m-d H:i:s',time()),
            'user_edit' => $user
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

    public function moteur_recherche($code_ogd, $num_secu, $nom_prenoms, $date_naissance) {
        $a = $this->bdd->prepare("SELECT num_secu, civilite_code AS code_civilite, genre_code AS code_sexe, nom, nom_patronymique, prenom, date_naissance FROM auxil_assures WHERE ogd_code = ? AND CONCAT(nom,' ',prenom) LIKE ? AND date_naissance LIKE ? AND num_secu LIKE ? ORDER BY nom, prenom");
        $a->execute(array($code_ogd, '%'.$nom_prenoms.'%','%'.$date_naissance.'%','%'.$num_secu.'%'));
        return $a->fetchAll();
    }
}