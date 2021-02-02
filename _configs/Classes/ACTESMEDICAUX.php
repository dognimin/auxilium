<?php


class ACTESMEDICAUX extends BDD
{
    public function trouver($code) {
        $a = $this->bdd->prepare("SELECT
          actes_date_maj AS date_maj,
          actes_type_acte AS type_acte, 
          actes_correspondance AS correspondance, 
          actes_code AS code, 
          actes_libelle AS libelle, 
          actes_titre AS titre, 
          actes_chapitre AS chapitre,
          actes_section AS section, 
          actes_article AS article, 
          actes_convention AS convention, 
          actes_grille AS grille, 
          actes_sous_grille AS sous_grille, 
          actes_lettre_cle AS lettre_cle, 
          actes_coefficient AS coefficient, 
          actes_tarif AS tarif, 
          actes_panier AS panier,
          actes_entente_prealable AS entente_prealable,
          actes_date_debut_validite AS date_debut_validite, 
          actes_date_fin_validite AS date_fin_validite, 
          date_reg, 
          user_reg
        FROM 
          auxil_ref_actes_medicaux 
        WHERE 
          actes_code = ? AND 
          actes_date_fin_validite IS NULL");
        $a->execute(array($code));
        return $a->fetch();
    }

    public function lister() {
        $a = $this->bdd->prepare("SELECT
          actes_date_maj AS date_maj,
          actes_type_acte AS type_acte, 
          actes_correspondance AS correspondance, 
          actes_code AS code, 
          actes_libelle AS libelle, 
          actes_titre AS titre, 
          actes_chapitre AS chapitre,
          actes_section AS section, 
          actes_article AS article, 
          actes_convention AS convention, 
          actes_grille AS grille, 
          actes_sous_grille AS sous_grille, 
          actes_lettre_cle AS lettre_cle, 
          actes_coefficient AS coefficient, 
          actes_tarif AS tarif, 
          actes_panier AS panier,
          actes_entente_prealable AS entente_prealable,
          actes_date_debut_validite AS date_debut_validite, 
          actes_date_fin_validite AS date_fin_validite, 
          date_reg, 
          user_reg
        FROM 
          auxil_ref_actes_medicaux 
        WHERE 
          actes_date_fin_validite IS NULL ORDER BY actes_libelle");
        $a->execute(array());
        return $a->fetchAll();
    }

    public function moteur_recherche($type, $code, $libelle, $code_lettre_cle) {
        $a = $this->bdd->prepare("SELECT
          actes_date_maj AS date_maj,
          actes_type_acte AS type_acte, 
          actes_correspondance AS correspondance, 
          actes_code AS code, 
          actes_libelle AS libelle, 
          actes_titre AS titre, 
          actes_chapitre AS chapitre,
          actes_section AS section, 
          actes_article AS article, 
          actes_convention AS convention, 
          actes_grille AS grille, 
          actes_sous_grille AS sous_grille, 
          actes_lettre_cle AS lettre_cle, 
          actes_coefficient AS coefficient, 
          actes_tarif AS tarif, 
          actes_panier AS panier,
          actes_entente_prealable AS entente_prealable,
          actes_date_debut_validite AS date_debut_validite, 
          actes_date_fin_validite AS date_fin_validite, 
          date_reg, 
          user_reg
        FROM 
          auxil_ref_actes_medicaux 
        WHERE 
          actes_type_acte = ? AND 
          actes_code LIKE ? AND 
          actes_libelle LIKE ? AND 
          actes_lettre_cle LIKE ? AND 
          actes_date_fin_validite IS NULL ORDER BY actes_libelle");
        $a->execute(array($type,'%'.$code.'%','%'.$libelle.'%','%'.$code_lettre_cle.'%'));
        return $a->fetchAll();
    }

    public function lecture_ficher_txt($type_enregistrement, $type_emetteur, $numero_emetteur, $programme_emetteur, $type_destinataire, $numero_destinataire, $programme_destinataire, $type_echange, $identification_fichier, $date_creation_fichier, $informations_NOEMIE, $numero_chronologique, $version_fichier, $type_fichier, $type_acte, $code, $designation, $libelle_titre, $libelle_section, $libelle_article, $libelle_chapitre, $code_lettre_cle) {
        if($type_enregistrement == '000') {
            $norme = 'REFNGAMBCI01';
            $LOGS = new LOGS();
            $version = $LOGS->trouver_historique_version_fichier($norme,$version_fichier);
            if($version) {
                $json[1] = array(
                    'message' => "La version du fichier: {$version_fichier} a déjà été utilisée pour un autre chargement"
                );
            }else {
                $json = array(
                    'message' => NULL
                );
            }
            $num_fichier = $LOGS->trouver_fichier($norme,$numero_chronologique,NULL,NULL);
            if($num_fichier) {
                $json[2] = array(
                    'message' => "Le numero fichier: {$numero_chronologique} a déjà été utilisé pour un autre chargement"
                );
            }else {
                $json = array(
                    'message' => NULL
                );
            }
        }else {
            $LETTRESCLES = new LETTRESCLES();
            if(strlen($code) == 7) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[0] = array(
                    'message' => "Le code de l'acte est incompatible avec la documentation fournie."
                );
            }
            $acte = $this->trouver($code);
            if($acte) {
                if(strtotime($acte['date_debut_validite']) == strtotime($date_creation_fichier)) {
                    $json[1] = array(
                        'message' => "L'acte ayant le code {$code} a déjà été enregistré dans le système."
                    );
                }else {
                    $json = array(
                        'message' => NULL
                    );
                }
            }else {
                $json = array(
                    'message' => NULL
                );
            }
            if($type_acte == 'NGAP') {
                if(strlen($type_enregistrement.$type_emetteur.$numero_emetteur.$programme_emetteur.$type_destinataire.$numero_destinataire.$programme_destinataire.$type_echange.$identification_fichier.str_replace('-','',$date_creation_fichier).$informations_NOEMIE.$numero_chronologique.$version_fichier.$type_fichier) == 68) {
                    $json = array(
                        'message' => NULL
                    );
                }else {
                    $json[0] = array(
                        'message' => "L'entete du fichier est incompatible avec la documentation fournie."
                    );
                }
                if($code_lettre_cle) {
                    $lettre_cle = $LETTRESCLES->trouver($code_lettre_cle);
                    if($lettre_cle) {
                        $json = array(
                            'message' => NULL
                        );
                    }else {
                        $json[2] = array(
                            'message' => "La lettre clé renseignée est incorrecte."
                        );
                    }
                }else {
                    $json[2] = array(
                        'message' => "L'acte ayant le code {$code} ne possède pas de lettre clé."
                    );
                }
                if($libelle_titre)  {
                    $json = array(
                        'message' => NULL
                    );
                }else {
                    $json[4] = array(
                        'message' => "Le titre de l'acte ayant le code {$code} n'a pas été renseigné."
                    );
                }
                if($libelle_section)  {
                    $json = array(
                        'message' => NULL
                    );
                }else {
                    $json[5] = array(
                        'message' => "La section de l'acte ayant le code {$code} n'a pas été renseignée."
                    );
                }
                if($libelle_chapitre)  {
                    $json = array(
                        'message' => NULL
                    );
                }else {
                    $json[6] = array(
                        'message' => "Le chapitre de l'acte ayant le code {$code} n'a pas été renseigné."
                    );
                }
                if($libelle_article)  {
                    $json = array(
                        'message' => NULL
                    );
                }else {
                    $json[7] = array(
                        'message' => "L'article de l'acte ayant le code {$code} n'a pas été renseigné."
                    );
                }
            }else {
                if(strlen($type_enregistrement.$type_emetteur.$numero_emetteur.$programme_emetteur.$type_destinataire.$numero_destinataire.$programme_destinataire.$type_echange.$identification_fichier.str_replace('-','',$date_creation_fichier).$informations_NOEMIE.$numero_chronologique.$version_fichier.$type_fichier) == 78) {
                    $json = array(
                        'message' => NULL
                    );
                }else {
                    $json[0] = array(
                        'message' => "L'entete du fichier est incompatible avec la documentation fournie."
                    );
                }
            }

            if($designation)  {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[3] = array(
                    'message' => "Le libellé de l'acte ayant le code {$code} n'a pas été renseigné."
                );
            }
        }

        return $json;
    }

    public function lecture_fichier_xml($type_acte, $code, $titre, $chapitre, $section, $article, $libelle, $code_lettre_cle, $tarif, $date_debut, $date_fin) {
        if($code) {
            $acte = $this->trouver($code);
            if(!$acte['code']) {
                $json[0] = NULL;
            }else {
                if(strtotime($acte['date_debut_validite']) != strtotime(date('Y-m-d',strtotime($date_debut)))) {
                    $json[0] = NULL;
                }else {
                    $json[1] = array(
                        'message' => "La lettre clé a déjà été enregistrée à la date {$date_debut}"
                    );
                }
            }
        }else {
            $json[1] = array(
                'message' => "Le code de l'acte: {$libelle} n'est pas renseigné."
            );
        }
        if($libelle) {
            $json[0] = NULL;
        }else {
            $json[2] = array(
                'message' => "Le nom de l'acte n'est pas renseigné."
            );
        }

        if($type_acte =='NGAP') {
            if($titre) {
                $json[0] = NULL;
            }else {
                $json[3] = array(
                    'message' => "Le titre de l'acte n'est pas défini."
                );
            }
            if($chapitre) {
                $json[0] = NULL;
            }else {
                $json[4] = array(
                    'message' => "Le chapitre de l'acte n'est pas défini."
                );
            }
            if($section) {
                $json[0] = NULL;
            }else {
                $json[5] = array(
                    'message' => "La section de l'acte n'est pas définie."
                );
            }
            if($article) {
                $json[0] = NULL;
            }else {
                $json[6] = array(
                    'message' => "L'article de l'acte n'est pas défini."
                );
            }
            if($code_lettre_cle) {
                $json[0] = NULL;
            }else {
                $json[7] = array(
                    'message' => "Le code de la lettre clé de l'acte n'est pas défini."
                );
            }
        }
        elseif ($type_acte =='FH') {
            if($tarif) {
                $json[0] = NULL;
            }else {
                $json[11] = array(
                    'message' => "Le tarif de l'acte n'est pas défini."
                );
            }
        }
        else {
            $json[8] = array(
                'message' => "Le type d'acte défini est incorrect."
            );
        }

        if($date_debut) {
            if(checkdate(date('m',strtotime($date_debut)),date('d',strtotime($date_debut)),date('Y',strtotime($date_debut)))) {
                $json[0] = NULL;
            }else {
                $json[9] = array(
                    'message' => "Le format de la date de début de la validité de l'acte est incorrect."
                );
            }
        }else {
            $json[9] = array(
                'message' => "La date de début de la validité de l'acte n'est pas renseignée."
            );
        }
        if(!empty($date_fin)) {
            if(checkdate(date('m',strtotime($date_fin)),date('d',strtotime($date_fin)),date('Y',strtotime($date_fin)))) {
                if($date_debut == $date_fin) {
                    $json[10] = array(
                        'message' => "Les dates de début: {$date_debut} et de fin: {$date_fin} de la validité de l'acte sont incorrects."
                    );
                }else {
                    $json[0] = NULL;
                }
            }else {
                $json[10] = array(
                    'message' => "Le format de la date de fin de la validité de llacte est incorrect."
                );
            }
        }else {
            $json[0] = NULL;
        }
        return $json;
    }

    private function ajouter($numero_chronologique, $date_creation_fichier, $type_acte, $code, $designation, $libelle_titre, $libelle_chapitre, $libelle_section, $libelle_article, $code_lettre_cle, $coefficient, $tarif, $user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_ref_actes_medicaux(fichier_num, actes_date_maj, actes_type_acte, actes_code, actes_libelle, actes_titre, actes_chapitre, actes_section, actes_article, actes_lettre_cle, actes_coefficient, actes_tarif, actes_date_debut_validite, user_reg) 
                    VALUES(:fichier_num, :actes_date_maj, :actes_type_acte, :actes_code, :actes_libelle, :actes_titre, :actes_chapitre, :actes_section, :actes_article, :actes_lettre_cle, :actes_coefficient, :actes_tarif, :actes_date_debut_validite, :user_reg)');
        $a->execute(array(
            'fichier_num' => $numero_chronologique,
            'actes_date_maj' => $date_creation_fichier,
            'actes_type_acte' => $type_acte,
            'actes_code' => $code,
            'actes_libelle' => $designation,
            'actes_titre' => $libelle_titre,
            'actes_chapitre' => $libelle_chapitre,
            'actes_section' => $libelle_section,
            'actes_article' => $libelle_article,
            'actes_lettre_cle' => $code_lettre_cle,
            'actes_coefficient' => intval($coefficient,10),
            'actes_tarif' => $tarif,
            'actes_date_debut_validite' => $date_creation_fichier,
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

    private function mise_a_jour($date_debut, $date_fin, $code, $user) {
        $a = $this->bdd->prepare('UPDATE auxil_ref_actes_medicaux SET actes_date_fin_validite = ?, date_edit = ?, user_edit = ? WHERE actes_code = ? AND actes_date_debut_validite = ?');
        $a->execute(array($date_fin,date('Y-m-d H:i:s',time()),$user,$code,$date_debut));
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

    public function edition($numero_chronologique, $date_creation_fichier, $type_acte, $code, $designation, $libelle_titre, $libelle_chapitre, $libelle_section, $libelle_article, $code_lettre_cle, $coefficient, $tarif, $user) {
        $acte = $this->trouver($code);
        if($acte['code']) {
            $type = 1;
            if(strtotime($acte['date_debut']) == strtotime($date_creation_fichier)) {
                $retour = array(
                    'success' => false,
                    'message' => 'Les données contenues dans ce fichier ont déjà été chargées. Veuillez vérifier votre fichier.'
                );
            }else {
                $date_fin = date('Y-m-d', strtotime("-1 days", strtotime($date_creation_fichier)));
                $mise_a_jour = $this->mise_a_jour($acte['date_debut_validite'],$date_fin,$code,$user);
                if($mise_a_jour['success'] == true) {
                    $retour = $this->ajouter($numero_chronologique, $date_creation_fichier, $type_acte, $code, $designation, $libelle_titre, $libelle_chapitre, $libelle_section, $libelle_article, $code_lettre_cle,$coefficient,$tarif,$user);
                }else{
                    $retour = $mise_a_jour;
                }
            }
        }else {
            $type = 0;
            $retour = $this->ajouter($numero_chronologique, $date_creation_fichier, $type_acte, $code, $designation, $libelle_titre, $libelle_chapitre, $libelle_section, $libelle_article, $code_lettre_cle,$coefficient,$tarif,$user);
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
}