<?php
/**
 * Created by PhpStorm.
 * User: Dognimin.Koulibali
 * Date: 23/02/2019
 * Time: 18:01
 */

class SCRIPTS extends BDD
{
    public function lister($code_ogd, $statut) {
        $a = $this->bdd->prepare('SELECT script_id AS id, script_nom AS nom, script_date_debut AS date_debut, script_date_fin AS date_fin, script_statut AS statut, script_description AS description, date_reg, user_reg, date_edit, user_edit FROM auxil_scripts WHERE ogd_code LIKE ? AND script_statut LIKE ? ORDER BY date_reg DESC');
        $a->execute(array('%'.$code_ogd.'%','%'.$statut.'%'));
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

    public function lecture_entete_fichier_xml($nom_modele, $version, $code_systeme_gestion, $code_organisme_emet, $nom_organisme_emet, $code_organisme_dest, $nom_organisme_dest, $num_transmission, $date_fichier, $occurrences, $nom_fichier) {
        $json = null;
        $LOGS = new LOGS();
        if($nom_modele) {
            $json[0] = NULL;
        }else {
            $json[1] = array(
                'message' => "La norme du fichier chargé n'est pas renseignée."
            );
        }
        if($nom_modele == 'SITFAMILLE') {
            $systeme_de_gestion = array('TEST','PRODUCTION');
            if($version) {
                $json[0] = NULL;
            }else {
                $json[2] = array(
                    'message' => "La version du fichier chargé n'est pas renseignée."
                );
            }
            if($code_systeme_gestion) {
                if(in_array($code_systeme_gestion,$systeme_de_gestion)) {
                    $json[0] = NULL;
                }else {
                    $json[3] = array(
                        'message' => "Le code du système de gestion du fichier chargé est inconnu."
                    );
                }

            }else {
                $json[3] = array(
                    'message' => "Le code du système de gestion du fichier chargé n'est pas renseigné."
                );
            }
            if($code_organisme_emet) {
                if($code_organisme_emet == '01001000') {
                    $json[0] = NULL;
                }else {
                    $json[4] = array(
                        'message' => "Le code de l'organisme emetteur du fichier chargé est inconnu."
                    );
                }

            }else {
                $json[4] = array(
                    'message' => "Le code de l'organisme emetteur du fichier chargé n'est pas renseigné."
                );
            }
            if($nom_organisme_emet) {
                if($nom_organisme_emet == 'CNAM') {
                    $json[0] = NULL;
                }else {
                    $json[5] = array(
                        'message' => "Le nom de l'organisme emetteur du fichier chargé est inconnu."
                    );
                }

            }else {
                $json[5] = array(
                    'message' => "Le nom de l'organisme emetteur du fichier chargé n'est pas renseigné."
                );
            }
            if($code_organisme_dest) {
                if($code_organisme_dest == 'OP') {
                    $json[0] = NULL;
                }else {
                    $UTILISATEURS = new UTILISATEURS();
                    $user = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
                    if($user) {
                        if($user['code_ogd']) {
                            if($user['code_ogd'] == $code_organisme_dest) {
                                $json[0] = NULL;
                            }else {
                                $json[6] = array(
                                    'message' => "Vous n'êtes pas habilité à charger le flux de cet OGD Prestations."
                                );
                            }
                        }else {
                            $json[6] = array(
                                'message' => "L'utilisateur n'est relié à aucun OGD Prestations, veuillez contacter votre administrateur."
                            );
                        }
                    }else {
                        $json[6] = array(
                            'message' => "Vous n'êtes pas habilité à acceder à cette plateforme."
                        );
                    }
                }

            }else {
                $json[6] = array(
                    'message' => "Le code de l'organisme destinataire du fichier chargé n'est pas renseigné."
                );
            }
            if($nom_organisme_dest) {
                if($nom_organisme_dest == 'OGD PRESTATIONS') {
                    $json[0] = NULL;
                }else {
                    $UTILISATEURS = new UTILISATEURS();
                    $user = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
                    if($user) {
                        if($user['code_ogd']) {
                            if($user['code_ogd'] == $code_organisme_dest) {
                                $json[0] = NULL;
                            }else {
                                $json[7] = array(
                                    'message' => "Vous n'êtes pas habilité à charger le flux de cet OGD Prestations."
                                );
                            }
                        }else {
                            $json[7] = array(
                                'message' => "L'utilisateur n'est relié à aucun OGD Prestations, veuillez contacter votre administrateur."
                            );
                        }
                    }else {
                        $json[7] = array(
                            'message' => "Vous n'êtes pas habilité à acceder à cette plateforme."
                        );
                    }
                }

            }else {
                $json[7] = array(
                    'message' => "Le nom de l'organisme destinataire du fichier chargé n'est pas renseigné."
                );
            }
            if($num_transmission) {
                $fichier = $LOGS->trouver_fichier($nom_modele,$num_transmission,NULL,NULL);
                if(!$fichier) {
                    $json[0] = NULL;
                }else {
                    $json[8] = array(
                        'message' => "Le numéro de transmission du fichier a déjà été utilisé pour un autre chargement."
                    );
                }

            }else {
                $json[8] = array(
                    'message' => "Le numéro de transmission du fichier chargé n'est pas renseigné."
                );
            }
            if($date_fichier) {
                if(checkdate(date('m',strtotime($date_fichier)),date('d',strtotime($date_fichier)),date('Y',strtotime($date_fichier)))) {
                    $json[0] = NULL;
                }else {
                    $json[9] = array(
                        'message' => "Le format de la date du fichier chargé est incorrect."
                    );
                }
            }else {
                $json[9] = array(
                    'message' => "La date du fichier chargé n'est pas renseignée."
                );
            }
            if($occurrences) {
                $json[0] = NULL;
            }else {
                $json[10] = array(
                    'message' => "L'occurrence du fichier chargé n'est pas renseignée."
                );
            }
            if($nom_fichier) {
                $fichier = $LOGS->trouver_fichier($nom_modele,NULL,$nom_fichier,NULL);
                if(!$fichier) {
                    $json[0] = NULL;
                }else {
                    $json[11] = array(
                        'message' => "Le fichier a déjà été utilisé pour un autre chargement."
                    );
                }
            }else {
                $json[11] = array(
                    'message' => "Le fichier chargé ne possède pas de nom. Veuillez le renommer, puis procéder à un nouveau chargement."
                );
            }
        }
        elseif($nom_modele == 'DECA' || $nom_modele == 'DECRET') {
            if($version) {
                $json[0] = NULL;
            }else {
                $json[2] = array(
                    'message' => "La version du fichier chargé n'est pas renseignée."
                );
            }
            if($code_organisme_emet) {
                if($code_organisme_emet == '01001000' || $code_organisme_emet == '1') {
                    $json[0] = NULL;
                }else {
                    $json[4] = array(
                        'message' => "Le numéro de l'organisme emetteur: {$code_organisme_emet} du fichier chargé est inconnu."
                    );
                }

            }else {
                $json[4] = array(
                    'message' => "Le code de l'organisme emetteur du fichier chargé n'est pas renseigné."
                );
            }
            if($nom_organisme_emet) {
                if($nom_organisme_emet == 'CN') {
                    $json[0] = NULL;
                }else {
                    $json[5] = array(
                        'message' => "Le type de l'organisme emetteur du fichier chargé est inconnu."
                    );
                }

            }else {
                $json[5] = array(
                    'message' => "Le type de l'organisme emetteur du fichier chargé n'est pas renseigné."
                );
            }
            if($code_organisme_dest) {
                if($code_organisme_dest == 'OP') {
                    $json[0] = NULL;
                }else {
                    $UTILISATEURS = new UTILISATEURS();
                    $user = $UTILISATEURS->trouver($_SESSION['auxilium_user_id'],NULL,NULL);
                    if($user) {
                        if($user['code_ogd']) {
                            if($user['code_ogd'] == $code_organisme_dest) {
                                $json[0] = NULL;
                            }else {
                                $json[6] = array(
                                    'message' => "Vous n'êtes pas habilité à charger le flux de cet OGD Prestations."
                                );
                            }
                        }else {
                            $json[6] = array(
                                'message' => "L'utilisateur n'est relié à aucun OGD Prestations, veuillez contacter votre administrateur."
                            );
                        }
                    }else {
                        $json[6] = array(
                            'message' => "Vous n'êtes pas habilité à acceder à cette plateforme."
                        );
                    }
                }

            }else {
                $json[6] = array(
                    'message' => "Le code de l'organisme destinataire du fichier chargé n'est pas renseigné."
                );
            }
            if($nom_organisme_dest) {
                if($nom_organisme_dest == 'OP') {
                    $json[0] = NULL;
                }else {
                    $json[5] = array(
                        'message' => "Le type de l'organisme destinataire du fichier chargé n'est pas renseigné."
                    );
                }

            }else {
                $json[7] = array(
                    'message' => "Le nom de l'organisme destinataire du fichier chargé n'est pas renseigné."
                );
            }
            if($num_transmission) {
                $fichier = $LOGS->trouver_fichier($nom_modele,$num_transmission,NULL,NULL);
                if(!$fichier) {
                    $json[0] = NULL;
                }else {
                    $json[8] = array(
                        'message' => "Le numéro de transmission du fichier a déjà été utilisé pour un autre chargement."
                    );
                }

            }else {
                $json[8] = array(
                    'message' => "Le numéro de transmission du fichier chargé n'est pas renseigné."
                );
            }
            if($date_fichier) {
                if(checkdate(date('m',strtotime($date_fichier)),date('d',strtotime($date_fichier)),date('Y',strtotime($date_fichier)))) {
                    $json[0] = NULL;
                }else {
                    $json[9] = array(
                        'message' => "Le format de la date du fichier chargé est incorrect."
                    );
                }
            }else {
                $json[9] = array(
                    'message' => "La date du fichier chargé n'est pas renseignée."
                );
            }
            if($occurrences) {
                $json[0] = NULL;
            }else {
                $json[10] = array(
                    'message' => "L'occurrence du fichier chargé n'est pas renseignée."
                );
            }
            if($nom_fichier) {
                $fichier = $LOGS->trouver_fichier($nom_modele,NULL,$nom_fichier,NULL);
                if(!$fichier) {
                    $json[0] = NULL;
                }else {
                    $json[11] = array(
                        'message' => "Le fichier a déjà été utilisé pour un autre chargement."
                    );
                }
            }else {
                $json[11] = array(
                    'message' => "Le fichier chargé ne possède pas de nom. Veuillez le renommer, puis procéder à un nouveau chargement."
                );
            }
        }
        else {
            $json[20] = array(
                'message' => "La norme n'est pas reconnu par le système."
            );
        }
        return $json;
    }
}