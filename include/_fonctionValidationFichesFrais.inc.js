//<![CDATA[
//récupérer les mois en fonction du visiteur choisir
function obtenirMoisFonctionVisiteur(idVisiteur) {
  if(idVisiteur){
      document.getElementById("formChoixVisiteur").submit();
  }  
}
//actualisation des frais forfaitisés
function actualiserFraisForfait(etp, km, nui, rep) {
   var modif = false;
   var txtModif ;
   //recherche des éléments modifier
   if(etp != document.getElementById("idETP").value){
     modif = true;
     txtModif = "Le nombre de forfait étape de " + etp + " à " + document.getElementById("idETP").value + "\n\n" ; 
   }
   if(km != document.getElementById("idKM").value){
     modif = true;
     txtModif += "Le nombre de frais kilométrique de " + km + " à " + document.getElementById("idKM").value + "\n\n" ;      
   }
   if(nui != document.getElementById("idNUI").value){
     modif = true; 
     txtModif += "Le nombre de nuitée Hôtel de " + nui + " à " + document.getElementById("idNUI").value + "\n\n" ;      
   }
   if(rep != document.getElementById("idREP").value){
     modif = true;         
     txtModif += "Le nombre de repas restaurant de " + rep + " à " + document.getElementById("idREP").value + "\n\n" ; 
   }
   if(modif){
       if(confirm("Etes vous sur de vouloir effectuer les modifications suivantes : \n\n" + txtModif)){      
           document.getElementById("formValidationFraisForfait").submit();
       }
   }else{
       alert("Aucune mise a jour n'a été faite");
       reinitialiserFraisForfait()
   }

}

//actualisation des frais hors forfait
function actualiserFraisHF(id, date, libelle, montant){
  var modif = false;
  var txtModif ;
  //vérification si il a eu des modifications
  if(date != document.getElementById("dateHF"+id).value){
      modif = true ;
      txtModif = "La date " +date+" par " + document.getElementById("dateHF"+id).value + "\n\n" ;
  }
  if(libelle != document.getElementById("libelleHF"+id).value){
      modif = true ;
       txtModif += "La description " +libelle+" par " + document.getElementById("libelleHF"+id).value + "\n\n";
 }
  if(montant != document.getElementById("montantHF"+id).value){
      modif = true ;
      txtModif += "Le montant " +montant+" par " + document.getElementById("montantHF"+id).value + "\n\n";
  }
  if(modif){
      if(confirm("Etes-vous sur de vouloir effectuer les modifications suivantes : \n\n" + txtModif)){
          document.getElementById("formLigneHF"+id).submit();
      }
  }else{
      alert("Aucune modification n'a été effectué");
      reinitialiserFraisHF(id);
  }
}

// actualiser le nombre de justificatifs
function actualiserNbJustif(nbJustif) {
    if(nbJustif != document.getElementById("txtJustificatifs").value){
        if(confirm("Etes vous sur de modifier le nombre de justificatif de"+nbJustif+" à "+document.getElementById("txtJustificatifs").value)){
            document.getElementById("formFraisHC").submit() ;
        }
    }else{
        alert("Vous n'avez effectué aucune modification");
        reinitialiserNbJustir();
    }
}

//réinitialiser les éléments des frais forfaitisés
function reinitialiserFraisForfait() {
    document.getElementById("formValidationFraisForfait").reset();
}

//réinitialiser un ligne de frais HF
function reinitialiserFraisHF(id) {
    document.getElementById("formLigneHF"+id).reset() ;
}

//réinitialisation du nombre de justificatifs
function reinitialiserNbJustir() {
    document.getElementById("formFraisHC").reset();
}

//refuser une ligne de frais HF
function refuserLigneHF(id) {
  if(confirm("Etes vous sur de vouloir refuser cette ligne hors forfait"))    
    document.getElementById("etape"+id).value = "refuserLigneHF";
    document.getElementById("formLigneHF"+id).submit();
}

//réintegrer un ligne de frais HF
function reintegrerLigneHF(id) {
  if(confirm("Etes vous sur de vouloir reintegrer cette ligne hors forfait"))    
    document.getElementById("etape"+id).value = "reintegrerLigneHF";
    document.getElementById("formLigneHF"+id).submit();    
}

//]]>


