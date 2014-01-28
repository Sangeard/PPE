delimiter //
create procedure reporterLigneFraisHF(IN i_idLigneHF INTEGER))

begin 

  declare v_idVisiteur CHAR(4) ;
  declare v_idMoisCourant CHAR(6) ;
  declare v_idMoisSuivant CHAR(6) ;
  declare v_libelleHF VARCHAR(100) ;
  declare v_dateHF DATE ;
  declare v_montantHF DECIMAL(10,2) ;
  declare v_mois CHAR(3) ;
  declare v_annee CHAR(4) ;
  declare v_test CHAR(4);
--
-- recuperation des donnees avec la requete
--
  select idVisiteur, mois, date, libelle, montant INTO v_idVisiteur, v_idMoisCourant, v_dateHF, v_libelleHF, v_montantHF
from LigneFraisHorsForfait where id = i_idLigneHF ;
--
-- calcul du mois suivant
--
SET v_mois := substring(v_idMoisCourant, 5 , 2);
SET v_annee := substring(v_idMoisCourant, 1, 4);
IF (v_mois = "12") THEN
   SET v_mois := "1";
   SET v_annee := v_annee + 1 ;
ELSE
   SET v_mois := v_mois + 1 ;
END IF ;
IF (CHARACTER_LENGTH(v_mois)=1) THEN
   SET v_mois := CONCAT("0", v_mois) ;
END IF ;
SET v_idMoisSuivant := CONCAT(v_annee , v_mois) ;
--
-- vérifier si la fiche du mois suivant existe
--
SELECT idVisiteur INTO v_test FROM FicheFrais WHERE idVisiteur = v_idVisiteur AND mois = v_idMoisSuivant ;
IF (v_test is null) THEN
  --
  -- ajout dune nouvelle fiche de frais
  --
  INSERT INTO FicheFrais(idVisiteur, mois, nbJustificatifs, MontantValide, idEtat, dateModif)
  VALUES (v_idVisiteur, v_idMoisSuivant, "0", NULL, "CR", CURRENT_DATE);
  --
  -- initialiser les frais forfaitisé à zéro
  --
  INSERT INTO LigneFraisForfait(idVisiteur, mois, idFraisForfait, quantite)
  VALUES (v_idVisiteur, v_idMoisSuivant, "ETP", 0),
         (v_idVisiteur, v_idMoisSuivant, "KM", 0),
         (v_idVisiteur, v_idMoisSuivant, "NUI", 0),
         (v_idVisiteur, v_idMoisSuivant, "REP", 0);
END IF ;

-- Créer la ligne HF dans le mois suivant--
INSERT INTO LigneFraisHorsForfait(idVisiteur, mois, date, libelle, montant)
VALUES (v_idVisiteur, v_idMoisSuivant, v_dateHF, v_libelleHF, v_montantHF) ;

-- Supprimer la ligne HF dans le mois courant--
DELETE FROM LigneFraisHorsForfait WHERE id = i_idLigneHF ;
   
end //
delimiter ;