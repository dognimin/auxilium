<?php


class OGD extends BDD {
    public function lister() {
        $a = $this->bdd->prepare('SELECT ogd_code AS code, ogd_libelle AS libelle, ogd_num_centre AS num_centre, ogd_grand_regime AS grand_regime, ogd_caisse AS caisse, ogd_date_debut AS date_debut, ogd_date_fin AS date_fin, date_reg, user_reg FROM auxil_ref_ogd_prestations ORDER BY ogd_libelle ASC');
        $a->execute(array());
        $json = $a->fetchAll();
        return $json;
    }

    public function trouver($code) {
        $a = $this->bdd->prepare('SELECT ogd_code AS code, ogd_libelle AS libelle, ogd_num_centre AS num_centre, ogd_grand_regime AS grand_regime, ogd_caisse AS caisse, ogd_date_debut AS date_debut, ogd_date_fin AS date_fin, date_reg, user_reg FROM auxil_ref_ogd_prestations WHERE ogd_code = ?');
        $a->execute(array($code));
        $json = $a->fetch();
        return $json;
    }

    public function editer($code_ogd, $libelle, $num_centre, $regime, $caisse, $date_debut, $date_fin, $user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_ref_ogd_prestations(ogd_code, ogd_libelle, ogd_num_centre, ogd_grand_regime, ogd_caisse, ogd_date_debut, ogd_date_fin, user_reg)
        VALUES(:ogd_code, :ogd_libelle, :ogd_num_centre, :ogd_grand_regime, :ogd_caisse, :ogd_date_debut, :ogd_date_fin, :user_reg)
        ON DUPLICATE KEY UPDATE ogd_libelle = :ogd_libelle, ogd_num_centre = :ogd_num_centre, ogd_grand_regime = :ogd_grand_regime, ogd_caisse = :ogd_caisse, ogd_date_debut = :ogd_date_debut, ogd_date_fin = :ogd_date_fin, date_edit = :date_edit, user_edit = :user_edit');
        $a->execute(array(
            'ogd_code' => $code_ogd,
            'ogd_libelle' => $libelle,
            'ogd_num_centre' => $num_centre,
            'ogd_grand_regime' => $regime,
            'ogd_caisse' => $caisse,
            'ogd_date_debut' => $date_debut,
            'ogd_date_fin' => $date_fin,
            'user_reg' => $user,
            'date_edit' => date('Y-m-d H:i:s',time()),
            'user_edit' => $user
        ))OR DIE('Erreur edition');

        $json = array(
            'success' => true
        );
        return $json;
    }
}