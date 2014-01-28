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
 $etape = lireDonnee("etape", "");
 $tabEltsFraisForfait = lireDonneePost("txtEltsFraisForfait", "");
 $tabEltsHorsForfait = lireDonneePost("txtEltsHorsForfait","");
 $nbJustificatif = lireDonneePost("txtJustificatifs", "");
  
 //récupération du mois précédent 
 $mois = sprintf("%04d%02d", date('Y'), date('m'));
 //cloture des fiches de frais
 cloturerFichesFrais($idConnexion, $mois);
 
 //script controle cas utilisation
 
 if($etape=="choixVisiteur"){
     //le visiteur est choisi
 }elseif($etape=="choixMois"){
     //le mois est choisi
 }elseif ($etape=="actualiserFraisForfait") {
     //vérifier si nb entier positif
     $ok = verifierEntiersPositifs($tabEltsFraisForfait);
     if(!$ok){
         ajouterErreur($tabErreurs, "Chaque quantité doit être renseignée et numérique positive");
     }else{
         modifierEltsForfait($idConnexion, $moisSaisi, $visiteurSaisi, $tabEltsFraisForfait);
     }
 }elseif ($etape == "actualiserLigneHF") {
     //utilisateur réalise des modifications du une ligne hors forfait
     // utilisateur valide ses modifications
     // recherche des éléements modifier
     // puis mise à jours

     foreach ($tabEltsHorsForfait as $cle => $val) {
         switch ($cle) {
             case 'libelle' :
                 $libelleFraisHF = $val;
                 break;  
             case 'date' :
                 $dateFraisHF = $val;
                 break;
             case 'montant' :
                 $montantFraisHF = $val;
                 break;
         }   
     }

     verifierLigneFraisHF($dateFraisHF, $libelleFraisHF, $montantFraisHF, $tabErreurs);
     if(nbErreurs($tabErreurs) == 0 ){
         //modification de la ligne hors forfait
         modifierLigneHorsForfait($idConnexion, $tabEltsHorsForfait);
     }
}elseif ($etape == "refuserLigneHF") {
    //recherche de id et libelle
     foreach ($tabEltsHorsForfait as $cle => $val) {
         switch ($cle) {
             case 'libelle' :
                 $libelleFraisHF = $val;
                 break;  
             case 'id' :
                 $idFraisHF = $val;
                 break;
         }   
     }    
    refuserLigneHF($idConnexion, $idFraisHF, $libelleFraisHF);
}elseif ($etape == "reintegrerLigneHF") {
     //recherche de id et libelle
     foreach ($tabEltsHorsForfait as $cle => $val) {
         switch ($cle) {
             case 'libelle' :
                 $libelleFraisHF = $val;
                 break;  
             case 'id' :
                 $idFraisHF = $val;
                 break;
         }   
     }
     reintegrerLigneHF($idConnexion, $idFraisHF, $libelleFraisHF) ;
}elseif ($etape == "reporterLigneHF") { 
     foreach ($tabEltsHorsForfait as $cle => $val) {
         if($cle == "id") {
             $idFraisHF = $val ;
         }
     }
     reporterLigneHF($idConnexion, $idFraisHF);
     
}elseif ($etape == "modifFraisHC") {
     $ok = estEntierPositif($nbJustificatif);
     if(!$ok){
         ajouterErreur($tabErreurs, "Le nombre de justificatifs doit être renseignée et numérique positive");
     }else{
         modifierNbJustificatif($idConnexion, $visiteurSaisi, $moisSaisi, $nbJustificatif);
     }      
   
}elseif ($etape == "validationFicheFrais") {
    modifierEtatFicheFrais($idConnexion, $moisSaisi, $visiteurSaisi, 'VA');
}
?>

<!--Division principale-->
<div id="contenu">
    <h2>Les fiches de frais à valider</h2>
    
  <!--Affichage message confirmation  ou erreur-->
  <?php
  if($etape == "actualiserFraisForfait"){
      if(nbErreurs($tabErreurs)> 0){
          echo toStringErreurs($tabErreurs);
      }else{
          ?>
           <p class="info">La modification des frais forfaitisés à bien été prise en compte</p>
          <?php 
      }
  }
  ?>
           
  <?php
  if($etape == "actualiserLigneHF"){
      if(nbErreurs($tabErreurs)> 0){
          echo toStringErreurs($tabErreurs);
      }else{
          ?>
           <p class="info">La modification des frais hors forfait à bien été prise en compte</p>
          <?php 
      }
  }
  ?> 
     
 <?php
  if($etape == "refuserLigneHF"){
     ?>
      <p class="info">Le refus de la ligne hors forfait à bien été prise en compte</p>
      <?php 
  }
  ?>   
      
  <?php
  if($etape == "reintegrerLigneHF"){
     ?>
      <p class="info">La réintégration de la ligne hors forfait à bien été prise en compte</p>
      <?php 
  }
  ?> 
      
   <?php
  if($etape == "reporterLigneHF"){
     ?>
      <p class="info">Le report de la ligne hors forfait à bien été prise en compte</p>
      <?php 
  }
  ?>      
           
  <?php
  if($etape == "modifFraisHC"){
      if(nbErreurs($tabErreurs)> 0){
          echo toStringErreurs($tabErreurs);
      }else{
          ?>
           <p class="info">La modification du nombre de justificatifs à bien été prise en compte</p>
          <?php 
      }
  }
  ?>  
           
    <?php
  if($etape == "validationFicheFrais"){
      $lgFicheFrais = obtenirDetailVisiteur($idConnexion, $visiteurSaisi);
     ?>
           <p class="info">La fiche de frais de <?php echo $lgFicheFrais['prenom'] ." ". $lgFicheFrais['nom']; ?> pour le mois de <?php echo obtenirLibelleMois(intval(substr($moisSaisi, 4, 2))) . " " . intval(substr($moisSaisi, 0, 4)); ?> à bien été validé et mise en paiement</p>
      <?php 
      $moisSaisi ="";
  }
  ?>           
    
    <h3>Visiteur et mois à selectionner</h3>
    <form id="formChoixVisiteur" action="" method="post">  
            <!-- choix du visiteur-->
            <input type="hidden" name="etape" value="choixVisiteur"/> 
            <label>Visiteur :</label>
            <select id="idLstVisiteur" name="lstVisiteur" title="Selectionner un visiteur" onChange="obtenirMoisFonctionVisiteur(this.options[this.selectedIndex].value)">
                    <option value="choixvisiteur">---Choix visiteur---</option>  
                  <?php
                    $req = obtenirReqListVisiteur();
                    echo $req ;
                    $idJeuVisiteur = mysql_query($req, $idConnexion);
                    $lgVisiteur = mysql_fetch_assoc($idJeuVisiteur);                   
                    while (is_array($lgVisiteur)){
                        $nomVisiteur = filtrerChainePourNavig($lgVisiteur['nom']);
                        $prenomVisiteur = filtrerChainePourNavig($lgVisiteur['prenom']);
                        $idVisiteur = filtrerChainePourNavig($lgVisiteur['idUtilisateur']) ;
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
         <input type="hidden" name="etape" value="choixMois"/>
        
         <label>Mois :</label>
         <select id="lstMois" name="lstMois" title="Sélectionnez le mois souhaité pour la fiche de frais"
                 onchange="this.form.submit()">  
             <option value="choixMois">---Choix mois---</option>
            <?php
                // on propose tous les mois pour lesquels le visiteur a une fiche de frais
                $req = obtenirReqMoisFicheFraisEtat($visiteurSaisi, "CL");
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
    

<?php 
 if($visiteurSaisi != "" && $moisSaisi != ""){
?>
    <!--modification des forfaits-->
    <div class="fondTableau">
    <form id="formValidationFraisForfait" action="" method="post">
        <input type="hidden" name="etape" value="actualiserFraisForfait" />
         <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurSaisi; ?>" />
         <input type="hidden" name="lstMois" value="<?php echo $moisSaisi; ?>" />
        
        <!--affichage forfait-->   
        <h4> Frais forfaités </h4>
            <?php
             
              //recupération des données
              $req = obtenirReqEltsForfaitFicheFrais($moisSaisi, $visiteurSaisi);
              $idJeuEltsFraisForfait = mysql_query($req, $idConnexion);
              $lgEltForfait = mysql_fetch_assoc($idJeuEltsFraisForfait);
              while(is_array($lgEltForfait)){
                  switch ($lgEltForfait['idFraisForfait']){
                      case "ETP" :
                        $etpLibelle = filtrerChainePourNavig($lgEltForfait['libelle']);
                          $etpQuantite = $lgEltForfait['quantite'];
                        break;
                    case "KM":
                        $kmLibelle = filtrerChainePourNavig($lgEltForfait['libelle']);
                        $kmQuantite = $lgEltForfait['quantite'];
                        break;
                      case "NUI" :
                          $nuiLibelle = filtrerChainePourNavig($lgEltForfait['libelle']);
                          $nuiQuantite = $lgEltForfait['quantite'];
                          break;
                      case "REP" : 
                          $repLibelle = filtrerChainePourNavig($lgEltForfait['libelle']);
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
                   size="15" maxlength="5"
                   title="Modifier la quantité de l'element forfaitisé" onchange="msgModificationFraisForfait();"
                   value="<?php echo $etpQuantite; ?>" />
                    </td>
                    <td><input type="text" id="idKM" name="txtEltsFraisForfait[KM]"
                   size="18" maxlength="5"
                   title="Modifier la quantité de l'element forfaitisé" onchange="msgModificationFraisForfait();"
                   value="<?php echo $kmQuantite; ?>" />
                    </td>
                    <td><input type="text" id="idNUI" name="txtEltsFraisForfait[NUI]"
                   size="15" maxlength="5"
                   title="Modifier la quantité de l'element forfaitisé" onchange="msgModificationFraisForfait();"
                   value="<?php echo $nuiQuantite; ?>" />
                    </td> 
                    <td><input type="text" id="idREP" name="txtEltsFraisForfait[REP]"
                   size="15" maxlength="5"
                   title="Modifier la quantité de l'element forfaitisé" onchange="msgModificationFraisForfait();"
                   value="<?php echo $repQuantite; ?>" />
                    </td> 
                    <td>
                        <div id="divActionFraisForfait">
                            <a id="lkActualiserFraisForfait" onclick="actualiserFraisForfait(<?php echo $etpQuantite.", ".$kmQuantite.", ".$nuiQuantite.", ".$repQuantite; ?>)" title="Actualiser les frais forfaits">Actualiser</a>
                        <a id="lkReinitialiserFraisForfait" onclick="reinitialiserFraisForfait()" title="Reinitialiser les valeurs de départ">Reinitialiser</a>
                        </div>
                    </td>
              </tr>
            </table>   
    </form>
    </div> 
    <div id="divMsgModifFraisForfait" class="infosNonActualisees">
        <p>Attention, les modifications doivent être actualisées pour vraiment être prise en compte</p>
    </div>
    <!--modification des frais hors forfaits-->
    <div id="divModifHF" class="fondTableau">
        <h4> Frais hors forfait </h4> 
        <?php
        $req = obtenirReqEltsHorsForfaitFicheFrais($moisSaisi, $visiteurSaisi);
        $idJeuEltsHorsForfait = mysql_query($req, $idConnexion);
        $lgEltHorsForfait = mysql_fetch_assoc($idJeuEltsHorsForfait);
        while(is_array($lgEltHorsForfait)){
            $idHF = filtrerChainePourNavig($lgEltHorsForfait['id']);
            $dateHF = convertirDateAnglaisVersFrancais($lgEltHorsForfait['date']);
            $libelleHF = filtrerChainePourNavig($lgEltHorsForfait['libelle']);
            $montantHF = $lgEltHorsForfait['montant'];
            $lgEltHorsForfait = mysql_fetch_assoc($idJeuEltsHorsForfait);
            
        ?>
        <form id="formLigneHF<?php echo $idHF; ?>" action="" method="post">
            <input type="hidden" id="etape<?php echo $idHF; ?>" name="etape" value="actualiserLigneHF" />
       <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurSaisi; ?>" />
         <input type="hidden" name="lstMois" value="<?php echo $moisSaisi; ?>" />
         <input type="hidden" name="txtEltsHorsForfait[id]" value="<?php echo $idHF; ?>" />

            <table>
            <tr>
                <th>Date</th><th>Libelle</th><th>Montant</th><th>Actions</th>
            </tr>
            <?php if (strpos($libelleHF, "REFUSER")== FALSE){
            ?>    
            <tr>
            <?php 
            }else{
            ?>    
            <tr style="background-color: #DDDDDD;">   
            <?php    
            }    
            ?>
                <td>
                    <input type="text" id="dateHF<?php echo $idHF; ?>" name="txtEltsHorsForfait[date]" size="10" onchange="msgModificationFraisHorsForfait(<?php echo $idHF; ?>);" value="<?php echo $dateHF; ?>" />
                </td>
                <td>
                    <input type="text" id="libelleHF<?php echo $idHF; ?>" name="txtEltsHorsForfait[libelle]" size="50" onchange="msgModificationFraisHorsForfait(<?php echo $idHF; ?>);" value="<?php echo $libelleHF; ?>" />
                </td>
                <td>
                    <input type="text" id="montantHF<?php echo $idHF; ?>" name="txtEltsHorsForfait[montant]" size="10" onchange="msgModificationFraisHorsForfait(<?php echo $idHF; ?>);" value="<?php echo $montantHF; ?>" />
                </td>
                <td>
                    
                    <a id="lkActualiserFraisHF<?php echo $idHF; ?>" onclick="actualiserFraisHF(<?php echo $idHF.", '".$dateHF."', '".$libelleHF."', ".$montantHF; ?>)" title="actualiser frais hors forfait">Actualiser</a>
                    <a id="lkReinitialiserHF<?php echo $idHF; ?>" onclick="reinitialiserFraisHF(<?php echo $idHF; ?>)" title="reinitialiser frais hors forfait">Reinitialiser</a>    
                <?php 
                if (strpos($libelleHF, "REFUSER")== FALSE){
                ?>    
                    <a id="reporter<?php echo $idHF; ?>" onclick="reporterFraisHF(<?php echo $idHF; ?>)"  title="reporter frais hors forfait">Reporter</a>
                    <a id="refuser<?php echo $idHF; ?>" onclick="refuserLigneHF(<?php echo $idHF; ?>)" title="refuser frais hors forfait">Refuser</a>                    
            <?php 
            }else{
            ?>    
                  <a id="reintegrer<?php echo $idHF; ?>" onclick="reintegrerLigneHF(<?php echo $idHF; ?>)" title="reintegrer frais hors forfait">Reintegrer</a>                    
            <?php    
            }
            ?>
                    
                 </td>
            
            </tr>
        </table> 
        </form>  
        <div id="divMsgFraisHorsForfait<?php echo $idHF; ?>" class="infosNonActualisees">
            <p>Attention, les modifications doivent être actualisées pour être vraiment prise en compte</p>
        </div>
        <?php
        }
        mysql_free_result($idJeuEltsHorsForfait);
        ?>
        
     </div>

    <!--modification des frais hors categorie-->
    <div>  
    <form id="formFraisHC" action="" method="post">
        <input type="hidden" name="etape" value="modifFraisHC"/>
        <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurSaisi; ?>" />
         <input type="hidden" name="lstMois" value="<?php echo $moisSaisi; ?>" />
       
        <?php
        $tabFicheFrais = obtenirDetailFicheFrais($idConnexion, $moisSaisi, $visiteurSaisi);
        $nbJustificatifs = $tabFicheFrais['nbJustificatifs'];
        ?>
        <label for="txtJustificatifs">Nombre de justificatifs :</label>
        <input type="text" name="txtJustificatifs" id="txtJustificatifs" size="5" 
               title="nombre de justificatifs" onchange="msgModificationNbJusitificatif();" value="<?php echo $nbJustificatifs; ?>" />
        <a id="lkActualisationNbJustif" onclick="actualiserNbJustif(<?php echo $nbJustificatifs; ?>)" title="validation de la modification du nombre de justificatifs">Actualiser</a>
        <a id="lkReinitialiserNbJustif" onclick="reinitialiserNbJustir()" title="réinitialiser le nombre de justificatifs">Réinitialiser</a>    
    </form>
    <div id="divMsgNbJustificatif" class="infosNonActualisees">
       <p>Attention, les modifications doivent être actualisées pour être vraiment prise en compte</p>
    </div>
    </div>     
    
    <!--validation de la fiche de frais-->
    <form id="formValideFiche" action="" method="post">
        <input type="hidden" name="etape" value="validationFicheFrais"/>
        <input type="hidden" name="lstVisiteur" value="<?php echo $visiteurSaisi; ?>" />
        <input type="hidden" name="lstMois" value="<?php echo $moisSaisi; ?>" />        
        <input type="button" onclick="validerFicheFrais()" id="valider" value="Valider" size="20"/>
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