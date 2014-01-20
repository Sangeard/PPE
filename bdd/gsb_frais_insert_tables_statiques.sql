--
-- Contenu de la table `FraisForfait`
--

INSERT INTO `FraisForfait` (`id`, `libelle`, `montant`) VALUES
('ETP', 'Forfait Etape', 110.00),
('KM', 'Frais Kilométrique', 0.62),
('NUI', 'Nuitée Hôtel', 80.00),
('REP', 'Repas Restaurant', 25.00);
--
-- Contenu de la table `LigneFraisForfait`
--

--
-- Contenu de la table `Etat`
--

INSERT INTO `Etat` (`id`, `libelle`) VALUES
('RB', 'Remboursée'),
('CL', 'Saisie clôturée'),
('CR', 'Fiche créée, saisie en cours'),
('VA', 'Validée et mise en paiement');


-- --------------------------------------------------------

--
-- Contenu de la table `Fonction`
--
-- --------------------------------------------------------
INSERT INTO `Fonction` (`idFonction`, `libelle`) VALUES
(1, 'Visiteur'),
(2, 'Comptable');
--
-- Contenu de la table `Visiteur`
--

INSERT INTO `Utilisateur` (`idUtilisateur`, `nom`, `prenom`, `login`, `mdp`, `idFonction`) VALUES
('a131', 'Villechalane', 'Louis', 'lvillachane', 'jux7g', 1),
('a17', 'Andre', 'David', 'dandre', 'oppg5', 1),
('a55', 'Bedos', 'Christian', 'cbedos', 'gmhxd', 1),
('a93', 'Tusseau', 'Louis', 'ltusseau', 'ktp3s', 1),
('b13', 'Bentot', 'Pascal', 'pbentot', 'doyw1', 1),
('b16', 'Bioret', 'Luc', 'lbioret', 'hrjfs', 1),
('b19', 'Bunisset', 'Francis', 'fbunisset', '4vbnd', 1),
('b25', 'Bunisset', 'Denise', 'dbunisset', 's1y1r', 1),
('b28', 'Cacheux', 'Bernard', 'bcacheux', 'uf7r3', 1),
('b34', 'Cadic', 'Eric', 'ecadic', '6u8dc', 1),
('b4', 'Charoze', 'Catherine', 'ccharoze', 'u817o', 1),
('b50', 'Clepkens', 'Christophe', 'cclepkens', 'bw1us', 1),
('b59', 'Cottin', 'Vincenne', 'vcottin', '2hoh9', 1),
('c14', 'Daburon', 'François', 'fdaburon', '7oqpv', 1),
('c3', 'De', 'Philippe', 'pde', 'gk9kx', 1),
('c54', 'Debelle', 'Michel', 'mdebelle', 'od5rt', 1),
('d13', 'Debelle', 'Jeanne', 'jdebelle', 'nvwqq', 1),
('d51', 'Debroise', 'Michel', 'mdebroise', 'sghkb', 1),
('e22', 'Desmarquest', 'Nathalie', 'ndesmarquest', 'f1fob', 1),
('e24', 'Desnost', 'Pierre', 'pdesnost', '4k2o5', 1),
('e39', 'Dudouit', 'Frédéric', 'fdudouit', '44im8', 1),
('e49', 'Duncombe', 'Claude', 'cduncombe', 'qf77j', 1),
('e5', 'Enault-Pascreau', 'Céline', 'cenault', 'y2qdu', 1),
('e52', 'Eynde', 'Valérie', 'veynde', 'i7sn3', 1),
('f21', 'Finck', 'Jacques', 'jfinck', 'mpb3t', 1),
('f39', 'Frémont', 'Fernande', 'ffremont', 'xs5tq', 1),
('f4', 'Gest', 'Alain', 'agest', 'dywvt', 1);

-- --------------------------------------------------------

--
-- Contenu de la table `Visiteur`
--

INSERT INTO `visiteur` (`id`, `adresse`, `cp`, `ville`, `dateEmbauche`) VALUES
('a131', '8 rue des Charmes', '46000', 'Cahors', '2005-12-21'),
('a17', '1 rue Petit', '46200', 'Lalbenque', '1998-11-23'),
('a55', '1 rue Peranud', '46250', 'Montcuq', '1995-01-12'),
('a93', '22 rue des Ternes', '46123', 'Gramat', '2000-05-01'),
('b13', '11 allée des Cerises', '46512', 'Bessines', '1992-07-09'),
('b16', '1 Avenue gambetta', '46000', 'Cahors', '1998-05-11'),
('b19', '10 rue des Perles', '93100', 'Montreuil', '1987-10-21'),
('b25', '23 rue Manin', '75019', 'paris', '2010-12-05'),
('b28', '114 rue Blanche', '75017', 'Paris', '2009-11-12'),
('b34', '123 avenue de la République', '75011', 'Paris', '2008-09-23'),
('b4', '100 rue Petit', '75019', 'Paris', '2005-11-12'),
('b50', '12 allée des Anges', '93230', 'Romainville', '2003-08-11'),
('b59', '36 rue Des Roches', '93100', 'Monteuil', '2001-11-18'),
('c14', '13 rue de Chanzy', '94000', 'Créteil', '2002-02-11'),
('c3', '13 rue Barthes', '94000', 'Créteil', '2010-12-14'),
('c54', '181 avenue Barbusse', '93210', 'Rosny', '2006-11-23'),
('d13', '134 allée des Joncs', '44000', 'Nantes', '2000-05-11'),
('d51', '2 Bld Jourdain', '44000', 'Nantes', '2001-04-17'),
('e22', '14 Place d Arc', '45000', 'Orléans', '2005-11-12'),
('e24', '16 avenue des Cèdres', '23200', 'Guéret', '2001-02-05'),
('e39', '18 rue de l église', '23120', 'GrandBourg', '2000-08-01'),
('e49', '19 rue de la tour', '23100', 'La souteraine', '1987-10-10'),
('e5', '25 place de la gare', '23200', 'Gueret', '1995-09-01'),
('e52', '3 Grand Place', '13015', 'Marseille', '1999-11-01'),
('f21', '10 avenue du Prado', '13002', 'Marseille', '2001-11-10'),
('f39', '4 route de la mer', '13012', 'Allauh', '1998-10-01'),
('f4', '30 avenue de la mer', '13025', 'Berre', '1985-11-01');
-- --------------------------------------------------------
-- --------------------------------------------------------
