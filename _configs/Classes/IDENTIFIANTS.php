<?php


class IDENTIFIANTS extends BDD
{
    public function lister(){
        $a = $this->bdd->prepare("SELECT identifiant_code AS code, identifiant_libelle AS libelle, nature_code AS code_nature, identifiant_date_debut AS date_debut FROM auxil_ref_type_identifiant WHERE identifiant_date_fin IS NULL ORDER BY identifiant_libelle");
        $a->execute(array());
        return $a->fetchAll();
    }

    public function trouver($code){
        $a = $this->bdd->prepare("SELECT identifiant_code AS code, identifiant_libelle AS libelle, nature_code AS code_nature, identifiant_date_debut AS date_debut FROM auxil_ref_type_identifiant WHERE identifiant_code = ? AND identifiant_date_fin IS NULL");
        $a->execute(array($code));
        return $a->fetch();
    }

    public function lister_natures(){
        $a = $this->bdd->prepare("SELECT nature_code AS code, nature_libelle AS libelle, nature_date_debut AS date_debut FROM auxil_ref_nature_identifiants WHERE nature_date_fin IS NULL ORDER BY nature_libelle");
        $a->execute(array());
        return $a->fetchAll();
    }

    public function trouver_nature($code){
        $a = $this->bdd->prepare("SELECT nature_code AS code, nature_libelle AS libelle, nature_ordre_1 AS ordre_1, nature_ordre_2 AS ordre_2, nature_date_debut AS date_debut FROM auxil_ref_nature_identifiants WHERE nature_code = ? AND nature_date_fin IS NULL");
        $a->execute(array($code));
        return $a->fetch();
    }


}