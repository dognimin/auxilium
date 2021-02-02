ALTER TABLE `auxil_assures` ADD `ogd_code` VARCHAR(8) NULL FIRST;
ALTER TABLE `auxil_assures` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `auxil_ref_civilite` ADD FOREIGN KEY (`genre_code`) REFERENCES `auxil_ref_genre`(`genre_code`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `auxil_assures_collectivites` ADD `ogd_code` VARCHAR(8) NULL FIRST;
ALTER TABLE `auxil_assures_collectivites` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `auxil_assures_contrats` ADD `ogd_code` VARCHAR(8) NULL FIRST; 
ALTER TABLE `auxil_assures_contrats` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `auxil_assures_coordonnees` ADD `ogd_code` VARCHAR(8) NULL FIRST; 
ALTER TABLE `auxil_assures_coordonnees` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `auxil_assures_historique_mouvements` ADD `ogd_code` VARCHAR(8) NULL AFTER `mouvement_id`; 
ALTER TABLE `auxil_assures_identifiants` ADD `ogd_code` VARCHAR(8) NULL FIRST; 
ALTER TABLE `auxil_assures_identifiants` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `auxil_assures_liens_personne` ADD `ogd_code` VARCHAR(8) NULL FIRST; 
ALTER TABLE `auxil_assures_liens_personne` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `auxil_assures_liens_personne` ADD FOREIGN KEY (`num_secu`) REFERENCES `auxil_assures`(`num_secu`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `auxil_assures_paiement` ADD `ogd_code` VARCHAR(8) NULL FIRST; 
ALTER TABLE `auxil_assures_paiement` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `auxil_assures_rattachements` ADD `ogd_code` VARCHAR(8) NULL FIRST; 
ALTER TABLE `auxil_assures_rattachements` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `auxil_assures_rattachements_droits` ADD `ogd_code` VARCHAR(8) NULL FIRST; 
ALTER TABLE `auxil_assures_rattachements_droits` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `auxil_bordereaux` ADD `ogd_code` VARCHAR(8) NULL AFTER `bordereau_id`; 
ALTER TABLE `auxil_bordereaux` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `auxil_factures` ADD `ogd_code` VARCHAR(8) NULL FIRST; 
ALTER TABLE `auxil_factures` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `auxil_factures_anomalies` ADD `ogd_code` VARCHAR(8) NULL FIRST; 
ALTER TABLE `auxil_factures_anomalies` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `auxil_factures_historique_statuts` ADD `ogd_code` VARCHAR(8) NULL AFTER `statut_id`; 
ALTER TABLE `auxil_factures_historique_statuts` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `auxil_factures_lignes_acte` ADD `ogd_code` VARCHAR(8) NULL FIRST; 
ALTER TABLE `auxil_factures_lignes_acte` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `auxil_log_difpop_stats` ADD `ogd_code` VARCHAR(8) NULL FIRST; 
ALTER TABLE `auxil_log_difpop_stats` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `auxil_log_montant_flux` ADD `ogd_code` VARCHAR(8) NULL FIRST; 
ALTER TABLE `auxil_log_montant_flux` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `auxil_ref_etablissement_sante` CHANGE `validite_date_debut` `ets_date_debut` DATE NOT NULL, CHANGE `validite_date_fin` `ets_date_fin` DATE NULL DEFAULT NULL; 
ALTER TABLE `auxil_utilisateur` ADD `ogd_code` VARCHAR(8) NULL AFTER `utilisateur_id`; 
ALTER TABLE `auxil_utilisateur` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE; 
ALTER TABLE `auxil_log_piste_audit` ADD `log_adresse_ip` VARCHAR(200) NULL AFTER `log_id`; 
ALTER TABLE `auxil_utilisateur` ADD `utilisateur_num_telephone` VARCHAR(10) NULL AFTER `utilisateur_fonction`; 
ALTER TABLE `auxil_ref_pathologie` ADD `pathologie_date_debut` DATE NULL AFTER `panier_statut`, ADD `pathologie_date_fin` DATE NULL AFTER `pathologie_date_debut`;
UPDATE `auxil_ref_pathologie` SET `pathologie_date_debut`= '2016-06-01';
ALTER TABLE `auxil_ref_pathologie` DROP PRIMARY KEY, ADD PRIMARY KEY( `pathologie_code`, `pathologie_date_debut`);
ALTER TABLE `auxil_scripts` ADD `ogd_code` VARCHAR(8) NULL AFTER `script_id`;
ALTER TABLE `auxil_scripts` ADD FOREIGN KEY (`ogd_code`) REFERENCES `auxil_ref_ogd_prestations`(`ogd_code`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `auxil_scripts` ADD `log_lecture_id` INT NULL AFTER `ogd_code`, ADD `log_chargement_id` INT NULL AFTER `log_lecture_id`;
ALTER TABLE `auxil_log_historique_fichier` CHANGE `log_num_fichier` `log_num_fichier` INT(15) NULL DEFAULT NULL;