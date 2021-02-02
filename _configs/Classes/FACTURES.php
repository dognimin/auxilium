<?php


class FACTURES extends BDD
{


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
}