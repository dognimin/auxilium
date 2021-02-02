
UPDATE `auxil_assures` SET `ogd_code`= '02102000';
UPDATE `auxil_assures_collectivites` SET `ogd_code`= '02102000';
UPDATE `auxil_assures_contrats` SET `ogd_code`= '02102000';
UPDATE `auxil_assures_coordonnees` SET `ogd_code`= '02102000';
UPDATE `auxil_assures_historique_mouvements` SET `ogd_code`= '02102000';
UPDATE `auxil_assures_identifiants` SET `ogd_code`= '02102000';
UPDATE `auxil_assures_rattachements` SET `ogd_code`= '02102000';
UPDATE `auxil_assures_rattachements_droits` SET `ogd_code`= '02102000';
UPDATE `auxil_bordereaux` SET `ogd_code`= '02102000';
UPDATE `auxil_factures` SET `ogd_code`= '02102000';
UPDATE `auxil_factures_historique_statuts` SET `ogd_code`= '02102000';
UPDATE `auxil_factures_lignes_acte` SET `ogd_code`= '02102000';
UPDATE `auxil_log_difpop_stats` SET `ogd_code`= '02102000';
UPDATE `auxil_log_montant_flux` SET `ogd_code`= '02102000';
UPDATE `auxil_scripts` SET `ogd_code`= '02102000';
UPDATE `auxil_utilisateur` SET `ogd_code`= '02102000';

UPDATE `auxil_utilisateur` SET `utilisateur_modules` = 'CHARGEMENTS_AFFICHAGE;CHARGEMENTS_EDITION;POPULATIONS_AFFICHAGE;POPULATIONS_EDITION;FACTURES_AFFICHAGE;FACTURES_EDITION;STATISTIQUES_AFFICHAGE;PARAMETRES_AFFICHAGE;PARAMETRES_EDITION;' WHERE `auxil_utilisateur`.`utilisateur_id` = 1;
UPDATE `auxil_utilisateur` SET `utilisateur_sous_modules` = 'CHARGEMENTS_IMPORTS_AFFICHAGE;CHARGEMENTS_IMPORTS_EDITION;CHARGEMENTS_EXPORTS_AFFICHAGE;CHARGEMENTS_EXPORTS_EDITION;FACTURES_RECHERCHE_AFFICHAGE;FACTURES_RECHERCHE_EDITION;FACTURES_VERIFICATION_AFFICHAGE;FACTURES_VERIFICATION_EDITION;FACTURES_LIQUIDATION_AFFICHAGE;FACTURES_LIQUIDATION_EDITION;FACTURES_DETAILS_AFFICHAGE;FACTURES_DETAILS_EDITION;PARAMETRES_UTILISATEURS_AFFICHAGE;PARAMETRES_UTILISATEURS_EDITION;PARAMETRES_REFERENTIELS_AFFICHAGE;PARAMETRES_REFERENTIELS_EDITION;PARAMETRES_SCRIPTS_AFFICHAGE;PARAMETRES_SCRIPTS_EDITION;' WHERE `auxil_utilisateur`.`utilisateur_id` = 1;
