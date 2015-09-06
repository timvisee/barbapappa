CREATE TABLE `bar_registry` (
	`registry_id` INT(11) NOT NULL AUTO_INCREMENT,
	`registry_key` TEXT NOT NULL,
	`registry_value` TEXT NULL,
	`registry_modified_datetime` DATETIME NOT NULL,
	PRIMARY KEY (`registry_id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
-- Dumping data for table barapp.bar_registry: ~0 rows (approximately)
/*!40000 ALTER TABLE `bar_registry` DISABLE KEYS */;
INSERT INTO `bar_registry` (`registry_id`, `registry_key`, `registry_value`, `registry_modified_datetime`) VALUES
	(1, 'account.login.allowUsername', '1', '2015-09-06 14:10:21'),
	(2, 'account.login.allowMail', '1', '2015-09-06 14:10:21'),
	(3, 'language.default.tag', 'nl-NL', '2015-09-06 14:10:21');
/*!40000 ALTER TABLE `bar_registry` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
