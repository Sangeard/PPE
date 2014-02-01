//<![CDATA[
//récupérer les mois en fonction du visiteur choisir
function obtenirMoisFonctionVisiteur(idVisiteur) {
  if(idVisiteur){
      if(getModifEnCours()){
          if(confirm("Attention, les modifications n\'ont pas été actualisées. Souhaitez-vous vraiment changer de visiteur sans actualiser les modifications ? ")){
             document.getElementById("formChoixVisiteur").submit();             
          }          
      }else{
             document.getElementById("formChoixVisiteur").submit();             

      }
  }  
}

// vérification si des modification sont en cours
function getModifEnCours() {
    var modif = false ;
    //si visiteur et mois choisi
    if(document.getElementById("divMsgModifFraisForfait")){
        //si modification type vehicule
        if(document.getElementById("divMsgModifTypeVehicule").style.display == "block"){
            modif=true;
            return modif;
        }
        //si modification frais forfait
        if(document.getElementById("divMsgModifFraisForfait").style.display == "block"){
            modif = true;
            return modif;
        }
        //si modification frais hors forfait
        var forms = document.getElementsByTagName("form");
        for (var cpt = 0; cpt < forms.length; cpt++){          
            var unForm = forms[cpt];
            if(unForm.id){
                if(unForm.id.search("formLigneHF") != -1){
                    if(document.getElementById("divMsgFraisHorsForfait"+ unForm.id.replace("formLigneHF", "")).style.display == "block"){
                        modif = true; 
                        return modif;
                    }
                }   
            }   
        }
        //modification en cours nombre de justificatif
        if(document.getElementById("divMsgNbJustificatif").style.display == "block"){
            modif = true;
            return modif;
        }       
    }
    return modif
}

//message actualisation frais forfait
function msgModificationFraisForfait(){
    document.getElementById("divMsgModifFraisForfait").style.display = "block";
    document.getElementById("divActionFraisForfait").style.display = "inline";
    document.getElementById("lkActualiserFraisForfait").style.display = "inline";
    document.getElementById("reinitialiserFraisForfait").style.display = "inline" ;
}

//message actualisation frais hors forfait
function msgModificationFraisHorsForfait(idAMontrer){
    document.getElementById("divMsgFraisHorsForfait"+idAMontrer).style.display = "block" ;
    document.getElementById("lkActualiserFraisHF"+idAMontrer).style.display = "inline" ;
    document.getElementById("lkReinitialiserHF"+idAMontrer).style.display = "inline" ;
    
}

//message actualisation nombre de justificatifs
function msgModificationNbJusitificatif() {
    document.getElementById("divMsgNbJustificatif").style.display = "block" ;
    document.getElementById("lkActualisationNbJustif").style.display = "inline" ;
    document.getElementById("lkReinitialiserNbJustif").style.display = "inline" ;
}

//message ectualisation du type de vehicule
function msgModificationTypeVehicule() {
   document.getElementById("divMsgModifTypeVehicule").style.display = "block" ;  
   document.getElementById("lkActualiserTypeVehicule").style.display = "inline" ;
   document.getElementById("lkReinitialiserTypeVehicule").style.display = "inline" ;   
}

//réinitialiser les éléments des frais forfaitisés
function reinitialiserFraisForfait() {
    document.getElementById("formValidationFraisForfait").reset();
    document.getElementById("divMsgModifFraisForfait").style.display = "none";
    document.getElementById("divActionFraisForfait").style.display = "none";   
}

//réinitialiser un ligne de frais HF
function reinitialiserFraisHF(id) {
    document.getElementById("formLigneHF"+id).reset() ;
    document.getElementById("divMsgFraisHorsForfait"+id).style.display = "none" ;
    document.getElementById("lkActualiserFraisHF"+id).style.display = "none" ;
    document.getElementById("lkReinitialiserHF"+id).style.display = "none" ;    
}

//réinitialisation du nombre de justificatifs
function reinitialiserNbJustir() {
    document.getElementById("formFraisHC").reset();
    document.getElementById("divMsgNbJustificatif").style.display = "none" ;
    document.getElementById("lkActualisationNbJustif").style.display = "none" ;
    document.getElementById("lkReinitialiserNbJustif").style.display = "none" ;    
}

//réinitialisation du type de vehicule
function reinitialiserTypeVehicule(){
    document.getElementById("formTypeVehicule").reset();
    document.getElementById("divMsgModifTypeVehicule").style.display = "none" ;
    document.getElementById("lkActualiserTypeVehicule").style.display = "none" ;
    document.getElementById("lkReinitialiserTypeVehicule").style.display = "none" ;  
}

//actualiser type de vehicule
function actualiserTypeVehicule(idVehicule) {
    var txtConfirm ;
  if(idVehicule != document.getElementById("lstTypeVehicule").value){
    txtConfirm = "Passer le type de vehicule de " + idVehicule;
    txtConfirm +=" a " +  document.getElementById("lstTypeVehicule").value;
    if(confirm("Etes vous sur de vouloir effectuer la modification suivante \n\n"+txtConfirm)){
        document.getElementById("formTypeVehicule").submit();
    }
  }else{
       alert("Aucune mise a jour n'a été faite");
       reinitialiserTypeVehicule()
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

//reporter un ligne de frais HF
function reporterFraisHF(id) {
   if(confirm("Etes vous sur de vouloir reporter cette ligne hors forfait"))    
    document.getElementById("etape"+id).value = "reporterLigneHF";
    document.getElementById("formLigneHF"+id).submit();   
}

//validation de la fiche de frais
function validerFicheFrais() {
    var nbRefus = 0;
    var nbAccepter = 0;
      var forms = document.getElementsByTagName("form");
      for(var cpt=0; cpt<forms.length; cpt++ ){
          var unForm = forms[cpt] ;
          if(unForm.id){
              if(unForm.id.search("formLigneHF") != -1){
                 if(document.getElementById("libelleHF"+unForm.id.replace("formLigneHF", "")).value.search("REFUSER") != -1){
                     nbRefus++ ;
                 }else{
                     nbAccepter++ ;
                 }
              }
          }
      }
      //vérification du nb de justificatifs par rapport au nb de ligne hors forfait accepter
      if(nbAccepter > document.getElementById("txtJustificatifs").value){
          alert("Attention, le nombre de justificatif devrait être au minimum égale au nombre de ligne hors forfait validé... ");
      }else{
          var message = "\n\n Détail de la validation";
          message += "\n - Lignes refusées : "+nbRefus;
          message += "\n - Lignes acceptées : "+nbAccepter;
          if(getModifEnCours()){
            if(confirm("Attention, les modifications n\'ont pas été actualisées. Souhaitez-vous vraiment valider la fiche sans actualiser les modifications ? ")){
              if(confirm("Une fois validée, cette fiche n\'apparaîtra plus dans les fiches à valider et vous ne pourrez plus la modifier. Souhaitez-vous valider tout de même cette fiche ?" +message)){
                  document.getElementById("formValideFiche").submit();
              }
           }
          }else{
              if(confirm("Une fois validée, cette fiche n\'apparaîtra plus dans les fiches à valider et vous ne pourrez plus la modifier. Souhaitez-vous valider tout de même cette fiche ?" +message)){
                  document.getElementById("formValideFiche").submit();
              }                
          }
         
     }  
}

//]]>


