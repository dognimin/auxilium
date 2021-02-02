<?php


class LOGS extends BDD
{
    function lister_fichiers($code_ogd,$norme,$type_mouvement,$num_fichier,$nom_fichier) {
        $a = $this->bdd->prepare('SELECT log_id AS fichier_id, log_mouvement AS mouvement, log_num_fichier AS num_fichier, log_norme AS norme, log_version AS version, log_date_fichier AS fichier_date, log_code_emetteur AS code_emetteur, log_code_destinataire AS code_destinataire, log_occurrences_fichier AS occurrences_fichier, log_nom_fichier AS fichier_nom, date_reg, user_reg FROM auxil_log_historique_fichier WHERE log_norme LIKE ? AND log_mouvement LIKE ? AND log_num_fichier LIKE ? AND log_nom_fichier LIKE ? AND (log_code_emetteur = ? OR log_code_destinataire = ?) ORDER BY date_reg DESC');
        $a->execute(array('%'.$norme.'%','%'.$type_mouvement.'%','%'.$num_fichier.'%','%'.$nom_fichier.'%',$code_ogd,$code_ogd));
        $json = $a->fetchAll();
        return $json;
    }

    /**
     * @param $norme
     * @param $num_fichier
     * @param $nom_fichier
     * @param $version_fichier
     * @return array|mixed
     */
    public function trouver_fichier($norme, $num_fichier, $nom_fichier, $version_fichier) {
        if($num_fichier) {
            $a = $this->bdd->prepare('
                SELECT 
                       log_id AS id_log, 
                       log_mouvement AS mouvement, 
                       log_num_fichier AS num_fichier, 
                       log_norme AS norme, 
                       log_version AS version, 
                       log_date_fichier AS date_fichier, 
                       log_code_emetteur AS code_emetteur, 
                       log_code_destinataire AS code_destinataire, 
                       log_occurrences_fichier AS occurrences_fichier, 
                       log_occurrences_creation AS occurences_creation, 
                       log_occurrences_modification AS occurrences_modification, 
                       log_nom_fichier AS nom_fichier, 
                       date_reg, 
                       user_reg 
                FROM 
                     auxil_log_historique_fichier 
                WHERE 
                      log_norme = ? 
                  AND log_num_fichier = ?
            ');
            $a->execute(array($norme,$num_fichier));
            $json = $a->fetch();
        }
        elseif ($nom_fichier) {
            $a = $this->bdd->prepare('
                SELECT 
                       log_id AS id_log, 
                       log_mouvement AS mouvement, 
                       log_num_fichier AS num_fichier, 
                       log_norme AS norme, 
                       log_version AS version, 
                       log_date_fichier AS date_fichier, 
                       log_code_emetteur AS code_emetteur, 
                       log_code_destinataire AS code_destinataire, 
                       log_occurrences_fichier AS occurrences_fichier, 
                       log_occurrences_creation AS occurences_creation, 
                       log_occurrences_modification AS occurrences_modification, 
                       log_nom_fichier AS nom_fichier, 
                       date_reg, 
                       user_reg 
                FROM 
                     auxil_log_historique_fichier 
                WHERE 
                      log_norme = ? 
                  AND log_nom_fichier = ?
            ');
            $a->execute(array($norme,$nom_fichier));
            $json = $a->fetch();
        }
        elseif ($version_fichier) {
            $a = $this->bdd->prepare('
                SELECT 
                       log_id AS id_log, 
                       log_mouvement AS mouvement, 
                       log_num_fichier AS num_fichier, 
                       log_norme AS norme, 
                       log_version AS version, 
                       log_date_fichier AS date_fichier, 
                       log_code_emetteur AS code_emetteur, 
                       log_code_destinataire AS code_destinataire, 
                       log_occurrences_fichier AS occurrences_fichier, 
                       log_occurrences_creation AS occurences_creation, 
                       log_occurrences_modification AS occurrences_modification, 
                       log_nom_fichier AS nom_fichier, 
                       date_reg, 
                       user_reg 
                FROM 
                     auxil_log_historique_fichier 
                WHERE 
                      log_norme = ? 
                  AND log_version = ?
            ');
            $a->execute(array($norme,$version_fichier));
            $json = $a->fetch();
        }
        else {
            $json = array(
                'success' => false,
                'message' => "Données d'entrée incorrectes."
            );
        }
        return $json;
    }

    function trouver_historique_version_fichier($norme,$version_fichier) {
        $a = $this->bdd->prepare('
                    SELECT 
                       log_id AS id_log, 
                       log_mouvement AS mouvement, 
                       log_num_fichier AS num_fichier, 
                       log_norme AS norme, 
                       log_version AS version, 
                       log_date_fichier AS date_fichier, 
                       log_code_emetteur AS code_emetteur, 
                       log_code_destinataire AS code_destinataire, 
                       log_occurrences_fichier AS occurrences_fichier, 
                       log_occurrences_creation AS occurences_creation, 
                       log_occurrences_modification AS occurrences_modification, 
                       log_nom_fichier AS nom_fichier, 
                       date_reg, 
                       user_reg  
                    FROM 
                         auxil_log_historique_fichier 
                    WHERE 
                      log_norme = ? 
                      AND log_version = ?
                  ');
        $a->execute(array($norme,$version_fichier));
        $json = $a->fetch();
        return $json;
    }

    public function ajouter_chargement_fichier($mouvement,$nom_fichier,$statut,$message,$user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_log_chargement_fichier(log_mouvement, log_nom_fichier, log_statut, log_message, user_reg) VALUES(:log_mouvement, :log_nom_fichier, :log_statut, :log_message, :user_reg)');
        $a->execute(array(
            'log_mouvement' => $mouvement,
            'log_nom_fichier' => $nom_fichier,
            'log_statut' => $statut,
            'log_message' => $message,
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

    public function ajouter_historique_fichier($mouvement,$num_fichier,$norme,$version,$date_fichier,$code_emetteur,$code_destinataire,$occurrences_fichier,$occurrences_creation,$occurrences_modification,$nom_fichier,$user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_log_historique_fichier(log_mouvement, log_num_fichier, log_norme, log_version, log_date_fichier, log_code_emetteur, log_code_destinataire, log_occurrences_fichier, log_occurrences_creation, log_occurrences_modification, log_nom_fichier, user_reg) 
        VALUES(:log_mouvement, :log_num_fichier, :log_norme, :log_version, :log_date_fichier, :log_code_emetteur, :log_code_destinataire, :log_occurrences_fichier, :log_occurrences_creation, :log_occurrences_modification, :log_nom_fichier, :user_reg)');
        $a->execute(array(
            'log_mouvement' => $mouvement,
            'log_num_fichier' => $num_fichier,
            'log_norme' => $norme,
            'log_version' => $version,
            'log_date_fichier' => $date_fichier,
            'log_code_emetteur' => $code_emetteur,
            'log_code_destinataire' => $code_destinataire,
            'log_occurrences_fichier' => $occurrences_fichier,
            'log_occurrences_creation' => $occurrences_creation,
            'log_occurrences_modification' => $occurrences_modification,
            'log_nom_fichier' => $nom_fichier,
            'user_reg' => $user
        ));
        if($a->errorCode() === '00000') {
            $json = array(
                'success' => true
            );
        }else {
            $json = array(
                'success' => false,
                'message' => 'Erreur nv 1: '.$a->errorCode().' <=> '.$a->errorInfo()[1].' <=> '.$a->errorInfo()[2]
            );
        }
        return $json;
    }

    public function ajouter_erreur_enregistrement_occurrence($erreur_num_fichier,$erreur_norme,$erreur_code,$erreur_libelle,$erreur_ligne,$erreur_description,$user_reg){
        $a= $this->bdd->prepare('INSERT IGNORE INTO auxil_log_chargement_fichier_erreurs(erreur_num_fichier,erreur_norme,erreur_code,erreur_libelle,erreur_ligne,erreur_description,user_reg) VALUES(:erreur_num_fichier, :erreur_norme, :erreur_code, :erreur_libelle, :erreur_ligne, :erreur_description, :user_reg)');
        $a->execute(array(
            'erreur_num_fichier' => $erreur_num_fichier,
            'erreur_norme' => $erreur_norme,
            'erreur_code' => $erreur_code,
            'erreur_libelle' => $erreur_libelle,
            'erreur_ligne' => $erreur_ligne,
            'erreur_description' => $erreur_description,
            'user_reg' => $user_reg
        ));
        if($a->errorCode() === '00000') {
            $json = array(
                'success' => true
            );
        }else {
            $json = array(
                'success' => false,
                'message' => 'Erreur nv 1: '.$a->errorCode().' <=> '.$a->errorInfo()[1].' <=> '.$a->errorInfo()[2]
            );
        }
        return $json;
    }

    public function trouver_log_fichiers_referentiels(){
        $a = $this->bdd->prepare('SELECT * FROM auxil_log_historique_fichier WHERE log_mouvement = ? AND (log_norme = ? OR log_norme = ? OR log_norme = ? OR log_norme = ? OR log_norme = ? OR log_norme = ? OR log_norme = ? OR log_norme = ? OR log_norme = ?) ORDER BY date_reg DESC');
        $a->execute(array('IMP','REFNGAMBCI01','REFETS01','REFFH01','REFLETCLE01','REFMED01','REFPROF01','REFREJ01','REFSPEC01','REFPATH01'));
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver_num_transmission($code_ogd, $type_mouvement, $norme) {
        $a = $this->bdd->prepare("SELECT MAX(log_num_fichier) AS num_fichier FROM auxil_log_historique_fichier WHERE log_code_emetteur = ? AND log_mouvement = ? AND log_norme = ?");
        $a->execute(array($code_ogd, $type_mouvement, $norme));
        return $a->fetch();
    }


}