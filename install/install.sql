CREATE TABLE IF NOT EXISTS `migration_version` (
  `version` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELETE FROM `migration_version`;
INSERT INTO `migration_version` (`version`) VALUES (0);