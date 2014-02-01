CREATE TABLE IF NOT EXISTS `TypeVehicule` (
  `idTypeVehicule` char(4) NOT NULL,
  `libelle` char(20) DEFAULT NULL,
  `montant` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`idTypeVehicule`)
) ENGINE=InnoDB;

ALTER TABLE Visiteur ADD idTypeVehicule CHAR(4) ;
ALTER TABLE Visiteur ADD FOREIGN KEY (idTypeVehicule) REFERENCES TypeVehicule (idTypeVehicule) ;