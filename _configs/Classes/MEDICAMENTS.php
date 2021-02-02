<?php


class MEDICAMENTS extends BDD
{
    public function trouver($code){
        $query = '
        SELECT 
          medicaments_code AS code,
          medicaments_laboratoire AS laboratoire,
          medicaments_ean13 AS ean13,
          medicaments_libelle AS libelle,
          medicaments_dosage AS dosage,
          medicaments_unite AS unite,
          medicaments_forme AS forme,
          medicaments_conditionnement AS conditionnement,
          medicaments_tableau_abc AS tableau_abc,
          medicaments_prix_unitaire AS prix_unitaire,
          medicaments_taux_remboursement AS taux_remboursement,
          medicaments_taux_exoneration AS taux_exoneration,
          medicaments_date_effet_pp AS date_effet_pp,
          medicaments_date_debut_remboursement_pp AS date_debut_remboursement_pp,
          medicaments_date_fin_remboursement_ppm AS date_fin_remboursement_ppm,
          medicaments_date_debut_validite AS date_debut_validite,
          medicaments_date_fin_validite AS date_fin_validite,
          date_reg,
          user_reg 
        FROM 
          auxil_ref_medicaments 
        WHERE 
          medicaments_code = ? AND 
          medicaments_date_fin_validite IS NULL
        ';
        $a = $this->bdd->prepare($query);
        $a->execute(array($code));
        return $a->fetch();
    }

    public function lecture_ficher_txt() {}
    public function lecture_fichier_xml($code,$code_ean13,$libelle,$dosage,$unite,$tarif,$date_debut,$date_fin) {
        if($code) {
            if(strlen($code) == 13) {
                $medicament = $this->trouver($code);
                if(!$medicament['code']) {
                    $json[0] = NULL;
                }else {
                    if(strtotime($medicament['date_debut_validite']) != strtotime(date('Y-m-d',strtotime($date_debut)))) {
                        $json[0] = NULL;
                    }else {
                        $json[1] = array(
                            'message' => "Le médicament a déjà été enregistré à la date {$date_debut}"
                        );
                    }
                }
            }else {
                $json[1] = array(
                    'message' => "La longueur du code médicament ne correspond pas à celle requise."
                );
            }
        }else {
            $json[1] = array(
                'message' => "Le code du médicament: {$libelle} n'est pas renseigné."
            );
        }
        if($code_ean13) {
            $json[0] = NULL;
        }else {
            $json[2] = array(
                'message' => "Le code EAN13 du médicament n'est pas renseigné."
            );
        }
        if($libelle) {
            $json[0] = NULL;
        }else {
            $json[3] = array(
                'message' => "Le nom du médicament n'est pas renseigné."
            );
        }
        if($dosage) {
            $json[0] = NULL;
        }else {
            $json[4] = array(
                'message' => "Le dosage du médicament n'est pas renseigné."
            );
        }
        if($unite) {
            $json[0] = NULL;
        }else {
            $json[5] = array(
                'message' => "L'unité du médicament n'est pas renseignée."
            );
        }
        if($tarif) {
            $json[0] = NULL;
        }else {
            $json[6] = array(
                'message' => "Le tarif du médicament n'est pas renseigné."
            );
        }
        if($date_debut) {
            if(checkdate(date('m',strtotime($date_debut)),date('d',strtotime($date_debut)),date('Y',strtotime($date_debut)))) {
                $json[0] = NULL;
            }else {
                $json[6] = array(
                    'message' => "Le format de la date de début de la validité du médicament est incorrect."
                );
            }
        }else {
            $json[7] = array(
                'message' => "La date de début de la validité de l'établissement n'est pas renseignée."
            );
        }
        if(!empty($date_fin)) {
            if(checkdate(date('m',strtotime($date_fin)),date('d',strtotime($date_fin)),date('Y',strtotime($date_fin)))) {
                if($date_debut == $date_fin) {
                    $json[7] = array(
                        'message' => "Les dates de début: {$date_debut} et de fin: {$date_fin} de la validité du médicament sont incorrects."
                    );
                }else {
                    $json[0] = NULL;
                }
            }else {
                $json[7] = array(
                    'message' => "Le format de la date de fin de la validité du médicament: {$code} est incorrect."
                );
            }
        }else {
            $json[0] = NULL;
        }
        return $json;
    }
    private function ajouter($num_fichier, $code_medicament, $code_laboratroire, $ean13, $libelle, $dosage, $unite_dosage, $libelle_forme, $conditionnement, $tableau_abc, $prix_ppm, $code_taux_remboursement, $code_taux_exonerationt, $date_maj_ppm_phm, $date_debut_remboursement_pp, $date_fin_remboursement_ppm, $date_debut_validite, $user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_ref_medicaments(fichier_num, medicaments_code, medicaments_laboratoire, medicaments_ean13, medicaments_libelle, medicaments_dosage, medicaments_unite, medicaments_forme, medicaments_conditionnement, medicaments_tableau_abc, medicaments_prix_unitaire, medicaments_taux_remboursement, medicaments_taux_exoneration, medicaments_date_effet_pp, medicaments_date_debut_remboursement_pp, medicaments_date_fin_remboursement_ppm, medicaments_date_debut_validite, user_reg) 
                VALUES(:fichier_num, :medicaments_code, :medicaments_laboratoire, :medicaments_ean13, :medicaments_libelle, :medicaments_dosage, :medicaments_unite, :medicaments_forme, :medicaments_conditionnement, :medicaments_tableau_abc, :medicaments_prix_unitaire, :medicaments_taux_remboursement, :medicaments_taux_exoneration, :medicaments_date_effet_pp, :medicaments_date_debut_remboursement_pp, :medicaments_date_fin_remboursement_ppm, :medicaments_date_debut_validite, :user_reg)');
        $a->execute(array(
            'fichier_num' => $num_fichier,
            'medicaments_code' => $code_medicament,
            'medicaments_laboratoire' => $code_laboratroire,
            'medicaments_ean13' => $ean13,
            'medicaments_libelle' => $libelle,
            'medicaments_dosage' => $dosage,
            'medicaments_unite' => $unite_dosage,
            'medicaments_forme' => $libelle_forme,
            'medicaments_conditionnement' => $conditionnement,
            'medicaments_tableau_abc' => $tableau_abc,
            'medicaments_prix_unitaire' => $prix_ppm,
            'medicaments_taux_remboursement' => $code_taux_remboursement,
            'medicaments_taux_exoneration' => $code_taux_exonerationt,
            'medicaments_date_effet_pp' => $date_maj_ppm_phm,
            'medicaments_date_debut_remboursement_pp' => $date_debut_remboursement_pp,
            'medicaments_date_fin_remboursement_ppm' => $date_fin_remboursement_ppm,
            'medicaments_date_debut_validite' => $date_debut_validite,
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
        $a = $this->bdd->prepare('UPDATE auxil_ref_medicaments SET medicaments_date_fin_validite = ?, date_edit = ?, user_edit = ? WHERE medicaments_code = ? AND medicaments_date_debut_validite = ?');
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
    public function edition($num_fichier, $code_medicament, $code_laboratroire, $ean13, $libelle, $dosage, $unite_dosage, $libelle_forme, $conditionnement, $tableau_abc, $prix_ppm, $code_taux_remboursement, $code_taux_exonerationt, $date_maj_ppm_phm, $date_debut_remboursement_pp, $date_fin_remboursement_ppm, $date_debut_validite, $user) {
        $medicament = $this->trouver($code_medicament);
        if($medicament['code']) {
            $type = 1;
            if(strtotime($medicament['date_debut']) == strtotime($date_debut_validite)) {
                $retour = array(
                    'success' => false,
                    'message' => 'Les données contenues dans ce fichier ont déjà été chargées. Veuillez vérifier votre fichier.'
                );
            }else {
                $date_fin = date('Y-m-d', strtotime("-1 days", strtotime($date_debut_validite)));
                $mise_a_jour = $this->mise_a_jour($medicament['date_debut_validite'],$date_fin,$code_medicament,$user);
                if($mise_a_jour['success'] == true) {
                    $retour = $this->ajouter($num_fichier, $code_medicament, $code_laboratroire, $ean13, $libelle, $dosage, $unite_dosage, $libelle_forme, $conditionnement, $tableau_abc, $prix_ppm, $code_taux_remboursement, $code_taux_exonerationt, $date_maj_ppm_phm, $date_debut_remboursement_pp, $date_fin_remboursement_ppm, $date_debut_validite, $user);
                }else{
                    $retour = $mise_a_jour;
                }
            }
        }else {
            $type = 0;
            $retour = $this->ajouter($num_fichier, $code_medicament, $code_laboratroire, $ean13, $libelle, $dosage, $unite_dosage, $libelle_forme, $conditionnement, $tableau_abc, $prix_ppm, $code_taux_remboursement, $code_taux_exonerationt, $date_maj_ppm_phm, $date_debut_remboursement_pp, $date_fin_remboursement_ppm, $date_debut_validite, $user);
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