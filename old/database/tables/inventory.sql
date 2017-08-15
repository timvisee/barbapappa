CREATE TABLE `bar_inventory` (
	`inventory_id` INT(11) NOT NULL AUTO_INCREMENT,
	`inventory_name` VARCHAR(64) NOT NULL,
	`inventory_creation_datetime` DATETIME NOT NULL,
	`inventory_modification_datetime` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`inventory_id`)
)
	COLLATE='latin1_swedish_ci'
	ENGINE=InnoDB
;
