<?php

/** 
 * Script de contrôle et d'affichage du cas d'utilisation "Suivre le paiement fiche de frais"
 * @package default
 * @todo  RAS
 */

$repInclude = './include/';
  require($repInclude . "_init.inc.php");

  // page inaccessible si utilisateur non connecté
  if ( ! estUtilisateurConnecte() ) {
      header("Location: cSeConnecter.php");  
  }
  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php");
  
  //récupération des données
  $idVisiteur = lireDonnee("lstVisiteur", "");
  $idMois = lireDonnee("lstMois", "");
  $etape = lireDonnee("etape", "");
  
  //gestion cas d'utilisation
  if($etape == "miseEnPaiement"){
      modifierEtatFicheFrais($idConnexion, $idMois, $idVisiteur, "MP");
  }
  
  
 ?> 

<!--division principal-->
<div id="contenu">
    <h1>Suivi des paiements de fiche de frais</h1>
    <!--Affichage message confirmation-->
    <?php 
     $lgVisiteur = obtenirDetailUtilisateur($idConnexion, $idVisiteur);
     $nom = $lgVisiteur['nom'];
     $prenom = $lgVisiteur['prenom'];
     if($etape == "miseEnPaiement") {
         ?>
    <p class="info">La fiche de frais de <?php echo $prenom." ".$nom; ?> pour le mois de <?php echo obtenirLibelleMois(intval(substr($idMois, 4, 2)))." ".  intval(substr($idMois, 0, 4)); ?> est mise en paiement</p>
    <?php
     }
     ?>
    <h2>Fiches frais validées</h2>
    <form id="formSuiviPaiement" action="" method="post">         
        <input type="hidden" id="etape" name="etape" value="miseEnPaiement" />
        <input id="lstVisiteur" type="hidden" name="lstVisiteur" value="" />
        <input id="lstMois" type="hidden" name="lstMois" value="" /> 
    <table cellpadding="10" border="1">
        <tr>
            <th rowspan="2" style="text-align:center; vertical-align: middle;">Visiteur</th><th rowspan="2" style="text-align:center; vertical-align: middle;">Mois</th><th colspan="3" style="text-align:center; vertical-align: middle;">Fiche Frais</th><th rowspan="2" style="text-align:center; vertical-align: middle;">Action</th>
        </tr> 
        <tr>
            <th>Forfait</th><th>Hors forfait</th><th>Total</th>
        </tr>
   <?php
    $req = obtenirReqEltsFicheFrais();
    $idJeuFicheFrais = mysql_query($req, $idConnexion);
    $lgFicheFrais = mysql_fetch_assoc($idJeuFicheFrais);                   
    while (is_array($lgFicheFrais)){
    $nomVisiteur = filtrerChainePourNavig($lgFicheFrais['nom']);
    $prenomVisiteur = filtrerChainePourNavig($lgFicheFrais['prenom']);
    $idVisiteur = filtrerChainePourNavig($lgFicheFrais['idUtilisateur']) ;
    $mois = filtrerChainePourNavig($lgFicheFrais['mois']);
    $montantForfait = $lgFicheFrais['montantForfait'];
    $montantHorsForfait = $lgFicheFrais['montantHorsForfait'];
    $montantValide = $lgFicheFrais['montantValide'];
    ?>

        <tr>
            <td><?php echo $nomVisiteur." ".$prenomVisiteur; ?></td>
            <td><?php echo $mois; ?></td>
            <td><?php echo $montantForfait; ?></td>
            <td><?php echo $montantHorsForfait; ?></td>
            <td><?php echo $montantValide; ?></td>
            <td><a id="lkMiseEnPaiement" onclick="miseEnPaiement('<?php echo $idVisiteur."', '".$mois; ?>');" title="Mise en paiement de la fiche">Mise en paiement</a></td>            
        </tr>   
   <?php  
     $lgFicheFrais = mysql_fetch_assoc($idJeuFicheFrais);
    }
    mysql_free_result($idJeuFicheFrais);
   ?>   
    </table>
    </form>       
</div>
  
  
  
  
  <script type="text/javascript">
function miseEnPaiement(id, mois){
    alert("ok");
    document.getElementById("lstVisiteur").value = id;
    document.getElementById("lstMois").value = mois;
    document.getElementById("formSuiviPaiement").submit();
}
</script>
<?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
 ?>