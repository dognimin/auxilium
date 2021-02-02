<?php

include "BDD.php";
class UTILISATEURS extends BDD {
    public function ajouter_log_piste_audit($adresse_ip,$action,$details,$user) {
        $a = $this->bdd->prepare('INSERT INTO auxil_log_piste_audit(log_adresse_ip, log_action, log_details, user_reg) VALUES(:log_adresse_ip, :log_action, :log_details, :user_reg)');
        $a->execute(array(
            'log_adresse_ip' => $adresse_ip,
            'log_action' => $action,
            'log_details' => $details,
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

    public function connexion($adresse_ip, $nom_utilisateur, $mot_de_passe) {
        $message_0 = "Nom d'utilisateur et/ou mot de passe incorrect.";
        $message_1 = "Ce compte a été désactivé. Prière de contacter votre administrateur.";

        $a = $this->bdd->prepare('SELECT utilisateur_id AS user_id, ogd_code AS code_ogd, utilisateur_pseudo AS pseudo, utilisateur_mot_de_passe AS mot_de_passe, utilisateur_mot_de_passe_statut AS statut_mdp, utilisateur_statut AS statut, utilisateur_derniere_connexion AS derniere_connexion, utilisateur_adresse_ip AS adresse_ip FROM auxil_utilisateur WHERE utilisateur_pseudo = ? OR utilisateur_email = ?');
        $a->execute(array($nom_utilisateur,$nom_utilisateur));
        $user = $a->fetch();
        if(!empty($user['user_id'])) {
            if(password_verify($mot_de_passe, $user['mot_de_passe'])) {
                if($user['statut'] == 1) {
                    $json = array(
                        'success' => true,
                        'user_id' => $user['user_id'],
                        'statut_mdp' => $user['statut_mdp'],
                        'message' => 'Dernière connexion réussie le '.date('d/m/Y',strtotime($user['derniere_connexion'])).' à '.date('H:i:s',strtotime($user['derniere_connexion'])).' à partir de l\'adresse IP: '.$user['adresse_ip']
                    );

                    $this->ajouter_log_piste_audit($adresse_ip,'CONNEXION',URL,$user['user_id']);
                }else {
                    $json = array(
                        'success' => false,
                        'message' => $message_1
                    );
                }
            }else {
                $json = array(
                    'success' => false,
                    'message' => $message_0
                );
            }
        }else {
            $json = array(
                'success' => false,
                'message' => $message_0
            );
        }
        return $json;
    }

    public function maj_derniere_connexion($user_id,$user_ip) {
        $a = $this->bdd->prepare('UPDATE auxil_utilisateur SET utilisateur_derniere_connexion = ?, utilisateur_adresse_ip = ? WHERE utilisateur_id = ?');
        $a->execute(array(date('Y-m-d H:i:s',time()),$user_ip,$user_id));
        $json = array(
            'success' => true
        );
        return $json;
    }

    public function trouver($id, $email, $pseudo) {
        if(empty($id)) {
            $a = $this->bdd->prepare('SELECT utilisateur_id AS user_id, ogd_code AS code_ogd, utilisateur_num_matricule AS num_matricule, utilisateur_pseudo AS pseudo, utilisateur_mot_de_passe AS mot_de_passe, utilisateur_mot_de_passe_statut AS mot_de_passe_statut, utilisateur_email AS email, utilisateur_nom AS nom, utilisateur_prenom AS prenom, utilisateur_direction AS direction, utilisateur_service AS service, utilisateur_fonction AS fonction, utilisateur_num_telephone AS num_telephone, utilisateur_statut AS statut, utilisateur_modules AS modules, utilisateur_sous_modules AS sous_modules, utilisateur_derniere_connexion AS derniere_connexion, utilisateur_adresse_ip AS adresse_ip, utilisateur_voucher AS voucher, date_reg, user_reg, date_edit, user_edit FROM auxil_utilisateur WHERE utilisateur_email LIKE ? AND utilisateur_pseudo LIKE ?');
            $a->execute(array('%'.$email.'%','%'.$pseudo.'%'));
        }else {
            $a = $this->bdd->prepare('SELECT utilisateur_id AS user_id, ogd_code AS code_ogd, utilisateur_num_matricule AS num_matricule, utilisateur_pseudo AS pseudo, utilisateur_mot_de_passe AS mot_de_passe, utilisateur_mot_de_passe_statut AS mot_de_passe_statut, utilisateur_email AS email, utilisateur_nom AS nom, utilisateur_prenom AS prenom, utilisateur_direction AS direction, utilisateur_service AS service, utilisateur_fonction AS fonction, utilisateur_num_telephone AS num_telephone, utilisateur_statut AS statut, utilisateur_modules AS modules, utilisateur_sous_modules AS sous_modules, utilisateur_derniere_connexion AS derniere_connexion, utilisateur_adresse_ip AS adresse_ip, utilisateur_voucher AS voucher, date_reg, user_reg, date_edit, user_edit FROM auxil_utilisateur WHERE utilisateur_id = ?');
            $a->execute(array($id));
        }
        $json = $a->fetch();
        return $json;

    }

    public function lister($code_ogd) {
        if(empty($code_ogd)) {
            $a = $this->bdd->prepare('SELECT utilisateur_id AS user_id, ogd_code AS code_ogd, utilisateur_num_matricule AS num_matricule, utilisateur_pseudo AS pseudo, utilisateur_mot_de_passe AS mot_de_passe, utilisateur_mot_de_passe_statut AS mot_de_passe_statut, utilisateur_email AS email, utilisateur_nom AS nom, utilisateur_prenom AS prenom, utilisateur_direction AS direction, utilisateur_service AS service, utilisateur_fonction AS fonction, utilisateur_statut AS statut, utilisateur_modules AS modules, utilisateur_sous_modules AS sous_modules, utilisateur_derniere_connexion AS derniere_connexion, utilisateur_adresse_ip AS adresse_ip, utilisateur_voucher AS voucher, date_reg, user_reg, date_edit, user_edit FROM auxil_utilisateur ORDER BY prenom ASC, nom ASC, pseudo ASC');
            $a->execute(array());
        }else {
            $a = $this->bdd->prepare('SELECT utilisateur_id AS user_id, ogd_code AS code_ogd, utilisateur_num_matricule AS num_matricule, utilisateur_pseudo AS pseudo, utilisateur_mot_de_passe AS mot_de_passe, utilisateur_mot_de_passe_statut AS mot_de_passe_statut, utilisateur_email AS email, utilisateur_nom AS nom, utilisateur_prenom AS prenom, utilisateur_direction AS direction, utilisateur_service AS service, utilisateur_fonction AS fonction, utilisateur_statut AS statut, utilisateur_modules AS modules, utilisateur_sous_modules AS sous_modules, utilisateur_derniere_connexion AS derniere_connexion, utilisateur_adresse_ip AS adresse_ip, utilisateur_voucher AS voucher, date_reg, user_reg, date_edit, user_edit FROM auxil_utilisateur WHERE ogd_code = ? ORDER BY prenom ASC, nom ASC, pseudo ASC');
            $a->execute(array($code_ogd));
        }
        $json = $a->fetchAll();
        return $json;
    }

    public function lister_type($pseudo, $email, $nom, $prenom) {
        $a = $this->bdd->prepare('SELECT utilisateur_id AS user_id, ogd_code AS code_ogd, utilisateur_num_matricule AS num_matricule, utilisateur_pseudo AS pseudo, utilisateur_mot_de_passe AS mot_de_passe, utilisateur_mot_de_passe_statut AS mot_de_passe_statut, utilisateur_email AS email, utilisateur_nom AS nom, utilisateur_prenom AS prenom, utilisateur_direction AS direction, utilisateur_service AS service, utilisateur_fonction AS fonction, utilisateur_statut AS statut, utilisateur_modules AS modules, utilisateur_sous_modules AS sous_modules, utilisateur_derniere_connexion AS derniere_connexion, utilisateur_adresse_ip AS adresse_ip, utilisateur_voucher AS voucher, date_reg, user_reg, date_edit, user_edit FROM auxil_utilisateur WHERE utilisateur_pseudo LIKE ? AND utilisateur_email LIKE ? AND utilisateur_nom LIKE ? AND utilisateur_prenom LIKE ? ORDER BY prenom ASC, nom ASC, pseudo ASC');
        $a->execute(array('%'.$pseudo.'%','%'.$email.'%','%'.$nom.'%','%'.$prenom.'%'));
        $json = $a->fetchAll();
        return $json;
    }

    public function maj_mot_de_passe($adresse_ip,$user_id,$actuel_mot_de_passe,$nouveau_mot_de_passe,$confirmer_nouveau_mot_de_passe) {
        if($actuel_mot_de_passe == $nouveau_mot_de_passe) {
            $json = array(
                'success' => false,
                'message' => "Le mot de passe actuel et le nouveau mot de passe ne doivent pas êtres identiques"
            );
        }elseif (strlen($nouveau_mot_de_passe) < 8){
            $json = array(
                'success' => false,
                'message' => "Le nouveau mot de passe doit contenir 8 caractères au minimum"
            );
        }else if($nouveau_mot_de_passe != $confirmer_nouveau_mot_de_passe) {
            $json = array(
                'success' => false,
                'message' => "Les nouveaux mots de passe doivent être identiques"
            );
        }else {
            $utilisateur = $this->trouver($user_id,NULL,NULL);
            if(!empty($utilisateur['user_id'])) {
                if(!password_verify($actuel_mot_de_passe,$utilisateur['mot_de_passe'])) {
                    $json = array(
                        'success' => false,
                        'message' => "Le mot de passe actuel est incorrect. Veuillez SVP reéssayer. "
                    );
                }else {
                    $options = [
                        'cost' => 11
                    ];
                    $password = password_hash($nouveau_mot_de_passe, PASSWORD_BCRYPT, $options);
                    $a = $this->bdd->prepare('UPDATE auxil_utilisateur SET utilisateur_mot_de_passe = ?, utilisateur_mot_de_passe_statut = ?, date_edit = ? WHERE utilisateur_id = ?');
                    $a->execute(array($password,1,date('Y-m-d H:i:s',time()),$user_id))OR DIE('Erreur MAJ mot de passe');

                    $this->ajouter_log_piste_audit($adresse_ip,'EDITION','UTILISATEUR: Mise à jour du mot de passe',$user_id);

                    $json = array(
                        'success' => true,
                        'message' => 'La mise à jour du mot de passe a été effectuée avec succès.'
                    );
                }
            }else {
                $json = array(
                    'success' => false,
                    'message' => "Utilisateur inconnu."
                );
            }
        }
        return $json;
    }

    public function maj_habilitations($id_user, $modules, $sous_modules, $user) {
        $a = $this->bdd->prepare("UPDATE auxil_utilisateur SET utilisateur_modules = ?, utilisateur_sous_modules = ?, user_edit = ?, date_edit = ? WHERE utilisateur_id = ?");
        $a->execute(array($modules, $sous_modules,$user, date('Y-m-d H:i:s',time()),$id_user));
        if($a->errorCode() == '00000') {
            $this->ajouter_log_piste_audit('','EDITION','HABILITATIONS MODULES: '.$modules.' -> HABILITATIONS SOUS MODULES: '.$sous_modules,$user);

            $json = array(
                'success' => true,
                'message' => 'Enregistrement effectué avec succès'
            );
        }else {
            $json = array(
                'success' => false,
                'message' => $a->errorInfo()[2]
            );
        }
        return $json;
    }

    public function reinitialiser_mot_de_passe($user_id,$mot_de_passe,$voucher) {
        $options = [
            'cost' => 11
        ];
        $password = password_hash($mot_de_passe, PASSWORD_BCRYPT, $options);
        $a = $this->bdd->prepare('UPDATE auxil_utilisateur SET utilisateur_mot_de_passe = ?, utilisateur_mot_de_passe_statut = ?, utilisateur_voucher = ? WHERE utilisateur_id = ?');
        $a->execute(array($password, 0, $voucher,$user_id));
        if($a->errorCode() == '00000') {
            $this->ajouter_log_piste_audit('','EDITION','UTILISATEUR: Réinitialisation du mot de passe',$user_id);

            $json = array(
                'success' => true,
                'message' => 'Enregistrement effectué avec succès'
            );
        }else {
            $json = array(
                'success' => false,
                'message' => $a->errorInfo()[2]
            );
        }
        return $json;
    }

    public function maj_activation_desactivation($user_id, $user){
        $utilisateur = $this->trouver($user_id,NULL,NULL);
        if ($utilisateur) {
            if($utilisateur['statut'] == 1) {
                $statut = 0;
            }else {
                $statut = 1;
            }
            $a = $this->bdd->prepare("UPDATE auxil_utilisateur SET utilisateur_statut = ?, date_edit = ?, user_edit = ? WHERE utilisateur_id = ?");
            $a->execute(array($statut, date('Y-m-d H:i:s',time()),$user,$user_id));
            if($a->errorCode() == '00000') {
                $this->ajouter_log_piste_audit('','EDITION','EDITION STATUT DE '.$utilisateur['nom'].' '.$utilisateur['prenom'].' '.$statut,$user);

                $json = array(
                    'success' => true,
                    'message' => 'Enregistrement effectué avec succès'
                );
            }else {
                $json = array(
                    'success' => false,
                    'message' => $a->errorInfo()[2]
                );
            }
        }else {
            $json = array(
                'success' => false,
                'message' => "Utilisateur inconnu."
            );
        }
        return $json;
    }

    public function ajouter($code_ogd, $num_matricule, $email, $username, $firstname, $lastname, $direction, $service, $fonction, $num_telephone, $statut, $user){
        $mot_de_passe = strtoupper(substr(sha1(rand(0,10000).time()),0,8));
        $options = [
            'cost' => 11
        ];
        $password = password_hash($mot_de_passe, PASSWORD_BCRYPT, $options);
        $a = $this->bdd->prepare('INSERT INTO auxil_utilisateur(ogd_code, utilisateur_num_matricule, utilisateur_pseudo, utilisateur_mot_de_passe, utilisateur_mot_de_passe_statut, utilisateur_email, utilisateur_nom, utilisateur_prenom, utilisateur_direction, utilisateur_service, utilisateur_fonction, utilisateur_num_telephone, utilisateur_statut, user_reg) 
                    VALUES(:ogd_code, :utilisateur_num_matricule, :utilisateur_pseudo, :utilisateur_mot_de_passe, :utilisateur_mot_de_passe_statut, :utilisateur_email, :utilisateur_nom, :utilisateur_prenom, :utilisateur_direction, :utilisateur_service, :utilisateur_fonction, :utilisateur_num_telephone, :utilisateur_statut, :user_reg)');
        $a->execute(array(
            'ogd_code' => $code_ogd,
            'utilisateur_num_matricule' => $num_matricule,
            'utilisateur_pseudo' => $username,
            'utilisateur_mot_de_passe' => $password,
            'utilisateur_mot_de_passe_statut' => 0,
            'utilisateur_email' => $email,
            'utilisateur_nom' => $lastname,
            'utilisateur_prenom' => $firstname,
            'utilisateur_direction' => $direction,
            'utilisateur_service' => $service,
            'utilisateur_fonction' => $fonction,
            'utilisateur_num_telephone' => $num_telephone,
            'utilisateur_statut' => $statut,
            'user_reg' => $user
        ));
        if($a->errorCode() == '00000') {
            $json = array(
                'success' => true,
                'type' => 'creation',
                'id_user' => $this->bdd->lastInsertId(),
                'password' => $mot_de_passe
            );
        }else {
            $json = array(
                'success' => false,
                'message' => $a->errorInfo()[2]
            );
        }
        return $json;
    }

    public function mise_a_jour($id_user, $code_ogd, $num_matricule, $email, $username, $firstname, $lastname, $direction, $service, $fonction, $num_telephone, $statut, $user) {
        $a = $this->bdd->prepare("UPDATE auxil_utilisateur SET ogd_code = ?, utilisateur_num_matricule = ?, utilisateur_email = ?, utilisateur_pseudo = ?, utilisateur_prenom = ?, utilisateur_nom = ?, utilisateur_direction = ?, utilisateur_service = ?, utilisateur_fonction = ?, utilisateur_num_telephone = ?, utilisateur_statut = ?, date_edit = ?, user_edit = ? WHERE utilisateur_id = ?");
        $a->execute(array($code_ogd, $num_matricule, $email, $username, $firstname, $lastname, $direction, $service, $fonction, $num_telephone, $statut, date('Y-m-d H:i:s',time()), $user, $id_user));
        if($a->errorCode() == '00000') {
            $json = array(
                'success' => true,
                'type' => 'edition',
                'id_user' => $id_user
            );
        }else {
            $json = array(
                'success' => false,
                'message' => $a->errorInfo()[2]
            );
        }
        return $json;
    }

    public function editer($id_user, $code_ogd, $num_matricule, $email, $username, $firstname, $lastname, $direction, $service, $fonction, $num_telephone, $statut, $user) {
        if(!$id_user) {
            $trouver_email = $this->trouver(NULL,$email,NULL);
            if(!$trouver_email) {
                $trouver_pseudo = $this->trouver(NULL,NULL,$username);
                if(!$trouver_pseudo) {
                    $json = $this->ajouter($code_ogd, $num_matricule, $email, $username, $firstname, $lastname, $direction, $service, $fonction, $num_telephone, $statut, $user);
                }else {
                    $json = array(
                        'success' => false,
                        'message' => 'Ce nom utilisateur est déjà utilisé par un autre utilisateur. Prière en saisir un autre.'
                    );
                }
            }else {
                $json = array(
                    'success' => false,
                    'message' => 'Cette adresse email est déjà utilisée par un autre utilisateur. Prière en saisir une autre.'
                );
            }
        }
        else {
            $trouver_email = $this->trouver(NULL,$email,NULL);
            if(!$trouver_email) {
                $trouver_pseudo = $this->trouver(NULL,NULL,$username);
                if(!$trouver_pseudo){
                    $json = $this->mise_a_jour($id_user, $code_ogd, $num_matricule, $email, $username, $firstname, $lastname, $direction, $service, $fonction, $num_telephone, $statut, $user);
                }else {
                    if($trouver_pseudo['user_id'] == $id_user) {
                        $json = $this->mise_a_jour($id_user, $code_ogd, $num_matricule, $email, $username, $firstname, $lastname, $direction, $service, $fonction, $num_telephone, $statut, $user);
                    }else {
                        $json = array(
                            'success' => false,
                            'message' => 'Ce nom utilisateur est déjà utilisé par un autre utilisateur. Prière en saisir un autre.'
                        );
                    }
                }
            }else {
                if($trouver_email['user_id'] == $id_user) {
                    $trouver_pseudo = $this->trouver(NULL,NULL,$username);
                    if(!$trouver_pseudo){
                        $json = $this->mise_a_jour($id_user, $code_ogd, $num_matricule, $email, $username, $firstname, $lastname, $direction, $service, $fonction, $num_telephone, $statut, $user);
                    }else {
                        if($trouver_pseudo['user_id'] == $id_user) {
                            $json = $this->mise_a_jour($id_user, $code_ogd, $num_matricule, $email, $username, $firstname, $lastname, $direction, $service, $fonction, $num_telephone, $statut, $user);
                        }else {
                            $json = array(
                                'success' => false,
                                'message' => 'Ce nom utilisateur est déjà utilisé par un autre utilisateur. Prière en saisir un autre.'
                            );
                        }
                    }
                }else {
                    $json = array(
                        'success' => false,
                        'message' => 'Cette adresse email est déjà utilisée par un autre utilisateur. Prière en saisir une autre.'
                    );
                }
            }
        }
        return $json;
    }
}
?>