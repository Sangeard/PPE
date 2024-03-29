DELIMITER //
CREATE FUNCTION montantTotal2(i_idVisiteur VARCHAR(5), i_idMois  VARCHAR(6)) RETURNS DECIMAL(10,2)

BEGIN

 DECLARE v_curseurvide BOOLEAN DEFAULT FALSE;
 DECLARE v_montantTotal DECIMAL(10,2);
 DECLARE v_montantForfait DECIMAL(10,2);
 DECLARE v_montantHorsForfait DECIMAL(10,2);
 DECLARE v_montantKm DECIMAL(10,2);
 DECLARE v_idLigneHF VARCHAR(5);
 DECLARE v_libelleLigneHF VARCHAR(100) ;
 DECLARE v_montantLigneHF DECIMAL(10,2);
 DECLARE c_montantLHF CURSOR FOR 
     SELECT id, libelle, montant
     FROM LigneFraisHorsForfait
     WHERE idVisiteur = i_idVisiteur
     AND mois = i_idMois ;
 DECLARE CONTINUE HANDLER
     FOR NOT FOUND
     SET v_curseurvide := TRUE;
SET v_montantHorsForfait := 0;

-- Calcul total frais forfait --
SELECT SUM(LigneFraisForfait.quantite * Bareme.montant) AS montantForfait INTO v_montantForfait 
    FROM LigneFraisForfait INNER JOIN FraisForfait ON LigneFraisForfait.idFraisForfait = FraisForfait.id
                           INNER JOIN FicheFrais ON FicheFrais.idVisiteur = LigneFraisForfait.idVisiteur AND FicheFrais.mois = LigneFraisForfait.mois
                           INNER JOIN Visiteur ON Visiteur.id = FicheFrais.idVisiteur
                           INNER JOIN Bareme ON Bareme.idFraisForfait = FraisForfait.id
    WHERE LigneFraisForfait.idVisiteur = i_idVisiteur
    AND LigneFraisForfait.mois = i_idMois
    AND (Bareme.idTypeVehicule IS NULL OR Bareme.idTypeVehicule = Visiteur.idTypeVehicule);
   
-- Calcul total hors forfait --
OPEN c_montantLHF ;
WHILE (NOT v_curseurvide) DO
    FETCH c_montantLHF INTO v_idLigneHF, v_libelleLigneHF, v_montantLigneHF ;
    IF(POSITION('REFUSER' IN v_libelleLigneHF) = 0) THEN
        SET v_montantHorsForfait := v_montantHorsForfait + v_montantLigneHF ;
    END IF;
END WHILE;
SET v_montantTotal := v_montantForfait + v_montantHorsForfait ;
CLOSE c_montantLHF;
RETURN v_montantTotal ;

END //

DELIMITER ;