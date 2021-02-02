<?php


class PATHOLOGIES extends BDD
{
    public function trouver($code) {
        $a = $this->bdd->prepare('
SELECT
       pathologie_code AS code, 
       pathologie_libelle AS libelle, 
       panier_statut AS statut, 
       pathologie_date_debut AS date_debut, 
       pathologie_date_fin AS date_fin, 
       date_reg, 
       user_reg, 
       date_edit, 
       user_edit 
FROM auxil_ref_pathologie WHERE pathologie_code = ? AND pathologie_date_fin IS NULL');
        $a->execute(array($code));
        return $a->fetch();
    }
    public function lecture_fichier_xml($code,$libelle,$date_debut,$date_fin) {
        if($code) {
            if(strlen($code) == 3) {
                $pathologie = $this->trouver($code);
                if(!$pathologie['code']) {
                    $json[0] = NULL;
                }else {
                    if(strtotime($pathologie['date_debut']) != strtotime(date('Y-m-d',strtotime($date_debut)))) {
                        $json[0] = NULL;
                    }else {
                        $json[1] = array(
                            'message' => "L'affection a déjà été enregistrée à la date {$date_debut}"
                        );
                    }
                }
            }else {
                $json[1] = array(
                    'message' => "La longueur du code affection ne correspond pas à celle requise."
                );
            }
        }else {
            $json[1] = array(
                'message' => "Le code de l' affection: {$libelle} n'est pas renseigné."
            );
        }
        if($libelle) {
            $json[0] = NULL;
        }else {
            $json[3] = array(
                'message' => "Le nom de l'affection n'est pas renseigné."
            );
        }
        if($date_debut) {
            if(checkdate(date('m',strtotime($date_debut)),date('d',strtotime($date_debut)),date('Y',strtotime($date_debut)))) {
                $json[0] = NULL;
            }else {
                $json[6] = array(
                    'message' => "Le format de la date de début de la validité de l'affection est incorrect."
                );
            }
        }else {
            $json[7] = array(
                'message' => "La date de début de la validité de l'affection n'est pas renseignée."
            );
        }
        if(!empty($date_fin)) {
            if(checkdate(date('m',strtotime($date_fin)),date('d',strtotime($date_fin)),date('Y',strtotime($date_fin)))) {
                if($date_debut == $date_fin) {
                    $json[7] = array(
                        'message' => "Les dates de début: {$date_debut} et de fin: {$date_fin} de la validité de l'affection sont incorrects."
                    );
                }else {
                    $json[0] = NULL;
                }
            }else {
                $json[7] = array(
                    'message' => "Le format de la date de fin de la validité de l'affection est incorrect."
                );
            }
        }else {
            $json[0] = NULL;
        }
        return $json;
    }
    private function ajouter($num_fichier, $code, $chapitre, $sous_chapitre, $libelle, $panier_soins, $date_debut, $user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_ref_pathologie(num_fichier, pathologie_code, pathologie_code_chapitre, pathologie_code_sous_chapitre, pathologie_libelle, pathologie_date_debut, panier_statut, user_reg) 
        VALUES(:num_fichier, :pathologie_code, :pathologie_code_chapitre, :pathologie_code_sous_chapitre, :pathologie_libelle, :pathologie_date_debut, :panier_statut, :user_reg)');
        $a->execute(array(
            'num_fichier' => $num_fichier,
            'pathologie_code' => $code,
            'pathologie_code_chapitre' => $chapitre,
            'pathologie_libelle' => $libelle,
            'pathologie_code_sous_chapitre' => $sous_chapitre,
            'pathologie_date_debut' => $date_debut,
            'panier_statut' => $panier_soins,
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
        $a = $this->bdd->prepare('UPDATE auxil_ref_pathologie SET pathologie_date_fin = ?, date_edit = ?, user_edit = ? WHERE pathologie_code = ? AND pathologie_date_debut = ?');
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
    public function edition($num_fichier, $code, $chapitre, $sous_chapitre, $libelle, $panier_soins, $date_debut, $user) {
        $pathologie = $this->trouver($code);
        if($pathologie['code']) {
            $type = 1;
            if(strtotime($pathologie['date_debut']) == strtotime($date_debut)) {
                $retour = array(
                    'success' => false,
                    'message' => 'Les données contenues dans ce fichier ont déjà été chargées. Veuillez vérifier votre fichier.'
                );
            }else {
                $date_fin_validite = date('Y-m-d', strtotime("-1 days", strtotime($date_debut)));
                $mise_a_jour = $this->mise_a_jour($pathologie['date_debut'],$date_fin_validite,$code,$user);
                if($mise_a_jour['success'] == true) {
                    $retour = $this->ajouter($num_fichier, $code, $chapitre, $sous_chapitre, $libelle, $panier_soins, $date_debut, $user);
                }else{
                    $retour = $mise_a_jour;
                }
            }
        }else {
            $type = 0;
            $retour = $this->ajouter($num_fichier, $code, $chapitre, $sous_chapitre, $libelle, $panier_soins, $date_debut, $user);
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