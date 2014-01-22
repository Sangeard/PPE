<?php

/** 
 * Script de contrôle et d'affichage du cas d'utilisation "Valider fiche de frais"
 * @package default
 * @todo  RAS
 */

$repInclude = './include/';
  require($repInclude . "_init.inc.php");

  // page inaccessible si visiteur non connecté
  if ( ! estVisiteurConnecte() ) {
      header("Location: cSeConnecter.php");  
  }
  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php");
  
  //recupération des variables de session
 $visiteurSaisi=lireDonnee("lstVisiteur", "");
 $moisSaisi=lireDonnee("lstMois", "");
?>

<!--Division principale-->
<div id="contenu">
    <h2>Les fiches de frais à valider</h2>
    <h3>Visiteur et mois à selectionner</h3>
    <form id="formChoixVisiteur" action="" method="post">  
            <!-- choix du visiteur-->
            <label>Visiteur :</label>
            <select id="idLstVisiteur" name="lstVisiteur" title="Selectionner un visiteur" onChange="obtenirMoisFonctionVisiteur(this.options[this.selectedIndex].value)">
                <?php
                    $req = obtenirReqListVisiteur();
                    $idJeuVisiteur = mysql_query($req, $idConnexion);
                    $lgVisiteur = mysql_fetch_assoc($idJeuVisiteur);                   
                    while (is_array($lgVisiteur)){
                        $nomVisiteur = $lgVisiteur['nom'];
                        $prenomVisiteur = $lgVisiteur['prenom'];
                        $idVisiteur = $lgVisiteur['idUtilisateur'] ;
                 ?>
                <option value="<?php echo $idVisiteur; ?>"<?php if($visiteurSaisi==$idVisiteur){?>selected="selected"<?php } ?>><?php echo $nomVisiteur." ".$prenomVisiteur; ?></option>   
                 <?php
                        $lgVisiteur = mysql_fetch_assoc($idJeuVisiteur);
                    }
                    mysql_free_result($idJeuVisiteur);
                 ?>
            </select>
    </form>
    <?php 
     if($visiteurSaisi != ""){
    ?>      
 
     <form id="formChoixMois" action="" method="post">
         <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurSaisi; ?>" />
        
         <label>Mois :</label>
         <select id="lstMois" name="lstMois" title="Sélectionnez le mois souhaité pour la fiche de frais"
                 onchange="this.form.submit()">
            <?php
                // on propose tous les mois pour lesquels le visiteur a une fiche de frais
                $req = obtenirReqMoisFicheFrais($visiteurSaisi);
                $idJeuMois = mysql_query($req, $idConnexion);
                $lgMois = mysql_fetch_assoc($idJeuMois);
                while ( is_array($lgMois) ) {
                    $mois = $lgMois["mois"];
                    $noMois = intval(substr($mois, 4, 2));
                    $annee = intval(substr($mois, 0, 4));
            ?>    
            <option value="<?php echo $mois; ?>"<?php if ($moisSaisi == $mois) { ?> selected="selected"<?php } ?>><?php echo obtenirLibelleMois($noMois) . " " . $annee; ?></option>
            <?php
                    $lgMois = mysql_fetch_assoc($idJeuMois);        
                }
                mysql_free_result($idJeuMois);
            ?>
        </select>    
     </form>
    <?php
     }
    ?>
    
    <!--affichage des message de confirmation ou d'erreur-->
    <form action="" method="post">
        <label name="txtErreur" value=""></label>
    </form>
<?php 
 if($visiteurSaisi != "" && $moisSaisi != ""){
?>
    <!--modification des forfaits-->
    <div class="corpsForm">
    <form action="" method="post">
        <input type="hidden" name="etape" value="modifFraisForfait" />
        
        <!--affichage forfait-->
        <fieldset>
            <legend>Elements forfaitisé</legend>
               
            <?php
             
              //recupération des données
              $req = obtenirReqEltsForfaitFicheFrais($moisSaisi, $visiteurSaisi);
              $idJeuEltsFraisForfait = mysql_query($req, $idConnexion);
              $lgEltForfait = mysql_fetch_assoc($idJeuEltsFraisForfait);
              while(is_array($lgEltForfait)){
                  switch ($lgEltForfait['idFraisForfait']){
                      case "ETP" :
                        $etpLibelle = $lgEltForfait['libelle'];
                          $etpQuantite = $lgEltForfait['quantite'];
                        break;
                    case "KM":
                        $kmLibelle = $lgEltForfait['libelle'];
                        $kmQuantite = $lgEltForfait['quantite'];
                        break;
                      case "NUI" :
                          $nuiLibelle = $lgEltForfait['libelle'];
                          $nuiQuantite = $lgEltForfait['quantite'];
                          break;
                      case "REP" : 
                          $repLibelle = $lgEltForfait['libelle'];
                          $repQuantite = $lgEltForfait['quantite'];
                          break;
                  }
              $lgEltForfait = mysql_fetch_assoc($idJeuEltsFraisForfait);
            }
            mysql_free_result($idJeuEltsFraisForfait);
           ?>  
            <table>
                <tr> 
                    <th><?php echo $etpLibelle; ?></th><th><?php echo $kmLibelle; ?></th>
                    <th><?php echo $nuiLibelle; ?></th><th><?php echo $repLibelle; ?></th><th>Action</th>
                </tr>
                <tr>
                    <td><input type="text" id="idETP" name="txtEltsFraisForfait[ETP]"
                   size="10" maxlength="5"
                   title="Modifier la quantité de l'element forfaitisé"
                   value="<?php echo $etpQuantite; ?>" />
                    </td>
                    <td><input type="text" id="idKM" name="txtEltsFraisForfait[KM]"
                   size="10" maxlength="5"
                   title="Modifier la quantité de l'element forfaitisé"
                   value="<?php echo $kmQuantite; ?>" />
                    </td>
                    <td><input type="text" id="idNUI" name="txtEltsFraisForfait[NUI]"
                   size="10" maxlength="5"
                   title="Modifier la quantité de l'element forfaitisé"
                   value="<?php echo $nuiQuantite; ?>" />
                    </td> 
                    <td><input type="text" id="idREP" name="txtEltsFraisForfait[REP]"
                   size="10" maxlength="5"
                   title="Modifier la quantité de l'element forfaitisé"
                   value="<?php echo $repQuantite; ?>" />
                    </td> 
                    <td>
                        <a id="actualiser" title="Actualiser les frais forfaits">Actualiser</a>
                        <a id="réinitialiser" title="Reinitialiser les valeurs de départ">Reinitialiser</a>
                    </td>
              </tr>
            </table>
        </fieldset>    
    </form>
    </div>    
    <!--modification des frais hors forfaits-->
    <div id="divModifHF">
        <input type="hidden" name="etape" value="modifFraisHF" />
        <?php
        $req = obtenirReqEltsHorsForfaitFicheFrais($moisSaisi, $visiteurSaisi);
        $idJeuEltsHorsForfait = mysql_query($req, $idConnexion);
        $lgEltHorsForfait = mysql_fetch_assoc($idJeuEltsHorsForfait);
        while(is_array($lgEltHorsForfait)){
            $idHF = $lgEltHorsForfait['id'];
            $dateHF = $lgEltHorsForfait['date'];
            $libelleHF = $lgEltHorsForfait['libelle'];
            $montantHF = $lgEltHorsForfait['montant'];
            $lgEltHorsForfait = mysql_fetch_assoc($idJeuEltsHorsForfait);
            
        ?>
        <form action="" method="post">
        <table>
            <tr>
                <th>Date</th><th>Libelle</th><th>Montant</th><th>Actions</th>
            </tr>
            <tr>
                <td>
                    <input type="text" name="txtDateHF[<?php echo $idHF; ?>]" size="10" value="<?php echo $dateHF; ?>" />
                </td>
                <td>
                    <input type="text" name="txtLibelleHF[<?php echo $idHF; ?>]" size="50" value="<?php echo $libelleHF; ?>" />
                </td>
                <td>
                    <input type="text" name="txtMontantHF[<?php echo $idHF; ?>]" size="10" value="<?php echo $montantHF; ?>" />
                </td>
                <td>
                    <a id="reporter<?php echo $idHF; ?>" title="reporter frais hors forfait">Reporter</a>
                    <a id="supprimer<?php echo $idHF; ?>" title="supprimer frais hors forfait">Supprimer</a>                    
                </td>
            
            </tr>
        </table> 
        </form> 
        <?php
        }
        mysql_free_result($idJeuEltsHorsForfait);
        ?>
     </div>


                                
             
          

    
    <!--modification des frais hors categorie-->
    <form action="" method="post">
        <input type="hidden" name="etape" value="modifFraisHC"/>
        <?php
        $tabFicheFrais = obtenirDetailFicheFrais($idConnexion, $moisSaisi, $visiteurSaisi);
        $nbJustificatifs = $tabFicheFrais['nbJustificatifs'];
        ?>
        <label for="txtJustificatifs">Nombre de justificatifs :</label>
        <input type="text" name="txtJustificatifs" id="txtJustificatifs" size="5" 
               title="nombre de justificatifs" value="<?php echo $nbJustificatifs; ?>" />
        <a id="modifNbJustif" title="validation de la modification du nombre de justificatifs">Actualiser</a>
    </form>
    
    <!--validation de la fiche de frais-->
    <form action="" method="post">
        <input type="hidden" name="etape" value="validationFicheFrais"/>
        <input type="submit" id="valider" value="Valider" size="20"/>
        <input type="reset" id="abandonner" value="Annuler" size="20"/>
    </form>  

<?php
 }
 ?>
    
</div>
<script type="text/javascript">
<?php
require($repInclude . "_fonctionValidationFichesFrais.inc.js");
?>
</script>
<?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?>                            