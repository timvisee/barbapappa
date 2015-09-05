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
