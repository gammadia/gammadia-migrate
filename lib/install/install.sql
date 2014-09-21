CREATE TABLE IF NOT EXISTS `migration_version` (
  `version` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `migration_version` (`version`) VALUES (0);