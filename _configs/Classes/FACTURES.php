<?php


class FACTURES extends BDD
{
    public function trouver($code_ogd,$num_facture) {
        $a = $this->bdd->prepare('SELECT num_deca, num_decliq, num_decret, bordereau_id, num_decpai, num_fichier, date_reception, date_liquidation, lien_archivage, facture_type AS type_facture, facture_num AS num_facture, facture_date AS date_facture, num_secu, nom, prenom, date_naissance, rang_gemellaire, num_ordonnance, type_decompte, ets_code, num_accident, date_accident, code_nature_assurance, num_ds, ets_code_ds, ps_code_ds, code_typologie_ds, code_pathologie_ds, date_debut_ds, date_fin_ds, num_destinataire_regelement, date_paiement, mode_paiement, info_bancaire_paiement, facture_statut AS statut, date_reg, user_reg, date_edit, user_edit FROM auxil_factures WHERE ogd_code LIKE ? AND facture_num = ?');
        $a->execute(array('%'.$code_ogd.'%',$num_facture));
        return $a->fetch();
    }

    public function lister_facture_lignes_actes($num_facture) {
        $a = $this->bdd->prepare('SELECT * FROM auxil_factures_lignes_acte WHERE num_facture = ?');
        $a->execute(array($num_facture));
        return $a->fetchAll();
    }

    public function trouver_montant_facture($num_facture) {
        $a = $this->bdd->prepare("SELECT SUM(montant_depense) AS depense, SUM(montant_remboursement_ro) AS remboursement_ro FROM auxil_factures_lignes_acte WHERE num_facture = ?");
        $a->execute(array($num_facture));
        return $a->fetch();
    }

    public function edition_facture($code_ogd, $num_deca, $num_decret,$num_fichier, $date_reception, $date_liquidation, $lien_archivage, $facture_type, $facture_num, $facture_date, $num_secu, $nom, $prenom, $date_naissance, $rang_gemellaire, $num_ordonnance, $type_decompte, $ets_code, $num_accident, $date_accident, $code_nature_assurance, $num_ds, $ets_code_ds, $ps_code_ds, $code_typologie_ds, $code_pathologie_ds, $date_debut_ds, $date_fin_ds, $num_destinataire_regelement, $date_paiement, $mode_paiement, $info_bancaire_paiement, $facture_statut, $user){
        if($num_deca) {
            $a = $this->bdd->prepare('INSERT INTO auxil_factures(ogd_code, num_deca, num_decret, num_fichier, date_reception, date_liquidation, lien_archivage, facture_type, facture_num, facture_date, num_secu, nom, prenom, date_naissance, rang_gemellaire, num_ordonnance, type_decompte, ets_code, num_accident, date_accident, code_nature_assurance, num_ds, ets_code_ds, ps_code_ds, code_typologie_ds, code_pathologie_ds, date_debut_ds, date_fin_ds, num_destinataire_regelement, date_paiement, mode_paiement, info_bancaire_paiement, facture_statut, user_reg) 
                                      VALUES(:ogd_code, :num_deca, :num_decret, :num_fichier, :date_reception, :date_liquidation, :lien_archivage, :facture_type, :facture_num, :facture_date, :num_secu, :nom, :prenom, :date_naissance, :rang_gemellaire, :num_ordonnance, :type_decompte, :ets_code, :num_accident, :date_accident, :code_nature_assurance, :num_ds, :ets_code_ds, :ps_code_ds, :code_typologie_ds, :code_pathologie_ds, :date_debut_ds, :date_fin_ds, :num_destinataire_regelement, :date_paiement, :mode_paiement, :info_bancaire_paiement, :facture_statut, :user_reg)
                                      ON DUPLICATE KEY UPDATE ogd_code = :ogd_code, num_deca = :num_deca, num_decret = :num_decret, num_fichier = :num_fichier, date_reception = :date_reception, date_liquidation = :date_liquidation, lien_archivage = :lien_archivage, facture_type = :facture_type, facture_date = :facture_date, num_secu = :num_secu, nom = :nom, prenom = :prenom, date_naissance = :date_naissance, rang_gemellaire = :rang_gemellaire, num_ordonnance = :num_ordonnance, type_decompte = :type_decompte, ets_code = :ets_code, num_accident = :num_accident, date_accident = :date_accident, code_nature_assurance = :code_nature_assurance, num_ds = :num_ds, ets_code_ds = :ets_code_ds, ps_code_ds = :ps_code_ds, code_typologie_ds = :code_typologie_ds, code_pathologie_ds = :code_pathologie_ds, date_debut_ds = :date_debut_ds, date_fin_ds = :date_fin_ds, num_destinataire_regelement = :num_destinataire_regelement, date_paiement = :date_paiement, mode_paiement = :mode_paiement, info_bancaire_paiement = :info_bancaire_paiement, facture_statut = :facture_statut, date_edit = :date_edit, user_edit = :user_edit           
                                      ');
            $a->execute(array(
                "ogd_code" => $code_ogd,
                "num_deca" => $num_deca,
                "num_decret" => $num_decret,
                "num_fichier" => $num_fichier,
                "date_reception" => $date_reception,
                "date_liquidation" => $date_liquidation,
                "lien_archivage" => $lien_archivage,
                "facture_type" => $facture_type,
                "facture_num" => $facture_num,
                "facture_date" => $facture_date,
                "num_secu" => $num_secu,
                "nom" => $nom,
                "prenom" => $prenom,
                "date_naissance" => $date_naissance,
                "rang_gemellaire" => $rang_gemellaire,
                "num_ordonnance" => $num_ordonnance,
                "type_decompte" => $type_decompte,
                "ets_code" => $ets_code,
                "num_accident" => $num_accident,
                "date_accident" => $date_accident,
                "code_nature_assurance" => $code_nature_assurance,
                "num_ds" => $num_ds,
                "ets_code_ds" => $ets_code_ds,
                "ps_code_ds" => $ps_code_ds,
                "code_typologie_ds" => $code_typologie_ds,
                "code_pathologie_ds" => $code_pathologie_ds,
                "date_debut_ds" => $date_debut_ds,
                "date_fin_ds" => $date_fin_ds,
                "num_destinataire_regelement" => $num_destinataire_regelement,
                "date_paiement" => $date_paiement,
                "mode_paiement" => $mode_paiement,
                "info_bancaire_paiement" => $info_bancaire_paiement,
                "facture_statut" => $facture_statut,
                "user_reg" => $user,
                "date_edit" => date('Y-m-d H:i:s',time()),
                "user_edit" => $user
            ));
        }elseif ($num_decret) {
            $a = $this->bdd->prepare("UPDATE auxil_factures SET num_decret = ?, date_reception = ?, facture_statut = ?, date_edit = ?, user_edit = ? WHERE facture_num = ?");
            $a->execute(array($num_decret, $date_reception, $facture_statut, date('Y-m-d H:i:s',time()),$user,$facture_num));
        }

        if($a->errorCode() === '00000') {
            $json = array(
                'success' => true
            );
        }else {
            $json = array(
                'success' => false,
                'message' => 'Erreur: '.$num_ds.' '.$a->errorCode().' <=> '.$a->errorInfo()[1].' <=> '.$a->errorInfo()[2]
            );
        }
        return $json;
    }

    public function edition_acte($num_facture, $date_debut_soins, $date_fin_soins, $code_acte, $code_complement_acte, $code_specialite, $code_dmt, $code_pathologie, $code_origine_prescription, $code_indicateur_parcours, $code_prescripteur, $code_specialite_prescripteur, $date_prescription, $code_executant, $code_zone_tarif_executant, $type_controle_droits, $num_transaction_ctrl_droits, $capitation_o_n, $montant_depense, $quantite_acte, $coefficient_acte, $prix_unitaire, $base_remboursement, $taux_remboursement_ro, $montant_remboursement_ro, $signe, $motif_rejet, $user){
        $facture = $this->trouver(NULL,$num_facture);
        if($facture) {
            if($facture['statut'] == 'R' || $facture['statut'] == 'V') {
                $a = $this->bdd->prepare("UPDATE auxil_factures_lignes_acte SET date_debut_soins = ?, date_fin_soins = ?, code_complement_acte = ?, code_executant = ?, montant_depense = ?, montant_remboursement_ro = ?, signe = ?, motif_rejet = ?, date_edit = ?, user_edit = ? WHERE code_acte = ?");
                $a->execute(array($date_debut_soins, $date_fin_soins, $code_complement_acte, $code_executant, $montant_depense, $montant_remboursement_ro, $signe, $motif_rejet, date('Y-m-d H:i:s',time()),$user, $code_acte));
            }else {
                $a = $this->bdd->prepare('INSERT INTO auxil_factures_lignes_acte(num_facture, date_debut_soins, date_fin_soins, code_acte, code_complement_acte, code_specialite, code_dmt, code_pathologie, code_origine_prescription, code_indicateur_parcours, code_prescripteur, code_specialite_prescripteur, date_prescription, code_executant, code_zone_tarif_executant, type_controle_droits, num_transaction_ctrl_droits, capitation_o_n, montant_depense, quantite_acte, coefficient_acte, prix_unitaire, base_remboursement, taux_remboursement_ro, montant_remboursement_ro, signe, motif_rejet, user_reg) 
            VALUES (:num_facture, :date_debut_soins, :date_fin_soins, :code_acte, :code_complement_acte, :code_specialite, :code_dmt, :code_pathologie, :code_origine_prescription, :code_indicateur_parcours, :code_prescripteur, :code_specialite_prescripteur, :date_prescription, :code_executant, :code_zone_tarif_executant, :type_controle_droits, :num_transaction_ctrl_droits, :capitation_o_n, :montant_depense, :quantite_acte, :coefficient_acte, :prix_unitaire, :base_remboursement, :taux_remboursement_ro, :montant_remboursement_ro, :signe, :motif_rejet, :user_reg)
            ON DUPLICATE KEY UPDATE date_debut_soins = :date_debut_soins, date_fin_soins = :date_fin_soins, code_complement_acte = :code_complement_acte, code_specialite = :code_specialite, code_dmt = :code_dmt, code_pathologie = :code_pathologie, code_origine_prescription = :code_origine_prescription, code_indicateur_parcours = :code_indicateur_parcours, code_prescripteur = :code_prescripteur, code_specialite_prescripteur = :code_specialite_prescripteur, date_prescription = :date_prescription, code_executant = :code_executant, code_zone_tarif_executant = :code_zone_tarif_executant, type_controle_droits = :type_controle_droits, num_transaction_ctrl_droits = :num_transaction_ctrl_droits, capitation_o_n = :capitation_o_n, montant_depense = :montant_depense, quantite_acte = :quantite_acte, coefficient_acte = :coefficient_acte, prix_unitaire = :prix_unitaire, base_remboursement = :base_remboursement, taux_remboursement_ro = :taux_remboursement_ro, montant_remboursement_ro = :montant_remboursement_ro, signe = :signe, motif_rejet = :motif_rejet, date_edit = :date_edit, user_edit = :user_edit');
                $a->execute(array(
                    'num_facture' => $num_facture,
                    'date_debut_soins' => $date_debut_soins,
                    'date_fin_soins' => $date_fin_soins,
                    'code_acte' => $code_acte,
                    'code_complement_acte' => $code_complement_acte,
                    'code_specialite' => $code_specialite,
                    'code_dmt' => $code_dmt,
                    'code_pathologie' => $code_pathologie,
                    'code_origine_prescription' => $code_origine_prescription,
                    'code_indicateur_parcours' => $code_indicateur_parcours,
                    'code_prescripteur' => $code_prescripteur,
                    'code_specialite_prescripteur' => $code_specialite_prescripteur,
                    'date_prescription' => $date_prescription,
                    'code_executant' => $code_executant,
                    'code_zone_tarif_executant' => $code_zone_tarif_executant,
                    'type_controle_droits' => $type_controle_droits,
                    'num_transaction_ctrl_droits' => $num_transaction_ctrl_droits,
                    'capitation_o_n' => $capitation_o_n,
                    'montant_depense' => $montant_depense,
                    'quantite_acte' => $quantite_acte,
                    'coefficient_acte' => $coefficient_acte,
                    'prix_unitaire' => $prix_unitaire,
                    'base_remboursement' => $base_remboursement,
                    'taux_remboursement_ro' => $taux_remboursement_ro,
                    'montant_remboursement_ro' => $montant_remboursement_ro,
                    'signe' => $signe,
                    'motif_rejet' => $motif_rejet,
                    'user_reg' => $user,
                    "date_edit" => date('Y-m-d H:i:s',time()),
                    "user_edit" => $user
                ));
            }

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
        }
        return $json;
    }

    public function edition_facture_statut($num_facture,$statut,$motif,$user) {
        $facture = $this->trouver(NULL,$num_facture);
        if($facture['num_facture']) {
            $a = $this->bdd->prepare('INSERT INTO auxil_factures_historique_statuts(facture_num, statut_code, motif_code, user_reg) VALUES(:facture_num, :statut_code, :motif_code, :user_reg)');
            $a->execute(array(
                'facture_num' => $num_facture,
                'statut_code' => $facture['statut'],
                'motif_code' => $motif,
                'user_reg' => $user
            ));
            if($a->errorCode() === '00000') {
                $b = $this->bdd->prepare('UPDATE auxil_factures SET facture_statut = ?, motif_code = ?, date_edit = ?, user_edit = ? WHERE facture_num = ?');
                $b->execute(array($statut,$motif,date('Y-m-d H:i:s',time()),$user,$num_facture));
                if($b->errorCode() === '00000') {
                    if($statut == 'L') {
                        $c = $this->bdd->prepare('UPDATE auxil_factures SET date_liquidation = ? WHERE facture_num = ?');
                        $c->execute(array(date('Y-m-d',time()),$num_facture));
                    }
                    $json = array(
                        'success' => true
                    );
                }else {
                    $json = array(
                        'success' => false,
                        'message' => 'Erreur: '.$b->errorCode().' <=> '.$a->errorInfo()[1].' <=> '.$a->errorInfo()[2]
                    );
                }
            }else {
                $json = array(
                    'success' => false,
                    'message' => 'Erreur: '.$a->errorCode().' <=> '.$a->errorInfo()[1].' <=> '.$a->errorInfo()[2]
                );
            }
        }
        return $json;
    }

    public function lecture_decompte($norme, $lien_archivage, $date_reception, $date_liquidation, $num_secu, $date_naissance, $rang_gemellaire, $nom, $prenom, $type_decompte, $code_etablissement, $num_ordonnance, $num_accident, $date_accident, $code_nature_assurance, $date_facture, $num_facture, $num_dossier_soins, $code_etab_dossier_soins, $code_ps_dossier_soins, $code_typologie_dossier_soins, $code_pathologie_dossier_soins, $date_debut_dossier_soins, $date_fin_dossier_soins, $num_destinataire_reglement, $date_paiement, $mode_paiement, $infos_bancaires_paiement, $occurrences_ligne_acte, $code_validation) {
        if($norme) {
            $json[0] = NULL;
        }else {
            $json[1] = array(
                'message' => "La norme du fichier chargé n'est pas renseignée."
            );
        }
        if($norme == 'DECA') {
            if($num_facture) {
                if(is_numeric($num_facture)) {
                    $json[0] = NULL;
                }else {
                    $json[2] = array(
                        'message' => "Le numero de la facture renseigné n'est pas un nombre."
                    );
                }
            }else {
                $json[2] = array(
                    'message' => "Le numero de la facture n'est pas renseigné."
                );
            }
            if($num_dossier_soins) {
                $json[0] = NULL;
            }else {
                $json[2] = array(
                    'message' => "Le dossier soins n'est pas renseigné."
                );
            }

        }
        elseif($norme == 'DECRET') {
            if($num_facture) {
                $facture = $this->trouver(NULL,$num_facture);
                if($facture) {
                    if($facture['statut'] == 'L') {
                        if($code_etablissement) {
                            $ets = $this->trouver_etablissement_sante($code_etablissement,$facture['date_facture']);
                            if($ets) {
                                $json[0] = NULL;
                            }else {
                                $json[2] = array(
                                    'message' => "Le code etablissement n'est pas reconnu à la date des soins."
                                );
                            }
                        }else {
                            $json[2] = array(
                                'message' => "Le code établissement de la facture n'est pas renseigné."
                            );
                        }
                    }else {
                        $json[2] = array(
                            'message' => "La facture {$num_facture} possède le statut :{$facture['statut']}. Elle ne peut être chargée en DECRET."
                        );
                    }
                }else {
                    $json[2] = array(
                        'message' => "La facture {$num_facture} n'est pas reconnu par le système."
                    );
                }
            }else {
                $json[2] = array(
                    'message' => "Le numéro de la facture n'est pas défini."
                );
            }
            if($lien_archivage) {
                $json[0] = NULL;
            }else {
                $json[3] = array(
                    'message' => "Le lien archivage de la facture n'est pas renseigné."
                );
            }
            if($date_liquidation) {
                if(checkdate(date('m',strtotime($date_liquidation)),date('d',strtotime($date_liquidation)),date('Y',strtotime($date_liquidation)))) {
                    $json[0] = NULL;
                }else {
                    $json[4] = array(
                        'message' => "Le format de la date liquidation de la facture est incorrect."
                    );
                }
            }else {
                $json[4] = array(
                    'message' => "La date liquidation de la facture n'est pas renseignée."
                );
            }
            if($num_secu) {
                $ASSURES = new ASSURES();
                $assure = $ASSURES->trouver($num_secu);
                if($assure) {
                    if($assure['type_mouvement'] != 'SUP') {
                        $json[0] = NULL;
                    }else {
                        $json[5] = array(
                            'message' => "L'assuré {$num_secu} ne fait plus parti de notre OGD."
                        );
                    }
                }else {
                    $json[5] = array(
                        'message' => "Le numéro sécu de la facture est incorrect."
                    );
                }
            }else {
                $json[5] = array(
                    'message' => "Le numéro sécu de la facture n'est pas renseigné."
                );
            }
            if($date_facture) {
                if(checkdate(date('m',strtotime($date_facture)),date('d',strtotime($date_facture)),date('Y',strtotime($date_facture)))) {
                    $json[0] = NULL;
                }else {
                    $json[6] = array(
                        'message' => "Le format de la date de la facture est incorrect."
                    );
                }
            }else {
                $json[6] = array(
                    'message' => "La date de la facture n'est pas renseignée."
                );
            }
            if($code_validation) {
                if($code_validation == 'R' || $code_validation == 'V') {
                    $json[0] = NULL;
                }else {
                    $json[7] = array(
                        'message' => "Le code validation de la facture n'est pas reconnu par le système."
                    );
                }

            }else {
                $json[7] = array(
                    'message' => "Le code validation de la facture n'est pas renseigné."
                );
            }
        }
        else {
            $json[20] = array(
                'message' => "La norme n'est pas reconnu par le système."
            );
        }
        return $json;
    }

    public function lecture_ligne_acte($norme, $num_facture, $num_ligne_acte, $date_debut_soins, $date_fin_soins, $code_acte, $code_complement_acte, $code_specialite, $code_dmt, $code_pathologie, $code_origine_prescription, $code_indicateur_parcours_soins, $code_prescripteur, $code_specialite_prescripteur, $date_prescription, $code_executant, $code_zone_tarif_executant, $type_controle_droits, $num_transaction_ctrl_droits, $capitation_o_n, $montant_depense, $quantite_acte, $coefficient_acte, $prix_unitaire_acte, $base_remboursement, $taux_remboursement_ro, $montant_remboursement_ro, $signe, $code_rejet, $libelle_rejet) {
        if($norme) {
            $json[0] = NULL;
        }else {
            $json[1] = array(
                'message' => "La norme du fichier chargé n'est pas renseignée."
            );
        }
        if($norme == 'DECA') {
            if($num_facture) {
                if(is_numeric($num_facture)) {
                    $json[0] = NULL;
                }else {
                    $json[2] = array(
                        'message' => "Le numero de la facture renseigné n'est pas un nombre."
                    );
                }
            }else {
                $json[2] = array(
                    'message' => "Le numero de la facture n'est pas renseigné."
                );
            }
        }
        elseif($norme == 'DECRET') {
            if($num_facture) {
                $facture = $this->trouver(NULL,$num_facture);
                if($facture) {
                    if($facture['statut'] == 'L') {
                        $json[0] = NULL;
                    }else {
                        $json[2] = array(
                            'message' => "La facture {$num_facture} possède le statut :{$facture['statut']}. Elle ne peut être chargée en DECRET."
                        );
                    }
                }else {
                    $json[2] = array(
                        'message' => "La facture {$num_facture} n'est pas reconnu par le système."
                    );
                }
            }else {
                $json[2] = array(
                    'message' => "Le numéro de la facture n'est pas défini."
                );
            }

        }
        else {
            $json[20] = array(
                'message' => "La norme n'est pas reconnu par le système."
            );
        }
        return $json;
    }

    public function trouver_professionnel_sante($code_ps,$date_soins) {
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
          validite_date_debut <= ? AND 
          (validite_date_fin >= ? OR validite_date_fin IS NULL)
        ');
        $a->execute(array($code_ps,$date_soins,$date_soins));
        $ps_existe = $a->rowCount();
        return $a->fetch();
    }

    public function trouver_etablissement_sante($code_ets, $date_soins) {
        $a = $this->bdd->prepare('
        SELECT 
               ets_code AS code, 
               ets_raison_sociale AS raison_sociale, 
               ets_honoraire_code AS secteur_activite, 
               ets_nom_bureau_distributeur AS ville, 
               ets_date_fin AS date_debut_validite
        FROM 
             auxil_ref_etablissement_sante 
        WHERE 
              ets_code = ? AND 
          ets_date_debut <= ? AND 
          (ets_date_fin >= ? OR ets_date_fin IS NULL)
              ');
        $a->execute(array($code_ets,$date_soins,$date_soins));
        return $a->fetch();
    }

    public function lister_etablissement_sante() {
        $a = $this->bdd->prepare('
        SELECT 
               DISTINCT(A.ets_code) AS code, 
               A.ets_raison_sociale AS raison_sociale, 
               A.ets_nom_bureau_distributeur AS ville
        FROM 
             auxil_ref_etablissement_sante A JOIN auxil_factures B ON A.ets_code = B.ets_code ORDER BY A.ets_raison_sociale');
        $a->execute(array());
        return $a->fetchAll();
    }

    public function lister_consommation($code_ogd, $num_secu) {
        $a = $this->bdd->prepare("SELECT A.facture_num AS num_facture, A.facture_date AS date_soins, A.ets_code AS code_ets, A.facture_statut AS statut, SUM(B.montant_depense) AS montant_depense, SUM(B.prix_unitaire * B.quantite_acte) AS montant_total, SUM(B.montant_remboursement_ro) AS montant_remboursement FROM auxil_factures A JOIN auxil_factures_lignes_acte B ON A.facture_num = B.num_facture AND A.ogd_code = ? AND A.num_secu = ? GROUP BY A.facture_num, A.facture_date, A.ets_code, A.facture_statut ORDER BY A.facture_date DESC, A.facture_num DESC");
        $a->execute(array($code_ogd, $num_secu));
        return $a->fetchAll();
    }

    public function lister_factures($code_ogd, $type_fichier,$num_fichier,$num_facture,$code_ets,$statut) {
        if(empty($type_fichier)) {
            $a = $this->bdd->prepare('SELECT num_deca, num_decliq, num_decret, bordereau_id, num_decpai, num_fichier, date_reception, date_liquidation, lien_archivage, facture_type AS type_facture, facture_num AS num_facture, facture_date AS date_facture, num_secu, nom, prenom, date_naissance, rang_gemellaire, num_ordonnance, type_decompte, ets_code, num_accident, date_accident, code_nature_assurance, num_ds, ets_code_ds, ps_code_ds, code_typologie_ds, code_pathologie_ds, date_debut_ds, date_fin_ds, num_destinataire_regelement, date_paiement, mode_paiement, info_bancaire_paiement, facture_statut AS statut, date_reg, user_reg, date_edit, user_edit FROM auxil_factures WHERE ogd_code = ? AND (facture_num LIKE ? OR num_ds LIKE ?) AND ets_code LIKE ? AND facture_statut LIKE ? ORDER BY facture_date DESC');
            $a->execute(array($code_ogd, '%'.$num_facture.'%','%'.$num_facture.'%','%'.$code_ets.'%','%'.$statut.'%'));
        }else {
            if($type_fichier == 'DECA') {
                $a = $this->bdd->prepare('SELECT num_deca, num_decliq, num_decret, bordereau_id, num_decpai, num_fichier, date_reception, date_liquidation, lien_archivage, facture_type AS type_facture, facture_num AS num_facture, facture_date AS date_facture, num_secu, nom, prenom, date_naissance, rang_gemellaire, num_ordonnance, type_decompte, ets_code, num_accident, date_accident, code_nature_assurance, num_ds, ets_code_ds, ps_code_ds, code_typologie_ds, code_pathologie_ds, date_debut_ds, date_fin_ds, num_destinataire_regelement, date_paiement, mode_paiement, info_bancaire_paiement, facture_statut AS statut, date_reg, user_reg, date_edit, user_edit FROM auxil_factures WHERE ogd_code = ? AND num_deca LIKE ? AND (facture_num LIKE ? OR num_ds LIKE ?) AND ets_code LIKE ? AND facture_statut LIKE ? ORDER BY facture_date DESC');
                $a->execute(array($code_ogd,'%'.$num_fichier.'%','%'.$num_facture.'%','%'.$num_facture.'%','%'.$code_ets.'%','%'.$statut.'%'));
            }
            if($type_fichier == 'DECLIQ') {
                $a = $this->bdd->prepare('SELECT num_deca, num_decliq, num_decret, bordereau_id, num_decpai, num_fichier, date_reception, date_liquidation, lien_archivage, facture_type AS type_facture, facture_num AS num_facture, facture_date AS date_facture, num_secu, nom, prenom, date_naissance, rang_gemellaire, num_ordonnance, type_decompte, ets_code, num_accident, date_accident, code_nature_assurance, num_ds, ets_code_ds, ps_code_ds, code_typologie_ds, code_pathologie_ds, date_debut_ds, date_fin_ds, num_destinataire_regelement, date_paiement, mode_paiement, info_bancaire_paiement, facture_statut AS statut, date_reg, user_reg, date_edit, user_edit FROM auxil_factures WHERE ogd_code = ? AND num_decliq LIKE ? AND (facture_num LIKE ? OR num_ds LIKE ?) AND ets_code LIKE ? AND facture_statut LIKE ? ORDER BY facture_date DESC');
                $a->execute(array($code_ogd, '%'.$num_fichier.'%','%'.$num_facture.'%','%'.$num_facture.'%','%'.$code_ets.'%','%'.$statut.'%'));
            }
            if($type_fichier == 'DECRET') {
                $a = $this->bdd->prepare('SELECT num_deca, num_decliq, num_decret, bordereau_id, num_decpai, num_fichier, date_reception, date_liquidation, lien_archivage, facture_type AS type_facture, facture_num AS num_facture, facture_date AS date_facture, num_secu, nom, prenom, date_naissance, rang_gemellaire, num_ordonnance, type_decompte, ets_code, num_accident, date_accident, code_nature_assurance, num_ds, ets_code_ds, ps_code_ds, code_typologie_ds, code_pathologie_ds, date_debut_ds, date_fin_ds, num_destinataire_regelement, date_paiement, mode_paiement, info_bancaire_paiement, facture_statut AS statut, date_reg, user_reg, date_edit, user_edit FROM auxil_factures WHERE ogd_code = ? AND num_decret LIKE ? AND (facture_num LIKE ? OR num_ds LIKE ?) AND ets_code LIKE ? AND facture_statut LIKE ? ORDER BY facture_date DESC');
                $a->execute(array($code_ogd, '%'.$num_fichier.'%','%'.$num_facture.'%','%'.$num_facture.'%','%'.$code_ets.'%','%'.$statut.'%'));
            }
            if($type_fichier == 'DECPAI') {
                $a = $this->bdd->prepare('SELECT num_deca, num_decliq, num_decret, bordereau_id, num_decpai, num_fichier, date_reception, date_liquidation, lien_archivage, facture_type AS type_facture, facture_num AS num_facture, facture_date AS date_facture, num_secu, nom, prenom, date_naissance, rang_gemellaire, num_ordonnance, type_decompte, ets_code, num_accident, date_accident, code_nature_assurance, num_ds, ets_code_ds, ps_code_ds, code_typologie_ds, code_pathologie_ds, date_debut_ds, date_fin_ds, num_destinataire_regelement, date_paiement, mode_paiement, info_bancaire_paiement, facture_statut AS statut, date_reg, user_reg, date_edit, user_edit FROM auxil_factures WHERE ogd_code = ? AND num_decpai LIKE ? AND (facture_num LIKE ? OR num_ds LIKE ?) AND ets_code LIKE ? AND facture_statut LIKE ? ORDER BY facture_date DESC');
                $a->execute(array($code_ogd, '%'.$num_fichier.'%','%'.$num_facture.'%','%'.$num_facture.'%','%'.$code_ets.'%','%'.$statut.'%'));
            }
        }
        return $a->fetchAll();
    }

    public function moteur_recherche($date_debut, $date_fin, $num_facture, $code_ets, $statut) {
        $a = $this->bdd->prepare("SELECT * FROM auxil_factures WHERE (facture_date BETWEEN ? AND ?) AND ets_code LIKE ? AND facture_num LIKE ? AND facture_statut LIKE ? ORDER BY facture_date DESC");
        $a->execute(array($date_debut, $date_fin, '%'.$code_ets.'%', '%'.$num_facture.'%', '%'.$statut.'%'));
        return $a->fetchAll();
    }

    public function lister_factures_ets($code_ogd, $type_fichier,$num_fichier,$num_facture,$code_ets,$statut) {

        if(empty($type_fichier)) {
            $a = $this->bdd->prepare('SELECT DISTINCT(A.ets_code) AS code_ets, B.ets_raison_sociale AS raison_sociale FROM auxil_factures A, auxil_ref_etablissement_sante B WHERE A.ets_code = B.ets_code AND A.ogd_code = ? AND A.facture_num LIKE ? AND A.ets_code LIKE ? AND A.facture_statut LIKE ? ORDER BY B.ets_raison_sociale ASC');
            $a->execute(array($code_ogd, '%'.$num_facture.'%','%'.$code_ets.'%','%'.$statut.'%'));
        }else {
            if($type_fichier == 'DECA') {
                $a = $this->bdd->prepare('SELECT DISTINCT(A.ets_code) AS code_ets, B.ets_raison_sociale AS raison_sociale FROM auxil_factures A,auxil_ref_etablissement_sante B WHERE A.ets_code = B.ets_code AND A.ogd_code = ? AND A.num_deca LIKE ? AND A.facture_num LIKE ? AND A.ets_code LIKE ? AND A.facture_statut LIKE ? ORDER BY B.ets_raison_sociale ASC');
                $a->execute(array($code_ogd, '%'.$num_fichier.'%','%'.$num_facture.'%','%'.$code_ets.'%','%'.$statut.'%'));
            }
            if($type_fichier == 'DECLIQ') {
                $a = $this->bdd->prepare('SELECT DISTINCT(A.ets_code) AS code_ets, B.ets_raison_sociale AS raison_sociale FROM auxil_factures A,auxil_ref_etablissement_sante B WHERE A.ets_code = B.ets_code AND A.ogd_code = ? AND A.num_decliq LIKE ? AND A.facture_num LIKE ? AND A.ets_code LIKE ? AND A.facture_statut LIKE ? ORDER BY B.ets_raison_sociale ASC');
                $a->execute(array($code_ogd, '%'.$num_fichier.'%','%'.$num_facture.'%','%'.$code_ets.'%','%'.$statut.'%'));
            }
            if($type_fichier == 'DECRET') {
                $a = $this->bdd->prepare('SELECT DISTINCT(A.ets_code) AS code_ets, B.ets_raison_sociale AS raison_sociale FROM auxil_factures A,auxil_ref_etablissement_sante B WHERE A.ets_code = B.ets_code AND A.ogd_code = ? AND A.num_decret LIKE ? AND A.facture_num LIKE ? AND A.ets_code LIKE ? AND A.facture_statut LIKE ? ORDER BY B.ets_raison_sociale ASC');
                $a->execute(array($code_ogd, '%'.$num_fichier.'%','%'.$num_facture.'%','%'.$code_ets.'%','%'.$statut.'%'));
            }
            if($type_fichier == 'DECPAI') {
                $a = $this->bdd->prepare('SELECT DISTINCT(A.ets_code) AS code_ets, B.ets_raison_sociale AS raison_sociale FROM auxil_factures A,auxil_ref_etablissement_sante B WHERE A.ets_code = B.ets_code AND A.ogd_code = ? AND A.num_decpai LIKE ? AND A.facture_num LIKE ? AND A.ets_code LIKE ? AND A.facture_statut LIKE ? ORDER BY B.ets_raison_sociale ASC');
                $a->execute(array($code_ogd, '%'.$num_fichier.'%','%'.$num_facture.'%','%'.$code_ets.'%','%'.$statut.'%'));
            }
        }
        return $a->fetchAll();
    }

    public function lister_decomptes($code_ogd, $norme,$statut) {
        if($norme == 'DECA') {
            $a = $this->bdd->prepare('SELECT A.num_deca AS num_fichier, B.log_nom_fichier AS nom_fichier,COUNT(A.num_deca) AS nombre FROM auxil_factures A,auxil_log_historique_fichier B WHERE A.num_deca = B.log_num_fichier AND B.log_code_destinataire = ? AND A.facture_statut LIKE ? AND B.log_norme = ? GROUP BY A.num_deca, B.log_nom_fichier ORDER BY A.num_deca DESC');
            $a->execute(array($code_ogd,'%'.$statut.'%',$norme));
        }
        if($norme == 'DECLIQ') {
            $a = $this->bdd->prepare('SELECT A.num_decliq AS num_fichier, B.log_nom_fichier AS nom_fichier,COUNT(A.num_decliq) AS nombre FROM auxil_factures A,auxil_log_historique_fichier B WHERE A.num_decliq = B.log_num_fichier AND B.log_code_destinataire = ? AND A.facture_statut LIKE ? AND B.log_norme = ? GROUP BY A.num_decliq, B.log_nom_fichier ORDER BY A.num_decliq DESC');
            $a->execute(array($code_ogd,'%'.$statut.'%',$norme));
        }
        if($norme == 'DECRET') {
            $a = $this->bdd->prepare('SELECT A.num_decret AS num_fichier, B.log_nom_fichier AS nom_fichier,COUNT(A.num_decret) AS nombre FROM auxil_factures A,auxil_log_historique_fichier B WHERE A.num_decret = B.log_num_fichier AND B.log_code_destinataire = ? AND A.facture_statut LIKE ? AND B.log_norme = ? GROUP BY A.num_decret, B.log_nom_fichier ORDER BY A.num_decret DESC');
            $a->execute(array($code_ogd,'%'.$statut.'%',$norme));
        }
        if($norme == 'DECPAI') {
            $a = $this->bdd->prepare('SELECT A.num_decpai AS num_fichier, B.log_nom_fichier AS nom_fichier,COUNT(A.num_decpai) AS nombre FROM auxil_factures A,auxil_log_historique_fichier B WHERE A.num_decpai = B.log_num_fichier AND B.log_code_destinataire = ? AND A.facture_statut LIKE ? AND B.log_norme = ? GROUP BY A.num_decpai, B.log_nom_fichier ORDER BY A.num_decpai DESC');
            $a->execute(array($code_ogd,'%'.$statut.'%',$norme));
        }
        return $a->fetchAll();
    }

    public function trouver_statut($code_statut) {
        $a = $this->bdd->prepare('SELECT statut_code AS code, statut_nom AS nom FROM auxil_factures_statuts_dictionnaire WHERE statut_code = ?');
        $a->execute(array($code_statut));
        return $a->fetch();
    }

    public function lister_factures_statut() {
        $a = $this->bdd->prepare("SELECT DISTINCT (A.facture_statut) AS code, B.statut_nom AS libelle FROM auxil_factures A JOIN auxil_factures_statuts_dictionnaire B ON A.facture_statut = B.statut_code ORDER BY B.statut_nom");
        $a->execute(array());
        return $a->fetchAll();
    }

    public function lister_fichiers_a_telecharger($code_ogd, $norme) {
        if($norme == 'DECLIQ') {
            $statut = 'L';
            $norme_chargee = 'DECA';

            $a = $this->bdd->prepare("SELECT A.log_norme AS norme, A.log_num_fichier AS num_fichier, A.log_nom_fichier AS nom_fichier, COUNT(B.facture_num) AS effectif FROM auxil_log_historique_fichier A JOIN auxil_factures B ON A.log_num_fichier = B.num_deca AND B.ogd_code = ? AND B.facture_statut = ? AND A.log_norme = ? GROUP BY A.log_norme, A.log_num_fichier, A.log_nom_fichier");
            $a->execute(array($code_ogd, $statut, $norme_chargee));
        }elseif($norme == 'DECPAI') {
            $statut = 'V';
            $norme_chargee = 'DECRET';
            $a = $this->bdd->prepare("SELECT A.log_norme AS norme, A.log_num_fichier AS num_fichier, A.log_nom_fichier AS nom_fichier, COUNT(B.facture_num) AS effectif FROM auxil_log_historique_fichier A JOIN auxil_factures B ON A.log_num_fichier = B.num_decret AND B.ogd_code = ? AND B.facture_statut = ? AND A.log_norme = ? GROUP BY A.log_norme, A.log_num_fichier, A.log_nom_fichier");
            $a->execute(array($code_ogd, $statut, $norme_chargee));
        }elseif($norme == 'TDM_EDT_009E') {
            $statut = 'P';
            $norme_chargee = 'DECPAI';
            $a = $this->bdd->prepare("SELECT A.log_norme AS norme, A.log_num_fichier AS num_fichier, A.log_nom_fichier AS nom_fichier, COUNT(B.facture_num) AS effectif FROM auxil_log_historique_fichier A JOIN auxil_factures B ON A.log_num_fichier = B.num_decpai AND B.ogd_code = ? AND B.facture_statut = ? AND A.log_norme = ? GROUP BY A.log_norme, A.log_num_fichier, A.log_nom_fichier");
            $a->execute(array($code_ogd, $statut, $norme_chargee));
        }elseif($norme == 'BPAIEMENTS') {
            $statut = 'T';
            $norme_chargee = 'DECPAI';
            $a = $this->bdd->prepare("SELECT A.log_norme AS norme, A.log_num_fichier AS num_fichier, A.log_nom_fichier AS nom_fichier, COUNT(B.facture_num) AS effectif FROM auxil_log_historique_fichier A JOIN auxil_factures B ON A.log_num_fichier = B.num_decpai AND B.ogd_code = ? AND B.facture_statut = ? AND A.log_norme = ? GROUP BY A.log_norme, A.log_num_fichier, A.log_nom_fichier");
            $a->execute(array($code_ogd, $statut, $norme_chargee));
        }elseif($norme == 'REJETSO') {
            $statut = 'R';
            $norme_chargee = 'DECLIQ';
            $a = $this->bdd->prepare("SELECT A.log_norme AS norme, A.log_num_fichier AS num_fichier, A.log_nom_fichier AS nom_fichier, COUNT(B.facture_num) AS effectif FROM auxil_log_historique_fichier A JOIN auxil_factures B ON A.log_num_fichier = B.num_decliq AND B.num_decret IS NULL AND B.ogd_code = ? AND B.facture_statut = ? AND A.log_norme = ? GROUP BY A.log_norme, A.log_num_fichier, A.log_nom_fichier");
            $a->execute(array($code_ogd, $statut, $norme_chargee));
        }elseif($norme == 'REJETSC') {
            $statut = 'R';
            $norme_chargee = 'DECRET';
            $a = $this->bdd->prepare("SELECT A.log_norme AS norme, A.log_num_fichier AS num_fichier, A.log_nom_fichier AS nom_fichier, COUNT(B.facture_num) AS effectif FROM auxil_log_historique_fichier A JOIN auxil_factures B ON A.log_num_fichier = B.num_decret AND B.ogd_code = ? AND B.facture_statut = ? AND A.log_norme = ? GROUP BY A.log_norme, A.log_num_fichier, A.log_nom_fichier");
            $a->execute(array($code_ogd, $statut, $norme_chargee));
        }
        return $a->fetchAll();

    }

    public function lister_factures_a_telecharger($code_ogd, $norme, $num_fichier) {
        if($norme == 'DECLIQ') {
            $statut = 'L';
            $norme_chargee = 'DECA';
            $a = $this->bdd->prepare("SELECT * FROM auxil_factures WHERE ogd_code = ? AND facture_statut = ? AND num_deca = ?");
            $a->execute(array($code_ogd, $statut, $num_fichier));
        }elseif($norme == 'DECPAI') {
            $statut = 'V';
            $norme_chargee = 'DECRET';
            $a = $this->bdd->prepare("SELECT * FROM auxil_factures WHERE ogd_code = ? AND facture_statut = ? AND num_decret = ?");
            $a->execute(array($code_ogd, $statut, $num_fichier));
        }elseif($norme == 'REJETSC') {
            $statut = 'R';
            $norme_chargee = 'DECRET';
            $a = $this->bdd->prepare("SELECT * FROM auxil_factures WHERE ogd_code = ? AND facture_statut = ? AND num_decret = ?");
            $a->execute(array($code_ogd, $statut, $num_fichier));
        }elseif($norme == 'TDM_EDT_009E') {
            $statut = 'P';
            $norme_chargee = 'DECPAI';
            $a = $this->bdd->prepare("SELECT * FROM auxil_factures WHERE ogd_code = ? AND facture_statut = ? AND num_decpai = ?");
            $a->execute(array($code_ogd, $statut, $num_fichier));
        }elseif($norme == 'BPAIEMENTS') {
            $statut = 'T';
            $norme_chargee = 'DECPAI';
            $a = $this->bdd->prepare("SELECT * FROM auxil_factures WHERE ogd_code = ? AND facture_statut = ? AND num_decpai = ?");
            $a->execute(array($code_ogd, $statut, $num_fichier));
        }elseif($norme == 'REJETSO') {
            $statut = 'R';
            $norme_chargee = 'DECLIQ';
            $a = $this->bdd->prepare("SELECT * FROM auxil_factures WHERE ogd_code = ? AND facture_statut = ? AND num_decliq = ?");
            $a->execute(array($code_ogd, $statut, $num_fichier));
        }
        return $a->fetchAll();

    }

    public function edition_facture_decliq($num_facture, $num_liquidation, $lien_archivage, $statut, $user) {

        $maj_statut = $this->edition_facture_statut($num_facture,$statut,NULL,$user);
        if($maj_statut['success'] == true) {
            $a = $this->bdd->prepare('UPDATE auxil_factures SET num_decliq = ?, lien_archivage = ?, date_edit = ?, user_edit = ? WHERE facture_num = ?');
            $a->execute(array($num_liquidation, $lien_archivage,date('Y-m-d H:i:s',time()),$user,$num_facture));
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
        }else {
            $json = $maj_statut;
        }


        return $json;
    }

    public function edition_facture_decpai($num_facture, $num_paiement, $lien_archivage, $statut, $user) {

        $maj_statut = $this->edition_facture_statut($num_facture,$statut,NULL,$user);
        if($maj_statut['success'] == true) {
            $a = $this->bdd->prepare('UPDATE auxil_factures SET num_decpai = ?, lien_archivage = ?, date_edit = ?, user_edit = ? WHERE facture_num = ?');
            $a->execute(array($num_paiement, $lien_archivage,date('Y-m-d H:i:s',time()),$user,$num_facture));
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
        }else {
            $json = $maj_statut;
        }


        return $json;
    }

    public function edition_facture_cheque($num_facture, $num_fichier, $lien_archivage, $statut, $user) {

        $maj_statut = $this->edition_facture_statut($num_facture,$statut,NULL,$user);
        if($maj_statut['success'] == true) {
            $a = $this->bdd->prepare('UPDATE auxil_factures SET num_fichier = ?, lien_archivage = ?, date_edit = ?, user_edit = ? WHERE facture_num = ?');
            $a->execute(array($num_fichier, $lien_archivage,date('Y-m-d H:i:s',time()),$user,$num_facture));
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
        }else {
            $json = $maj_statut;
        }


        return $json;
    }

    public function edition_factures_organisme_rejetees($code_ogd, $num_fichier_deca, $num_fichier_decliq, $user) {
        $a = $this->bdd->prepare('UPDATE auxil_factures SET num_decliq = ?, date_edit = ?, user_edit = ? WHERE ogd_code = ? AND num_deca = ? AND facture_statut = ? AND num_decliq IS NULL');
        $a->execute(array($num_fichier_decliq, date('Y-m-d H:i:s',time()), $user, $code_ogd, $num_fichier_deca,'R'));
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

    public function lister_factures_ets_payees($code_ogd, $num_fichier, $statut) {
        $a = $this->bdd->prepare("SELECT ets_code AS code_ets, COUNT(facture_num) AS nombre_factures FROM auxil_factures WHERE ogd_code = ? AND num_decpai = ? AND facture_statut = ? AND facture_num IN (SELECT num_facture FROM auxil_factures_lignes_acte) GROUP BY ets_code");
        $a->execute(array($code_ogd, $num_fichier, $statut));
        return $a->fetchAll();
    }

    public function trouver_montant_paiement_par_ets($code_ogd, $num_fichier, $statut, $code_ets) {
        $a = $this->bdd->prepare("SELECT COUNT(DISTINCT A.facture_num) AS nombre_factures, ROUND(SUM(B.montant_depense),2) AS montant_depense,ROUND(SUM(B.montant_remboursement_ro),2) AS montant_remboursement FROM auxil_factures A,auxil_factures_lignes_acte B WHERE A.facture_num = B.num_facture AND A.ogd_code = ? AND A.num_decpai = ? AND A.facture_statut = ? AND A.ets_code = ?");
        $a->execute(array($code_ogd, $num_fichier, $statut, $code_ets));
        return $a->fetch();
    }

    public function lister_factures_payees_a_telecharger_par($code_ogd, $num_fichier, $statut, $code_ets) {
        $a = $this->bdd->prepare("SELECT * FROM auxil_factures WHERE ogd_code = ? AND num_decpai = ? AND facture_statut = ? AND facture_num IN (SELECT num_facture FROM auxil_factures_lignes_acte) AND ets_code = ?");
        $a->execute(array($code_ogd, $num_fichier, $statut, $code_ets));
        return $a->fetchAll();
    }

    public function lister_ets_bordereaux_paiements($code_ogd, $statut, $num_fichier) {
        $a = $this->bdd->prepare("SELECT A.ets_code AS code_ets, B.ets_raison_sociale AS raison_sociale, B.ets_nom_bureau_distributeur AS ville, count(A.facture_num) AS nb_factures FROM auxil_factures A JOIN auxil_ref_etablissement_sante B ON A.ets_code = B.ets_code AND A.ogd_code = ? AND A.facture_statut = ?  AND A.num_decpai = ? GROUP BY A.ets_code, B.ets_raison_sociale, B.ets_nom_bureau_distributeur");
        $a->execute(array($code_ogd, $statut, $num_fichier));
        return $a->fetchAll();
    }
}