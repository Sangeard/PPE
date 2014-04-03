<?php

/**
 * Regroupe les fonctions d'accès aux données.
 * @package default
 * @author Arthur Martin
 * @todo Fonctions retournant plusieurs lignes sont à réécrire.
 */

/**
 * Se connecte au serveur de données MySql.                      
 * Se connecte au serveur de données MySql à partir de valeurs
 * prédéfinies de connexion (hôte, compte utilisateur et mot de passe). 
 * Retourne l'identifiant de connexion si succès obtenu, le booléen false 
 * si problème de connexion.
 * @return resource identifiant de connexion
 */
function connecterServeurBD() {
    $hote = "localhost";
    $login = "GSBfrais";
    $mdp = "examen3";
    return mysql_connect($hote, $login, $mdp);
}

/**
 * Sélectionne (rend active) la base de données.
 * Sélectionne (rend active) la BD prédéfinie gsb_frais sur la connexion
 * identifiée par $idCnx. Retourne true si succès, false sinon.
 * @param resource $idCnx identifiant de connexion
 * @return boolean succès ou échec de sélection BD 
 */
function activerBD($idCnx) {
    $bd = "gsb_frais";
    $query = "SET CHARACTER SET utf8";
    // Modification du jeu de caractères de la connexion
    $res = mysql_query($query, $idCnx);
    $ok = mysql_select_db($bd, $idCnx);
    return $ok;
}

/**
 * Ferme la connexion au serveur de données.
 * Ferme la connexion au serveur de données identifiée par l'identifiant de 
 * connexion $idCnx.
 * @param resource $idCnx identifiant de connexion
 * @return void  
 */
function deconnecterServeurBD($idCnx) {
    mysql_close($idCnx);
}

/**
 * Echappe les caractères spéciaux d'une chaîne.
 * Envoie la chaîne $str échappée, càd avec les caractères considérés spéciaux
 * par MySql (tq la quote simple) précédés d'un \, ce qui annule leur effet spécial
 * @param string $str chaîne à échapper
 * @return string chaîne échappée 
 */
function filtrerChainePourBD($str) {
    if (!get_magic_quotes_gpc()) {
        // si la directive de configuration magic_quotes_gpc est activée dans php.ini,
        // toute chaîne reçue par get, post ou cookie est déjà échappée 
        // par conséquent, il ne faut pas échapper la chaîne une seconde fois                              
        $str = mysql_real_escape_string($str);
    }
    return $str;
}

/**
 * Fournit les informations sur un utilisateur demandé. 
 * Retourne les informations du utilisateur d'id $unId sous la forme d'un tableau
 * associatif dont les clés sont les noms des colonnes(id, nom, prenom).
 * @param resource $idCnx identifiant de connexion
 * @param string $unId id de l'utilisateur
 * @return array  tableau associatif du utilisateur
 */
function obtenirDetailUtilisateur($idCnx, $unId) {
    $id = filtrerChainePourBD($unId);
    $requete = "select idUtilisateur, nom, prenom, idFonction from Utilisateur where idUtilisateur='" . $unId . "'";
    $idJeuRes = mysql_query($requete, $idCnx);
    $ligne = false;
    if ($idJeuRes) {
        $ligne = mysql_fetch_assoc($idJeuRes);
        mysql_free_result($idJeuRes);
    }
    return $ligne;
}

/**
 * Fournit les informations d'une fiche de frais. 
 * Retourne les informations de la fiche de frais du mois de $unMois (MMAAAA)
 * sous la forme d'un tableau associatif dont les clés sont les noms des colonnes
 * (nbJustitificatifs, idEtat, libelleEtat, dateModif, montantValide).
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demandé (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return array tableau associatif de la fiche de frais
 */
function obtenirDetailFicheFrais($idCnx, $unMois, $unIdVisiteur) {
    $unMois = filtrerChainePourBD($unMois);
    $ligne = false;
    $requete = "select IFNULL(nbJustificatifs,0) as nbJustificatifs, Etat.id as idEtat, libelle as libelleEtat, dateModif, montantValide 
    from FicheFrais inner join Etat on idEtat = Etat.id 
    where idVisiteur='" . $unIdVisiteur . "' and mois='" . $unMois . "'";
    $idJeuRes = mysql_query($requete, $idCnx);
    if ($idJeuRes) {
        $ligne = mysql_fetch_assoc($idJeuRes);
    }
    mysql_free_result($idJeuRes);

    return $ligne;
}

/**
 * Vérifie si une fiche de frais existe ou non. 
 * Retourne true si la fiche de frais du mois de $unMois (MMAAAA) du visiteur 
 * $idVisiteur existe, false sinon. 
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demandé (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return booléen existence ou non de la fiche de frais
 */
function existeFicheFrais($idCnx, $unMois, $unIdVisiteur) {
    $unMois = filtrerChainePourBD($unMois);
    $requete = "select idVisiteur from FicheFrais where idVisiteur='" . $unIdVisiteur .
            "' and mois='" . $unMois . "'";
    $idJeuRes = mysql_query($requete, $idCnx);
    $ligne = false;
    if ($idJeuRes) {
        $ligne = mysql_fetch_assoc($idJeuRes);
        mysql_free_result($idJeuRes);
    }

    // si $ligne est un tableau, la fiche de frais existe, sinon elle n'exsite pas
    return is_array($ligne);
}

/**
 * Fournit le mois de la dernière fiche de frais d'un visiteur.
 * Retourne le mois de la dernière fiche de frais du visiteur d'id $unIdVisiteur.
 * @param resource $idCnx identifiant de connexion
 * @param string $unIdVisiteur id visiteur  
 * @return string dernier mois sous la forme AAAAMM
 */
function obtenirDernierMoisSaisi($idCnx, $unIdVisiteur) {
    $requete = "select max(mois) as dernierMois from FicheFrais where idVisiteur='" .
            $unIdVisiteur . "'";
    $idJeuRes = mysql_query($requete, $idCnx);
    $dernierMois = false;
    if ($idJeuRes) {
        $ligne = mysql_fetch_assoc($idJeuRes);
        $dernierMois = $ligne["dernierMois"];
        mysql_free_result($idJeuRes);
    }
    return $dernierMois;
}

/**
 * Ajoute une nouvelle fiche de frais et les éléments forfaitisés associés, 
 * Ajoute la fiche de frais du mois de $unMois (MMAAAA) du visiteur 
 * $idVisiteur, avec les éléments forfaitisés associés dont la quantité initiale
 * est affectée à 0. Clôt éventuellement la fiche de frais précédente du visiteur. 
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demandé (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return void
 */
function ajouterFicheFrais($idCnx, $unMois, $unIdVisiteur) {
    $unMois = filtrerChainePourBD($unMois);
    // modification de la dernière fiche de frais du visiteur
    $dernierMois = obtenirDernierMoisSaisi($idCnx, $unIdVisiteur);
    $laDerniereFiche = obtenirDetailFicheFrais($idCnx, $dernierMois, $unIdVisiteur);
    if (is_array($laDerniereFiche) && $laDerniereFiche['idEtat'] == 'CR') {
        modifierEtatFicheFrais($idCnx, $dernierMois, $unIdVisiteur, 'CL');
    }

    // ajout de la fiche de frais à l'état Créé
    $requete = "insert into FicheFrais (idVisiteur, mois, nbJustificatifs, montantValide, idEtat, dateModif) values ('"
            . $unIdVisiteur
            . "','" . $unMois . "',0,NULL, 'CR', '" . date("Y-m-d") . "')";
    mysql_query($requete, $idCnx);

    // ajout des éléments forfaitisés
    $requete = "select id from FraisForfait";
    $idJeuRes = mysql_query($requete, $idCnx);
    if ($idJeuRes) {
        $ligne = mysql_fetch_assoc($idJeuRes);
        while (is_array($ligne)) {
            $idFraisForfait = $ligne["id"];
            // insertion d'une ligne frais forfait dans la base
            $requete = "insert into LigneFraisForfait (idVisiteur, mois, idFraisForfait, quantite)
                        values ('" . $unIdVisiteur . "','" . $unMois . "','" . $idFraisForfait . "',0)";
            mysql_query($requete, $idCnx);
            // passage au frais forfait suivant
            $ligne = mysql_fetch_assoc($idJeuRes);
        }
        mysql_free_result($idJeuRes);
    }
}

/**
 * Retourne le texte de la requête select concernant les mois pour lesquels un 
 * visiteur a une fiche de frais. 
 * 
 * La requête de sélection fournie permettra d'obtenir les mois (AAAAMM) pour 
 * lesquels le visiteur $unIdVisiteur a une fiche de frais. 
 * @param string $unIdVisiteur id visiteur  
 * @return string texte de la requête select
 */
function obtenirReqMoisFicheFrais($unIdVisiteur) {
    $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='"
            . $unIdVisiteur . "' order by fichefrais.mois desc ";
    return $req;
}

/**
 * Retourne le texte de la requête select concernant les éléments forfaitisés 
 * d'un visiteur pour un mois donnés. 
 * 
 * La requête de sélection fournie permettra d'obtenir l'id, le libellé et la
 * quantité des éléments forfaitisés de la fiche de frais du visiteur
 * d'id $idVisiteur pour le mois $mois    
 * @param string $unMois mois demandé (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return string texte de la requête select
 */
function obtenirReqEltsForfaitFicheFrais($unMois, $unIdVisiteur) {
    $unMois = filtrerChainePourBD($unMois);
    $requete = "select idFraisForfait, libelle, quantite from LigneFraisForfait
              inner join FraisForfait on FraisForfait.id = LigneFraisForfait.idFraisForfait
              where idVisiteur='" . $unIdVisiteur . "' and mois='" . $unMois . "'";
    return $requete;
}

/**
 * Retourne le texte de la requête select concernant les éléments hors forfait 
 * d'un visiteur pour un mois donnés. 
 * 
 * La requête de sélection fournie permettra d'obtenir l'id, la date, le libellé 
 * et le montant des éléments hors forfait de la fiche de frais du visiteur
 * d'id $idVisiteur pour le mois $mois    
 * @param string $unMois mois demandé (MMAAAA)
 * @param string $unIdVisiteur id visiteur  
 * @return string texte de la requête select
 */
function obtenirReqEltsHorsForfaitFicheFrais($unMois, $unIdVisiteur) {
    $unMois = filtrerChainePourBD($unMois);
    $requete = "select id, date, libelle, montant from LigneFraisHorsForfait
              where idVisiteur='" . $unIdVisiteur
            . "' and mois='" . $unMois . "'";
    return $requete;
}

/**
 * Supprime une ligne hors forfait.
 * Supprime dans la BD la ligne hors forfait d'id $unIdLigneHF
 * @param resource $idCnx identifiant de connexion
 * @param string $idLigneHF id de la ligne hors forfait
 * @return void
 */
function supprimerLigneHF($idCnx, $unIdLigneHF) {
    $requete = "delete from LigneFraisHorsForfait where id = " . $unIdLigneHF;
    mysql_query($requete, $idCnx);
}

/**
 * Ajoute une nouvelle ligne hors forfait.
 * Insère dans la BD la ligne hors forfait de libellé $unLibelleHF du montant 
 * $unMontantHF ayant eu lieu à la date $uneDateHF pour la fiche de frais du mois
 * $unMois du visiteur d'id $unIdVisiteur
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demandé (AAMMMM)
 * @param string $unIdVisiteur id du visiteur
 * @param string $uneDateHF date du frais hors forfait
 * @param string $unLibelleHF libellé du frais hors forfait 
 * @param double $unMontantHF montant du frais hors forfait
 * @return void
 */
function ajouterLigneHF($idCnx, $unMois, $unIdVisiteur, $uneDateHF, $unLibelleHF, $unMontantHF) {
    $unLibelleHF = filtrerChainePourBD($unLibelleHF);
    $uneDateHF = filtrerChainePourBD(convertirDateFrancaisVersAnglais($uneDateHF));
    $unMois = filtrerChainePourBD($unMois);
    $requete = "insert into LigneFraisHorsForfait(idVisiteur, mois, date, libelle, montant) 
                values ('" . $unIdVisiteur . "','" . $unMois . "','" . $uneDateHF . "','" . $unLibelleHF . "'," . $unMontantHF . ")";
    mysql_query($requete, $idCnx);
}

/**
 * Modifie les quantités des éléments forfaitisés d'une fiche de frais. 
 * Met à jour les éléments forfaitisés contenus  
 * dans $desEltsForfaits pour le visiteur $unIdVisiteur et
 * le mois $unMois dans la table LigneFraisForfait, après avoir filtré 
 * (annulé l'effet de certains caractères considérés comme spéciaux par 
 *  MySql) chaque donnée   
 * @param resource $idCnx identifiant de connexion
 * @param string $unMois mois demandé (MMAAAA) 
 * @param string $unIdVisiteur  id visiteur
 * @param array $desEltsForfait tableau des quantités des éléments hors forfait
 * avec pour clés les identifiants des frais forfaitisés 
 * @return void  
 */
function modifierEltsForfait($idCnx, $unMois, $unIdVisiteur, $desEltsForfait) {
    $unMois = filtrerChainePourBD($unMois);
    $unIdVisiteur = filtrerChainePourBD($unIdVisiteur);
    foreach ($desEltsForfait as $idFraisForfait => $quantite) {
        $requete = "update LigneFraisForfait set quantite = " . $quantite
                . " where idVisiteur = '" . $unIdVisiteur . "' and mois = '"
                . $unMois . "' and idFraisForfait='" . $idFraisForfait . "'";
        mysql_query($requete, $idCnx);
    }
}

/**
 * Contrôle les informations de connexionn d'un utilisateur.
 * V�rifie si les informations de connexion $unLogin, $unMdp sont ou non valides.
 * Retourne les informations de l'utilisateur sous forme de tableau associatif 
 * dont les clés sont les noms des colonnes (id, nom, prenom, login, mdp)
 * si login et mot de passe existent, le booléen false sinon. 
 * @param resource $idCnx identifiant de connexion
 * @param string $unLogin login 
 * @param string $unMdp mot de passe 
 * @return array tableau associatif ou booléen false 
 */
function verifierInfosConnexion($idCnx, $unLogin, $unMdp) {
    $unLogin = filtrerChainePourBD($unLogin);
    $unMdp = sha1(filtrerChainePourBD($unMdp));
    // le mot de passe est crypté dans la base avec la fonction de hachage md5
    $req = "select idUtilisateur, nom, prenom, login, mdp from Utilisateur where login='" . $unLogin . "' and mdp='" . $unMdp . "'";
    $idJeuRes = mysql_query($req, $idCnx);
    $ligne = false;
    if ($idJeuRes) {
        $ligne = mysql_fetch_assoc($idJeuRes);
        mysql_free_result($idJeuRes);
    }
    return $ligne;
}

/**
 * Modifie l'état et la date de modification d'une fiche de frais

 * Met à jour l'état de la fiche de frais du visiteur $unIdVisiteur pour
 * le mois $unMois à la nouvelle valeur $unEtat et passe la date de modif à 
 * la date d'aujourd'hui
 * @param resource $idCnx identifiant de connexion
 * @param string $unIdVisiteur 
 * @param string $unMois mois sous la forme aaaamm
 * @return void 
 */
function modifierEtatFicheFrais($idCnx, $unMois, $unIdVisiteur, $unEtat) {
    $requete = "update FicheFrais set idEtat = '" . $unEtat .
            "', dateModif = now() where idVisiteur ='" .
            $unIdVisiteur . "' and mois = '" . $unMois . "'";
    mysql_query($requete, $idCnx);
}

/**
 * Retourner le libellé de la fonction en fonction de l'idfonction passé en paramètre
 * @param resource $idCnx identifiant de connexion
 * @param int $idFonction identifiant de la fonction
 * @return array libellé de la fonction
 */
function libelleIdFonction($idCnx, $idFonction) {
    $req = "select libelle from Fonction where idFonction = '" . $idFonction . "'";
    $idJeuRes = mysql_query($req, $idCnx);
    $ligne = false;
    if ($idJeuRes) {
        $ligne = mysql_fetch_assoc($idJeuRes);
        mysql_free_result($idJeuRes);
    }
    return $ligne;
}

/**
 * Retourner la liste des id de tout les visiteurs
 * @return string texte de la requete select
 */
function obtenirReqListVisiteur() {
    $requete = "select distinct idUtilisateur, nom, prenom from Utilisateur INNER JOIN Visiteur ON Utilisateur.idUtilisateur = Visiteur.id INNER JOIN FicheFrais ON Visiteur.id = FicheFrais.idVisiteur where idFonction = 1 AND idEtat = 'CL'";
    return $requete;
}

/**
 * Retourne le texte de la requête select concernant les mois pour lesquels un 
 * visiteur a une fiche de frais a l'etat passer en parametre. 
 * 
 * La requête de sélection fournie permettra d'obtenir les mois (AAAAMM) pour 
 * lesquels le visiteur $unIdVisiteur a une fiche de frais et pour l'etat $unIdEtat.
 * @param string $unIdEtat id etat 
 * @param string $unIdVisiteur id visiteur  
 * @return string texte de la requête select
 */
function obtenirReqMoisFicheFraisEtat($unIdVisiteur, $unIdEtat) {
    $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='"
            . $unIdVisiteur . "' and fichefrais.idetat = '" . $unIdEtat . "' order by fichefrais.mois desc ";
    return $req;
}

/**
 * Modifie les quantités d'une ligne d'élément de frais hors forfait d'une fiche de frais. 
 * Met à jour les éléments de la ligne hors forfait pour une ligne $idLigne dans la table LigneFraisHorsForfait, après avoir filtré 
 * (annulé l'effet de certains caractères considérés comme spéciaux par 
 *  MySql) chaque donnée   
 * @param resource $idCnx identifiant de connexion
 * @param array $desEltsFraisHF tableau des quantités des éléments hors forfait
 * avec pour clés les identifiants des frais
 * @return void  
 */
function modifierLigneHorsForfait($idCnx, $desEltsFraisHF) {
    foreach ($desEltsFraisHF as $cle => $val) {
        switch ($cle) {
            case "libelle" :
                $libelleFraisHF = $val;
                break;
            case "montant" :
                $montantFraisHF = $val;
                break;
            case "date" :
                $dateFraisHF = $val;
                break;
            case "id" :
                $idLigneHF = $val;
                break;
        }
    }
    $req = "update LigneFraisHorsForfait set date = '" . filtrerChainePourBD(convertirDateFrancaisVersAnglais($dateFraisHF)) .
            "', libelle = '" . filtrerChainePourBD($libelleFraisHF) . "', montant = " . filtrerChainePourBD($montantFraisHF) . "
             where id = '" . filtrerChainePourBD($idLigneHF) . "'";
    mysql_query($req, $idCnx) or die("erreur dans la requete" . $req);
}

/* * Modifie le nombre de justificatif pour une fiche de frais
 * met à jour le nombre de justificatif dans la table FicheFrais
 * pour un visiteur idVisiteur et un moi idMois
 * @param resource $idCnx identifiant de connexion
 * @param string unIdVisiteur id visiteur
 * @param string unIdMois id mois
 * @param int nbJustif nombre de justificatif
 * @return void
 */

function modifierNbJustificatif($idCnx, $unIdVisiteur, $unIdMois, $nbJustif) {
    $unIdVisiteur = filtrerChainePourBD($unIdVisiteur);
    $unIdMois = filtrerChainePourBD($unIdMois);
    $nbJustif = filtrerChainePourBD($nbJustif);
    $req = "update FicheFrais set nbJustificatifs = " . $nbJustif .
            " where idVisiteur = '" . $unIdVisiteur . "' and mois = '" . $unIdMois . "'";
    mysql_query($req, $idCnx) or die("erreur dans la requete" . $req);
}

/* * Refuser une ligne hors forfait
 * mise a jour du libelle de la ligne hors forfait
 * en ajoutant "REFUSE" devant le texte du libelle 
 * dans la table LigneFraisHorsForfait
 * @param resource $idCnx identifiant de connexion
 * @param idLigneHF id de la ligne hors forfait
 * @param unLibelle libelle de la ligne hors forfait
 * @return void
 */

function refuserLigneHF($idCnx, $idLigneHF, $unLibelle) {
    $idLigneHF = filtrerChainePourBD($idLigneHF);
    $unLibelle = filtrerChainePourBD($unLibelle);
    $req = "update LigneFraisHorsForfait set libelle = '[REFUSER] " . $unLibelle .
            "' where id = '" . $idLigneHF . "'";
    mysql_query($req, $idCnx) or die("Erreur dans la requete" . $req);
}

/* * Reintegrer une ligne hors forfait
 * mise a jour du libelle de la ligne hors forfait
 * en enlevant "REFUSE" devant le texte du libelle 
 * dans la table LigneFraisHorsForfait
 * @param resource $idCnx identifiant de connexion
 * @param idLigneHF id de la ligne hors forfait
 * @param unLibelle libelle de la ligne hors forfait
 * @return void
 */

function reintegrerLigneHF($idCnx, $idLigneHF, $unLibelle) {
    $idLigneHF = filtrerChainePourBD($idLigneHF);
    $unLibelle = filtrerChainePourBD($unLibelle);
    $unLibelle = substr($unLibelle, 9);
    $req = "update LigneFraisHorsForfait set libelle = '" . $unLibelle .
            "' where id = '" . $idLigneHF . "'";
    mysql_query($req, $idCnx) or die("Erreur dans la requete" . $req);
}

/* * Reporter une ligne hors forfait
 * appel de le procedure stocke reporterLigneFraisHF
 * vérifie si la fiche de mois suivant existe
 * si non elle la créer. Ajoute la ligne HF a la fiche du mois suivant
 * supprimer la ligne HF du mois courant
 * @param resource $idCnx identifiant de connexion
 * @param string idLigneHF id de la ligne hors forfait
 * @return void
 */

function reporterLigneHF($idCnx, $idLigneHF) {
    mysql_query("CALL reporterLigneFraisHF(" . $idLigneHF . ");", $idCnx);
}

/** cloturer les fiches de frais du mois précédent 
 * et les passer à l'état 'cl'
 * @param resource $idCnx identifiant de connexion
 * @param string $unIdMois
 * @return void 
 */
function cloturerFichesFrais($idCnx, $unIdMois) {
    $req = "SELECT idVisiteur, mois FROM ficheFrais where idEtat = 'CR' and CAST(mois AS unsigned) <  $unIdMois ;";
    $idJeuFicheFrais = mysql_query($req, $idCnx);
    while ($lgFicheFrais = mysql_fetch_array($idJeuFicheFrais)) {
        modifierEtatFicheFrais($idCnx, $lgFicheFrais['mois'], $lgFicheFrais['idVisiteur'], "CL");
        //vérification si la fiche de frais suivante existe
        $existeFicheFrais = existeFicheFrais($idCnx, $unIdMois, $lgFicheFrais['idVisiteur']);
        if (!$existeFicheFrais) {
            ajouterFicheFrais($idCnx, $unIdMois, $lgFicheFrais['idVisiteur']);
        }
    }
}

/**
 * Retourne le texte de la requête select concernant les éléments d'une fiche de frais 
 * d'un visiteur pour un mois donnés. 
 * 
 * La requête de sélection fournie permettra d'obtenir l'id, le prenom, le nom, le mois
 * le total des frais forfaitisés, le total des frais hors forfait, le total des frais
 * et l'etat de la fiche de frais du visiteur 
 * d'id $idVisiteur pour le mois $mois     
 * @return string texte de la requête select
 */
function obtenirReqEltsFicheFrais() {
    $requete = "select Utilisateur.idUtilisateur,nom, prenom, FicheFrais.mois,
        SUM(LigneFraisForfait.quantite * Bareme.montant) AS montantForfait,
        (FicheFrais.montantValide - SUM(LigneFraisForfait.quantite * Bareme.montant)) AS montantHorsForfait,
        FicheFrais.montantValide
        FROM Utilisateur INNER JOIN Visiteur ON Utilisateur.idUtilisateur = Visiteur.id
                         INNER JOIN FicheFrais ON Visiteur.id = FicheFrais.idVisiteur
                         INNER JOIN LigneFraisForfait ON FicheFrais.idVisiteur = LigneFraisForfait.idVisiteur AND FicheFrais.mois = LigneFraisForfait.mois
                         INNER JOIN FraisForfait ON LigneFraisForfait.idFraisForfait = FraisForfait.id
                         INNER JOIN Bareme ON Bareme.idFraisForfait = FraisForfait.id
        WHERE FicheFrais.idEtat = 'V'
        AND (Bareme.idTypeVehicule IS NULL OR Bareme.idTypeVehicule = Visiteur.idTypeVehicule)
        GROUP BY nom, prenom, FicheFrais.mois";
    return $requete;
}

/**
 * Fournit les informations sur le type de vehicule du visiteur. 
 * @param resource $idCnx identifiant de connexion
 * @param string $unId id de l'utilisateur
 * @return array type vehicule du visiteur
 */
function obtenirTypeVehiculeVisiteur($idCnx, $id) {
    $req = "SELECT idTypeVehicule
            FROM Visiteur
            WHERE Visiteur.id = '" . $id . "'";
    $idJeuRes = mysql_query($req, $idCnx);
    $typeVehicule = false;
    if ($idJeuRes) {
        $ligne = mysql_fetch_assoc($idJeuRes);
        $typeVehicule = $ligne["idTypeVehicule"];
        mysql_free_result($idJeuRes);
    }
    return $typeVehicule;
}

/**
 * Retourner la liste des types de vehicule
 * @return string texte de la requete select
 */
function obtenirReqListTypeVehicule() {
    $requete = "SELECT libelle, idTypeVehicule 
            FROM TypeVehicule";
    return $requete;
}

/** modifier le type de vehicule pour le visiteur
 * @param resource $idCnx identifiant de connexion
 * @param $idVisiteur id visiteur
 * @param $idTypeVehicule
 * @return void
 */
function modifierTypeVehicule($idCnx, $idVisiteur, $idTypeVehicule) {
    $idVisiteur = filtrerChainePourBD($idVisiteur);
    $idTypeVehicule = filtrerChainePourBD($idTypeVehicule);
    $req = "UPDATE Visiteur SET idTypeVehicule = '" . $idTypeVehicule . "' WHERE id = '" . $idVisiteur . "'";
    mysql_query($req, $idCnx) or die("erreur dans la requete" . $req);
}

?>