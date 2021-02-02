<?php


class STATISTIQUES extends BDD
{
    public function trouver_effectif_populations($code_ogd) {
        $a = $this->bdd->prepare("SELECT COUNT(*) AS effectif FROM auxil_assures WHERE ogd_code = ? AND type_mouvement != ?");
        $a->execute(array($code_ogd,'SUP'));
        return $a->fetch();
    }
    public function trouver_effectif_factures($code_ogd) {
        $a = $this->bdd->prepare("SELECT COUNT(*) AS effectif FROM auxil_factures WHERE ogd_code = ?");
        $a->execute(array($code_ogd));
        return $a->fetch();
    }
    public function lister_effectif_types_factures($code_ogd) {
        $a = $this->bdd->prepare("SELECT A.facture_statut AS code_statut, C.statut_nom AS libelle, COUNT(A.facture_num) AS effectif FROM auxil_factures A JOIN auxil_factures_statuts_dictionnaire C ON A.facture_statut = C.statut_code AND A.ogd_code = ? GROUP BY A.facture_statut, C.statut_nom ORDER BY COUNT(A.facture_num) DESC");
        $a->execute(array($code_ogd));
        return $a->fetchAll();
    }
    public function trouver_montant_factures($code_ogd) {
        $a = $this->bdd->prepare("SELECT SUM(B.montant_depense) AS montant FROM auxil_factures A JOIN auxil_factures_lignes_acte B ON A.facture_num = B.num_facture AND A.ogd_code = ?");
        $a->execute(array($code_ogd));
        return $a->fetch();
    }
    public function lister_montant_types_factures($code_ogd) {
        $a = $this->bdd->prepare("SELECT A.facture_statut AS code_statut, C.statut_nom AS libelle, SUM(B.montant_depense) AS montant FROM auxil_factures A JOIN auxil_factures_lignes_acte B ON A.facture_num = B.num_facture JOIN auxil_factures_statuts_dictionnaire C ON A.facture_statut = C.statut_code AND A.ogd_code = ? GROUP BY A.facture_statut, C.statut_nom ORDER BY SUM(B.montant_depense) DESC");
        $a->execute(array($code_ogd));
        return $a->fetchAll();
    }
}