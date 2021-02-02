<?php
/**
 * Created by PhpStorm.
 * User: Dognimin.Koulibali
 * Date: 23/02/2019
 * Time: 18:01
 */

class SCRIPTS extends BDD
{
    public function trouver_liste($statut) {
        $a = $this->bdd->prepare('SELECT script_id AS id, script_nom AS nom, script_date_debut AS date_debut, script_date_fin AS date_fin, script_statut AS statut, script_description AS description, date_reg, user_reg, date_edit, user_edit FROM auxil_scripts WHERE script_statut LIKE ?');
        $a->execute(array('%'.$statut.'%'));
        return $a->fetchAll();
    }

    public function ajouter($nom,$date_debut,$date_fin,$statut,$description,$user) {
        $a = $this->bdd->prepare("INSERT INTO auxil_scripts(script_nom, script_date_debut, script_date_fin, script_statut, script_description, user_reg) 
            VALUES(:script_nom, :script_date_debut, :script_date_fin, :script_statut, :script_description, :user_reg)");
        $a->execute(array(
            'script_nom' => $nom,
            'script_date_debut' => $date_debut,
            'script_date_fin' => $date_fin,
            'script_statut' => $statut,
            'script_description' => $description,
            'user_reg' => $user
        ));
        if($a->errorCode() === '00000') {
            $json = array(
                'success' => true,
                'id' => $this->bdd->lastInsertId()
            );
        }else {
            $json = array(
                'success' => false,
                'message' => 'Erreur: '.$a->errorCode().' <=> '.$a->errorInfo()[1].' <=> '.$a->errorInfo()[2]
            );
        }
        return $json;
    }

    public function mise_a_jour($id,$lecture_id,$chargement_id,$date_fin,$statut,$description,$user) {
        $a = $this->bdd->prepare('UPDATE auxil_scripts SET log_lecture_id = ?, log_chargement_id = ?, script_date_fin = ?, script_statut = ?, script_description = ?, date_edit = ?, user_edit = ? WHERE script_id = ?');
        $a->execute(array($lecture_id,$chargement_id,$date_fin,$statut,$description,date('Y-m-d H:i:s',time()),$user,$id));
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
}