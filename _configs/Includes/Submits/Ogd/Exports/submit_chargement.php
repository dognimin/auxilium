<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once '../../../../Classes/UTILISATEURS.php';
if(isset($_SESSION['auxilium_user_id'])) {
    $UTILISATEURS = new UTILISATEURS();
    $utilisateur = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
    if(!empty($utilisateur['user_id'])){
        if($utilisateur['code_ogd']) {
            require_once '../../../../Classes/OGD.php';
            $OGD = new OGD();
            $ogd = $OGD->trouver($utilisateur['code_ogd']);
            if($ogd) {
                $parametres = array(
                    'type_ref' => htmlentities(trim($_POST['type_ref'])),
                    'num_fichier' => htmlentities(trim($_POST['num_fichier']))
                );
                if(count($parametres) == 2) {
                    if(!empty($parametres['type_ref']) && $parametres['num_fichier']) {
                        $type_ref = $parametres['type_ref'];
                        $num_fichier = $parametres['num_fichier'];

                        $path = DIR.'EXPORTS/CHARGEMENTS/'.$type_ref.'/'.$ogd['code'].'/';
                        $lien = URL.'EXPORTS/CHARGEMENTS/'.$type_ref.'/'.$ogd['code'].'/';
                        $path_export = DIR.'EXPORTS/RAPPORTS_INTEGRATION/CHARGEMENTS/'.$ogd['code'].'/';
                        $lien_export = URL.'EXPORTS/RAPPORTS_INTEGRATION/CHARGEMENTS/'.$ogd['code'].'/';
                        if(!file_exists($path)){
                            mkdir($path,0777,true);
                        }
                        require_once '../../../../Classes/ETABLISSEMENTSANTE.php';
                        require_once '../../../../Classes/ACTESMEDICAUX.php';
                        require_once '../../../../Classes/IDENTIFIANTS.php';
                        require_once '../../../../Classes/MEDICAMENTS.php';
                        require_once '../../../../Classes/FACTURES.php';
                        require_once '../../../../Classes/ASSURES.php';
                        require_once '../../../../Classes/SCRIPTS.php';
                        require_once '../../../../Classes/LOGS.php';


                        $ETABLISSEMENTSANTE = new ETABLISSEMENTSANTE();
                        $ACTESMEDICAUX = new ACTESMEDICAUX();
                        $IDENTIFIANTS = new IDENTIFIANTS();
                        $MEDICAMENTS = new MEDICAMENTS();
                        $FACTURES = new FACTURES();
                        $ASSURES = new ASSURES();
                        $SCRIPTS = new SCRIPTS();
                        $LOGS = new LOGS();


                        $scripts = $SCRIPTS->lister($ogd['code'],'ENC');
                        $nb_scripts = count($scripts);
                        if($nb_scripts == 0) {
                            $factures = $FACTURES->lister_factures_a_telecharger($ogd['code'],$type_ref,$num_fichier);
                            $nb_factures = count($factures);
                            if($nb_factures != 0) {
                                $nouveau_script = $SCRIPTS->ajouter('SCRIPT_GENERATION_'.$type_ref,date('Y-m-d H:i:s',time()),NULL,'ENC','Début de génération '.$type_ref,$utilisateur['user_id']);
                                if($nouveau_script['success'] == true)  {
                                    if($type_ref == 'DECLIQ') {
                                        $statut_fin = 'D';
                                        $trouver_num_transmission = $LOGS->trouver_num_transmission($ogd['code'],'EXP',$type_ref);
                                        if($trouver_num_transmission['num_fichier']) {
                                            $num_liquidation = (intval($trouver_num_transmission['num_fichier']) + 1);
                                        }else {
                                            $num_liquidation = 1;
                                        }
                                        $norme = "DECLIQ";
                                        $version = "201509";
                                        $date_fichier = date('Y-m-d',time());
                                        $type_emetteur = "OP";
                                        $num_emetteur = $ogd['code'];
                                        $type_destinataires = "CN";
                                        $num_destinataires = 1;

                                        $file_name = $norme."_".$ogd['code'].date('dmY_His',time()).".xml";

                                        $xmlWriter = new XMLWriter();
                                        $xmlWriter->openMemory();
                                        $xmlWriter->startDocument('1.0', 'UTF-8');
                                        $xmlWriter->setIndent(150);

                                        $xmlWriter->startElement('ENTETE_FICHIER');
                                        $xmlWriter->writeElement('CODE_NORME', $norme);
                                        $xmlWriter->writeElement('VERSION_NORME', $version);
                                        $xmlWriter->writeElement('DATE_FICHIER', date('d/m/Y',strtotime($date_fichier)));
                                        $xmlWriter->writeElement('NUM_FICHIER', $num_liquidation);
                                        $xmlWriter->writeElement('TYPE_EMETTEUR', $type_emetteur);
                                        $xmlWriter->writeElement('NUM_EMETTEUR', $num_emetteur);
                                        $xmlWriter->writeElement('TYPE_DESTINATAIRE', $type_destinataires);
                                        $xmlWriter->writeElement('NUM_DESTINATAIRE', $num_destinataires);
                                        $xmlWriter->writeElement('OCCURRENCES_DECOMPTE', $nb_factures);

                                        $montant_flux = 0;
                                        $succes = 0;
                                        $echec = 0;
                                        foreach ($factures as $facture) {
                                            $actes = $FACTURES->lister_facture_lignes_actes($facture['facture_num']);
                                            $nb_actes = count($actes);
                                            if($nb_actes != 0) {
                                                if($facture['lien_archivage']) {
                                                    $lien_archivage = $facture['lien_archivage'];
                                                }else {
                                                    $lien_archivage = $num_emetteur.$facture['facture_num'].date('dmyHis',time());
                                                }
                                                if (!trim($facture['date_accident'])) {
                                                    $num_vehicule = '';
                                                    $facture_date_accident = '';
                                                } else {
                                                    $num_vehicule = $facture['num_accident'];
                                                    $facture_date_accident = date('d/m/Y', strtotime($facture['date_accident']));
                                                }
                                                if (!trim($facture['date_fin_ds'])) {
                                                    $date_fin_ds = '';
                                                } else {
                                                    $date_fin_ds = date('d/m/Y', strtotime($facture['date_fin_ds']));
                                                }


                                                $xmlWriter->startElement('DECOMPTE');
                                                $xmlWriter->writeElement('LIEN_ARCHIVAGE',$lien_archivage );
                                                $xmlWriter->writeElement('DATE_RECEPTION', date('d/m/Y',time()));
                                                $xmlWriter->writeElement('DATE_LIQUIDATION', date('d/m/Y',strtotime($facture['date_liquidation'])));
                                                $xmlWriter->writeElement('NUM_SECU_BENEFICIAIRE', $facture['num_secu']);
                                                $xmlWriter->writeElement('DATE_NAISSANCE_BENEFICIAIRE', date('d/m/Y',strtotime($facture['date_naissance'])));
                                                $xmlWriter->writeElement('RANG_GEMELLAIRE_BENEFICIAIRE', 1);
                                                $xmlWriter->writeElement('NOM_BENEFICIAIRE', $facture['nom']);
                                                $xmlWriter->writeElement('PRENOM_BENEFICIAIRE', $facture['prenom']);
                                                $xmlWriter->writeElement('TYPE_DECOMPTE', 'TP');
                                                $xmlWriter->writeElement('CODE_ETABLISSEMENT', $facture['ets_code']);
                                                $xmlWriter->writeElement('NUM_ORDONNANCE', '');
                                                $xmlWriter->writeElement('NUM_ACCIDENT', $num_vehicule);
                                                $xmlWriter->writeElement('DATE_ACCIDENT', $facture_date_accident);
                                                $xmlWriter->writeElement('CODE_NATURE_ASSURANCE', 'AS');
                                                $xmlWriter->writeElement('DATE_FACTURE', date('d/m/Y',strtotime($facture['facture_date'])));
                                                $xmlWriter->writeElement('NUM_FACTURE', $facture['facture_num']);
                                                $xmlWriter->writeElement('NUM_DOSSIER_SOINS', $facture['num_ds']);
                                                $xmlWriter->writeElement('CODE_ETAB_DOSSIER_SOINS', $facture['ets_code_ds']);
                                                $xmlWriter->writeElement('CODE_PS_DOSSIER_SOINS', $facture['ps_code_ds']);
                                                $xmlWriter->writeElement('CODE_TYPOLOGIE_DOSSIER_SOINS', $facture['code_typologie_ds']);
                                                $xmlWriter->writeElement('CODE_PATHOLOGIE_DOSSIER_SOINS', $facture['code_pathologie_ds']);
                                                $xmlWriter->writeElement('DATE_FIN_DOSSIER_SOINS', $date_fin_ds);
                                                $xmlWriter->writeElement('NUM_DESTINATAIRE_REGLEMENT', '');
                                                $xmlWriter->writeElement('DATE_PAIEMENT', '');
                                                $xmlWriter->writeElement('MODE_PAIEMENT', '');
                                                $xmlWriter->writeElement('INFOS_BANCAIRES_PAIEMENT', '');
                                                $xmlWriter->writeElement('OCCURRENCES_LIGNE_ACTE', $nb_actes);
                                                foreach ($actes as $acte) {
                                                    if (empty($acte['date_debut_soins']) || $acte['date_debut_soins'] == NULL) {
                                                        $acte_date_debut_soins = date('d/m/Y', strtotime($facture['facture_date']));
                                                    } else {
                                                        $acte_date_debut_soins = date('d/m/Y', strtotime($acte['date_debut_soins']));
                                                    }
                                                    if (empty($acte['date_fin_soins']) || $acte['date_fin_soins'] == NULL) {
                                                        $acte_date_fin_soins = $acte_date_debut_soins;
                                                    } else {
                                                        $acte_date_fin_soins = date('d/m/Y', strtotime($acte['date_fin_soins']));
                                                    }
                                                    if (empty($acte['date_prescription']) || $acte['date_prescription'] == NULL) {
                                                        $date_prescription = '';
                                                    } else {
                                                        $date_prescription = date('d/m/Y', strtotime($acte['date_prescription']));
                                                    }
                                                    $code_de_acte = $acte['code_acte'];
                                                    $code_complement_acte = '-';
                                                    $code_dmt = 0;
                                                    $code_origine_prescription = '-';
                                                    $code_zone_tarif_executant = 99;
                                                    $type_ctrl_droits = 'CD';
                                                    $capitation_o_n = '0';
                                                    $coefficient_acte = 1;
                                                    $taux_remboursement_ro = 70;
                                                    $signe = 'P';

                                                    $xmlWriter->startElement('LIGNE_ACTE');
                                                    $xmlWriter->writeElement('DATE_DEBUT_SOINS', $acte_date_debut_soins);
                                                    $xmlWriter->writeElement('DATE_FIN_SOINS', $acte_date_fin_soins);
                                                    $xmlWriter->writeElement('CODE_ACTE', $code_de_acte);
                                                    $xmlWriter->writeElement('CODE_COMPLEMENT_ACTE', $code_complement_acte);
                                                    $xmlWriter->writeElement('CODE_SPECIALITE', $acte['code_specialite']);
                                                    $xmlWriter->writeElement('CODE_DMT', $code_dmt);
                                                    $xmlWriter->writeElement('CODE_PATHOLOGIE', $acte['code_pathologie']);
                                                    $xmlWriter->writeElement('CODE_ORIGINE_PRESCRIPTION', $acte['code_origine_prescription']);
                                                    $xmlWriter->writeElement('CODE_INDICATEUR_PARCOURS_SOINS', $acte['code_indicateur_parcours']);
                                                    $xmlWriter->writeElement('CODE_PRESCRIPTEUR', $acte['code_prescripteur']);
                                                    $xmlWriter->writeElement('CODE_SPECIALITE_PRESCRIPTEUR', $acte['code_specialite_prescripteur']);
                                                    $xmlWriter->writeElement('DATE_PRESCRIPTION', date('d/m/Y', strtotime($date_prescription)));
                                                    $xmlWriter->writeElement('CODE_EXECUTANT', $acte['code_executant']);
                                                    $xmlWriter->writeElement('CODE_ZONE_TARIF_EXECUTANT', $code_zone_tarif_executant);
                                                    $xmlWriter->writeElement('TYPE_CONTROLE_DROITS', $type_ctrl_droits);
                                                    $xmlWriter->writeElement('NUM_TRANSACTION_CTRL_DROITS', $acte['num_transaction_ctrl_droits']);
                                                    $xmlWriter->writeElement('CAPITATION_O_N', $capitation_o_n);
                                                    $xmlWriter->writeElement('MONTANT_DEPENSE', $acte["montant_depense"]);
                                                    $xmlWriter->writeElement('QUANTITE_ACTE', $acte['quantite_acte']);
                                                    $xmlWriter->writeElement('COEFFICIENT_ACTE', $coefficient_acte);
                                                    $xmlWriter->writeElement('PRIX_UNITAIRE_ACTE', $acte['prix_unitaire']);
                                                    $xmlWriter->writeElement('BASE_REMBOURSEMENT', $acte['base_remboursement']);
                                                    $xmlWriter->writeElement('TAUX_REMBOURSEMENT_RO', $taux_remboursement_ro);
                                                    $xmlWriter->writeElement('MONTANT_REMBOURSEMENT_RO', $acte["montant_remboursement_ro"]);
                                                    $xmlWriter->writeElement('SIGNE', $signe);
                                                    $xmlWriter->endElement();
                                                }
                                                $xmlWriter->endElement();
                                            }

                                            $edition = $FACTURES->edition_facture_decliq($facture['facture_num'],$num_fichier,$lien_archivage,$statut_fin,$utilisateur['user_id']);
                                            if($edition['success'] == true) {
                                                $succes++;
                                            }else {
                                                $echec++;
                                            }
                                        }
                                        $xmlWriter->endDocument();

                                        file_put_contents($path.'/'.$file_name, $xmlWriter->flush(true), FILE_APPEND);
                                        $chargement = $LOGS->ajouter_chargement_fichier('EXP',$file_name,'FIN',"FIN DE GENERATION",$utilisateur['user_id']);
                                        if($chargement['success'] == true) {
                                            $log = $UTILISATEURS->ajouter_log_piste_audit(NULL,'CREATION','CHARGEMENT FICHIER: (mouvement => EXP, nom_fichier => '.$file_name.')',$utilisateur['user_id']);
                                            if($log['success'] == true) {
                                                $historique = $LOGS->ajouter_historique_fichier('EXP',$num_liquidation,$norme,$version,date('Y-m-d',time()),$num_emetteur,$num_destinataires,$nb_factures,$succes,$echec,$file_name,$utilisateur['user_id']);
                                                if($historique['success'] == true) {
                                                    $message = 'Fichier '.$file_name.' généré avec succès.<br /><a href="'.$lien.$file_name.'" target="_blank" download="'.$file_name.'">Cliquez ici pour télécharger le fichier</a>';
                                                    $maj_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],NULL,NULL,date('Y-m-d H:i:s',time()),'FIN',$message,$utilisateur['user_id']);
                                                    if($maj_script['success'] == true) {
                                                        $edition_rejets = $FACTURES->edition_factures_organisme_rejetees($ogd['code'],$num_fichier,$num_liquidation,$utilisateur['user_id']);
                                                        $json = array(
                                                            'success' => true,
                                                            'message' => $message
                                                        );
                                                    }else {
                                                        $json = $maj_script;
                                                    }
                                                }else {
                                                    $json = $historique;
                                                }
                                            }else {
                                                $json = $log;
                                            }
                                        }else {
                                            $json = $chargement;
                                        }
                                    }
                                    elseif($type_ref == 'DECPAI') {
                                        $statut_fin = 'P';
                                        $trouver_num_transmission = $LOGS->trouver_num_transmission($ogd['code'],'EXP',$type_ref);
                                        if($trouver_num_transmission['num_fichier']) {
                                            $num_paiement = intval($trouver_num_transmission['num_fichier']) + 1;
                                        }else {
                                            $num_paiement = 1;
                                        }
                                        $norme = "DECPAI";
                                        $version = "201509";
                                        $date_fichier = date('Y-m-d',time());
                                        $type_emetteur = "OP";
                                        $num_emetteur = $ogd['code'];
                                        $type_destinataires = "CN";
                                        $num_destinataires = 1;

                                        $file_name = $norme."_".$ogd['code'].date('dmY_His',time()).".xml";

                                        $xmlWriter = new XMLWriter();
                                        $xmlWriter->openMemory();
                                        $xmlWriter->startDocument('1.0', 'UTF-8');
                                        $xmlWriter->setIndent(150);

                                        $xmlWriter->startElement('ENTETE_FICHIER');
                                        $xmlWriter->writeElement('CODE_NORME', $norme);
                                        $xmlWriter->writeElement('VERSION_NORME', $version);
                                        $xmlWriter->writeElement('DATE_FICHIER', date('d/m/Y',strtotime($date_fichier)));
                                        $xmlWriter->writeElement('NUM_FICHIER', $num_paiement);
                                        $xmlWriter->writeElement('TYPE_EMETTEUR', $type_emetteur);
                                        $xmlWriter->writeElement('NUM_EMETTEUR', $num_emetteur);
                                        $xmlWriter->writeElement('TYPE_DESTINATAIRE', $type_destinataires);
                                        $xmlWriter->writeElement('NUM_DESTINATAIRE', $num_destinataires);
                                        $xmlWriter->writeElement('OCCURRENCES_DECOMPTE', $nb_factures);

                                        $montant_flux = 0;
                                        foreach ($factures as $facture) {
                                            $actes = $FACTURES->lister_facture_lignes_actes($facture['facture_num']);
                                            $nb_actes = count($actes);
                                            if($nb_actes != 0) {
                                                $lien_archivage = $num_emetteur.$facture['facture_num'].date('dmyHis',time());
                                                if (!trim($facture['date_accident'])) {
                                                    $num_vehicule = '';
                                                    $facture_date_accident = '';
                                                } else {
                                                    $num_vehicule = $facture['num_accident'];
                                                    $facture_date_accident = date('d/m/Y', strtotime($facture['date_accident']));
                                                }
                                                if (!trim($facture['date_fin_ds'])) {
                                                    $date_fin_ds = '';
                                                } else {
                                                    $date_fin_ds = date('d/m/Y', strtotime($facture['date_fin_ds']));
                                                }


                                                $xmlWriter->startElement('DECOMPTE');
                                                $xmlWriter->writeElement('LIEN_ARCHIVAGE',$lien_archivage );
                                                $xmlWriter->writeElement('DATE_RECEPTION', date('d/m/Y',time()));
                                                $xmlWriter->writeElement('DATE_LIQUIDATION', date('d/m/Y',strtotime($facture['date_liquidation'])));
                                                $xmlWriter->writeElement('NUM_SECU_BENEFICIAIRE', $facture['num_secu']);
                                                $xmlWriter->writeElement('DATE_NAISSANCE_BENEFICIAIRE', date('d/m/Y',strtotime($facture['date_naissance'])));
                                                $xmlWriter->writeElement('RANG_GEMELLAIRE_BENEFICIAIRE', 1);
                                                $xmlWriter->writeElement('NOM_BENEFICIAIRE', $facture['nom']);
                                                $xmlWriter->writeElement('PRENOM_BENEFICIAIRE', $facture['prenom']);
                                                $xmlWriter->writeElement('TYPE_DECOMPTE', 'TP');
                                                $xmlWriter->writeElement('CODE_ETABLISSEMENT', $facture['ets_code']);
                                                $xmlWriter->writeElement('NUM_ORDONNANCE', '');
                                                $xmlWriter->writeElement('NUM_ACCIDENT', $num_vehicule);
                                                $xmlWriter->writeElement('DATE_ACCIDENT', $facture_date_accident);
                                                $xmlWriter->writeElement('CODE_NATURE_ASSURANCE', 'AS');
                                                $xmlWriter->writeElement('DATE_FACTURE', date('d/m/Y',strtotime($facture['facture_date'])));
                                                $xmlWriter->writeElement('NUM_FACTURE', $facture['facture_num']);
                                                $xmlWriter->writeElement('NUM_DOSSIER_SOINS', $facture['num_ds']);
                                                $xmlWriter->writeElement('CODE_ETAB_DOSSIER_SOINS', $facture['ets_code_ds']);
                                                $xmlWriter->writeElement('CODE_PS_DOSSIER_SOINS', $facture['ps_code_ds']);
                                                $xmlWriter->writeElement('CODE_TYPOLOGIE_DOSSIER_SOINS', $facture['code_typologie_ds']);
                                                $xmlWriter->writeElement('CODE_PATHOLOGIE_DOSSIER_SOINS', $facture['code_pathologie_ds']);
                                                $xmlWriter->writeElement('DATE_FIN_DOSSIER_SOINS', $date_fin_ds);
                                                $xmlWriter->writeElement('NUM_DESTINATAIRE_REGLEMENT', '');
                                                $xmlWriter->writeElement('DATE_PAIEMENT', date('d/m/Y',strtotime($date_fichier)));
                                                $xmlWriter->writeElement('MODE_PAIEMENT', 'CHQ');
                                                $xmlWriter->writeElement('INFOS_BANCAIRES_PAIEMENT', '');
                                                $xmlWriter->writeElement('OCCURRENCES_LIGNE_ACTE', $nb_actes);
                                                foreach ($actes as $acte) {
                                                    if (empty($acte['date_debut_soins']) || $acte['date_debut_soins'] == NULL) {
                                                        $acte_date_debut_soins = date('d/m/Y', strtotime($facture['facture_date']));
                                                    } else {
                                                        $acte_date_debut_soins = date('d/m/Y', strtotime($acte['date_debut_soins']));
                                                    }
                                                    if (empty($acte['date_fin_soins']) || $acte['date_fin_soins'] == NULL) {
                                                        $acte_date_fin_soins = $acte_date_debut_soins;
                                                    } else {
                                                        $acte_date_fin_soins = date('d/m/Y', strtotime($acte['date_fin_soins']));
                                                    }
                                                    if (empty($acte['date_prescription']) || $acte['date_prescription'] == NULL) {
                                                        $date_prescription = '';
                                                    } else {
                                                        $date_prescription = date('d/m/Y', strtotime($acte['date_prescription']));
                                                    }
                                                    $code_de_acte = $acte['code_acte'];
                                                    $code_complement_acte = '-';
                                                    $code_dmt = 0;
                                                    $code_origine_prescription = '-';
                                                    $code_zone_tarif_executant = 99;
                                                    $type_ctrl_droits = 'CD';
                                                    $capitation_o_n = '0';
                                                    $coefficient_acte = 1;
                                                    $taux_remboursement_ro = 70;
                                                    $signe = 'P';

                                                    $xmlWriter->startElement('LIGNE_ACTE');
                                                    $xmlWriter->writeElement('DATE_DEBUT_SOINS', $acte_date_debut_soins);
                                                    $xmlWriter->writeElement('DATE_FIN_SOINS', $acte_date_fin_soins);
                                                    $xmlWriter->writeElement('CODE_ACTE', $code_de_acte);
                                                    $xmlWriter->writeElement('CODE_COMPLEMENT_ACTE', $code_complement_acte);
                                                    $xmlWriter->writeElement('CODE_SPECIALITE', $acte['code_specialite']);
                                                    $xmlWriter->writeElement('CODE_DMT', $code_dmt);
                                                    $xmlWriter->writeElement('CODE_PATHOLOGIE', $acte['code_pathologie']);
                                                    $xmlWriter->writeElement('CODE_ORIGINE_PRESCRIPTION', $acte['code_origine_prescription']);
                                                    $xmlWriter->writeElement('CODE_INDICATEUR_PARCOURS_SOINS', $acte['code_indicateur_parcours']);
                                                    $xmlWriter->writeElement('CODE_PRESCRIPTEUR', $acte['code_prescripteur']);
                                                    $xmlWriter->writeElement('CODE_SPECIALITE_PRESCRIPTEUR', $acte['code_specialite_prescripteur']);
                                                    $xmlWriter->writeElement('DATE_PRESCRIPTION', date('d/m/Y', strtotime($date_prescription)));
                                                    $xmlWriter->writeElement('CODE_EXECUTANT', $acte['code_executant']);
                                                    $xmlWriter->writeElement('CODE_ZONE_TARIF_EXECUTANT', $code_zone_tarif_executant);
                                                    $xmlWriter->writeElement('TYPE_CONTROLE_DROITS', $type_ctrl_droits);
                                                    $xmlWriter->writeElement('NUM_TRANSACTION_CTRL_DROITS', $acte['num_transaction_ctrl_droits']);
                                                    $xmlWriter->writeElement('CAPITATION_O_N', $capitation_o_n);
                                                    $xmlWriter->writeElement('MONTANT_DEPENSE', $acte["montant_depense"]);
                                                    $xmlWriter->writeElement('QUANTITE_ACTE', $acte['quantite_acte']);
                                                    $xmlWriter->writeElement('COEFFICIENT_ACTE', $coefficient_acte);
                                                    $xmlWriter->writeElement('PRIX_UNITAIRE_ACTE', $acte['prix_unitaire']);
                                                    $xmlWriter->writeElement('BASE_REMBOURSEMENT', $acte['base_remboursement']);
                                                    $xmlWriter->writeElement('TAUX_REMBOURSEMENT_RO', $taux_remboursement_ro);
                                                    $xmlWriter->writeElement('MONTANT_REMBOURSEMENT_RO', $acte["montant_remboursement_ro"]);
                                                    $xmlWriter->writeElement('SIGNE', $signe);
                                                    $xmlWriter->endElement();
                                                }
                                                $xmlWriter->endElement();
                                            }

                                            $edition = $FACTURES->edition_facture_decpai($facture['facture_num'],$num_paiement,$lien_archivage,$statut_fin,$utilisateur['user_id']);
                                            if($edition['success'] == true) {
                                                $succes++;
                                            }else {
                                                $echec++;
                                            }
                                        }
                                        $xmlWriter->endDocument();

                                        file_put_contents($path.'/'.$file_name, $xmlWriter->flush(true), FILE_APPEND);
                                        $chargement = $LOGS->ajouter_chargement_fichier('EXP',$file_name,'FIN',"FIN DE GENERATION",$utilisateur['user_id']);
                                        if($chargement['success'] == true) {
                                            $log = $UTILISATEURS->ajouter_log_piste_audit(NULL,'CREATION','CHARGEMENT FICHIER: (mouvement => EXP, nom_fichier => '.$file_name.')',$utilisateur['user_id']);
                                            if($log['success'] == true) {
                                                $historique = $LOGS->ajouter_historique_fichier('EXP',$num_paiement,$norme,$version,date('Y-m-d',time()),$num_emetteur,$num_destinataires,$nb_factures,$succes,$echec,$file_name,$utilisateur['user_id']);
                                                if($historique['success'] == true) {
                                                    $message = 'Fichier '.$file_name.' généré avec succès.<br /><a href="'.$lien.$file_name.'" target="_blank" download="'.$file_name.'">Cliquez ici pour télécharger le fichier</a>';
                                                    $maj_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],NULL,NULL,date('Y-m-d H:i:s',time()),'FIN',$message,$utilisateur['user_id']);
                                                    if($maj_script['success'] == true) {
                                                        $edition_rejets = $FACTURES->edition_factures_organisme_rejetees($ogd['code'],$num_fichier,$num_paiement,$utilisateur['user_id']);
                                                        $json = array(
                                                            'success' => true,
                                                            'message' => $message
                                                        );
                                                    }else {
                                                        $json = $maj_script;
                                                    }
                                                }else {
                                                    $json = $historique;
                                                }
                                            }else {
                                                $json = $log;
                                            }
                                        }else {
                                            $json = $chargement;
                                        }
                                    }
                                    elseif($type_ref == 'REJETSO') {
                                        $trouver_num_transmission = $LOGS->trouver_num_transmission($ogd['code'],'EXP',$type_ref);
                                        if($trouver_num_transmission['num_fichier']) {
                                            $num_rejet = intval($trouver_num_transmission['num_fichier']) + 1;
                                        }else {
                                            $num_rejet = 1;
                                        }
                                        $norme = $type_ref;
                                        $version = "202103";
                                        $date_fichier = date('Y-m-d',time());
                                        $type_emetteur = "OP";
                                        $num_emetteur = $ogd['code'];
                                        $type_destinataires = "CN";
                                        $num_destinataires = 1;
                                        $file_name = $norme.'_'.$num_fichier.'_'.$num_rejet.'.csv';
                                        header('Content-Type: text/csv; charset=utf-8');
                                        header('Content-Disposition: attachment; filename='.$file_name);

                                        $save = fopen($path.$file_name,"w");
                                        fputcsv($save, array('NUM FACTURE', 'NUM DOSSIER', 'DATE SOINS', 'DATE RECEPTION', 'DATE LIQUIDATION', 'NUMERO SECU', 'NOM', 'PRENOM', 'DATE NAISSANCE', 'CODE ETABLISSEMENT', 'CODE REJET'),';');
                                        foreach ($factures as $facture) {
                                            fputcsv($save, array($facture['facture_num'], $facture['num_ds'], $facture['facture_date'], $facture['date_reception'], $facture['date_liquidation'], $facture['num_secu'], $facture['nom'], $facture['prenom'], $facture['date_naissance'], $facture['ets_code_ds'], $facture['motif_code']),';');
                                        }
                                        fclose($save);
                                        $chargement = $LOGS->ajouter_chargement_fichier('EXP',$file_name,'FIN',"FIN DE GENERATION",$utilisateur['user_id']);
                                        if($chargement['success'] == true) {
                                            $log = $UTILISATEURS->ajouter_log_piste_audit(NULL,'CREATION','CHARGEMENT FICHIER: (mouvement => EXP, nom_fichier => '.$file_name.')',$utilisateur['user_id']);
                                            if($log['success'] == true) {
                                                $historique = $LOGS->ajouter_historique_fichier('EXP',$num_rejet,$norme,$version,date('Y-m-d',time()),$num_emetteur,$num_destinataires,$nb_factures,$nb_factures,NULL,$file_name,$utilisateur['user_id']);
                                                if($historique['success'] == true) {
                                                    $message = 'Fichier '.$file_name.' généré avec succès.<br /><a href="'.$lien.$file_name.'" target="_blank" download="'.$file_name.'">Cliquez ici pour télécharger le fichier</a>';
                                                    $maj_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],NULL,NULL,date('Y-m-d H:i:s',time()),'FIN',$message,$utilisateur['user_id']);
                                                    if($maj_script['success'] == true) {
                                                        $json = array(
                                                            'success' => true,
                                                            'message' => $message
                                                        );
                                                    }else {
                                                        $json = $maj_script;
                                                    }
                                                }else {
                                                    $json = $historique;
                                                }
                                            }else {
                                                $json = $log;
                                            }
                                        }else {
                                            $json = $chargement;
                                        }
                                    }
                                    elseif($type_ref == 'REJETSC') {

                                    }
                                    elseif($type_ref == 'TDM_EDT_009E') {
                                        $etablissements = $FACTURES->lister_factures_ets_payees($ogd['code'],$parametres['num_fichier'],'P');
                                        $nb_etablissements = count($etablissements);

                                        if($nb_etablissements != 0) {
                                            $statut_fin = 'T';
                                            $trouver_num_transmission = $LOGS->trouver_num_transmission($ogd['code'],'EXP',$type_ref);
                                            if($trouver_num_transmission['num_fichier']) {
                                                $num_paiement = intval($trouver_num_transmission['num_fichier']) + 1;
                                            }else {
                                                $num_paiement = 1;
                                            }
                                            $norme = 'TDM_EDT_009E';
                                            $version = "4.4.3.1";
                                            $date_edition = date('d/m/Y',time());

                                            $file_name = $norme."_".$ogd['code'].date('dmY_His',time()).".xml";

                                            $xmlWriter = new XMLWriter();
                                            $xmlWriter->openMemory();
                                            $xmlWriter->startDocument('1.0', 'UTF-8');
                                            $xmlWriter->setIndent(10);
                                            $xmlWriter->startElement('FLUX');
                                            foreach ($etablissements as $etablissement) {
                                                $ets = $ETABLISSEMENTSANTE->trouver($etablissement['code_ets']);

                                                $montant_total_remboursements = $FACTURES->trouver_montant_paiement_par_ets($ogd['code'],$parametres['num_fichier'],'P',$ets['code']);

                                                $factures = $FACTURES->lister_factures_payees_a_telecharger_par($ogd['code'],$parametres['num_fichier'],'P',$ets['code']);
                                                $nb_factures = count($factures);

                                                $xmlWriter->startElement('ENTETE');
                                                $xmlWriter->writeElement('NOM_MODELE', $norme);
                                                $xmlWriter->writeElement('VERSION', $version);
                                                $xmlWriter->writeElement('DATE_EDITION', $date_edition);
                                                $xmlWriter->writeElement('DUPLICATA_O_N', 0);
                                                $xmlWriter->writeElement('OCCURRENCES_ENTETE', $etablissement['nombre_factures']);
                                                $xmlWriter->writeElement('CODE_MUTUELLE', 1);
                                                $xmlWriter->writeElement('NOM_MUTUELLE', $ogd['libelle']);
                                                for ($id_lib_mutuelle = 1; $id_lib_mutuelle <= 6; $id_lib_mutuelle++) {
                                                    $xmlWriter->writeElement('LIB_ADR_'.$id_lib_mutuelle.'_MUTUELLE', '');
                                                }
                                                $xmlWriter->writeElement('TEL_MUTUELLE', '');
                                                $xmlWriter->writeElement('FAX_MUTUELLE', '');
                                                $xmlWriter->writeElement('MAIL_MUTUELLE', '');
                                                $xmlWriter->writeElement('CODE_ORGANISME', $ogd['code']);
                                                $xmlWriter->writeElement('NOM_ORGANISME', $ogd['libelle']);
                                                for ($id_lib_organisme = 1; $id_lib_organisme <= 6; $id_lib_organisme++) {
                                                    $xmlWriter->writeElement('LIB_ADR_'.$id_lib_organisme.'_ORGANISME', '');
                                                }
                                                $xmlWriter->writeElement('TEL_ORGANISME', '');
                                                $xmlWriter->writeElement('FAX_ORGANISME', '');
                                                $xmlWriter->writeElement('MAIL_ORGANISME', '');
                                                $xmlWriter->writeElement('CODE_AGENCE', '');
                                                $xmlWriter->writeElement('NOM_AGENCE', '');
                                                for ($id_lib_agence = 1; $id_lib_agence <= 6; $id_lib_agence++) {
                                                    $xmlWriter->writeElement('LIB_ADR_'.$id_lib_agence.'_AGENCE', '');
                                                }
                                                $xmlWriter->writeElement('TEL_AGENCE', '');
                                                $xmlWriter->writeElement('FAX_AGENCE', '');
                                                $xmlWriter->writeElement('MAIL_AGENCE', '');
                                                $xmlWriter->writeElement('CODE_CONSEILLER', '');
                                                $xmlWriter->writeElement('NOM_CONSEILLER', '');
                                                $xmlWriter->writeElement('PRENOM_CONSEILLER', '');
                                                $xmlWriter->writeElement('TEL_CONSEILLER', '');
                                                $xmlWriter->writeElement('FAX_CONSEILLER', '');
                                                $xmlWriter->writeElement('MAIL_CONSEILLER', '');
                                                $xmlWriter->writeElement('TYPE_ACT_DES_COU', 'ETAB');
                                                $xmlWriter->writeElement('NUM_ACT_DES_COU', NULL);
                                                $xmlWriter->writeElement('CODE_CIV_DES_COU', '');
                                                $xmlWriter->writeElement('LIB_CIV_DES_COU', '');
                                                $xmlWriter->writeElement('NOM_DES_COU', '');
                                                $xmlWriter->writeElement('PRENOM_DES_COU', '');
                                                $xmlWriter->writeElement('RAIS_SOC_DES_COU', $ets['raison_sociale']);
                                                for ($id_lib_cou = 1; $id_lib_cou <= 6; $id_lib_cou++) {
                                                    $xmlWriter->writeElement('LIB_ADR_'.$id_lib_cou.'_DES_COU', '');
                                                }
                                                $xmlWriter->writeElement('CODE_POSTAL_DES_COU', '00000');
                                                $xmlWriter->writeElement('DN_CODE_DEPARTEMENT', $ets['code_psh']);
                                                $xmlWriter->writeElement('NOM_ACHEMINEMENT_DES_COU', $ets['ville']);
                                                $xmlWriter->writeElement('CODE_PAYS_DES_COU', 'CIV');
                                                $xmlWriter->writeElement('NOM_PAYS_DES_COU', 'CÔTE D\'IVOIRE');
                                                $xmlWriter->writeElement('TEL_DES_COU', '');
                                                $xmlWriter->writeElement('FAX_DES_COU', '');
                                                $xmlWriter->writeElement('MAIL_DES_COU', '');
                                                $xmlWriter->writeElement('CODE_SYSTEME_GESTION', 'PRODUCTION');
                                                $xmlWriter->writeElement('ID_NUM_EVENEMENT', '');
                                                $xmlWriter->writeElement('CODE_SIGNATAIRE_MAIL', '');
                                                $xmlWriter->writeElement('NUM_RELEVE', NULL);
                                                $xmlWriter->writeElement('TYPE_EDITION', 'EDT');
                                                $xmlWriter->writeElement('INFO_DUPLICATA_O_N', 0);
                                                $xmlWriter->writeElement('DATE_DEBUT_RELEVE', '01/01/'.date('Y',time()));
                                                $xmlWriter->writeElement('DATE_FIN_RELEVE', $date_edition);
                                                $xmlWriter->writeElement('CODE_EXECUTANT', $ets['code']);
                                                $xmlWriter->writeElement('CODE_CONVENTION_EDITION', 'BRLTOU'); /*A vérifier*/
                                                $xmlWriter->writeElement('MTT_TOTAL_REMB', $montant_total_remboursements['montant_remboursement']);
                                                $xmlWriter->writeElement('MTT_TOTAL_REJET_AUTO', '');
                                                $xmlWriter->writeElement('MTT_TOTAL_NON_LIQ', '');
                                                $xmlWriter->writeElement('MTT_TOTAL_RELEVE', $montant_total_remboursements['montant_depense']);
                                                for ($id_message = 1; $id_message <= 5; $id_message++) {
                                                    $xmlWriter->writeElement('LIB_MESSAGE_'.$id_message, '');
                                                }
                                                $xmlWriter->writeElement('SOLDE_INDU', '0.00');
                                                $xmlWriter->writeElement('OCCURRENCES_REMB', $nb_factures);
                                                $xmlWriter->writeElement('OCCURRENCES_REJET_AUTO', '');
                                                $xmlWriter->writeElement('OCCURRENCES_REJET_MANU', '');
                                                $xmlWriter->writeElement('OCCURRENCES_REJET_DOSSIER', '');
                                                $xmlWriter->startElement('LIST_REMBOURSEMENT');
                                                foreach ($factures as $facture) {
                                                    $lien_archivage = $ogd['code'].$facture['facture_num'].date('dmyHis',time());
                                                    $assure = $ASSURES->trouver($facture['num_secu']);
                                                    $assure_identifiants = $ASSURES->lister_identifiants($facture['num_secu']);
                                                    $lignes_actes = $FACTURES->lister_facture_lignes_actes($facture['facture_num']);

                                                    $montant = $FACTURES->trouver_montant_facture($facture['facture_num']);

                                                    $xmlWriter->startElement('REMBOURSEMENT');
                                                    $xmlWriter->writeElement('NUM_PAIEMENT', $facture['num_decpai']);
                                                    $xmlWriter->writeElement('LIB_BANQUE_EMETTEUR', '');
                                                    $xmlWriter->writeElement('NUM_CHEQUE', $facture['num_decpai']);
                                                    $xmlWriter->writeElement('DATE_PAIEMENT', $date_edition);
                                                    $xmlWriter->writeElement('CODE_TYPE_PAIEMENT', 'INT');
                                                    $xmlWriter->writeElement('CODE_MODE_PAIEMENT', 'CHQ');
                                                    $xmlWriter->writeElement('MTT_PAIEMENT', round(($montant['depense'] * 0.7),2));
                                                    $xmlWriter->writeElement('COMPTE_BANCAIRE', '');
                                                    $xmlWriter->writeElement('CODE_BIC', '');
                                                    $xmlWriter->writeElement('NUM_IBAN', '');
                                                    $xmlWriter->writeElement('MTT_TOTAL_PRESTATIONS', round(($montant['depense'] * 0.7),2));
                                                    $xmlWriter->writeElement('MTT_ESCOMPTE_REMBOURSEMENT', '');
                                                    $xmlWriter->startElement('LIST_BORDEREAU');
                                                    $xmlWriter->startElement('BORDEREAU');
                                                    $xmlWriter->writeElement('NUM_BORDEREAU', '');
                                                    $xmlWriter->startElement('LIST_DOSSIER_REMB');
                                                    $xmlWriter->startElement('DOSSIER_REMB');
                                                    $xmlWriter->writeElement('NUM_FACTURE', $facture['facture_num']);
                                                    $xmlWriter->writeElement('NUM_DOSSIER_MALADIE', $facture['lien_archivage']);
                                                    $xmlWriter->writeElement('CODE_STATUT_PDS_DOSMAL', '');
                                                    $xmlWriter->writeElement('LIB_STATUT_PDS_DOSMAL', '');

                                                    $xmlWriter->writeElement('CODE_COLL_CTT', '');
                                                    $xmlWriter->writeElement('RAISON_SOCIALE_COLL_CTT', '');
                                                    $xmlWriter->writeElement('CODE_COLLEGE_CTT', '');
                                                    $xmlWriter->writeElement('LIB_COLLEGE_CTT', '');

                                                    $xmlWriter->writeElement('NUM_DOSSIER_SOINS', $facture['num_ds']);
                                                    $xmlWriter->writeElement('CODE_STATUT_PDS_DOSOINS', '');
                                                    $xmlWriter->writeElement('LIB_STATUT_PDS_DOSOINS', '');
                                                    $xmlWriter->writeElement('LIEN_ARCHIVAGE_NOEMIE', '');
                                                    $xmlWriter->writeElement('CODE_COLLECTIVITE_DEM', '');
                                                    $xmlWriter->writeElement('RAIS_SOC_COLLECTIVITE_DEM', '');
                                                    $xmlWriter->writeElement('NUM_APPORTEUR_DEM', '');
                                                    $xmlWriter->writeElement('RAISON_SOCIALE_APPORTEUR_DEM', '');
                                                    $xmlWriter->writeElement('TRANSMIS_SECU_O_N', '');
                                                    $xmlWriter->writeElement('MTT_TOTAL_DOSSIER', $montant['montant_depense']);
                                                    $xmlWriter->writeElement('OCCURRENCES_ACTE_REMB', count($lignes_actes));
                                                    $xmlWriter->startElement('LIST_ACTE_REMB');
                                                    foreach ($lignes_actes as $lignes_acte) {
                                                        if(strlen($lignes_acte['code_acte']) == 7) {
                                                            $acte = $ACTESMEDICAUX->trouver($lignes_acte['code_acte']);
                                                        }else {
                                                            $acte = $MEDICAMENTS->trouver($lignes_acte['code_acte']);
                                                        }
                                                        $xmlWriter->startElement('ACTE_REMB');
                                                        $xmlWriter->writeElement('NOM_BENEF', $assure['nom']);
                                                        $xmlWriter->writeElement('PRENOM_BENEF', $assure['prenom']);
                                                        $xmlWriter->writeElement('NOM_PRENOM_BENEF', $assure['nom'].' '.$assure['prenom']);
                                                        $xmlWriter->writeElement('NUM_SECU_BENEF', $assure['num_secu']);
                                                        $xmlWriter->writeElement('NUM_PERSONNE_BENEF', $assure['identifiant_personne']);
                                                        $xmlWriter->writeElement('DATE_NAISSANCE_BENEF', date('d/m/Y',strtotime($assure['date_naissance'])));
                                                        $xmlWriter->writeElement('CODE_ACTE', $acte['code']);
                                                        $xmlWriter->writeElement('LIB_RELEVE_ACTE', $acte['libelle']);
                                                        $xmlWriter->writeElement('CODE_COMPLEMENT_ACTE', '-');
                                                        $xmlWriter->writeElement('LIB_COMPLEMENT_ACTE', 'pas de complément');
                                                        $xmlWriter->writeElement('COMMENTAIRE_LIQ', '');
                                                        $xmlWriter->writeElement('DATE_DEBUT_SOINS', date('d/m/Y',strtotime($lignes_acte['date_debut_soins'])));
                                                        $xmlWriter->writeElement('DATE_FIN_SOINS', date('d/m/Y',strtotime($lignes_acte['date_fin_soins'])));
                                                        $xmlWriter->writeElement('INCOHERENCE_RG_PDS_O_N', 0);
                                                        $xmlWriter->writeElement('QTE_ACTE', $lignes_acte['quantite_acte']);
                                                        $xmlWriter->writeElement('PRIX_UNITAIRE', $lignes_acte['prix_unitaire']);
                                                        $xmlWriter->writeElement('COEFF_ACTE', $lignes_acte['coefficient_acte'].'.00');
                                                        $xmlWriter->writeElement('MTT_DEPENSE', $lignes_acte['montant_depense']);
                                                        $xmlWriter->writeElement('MTT_BASE_REMB', $lignes_acte['base_remboursement']);
                                                        $xmlWriter->writeElement('MTT_RO', $lignes_acte['montant_remboursement_ro']);
                                                        $xmlWriter->writeElement('MTT_RC', '0.00');
                                                        $xmlWriter->writeElement('MTT_ESCOMPTE_ACTE_REMB', '0.00');
                                                        $xmlWriter->writeElement('PRIX_UNITAIRE_DEMANDE', $lignes_acte['prix_unitaire']);
                                                        $xmlWriter->writeElement('MTT_RC_DEMANDE', '0.00');
                                                        for ($id_cumul_rc = 1; $id_cumul_rc <= 5; $id_cumul_rc++) {
                                                            $xmlWriter->writeElement('MTT_RC_CUMUL_'.$id_cumul_rc, '');
                                                        }
                                                        for ($id_cumul_ro = 1; $id_cumul_ro <= 5; $id_cumul_ro++) {
                                                            $xmlWriter->writeElement('MTT_RO_CUMUL_'.$id_cumul_ro, '');
                                                        }
                                                        $xmlWriter->writeElement('CAPITATION_O_N_LA', 0);
                                                        $xmlWriter->writeElement('OCCURRENCES_LIGNE_REMB', 0);
                                                        $xmlWriter->writeElement('MTT_DEJA_REMB', '0.00');
                                                        $xmlWriter->startElement('LIST_LIGNE_REMB');
                                                        $xmlWriter->startElement('LIGNE_REMB');
                                                        $xmlWriter->writeElement('NUM_LIGNE_REMBOURSEMENT', '');
                                                        $xmlWriter->writeElement('ID_NUM_BENEF_CONTRAT_CTT', '');
                                                        $xmlWriter->writeElement('CODE_STATUT_CONTRAT_CTT', '');
                                                        $xmlWriter->writeElement('ID_NUM_BENEF_CONTRAT_BENEF', '');
                                                        $xmlWriter->writeElement('CODE_STATUT_CONTRAT_BENEF', '');
                                                        $xmlWriter->writeElement('ID_NUM_PERSONNE_CTT', '');
                                                        $xmlWriter->writeElement('NOM_CTT', '');
                                                        $xmlWriter->writeElement('PRENOM_CTT', '');
                                                        $xmlWriter->writeElement('CODE_PART_REMBOURSEMENT', '');
                                                        $xmlWriter->writeElement('LIB_PART_REMBOURSEMENT', '');
                                                        $xmlWriter->writeElement('CODE_PRODUIT', '');
                                                        $xmlWriter->writeElement('NOM_PRODUIT', '');
                                                        $xmlWriter->writeElement('MTT_REMB', '');
                                                        $xmlWriter->writeElement('MTT_ESCOMPTE_LIGNE_REMB', '0.00');
                                                        $xmlWriter->writeElement('CAPITATION_O_N_LR', '');
                                                        $xmlWriter->writeElement('CAPITATION_O_N_LR', '');
                                                        $xmlWriter->startElement('LIST_IDENTIFIANTS_EXT_CTT');
                                                        $xmlWriter->endElement();
                                                        $xmlWriter->writeElement('OCCURRENCES_IDENT_EXT_CTT', 0);
                                                        $maj_facture_statut = $FACTURES->edition_facture_cheque($facture['facture_num'],$num_paiement,$lien_archivage,$statut_fin,$utilisateur['user_id']);
                                                        if($maj_facture_statut['success'] == true) {
                                                            $succes++;
                                                        }
                                                        $xmlWriter->endElement();
                                                        $xmlWriter->endElement();
                                                        $xmlWriter->startElement('LIST_IDENTIFIANTS_EXT_BENEF_AR');
                                                        foreach ($assure_identifiants as $assure_identifiant) {
                                                            $identifiant = $IDENTIFIANTS->trouver($assure_identifiant['type']);
                                                            $nature = $IDENTIFIANTS->trouver($identifiant['code_nature']);

                                                            if(empty($assure_identifiant['date_fin'])) {
                                                                $identifiant_date_fin = NULL;
                                                            }else {
                                                                $identifiant_date_fin = date('d-M-y',strtotime($assure_identifiant['date_fin']));
                                                            }

                                                            $xmlWriter->startElement('IDENTIFIANT_EXT_BENEF_AR');
                                                            $xmlWriter->writeElement('CODE_TYPE_IDE_EXT', $assure_identifiant['type']);
                                                            $xmlWriter->writeElement('LIB_TYPE_IDE_EXT', $identifiant['libelle']);
                                                            $xmlWriter->writeElement('NUM_IDENTIFIANT', $assure_identifiant['numero']);
                                                            $xmlWriter->writeElement('DATE_DEBUT_VALIDITE', date('d-M-y',strtotime($assure_identifiant['date_debut'])));
                                                            $xmlWriter->writeElement('DATE_FIN_VALIDITE', '');
                                                            $xmlWriter->writeElement('CODE_NATURE_IDE_EXT', $identifiant['code_nature']);
                                                            $xmlWriter->writeElement('LIB_NATURE_IDE_EXT', $nature['libelle']);
                                                            $xmlWriter->writeElement('ORDRE_NATURE_IDE_EXT', $nature['ordre_1']);
                                                            $xmlWriter->endElement();
                                                        }
                                                        $xmlWriter->endElement();
                                                        $xmlWriter->writeElement('OCCURRENCES_IDENT_EXT_BENEF_AR', count($assure_identifiants));
                                                        $xmlWriter->endElement();
                                                    }
                                                    $xmlWriter->endElement();
                                                    $xmlWriter->endElement();
                                                    $xmlWriter->endElement();
                                                    $xmlWriter->writeElement('MTT_BORDEREAU', round(($montant['depense'] * 0.7),2));
                                                    $xmlWriter->writeElement('OCCURRENCES_DOSSIER_REMB', '1');
                                                    $xmlWriter->endElement();
                                                    $xmlWriter->endElement();

                                                    $xmlWriter->writeElement('CODE_ORGANISME_LOT_PAI', '');
                                                    $xmlWriter->writeElement('NOM_ORGANISME_LOT_PAI', '');
                                                    $xmlWriter->writeElement('OCCURRENCES_BORDEREAU', '1');
                                                    $xmlWriter->endElement();
                                                }
                                                $xmlWriter->endElement();
                                                $xmlWriter->startElement('LIST_REJET_AUTO');
                                                $xmlWriter->endElement();
                                                $xmlWriter->startElement('LIST_REJET_MANU');
                                                $xmlWriter->endElement();
                                                $xmlWriter->startElement('LIST_REJET_DM');
                                                $xmlWriter->endElement();
                                                $xmlWriter->endElement();
                                            }
                                            $xmlWriter->endElement();
                                            $xmlWriter->endDocument();

                                            file_put_contents($path.'/'.$file_name, $xmlWriter->flush(true), FILE_APPEND);
                                            $chargement = $LOGS->ajouter_chargement_fichier('EXP',$file_name,'FIN',"FIN DE GENERATION",$utilisateur['user_id']);
                                            if($chargement['success'] == true) {
                                                $log = $UTILISATEURS->ajouter_log_piste_audit(NULL,'CREATION','CHARGEMENT FICHIER: (mouvement => EXP, nom_fichier => '.$file_name.')',$utilisateur['user_id']);
                                                if($log['success'] == true) {
                                                    $historique = $LOGS->ajouter_historique_fichier('EXP',$num_paiement,$norme,$version,date('Y-m-d',time()),$ogd['code'],$ogd['code'],$nb_factures,$succes,NULL,$file_name,$utilisateur['user_id']);
                                                    if($historique['success'] == true) {
                                                        $message = 'Fichier '.$file_name.' généré avec succès.<br /><a href="'.$lien.$file_name.'" target="_blank" download="'.$file_name.'">Cliquez ici pour télécharger le fichier</a>';
                                                        $maj_script = $SCRIPTS->mise_a_jour($nouveau_script['id'],NULL,NULL,date('Y-m-d H:i:s',time()),'FIN',$message,$utilisateur['user_id']);
                                                        if($maj_script['success'] == true) {
                                                            $edition_rejets = $FACTURES->edition_factures_organisme_rejetees($ogd['code'],$num_fichier,$num_paiement,$utilisateur['user_id']);
                                                            $json = array(
                                                                'success' => true,
                                                                'message' => $message
                                                            );
                                                        }
                                                        else {
                                                            $json = $maj_script;
                                                        }
                                                    }
                                                    else {
                                                        $json = $historique;
                                                    }
                                                }
                                                else {
                                                    $json = $log;
                                                }
                                            }
                                            else {
                                                $json = $chargement;
                                            }
                                        }
                                        else {
                                            $message = "Aucun établissement identifié pour générer le fichier chèque.";
                                            $json = array(
                                                'success' => false,
                                                'message' => $message
                                            );
                                        }
                                    }
                                    elseif($type_ref == 'BPAIEMENTS') {
                                        $norme = $type_ref;
                                        $version = "4.4.3.1";
                                        $date_edition = date('d/m/Y',time());

                                        $etablissements = $FACTURES->lister_ets_bordereaux_paiements($ogd['code'], 'T',$parametres['num_fichier']);
                                        $nb_etablissements = count($etablissements);
                                        if($nb_etablissements != 0) {
                                            foreach ($etablissements as $etablissement) {
                                                $path_ets = $path.$etablissement['code_ets'].'/'.date('Y',time()).'/'.date('m',time()).'/';
                                                $lien_ets = $lien.$etablissement['code_ets'].'/'.date('Y',time()).'/'.date('m',time()).'/';
                                                if(!file_exists($path_ets)){
                                                    mkdir($path_ets,0777,true);
                                                }
                                                $file_name = $norme."_".$ogd['code'].'_'.$etablissement['code_ets'].'_'.date('dmY_His',time()).".xml";




                                                $spreadsheet = new Spreadsheet();
                                                $sheet = $spreadsheet->getActiveSheet();

                                                $sheet->getPageSetup()->setFitToWidth(1);
                                                $sheet->getPageSetup()->setFitToHeight(0);
                                                /*$sheet->getPageMargins()->setTop(1.4);
                                                $sheet->getPageMargins()->setRight(1.3);
                                                $sheet->getPageMargins()->setLeft(1.3);
                                                $sheet->getPageMargins()->setBottom(1.4);*/
                                                $sheet->getPageSetup()->setHorizontalCentered(true);
                                                $sheet->getPageSetup()->setVerticalCentered(false);

                                                $sheet->getColumnDimension('A')->setWidth(16);
                                                $sheet->getColumnDimension('B')->setWidth(50);
                                                $sheet->getColumnDimension('C')->setWidth(14);
                                                $sheet->getColumnDimension('D')->setWidth(17);
                                                $sheet->getColumnDimension('E')->setWidth(16);

                                                $styleEts= [
                                                    'borders' => [
                                                        'allBorders' => [
                                                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                                                            'color' => ['rgb' => '000000'],
                                                        ],
                                                    ],
                                                    'fill' => [
                                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                                                        'rotation' => 90,
                                                        'startColor' => [
                                                            'rgb' => 'F0E68C',
                                                        ],
                                                        'endColor' => [
                                                            'rgb' => 'F0E68C',
                                                        ],
                                                    ],
                                                    'font' => [
                                                        'bold' => true,
                                                        'size' => 14,
                                                    ],
                                                    'alignment' => [
                                                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                                    ],
                                                ];

                                                $styleEntete = [
                                                    'borders' => [
                                                        'allBorders' => [
                                                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                                            'color' => ['rgb' => '000000'],
                                                        ],
                                                    ],
                                                    'font' => [
                                                        'bold' => true,
                                                    ],
                                                    'fill' => [
                                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                                                        'rotation' => 90,
                                                        'startColor' => [
                                                            'rgb' => '4682B4',
                                                        ],
                                                        'endColor' => [
                                                            'rgb' => '4682B4',
                                                        ],
                                                    ],
                                                ];

                                                $stylePeriode = [
                                                    'borders' => [
                                                        'allBorders' => [
                                                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
                                                        ],
                                                    ],
                                                    'fill' => [
                                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                                                        'rotation' => 90,
                                                        'startColor' => [
                                                            'rgb' => 'FAF0E6',
                                                        ],
                                                        'endColor' => [
                                                            'rgb' => 'FAF0E6',
                                                        ],
                                                    ],
                                                    'font' => [
                                                        'bold' => true,
                                                        'italic' => true,
                                                    ],
                                                    'alignment' => [
                                                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                                    ],
                                                ];

                                                $styleLigne = [
                                                    'borders' => [
                                                        'allBorders' => [
                                                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                                            'color' => ['rgb' => '000000'],
                                                        ],
                                                    ],
                                                ];

                                                $styleResultat = [
                                                    'borders' => [
                                                        'allBorders' => [
                                                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                                            'color' => ['rgb' => '000000'],
                                                        ],
                                                    ],
                                                    'font' => [
                                                        'bold' => true,
                                                    ],
                                                    'fill' => [
                                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                                                        'rotation' => 90,
                                                        'startColor' => [
                                                            'rgb' => 'C0C0C0',
                                                        ],
                                                        'endColor' => [
                                                            'rgb' => 'C0C0C0',
                                                        ],
                                                    ],
                                                ];

                                                $styleTotal = [
                                                    'borders' => [
                                                        'outline' => [
                                                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                                            'color' => ['rgb' => '000000'],
                                                        ],
                                                    ],
                                                    'font' => [
                                                        'bold' => true,
                                                        'color'=>['rgb' => 'ffffff'],
                                                    ],
                                                    'fill' => [
                                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                                                        'rotation' => 90,
                                                        'startColor' => [
                                                            'rgb' => '1E90FF',
                                                        ],
                                                        'endColor' => [
                                                            'rgb' => '1E90FF',
                                                        ],
                                                    ],
                                                ];

                                                $sheet->getStyle('A1:E1')->applyFromArray($styleEts);



                                                $writer = new Xlsx($spreadsheet);
                                                $writer->save($path_ets.$file_name);
                                            }
                                        }else {
                                            $message = "Aucun établissement en attente de génération de bordereau.";
                                            $json = array(
                                                'success' => false,
                                                'message' => $message
                                            );
                                        }
                                    }
                                    else {
                                        $message = 'Le type de télechargement défini est incorrect.';
                                        $json = array(
                                            'success' => false,
                                            'message' => $message
                                        );
                                    }
                                }
                                else {
                                    $json = $nouveau_script;
                                }

                            }
                            else {
                                $message = 'Aucune facture n\'est disponible pour ce téléchargement.';
                                $json = array(
                                    'success' => false,
                                    'message' => $message
                                );
                            }
                        }
                        else {
                            $message = 'Un autre script est en cours d\'exécution. Veuillez attendre la fin de ce script avant de continuer.';
                            $json = array(
                                'success' => false,
                                'message' => $message
                            );
                        }
                    }
                    else {
                        $message = 'Les paramètres requis ne sont pas renseignés.';
                        $json = array(
                            'success' => false,
                            'message' => $message
                        );
                    }
                }
                else {
                    $message = 'Les paramètres requis sont erronés.';
                    $json = array(
                        'success' => false,
                        'message' => $message
                    );
                }
            }
            else{
                $message = 'L\'organisme de l\'utilisateur est inconnu.';
                $json = array(
                    'success' => false,
                    'message' => $message,
                    'test' => $utilisateur['code_ogd']
                );
            }
        }
        else {
            $message = 'L\'organisme de l\'utilisateur n\'est pas défini.';
            $json = array(
                'success' => false,
                'message' => $message
            );
        }
    }
    else {
        $message = 'Aucune session disponible pour cet utilisateur.!!! contactez votre administrateur.';
        $json = array(
            'success' => false,
            'message' => $message
        );
    }
}
else {
    $message = 'Aucune session disponible pour cet utilisateur.!!! contactez votre administrateur.';
    $json = array(
        'success' => false,
        'message' => $message
    );
}
echo json_encode($json);
?>