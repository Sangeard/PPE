<?php
/** 
 * Script génération de PDF
 * @package default
 * @todo  RAS
 */
require("_gestionSession.lib.php");
session_start();
//vérification si connecté
 if ( ! estVisiteurConnecte() ) {
      header("Location: ../cSeConnecter.php");  
  }
  
  require("_utilitairesEtGestionErreurs.lib.php");  
  require("fpdf.php");
 

class PDF extends FPDF
{
// En-tête
function Header()
{
    // Logo
    $this->Image('../images/logo.jpg',20,6,30);
    // Police Arial gras 15
    $this->SetTextColor(0, 51, 102);
    $this->SetFont('Arial','BU',20);
    // Décalage à droite
    $this->Cell(80);
    // Titre
    $this->Cell(30,10,'Fiche de frais',0,0,'C');
    // Saut de ligne
    $this->Ln(30);
}
function enteteFiche($bdd, $idVisiteur, $idMois){
    $idJeuFicheFrais = $bdd->query("SELECT idUtilisateur, prenom, nom FROM Utilisateur WHERE idUtilisateur = '".$idVisiteur."'");
    $lgFicheFrais = $idJeuFicheFrais->fetch();
    $idJeuFicheFrais->closeCursor();
    $this->SetFont('Times', 'BU', 15);
    $this->SetTextColor(0, 51, 102);
    $this->Cell(10);
    $this->Cell(30,7,'Visiteur : ',0,0);
    $this->SetFont('Times', '', 12);
    $this->SetTextColor(0,0,0);
    $this->SetFillColor(169, 234, 254);
    $this->Cell(20,7, utf8_decode('[ '.$lgFicheFrais['idUtilisateur'].' ]'),'LTB',0,'C',true);
    $this->Cell(25,7, utf8_decode($lgFicheFrais['prenom']),'TB',0,'R',true);
    $this->Cell(30, 7, utf8_decode($lgFicheFrais['nom']),'TBR',0,'L',true);
    $this->SetFont('Times', 'BU', 15);
    $this->SetTextColor(0, 51, 102);
    $this->Ln(15);
    $this->Cell(10);
    $this->Cell(30,7,'Mois :',0,0);
    $this->SetTextColor(0,0,0);
    $this->SetFont('Times', '', 12);
    $noMois = intval(substr($idMois, 4, 2));
    $annee = intval(substr($idMois, 0, 4));
    $this->Cell(50,7, utf8_decode(obtenirLibelleMois($noMois)).' '.$annee ,1,0,'C', true);    
}

//fonction frais forfait
    function tabFraisForfait($bdd, $idVisiteur, $idMois) {
      //affichage
        $this->Ln(25);
        $this->SetFont('Times', 'BI', 17);
        $this->SetTextColor(0, 51, 102);
        $this->Cell(70, 10, utf8_decode('Les frais forfaitisés'),0,0);
        $this->SetFont('Times', '', 12);
        $this->SetTextColor(0, 0,0);
        $this->Ln(15);
        $this->Cell(10);
        $this->SetFillColor(169, 234, 254);
        $this->Cell(50, 7, 'Frais forfaitaires', 'LTB', 0, 'C', true); 
        $this->Cell(40, 7, utf8_decode('Quantité'),'TB',0, 'C', true);
        $this->Cell(40, 7,'Montant unitaire','TB',0,'C', true);
        $this->Cell(40, 7,'Montant total','TBR',0, 'C', true); 
        $this->Ln();
        
     //donnees
        $idJeuFraisForfait =$bdd->query("SELECT FraisForfait.libelle,LigneFraisForfait.quantite, Bareme.montant,  SUM(LigneFraisForfait.quantite * Bareme.montant) AS montantForfait
    FROM LigneFraisForfait INNER JOIN FraisForfait ON LigneFraisForfait.idFraisForfait = FraisForfait.id
                           INNER JOIN FicheFrais ON FicheFrais.idVisiteur = LigneFraisForfait.idVisiteur AND FicheFrais.mois = LigneFraisForfait.mois
                           INNER JOIN Visiteur ON Visiteur.id = FicheFrais.idVisiteur
                           INNER JOIN Bareme ON Bareme.idFraisForfait = FraisForfait.id
    WHERE LigneFraisForfait.idVisiteur = '".$idVisiteur."'
    AND LigneFraisForfait.mois = '".$idMois."'
    AND (Bareme.idTypeVehicule IS NULL OR Bareme.idTypeVehicule = Visiteur.idTypeVehicule)
    GROUP BY FraisForfait.libelle ") or die("Erreur dans la req");  
        while($lgFraisForfait = $idJeuFraisForfait->fetch()){
            $this->Cell(10);
            $this->Cell(50, 7, utf8_decode($lgFraisForfait['libelle']), 1, 0, 'C'); 
            $this->Cell(40, 7, utf8_decode($lgFraisForfait['quantite']), 1, 0, 'C'); 
            $this->Cell(40, 7, utf8_decode($lgFraisForfait['montant']), 1, 0, 'C'); 
            $this->Cell(40, 7, utf8_decode($lgFraisForfait['montantForfait']), 1, 1, 'C'); 
        }
        $idJeuFraisForfait->closeCursor();
     
    }
    
    //fonction frais hors forfait
    function tabFraisHorsForfait($bdd, $idVisiteur, $idMois) {
        //affichage
        $this->Ln(15);
        $this->SetFont('Times', 'BI', 17);
        $this->SetTextColor(0, 51, 102);
        $this->Cell(70, 10, utf8_decode('Les frais hors forfait'),0,0);
        $this->SetFont('Times', '', 12);
        $this->SetTextColor(0, 0,0);
        $this->Ln(15);
        $this->Cell(10);
        $this->SetFillColor(169, 234, 254);
        $this->Cell(35, 7, 'Date', 'LTB', 0, 'C', true); 
        $this->Cell(100, 7, utf8_decode('Libellé'),'TB',0, 'C', true);
        $this->Cell(35, 7,'Montant','TBR',0,'C', true);
        //donnes
        $idJeuFraisHorsForfait =$bdd->query("select id, date, libelle, montant from LigneFraisHorsForfait
              where idVisiteur='" .$idVisiteur. "' and mois='" .$idMois. "'"); 
        while($lgFraisHorsForfait = $idJeuFraisHorsForfait->fetch()){
            $this->Ln();
            $this->Cell(10);
            $this->Cell(35, 7, utf8_decode($lgFraisHorsForfait['date']), 1, 0, 'C'); 
            $this->Cell(100, 7, utf8_decode($lgFraisHorsForfait['libelle']), 1, 0, 'C'); 
            $this->Cell(35, 7, utf8_decode($lgFraisHorsForfait['montant']), 1, 0, 'C'); 
        }
        $idJeuFraisHorsForfait->closeCursor();
    }
    
    // fonction totel des frais
    function totalFrais($bdd, $idVisiteur, $idMois) {
        // affichage
        $this->Ln(20);
        $this->Cell(100);
        $this->SetDrawColor(0, 51, 102);
        $this->SetLineWidth(0.5);
        $this->Cell(45,7,'Total des frais : ',1,0,'C');
        
        //donnees
        $idJeuTotalFrais = $bdd->query("select SUM(LigneFraisForfait.quantite * Bareme.montant) AS montantForfait, (FicheFrais.montantValide - SUM(LigneFraisForfait.quantite * Bareme.montant)) AS montantHorsForfait
        FROM Visiteur INNER JOIN FicheFrais ON Visiteur.id = FicheFrais.idVisiteur
                      INNER JOIN LigneFraisForfait ON FicheFrais.idVisiteur = LigneFraisForfait.idVisiteur AND FicheFrais.mois = LigneFraisForfait.mois
                      INNER JOIN FraisForfait ON LigneFraisForfait.idFraisForfait = FraisForfait.id
                      INNER JOIN Bareme ON Bareme.idFraisForfait = FraisForfait.id
        WHERE LigneFraisForfait.idVisiteur = '".$idVisiteur."'
        AND LigneFraisForfait.mois = '".$idMois."'
        AND (Bareme.idTypeVehicule IS NULL OR Bareme.idTypeVehicule = Visiteur.idTypeVehicule)") or die("Erreur dans la req");
        $lgTotalFrais = $idJeuTotalFrais->fetch();
        $idJeuTotalFrais->closeCursor();
        $montantTotal = $lgTotalFrais['montantForfait'] + $lgTotalFrais['montantHorsForfait'];
        $this->Cell(35, 7, utf8_decode($montantTotal), 1, 0, 'C');
    }
    
    //afficher la signature
    function afficherSignature(){
        $this->Ln(15);
        $this->Cell(100);
        $this->Cell(80, 10, utf8_decode("Fait à Le Mans le ".date('j')." ".obtenirLibelleMois(date('n'))." ".date('Y')), 0, 0);
        $this->Ln(10);
        $this->Cell(100);
        $this->Image('../images/signature.png', null, null);
    }

function AfficherFicheFrais(){
    try {
       $bdd=new PDO('mysql:host=localhost;dbname=gsb_frais', 'GSBfrais', 'pato'); 
    } catch (Exception $e) {
        die ("Erreur". $e->getMessage());
    }
    $idVisiteur = lireDonnee('lstVisiteur', "");
    $idMois = lireDonnee('lstMois', "");
    $this->AliasNbPages();
    $this->AddPage();
    $this->SetFont('Times','',12);
    $this->enteteFiche($bdd, $idVisiteur, $idMois);
    $this->tabFraisForfait($bdd, $idVisiteur, $idMois);
    $this->tabFraisHorsForfait($bdd, $idVisiteur, $idMois);
    $this->totalFrais($bdd, $idVisiteur, $idMois);
    $this->afficherSignature();
}

// Pied de page
function Footer()
{
    // Positionnement à 1,5 cm du bas
    $this->SetY(-15);
    // Police Arial italique 8
    $this->SetFont('Arial','I',8);
    // Numéro de page
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}
//recuperation des données
$idVisiteur = lireDonnee('lstVisiteur', "");
$idMois = lireDonnee('lstMois', "");
$fichier = "../pdf/".$idMois.$idVisiteur.".pdf";

//vérification si la personne a entre url
if(empty($idVisiteur) || empty($idMois)){
    header("Location: ../cSeConnecter.php");  
}

//verification fichier existe
if(!file_exists($fichier)){
 $pdf = new PDF();
 $pdf->AfficherFicheFrais();
 $pdf->Output($fichier);  
}
header('Content-type: application/pdf');
header('Content-Disposition: attachment; filename="downloaded.pdf"');
header('Cache-Control: private, max-age=0, must-revalidate');
readfile($fichier);
?>

