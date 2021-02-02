<?php


class LETTRESCLES extends BDD
{
    public function trouver($code){
        $a = $this->bdd->prepare("
        SELECT 
               lettre_date_maj AS date_maj, 
               lettre_code AS code, 
               lettre_libelle AS libelle, 
               lettre_convention AS convention, 
               lettre_prix_unitaire AS prix_unitaire, 
               lettre_date_effet AS date_effet, 
               lettre_date_debut AS date_debut, 
               lettre_date_fin AS date_fin, 
               date_reg, user_reg 
        FROM 
             auxil_ref_lettre_cle 
        
        WHERE lettre_code = ?
        ");
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }

    public function lecture_ficher_txt($type_enregistrement, $type_emetteur, $numero_emetteur, $programme_emetteur, $type_destinataire, $numero_destinataire, $programme_destinataire, $type_echange, $identification_fichier, $date_creation_fichier, $informations_NOEMIE, $numero_chronologique, $version_fichier, $type_fichier, $filler, $code_lc, $designation, $date_creation, $code_convention, $libelle_convention, $prix_unitaire, $date_effet_lettre_cle) {
        $norme = 'REFLETCLE01';
        $LOGS = new LOGS();
        if($type_enregistrement == '000') {
            if(strlen($type_enregistrement.$type_emetteur.$numero_emetteur.$programme_emetteur.$type_destinataire.$numero_destinataire.$programme_destinataire.$type_echange.$identification_fichier.str_replace('-','',$date_creation_fichier).$informations_NOEMIE.$numero_chronologique.$version_fichier.$type_fichier.$filler) == 68) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[0] = array(
                    'message' => "L'entete du fichier est incompatible avec la documentation fournie."
                );
            }
            if(strlen($numero_chronologique) == 5) {
                $num_fichier = $LOGS->trouver_historique_version_fichier($norme,$numero_chronologique);
                if($num_fichier) {
                    $json[1] = array(
                        'message' => "Le numero fichier: {$numero_chronologique} a déjà été utilisé pour un autre chargement"
                    );
                }else {
                    $json = array(
                        'message' => NULL
                    );
                }
            }else {
                $json[1] = array(
                    'message' => "Le numero: {$numero_chronologique} du fichier est incorrect."
                );
            }
            if(strlen($version_fichier) == 4) {
                $version = $LOGS->trouver_historique_version_fichier($norme,$numero_chronologique);
                if($version) {
                    $json[2] = array(
                        'message' => "La version: {$version_fichier} a déjà été utilisée pour un autre chargement"
                    );
                }else {
                    $json = array(
                        'message' => NULL
                    );
                }
            }else {
                $json[1] = array(
                    'message' => "La version: {$version_fichier} du fichier est incorrecte."
                );
            }
        }else {
            if($code_lc) {
                $json = array(
                    'message' => NULL
                );
            }else {
                $json[1] = array(
                    'message' => "Le code de la lettre clé est incorrect."
                );
            }
            $json = array(
                'message' => NULL
            );
        }

        return $json;
    }

    public function ajouter($num_fichier, $date_maj, $code, $libelle, $convention, $prix_unitaire, $date_effet, $date_debut, $user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_ref_lettre_cle(fichier_num, lettre_date_maj, lettre_code, lettre_libelle, lettre_convention, lettre_prix_unitaire, lettre_date_effet, lettre_date_debut, user_reg) 
                    VALUES(:fichier_num, :lettre_date_maj, :lettre_code, :lettre_libelle, :lettre_convention, :lettre_prix_unitaire, :lettre_date_effet, :lettre_date_debut, :user_reg)');
        $a->execute(array(
            'fichier_num' => $num_fichier,
            'lettre_date_maj' => $date_maj,
            'lettre_code' => $code,
            'lettre_libelle' => $libelle,
            'lettre_convention' => $convention,
            'lettre_prix_unitaire' => $prix_unitaire,
            'lettre_date_effet' => $date_maj,
            'lettre_date_debut' => $date_debut,
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

    public function mise_a_jour($date_debut, $date_fin, $code, $user) {
        $a = $this->bdd->prepare('UPDATE auxil_ref_lettre_cle SET lettre_date_fin = ?, date_edit = ?, user_edit = ? WHERE lettre_code = ? AND lettre_date_debut = ?');
        $a->execute(array($date_fin,$date_debut,$user,$code,$date_debut));
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

    public function edition($num_fichier, $date_maj, $code, $libelle, $convention, $prix_unitaire, $date_effet, $date_debut, $user) {
        $lettre = $this->trouver($code);
        if($lettre['code']) {
            $type = 1;
            if(strtotime($lettre['date_debut']) == strtotime($date_debut)) {
                $retour = array(
                    'success' => false,
                    'message' => 'Les données contenues dans ce fichier ont déjà été chargées. Veuillez vérifier votre fichier.'
                );
            }else {
                $date_fin = date('Y-m-d', strtotime("-1 days", strtotime($date_debut)));
                $mise_a_jour = $this->mise_a_jour($lettre['date_debut'],$date_fin,$code,$user);
                if($mise_a_jour['success'] == true) {
                    $retour = $this->ajouter($num_fichier, $date_maj, $code, $libelle, $convention, $prix_unitaire, $date_effet, $date_debut, $user);
                }else{
                    $retour = $mise_a_jour;
                }
            }
        }else {
            $type = 0;
            $retour = $this->ajouter($num_fichier, $date_maj, $code, $libelle, $convention, $prix_unitaire, $date_effet, $date_debut, $user);
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